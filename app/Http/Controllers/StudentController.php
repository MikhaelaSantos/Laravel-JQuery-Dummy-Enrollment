<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Student;
use App\Models\File;
use Validator;
use App\Imports\StudentsImport;
use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class StudentController extends Controller
{
  public function validator (Request $request){

    return Validator::make($request->all(), [
      'first_name'  => 'required',
      'last_name'   => 'required',
      'middle_name' => 'required',
      'birthdate'   => 'required|date',
      'age'         => 'required|numeric'
    ]);

  }
    public function __construct(){
      $this->middleware('auth');
    }

    public function index(){
      $subjects = Subject::all();
      $students = Student::all();
      return view('student.student', ['students' => $students, 'subjects' => $subjects]);
    }

    public function create(){
      return view('student.create');
    }

    public function save(Request $request){
      $data = $this->validator($request);
  
      if ($data->fails()) {
        return redirect(route('student.create'))
                    ->withErrors($data)
                    ->withInput();
      }

      $newStudent = Student::create($data->validated());

      return redirect(route('student.index'))->with('success', 'Student "'.$newStudent->first_name.' '.$newStudent->last_name.'" details has been updated successfully.');

    }

    public function edit(Student $student){
      return view('student.edit', ['student' => $student]);
    }

    public function update(Request $request, Student $student){
      $data = $this->validator($request);
  
      if ($data->fails()) {
        return redirect(route('student.edit', ['student' => $student]))
                    ->withErrors($data)
                    ->withInput();
      }

      $student->update($data->validated());
  
      return redirect(route('student.index'))->with('success', 'Student "'.$student->first_name.' '.$student->last_name.'" details has been updated successfully.');
    }

    public function delete($student){
      $student=Student::where('id',$student)->delete();
      return redirect(route('student.index'))->with('success', 'Student details has been deleted successfully.');
    }

    public function bulkDel($student){
      $students = explode(',', $student);
  
      foreach($students as $student){
        $student=Student::where('id',$student)->delete();
      }
      return redirect(route('student.index'))->with('success', 'The details of all selected studets has been deleted successfully.');
    }


    // Excel Import Controller
    public function excelImport(Request $request) 
    {
      $data = Validator::make($request->all(), [
        'file'  => 'required|mimes:xls,xlsx'
      ]);

      if ($data->fails()) {
        return redirect(route('student.index'))
                    ->withErrors($data)
                    ->withInput();
      }

      // Collection of the Imported Data
      //     Excel::import(new StudentsImport, $request->file('file'));
      // } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
      //   dd($e->failures());
      //     foreach ($e->failures() as $failure) {
      //         $errors =[
      //           'row'   => $failure->row(),
      //           'error' => $failure->errors(),
      //           'value' => $failure->values()[$failure->attribute()]
      //         ];
      //     }
          
      //     return redirect(route('student.index'))
      //       ->withErrors($errors)
      //       ->withInput();
      // } 

      // $theCollection = Excel::toCollection(collect(), $request->file('file'))[0];
      $theCollection = Excel::toCollection(new StudentsImport, request()->file('file'))[0];
      $needColumns = [
        'ID', 
        'First Name',
        'Last Name',
        'Middle Name',
        'Birthdate',
        'Age',
      ];   

      // Check the Header Row on the Excel if it Matches the Needed Columns
      if($theCollection[0]->toArray() !== $needColumns) {
        if(count($theCollection[0]->toArray()) != count($needColumns)) {
          $error = [
            'file' => "The column in the excel doesn't match on the required number of columns."
          ];
        }else{
          $error = [
            'file' => "The column in the excel doesn't match on the required columns."
          ];
        }
        return redirect(route('student.index'))
            ->withErrors($error)
            ->withInput();
      }

      // Custom Validation Rule for Age
      Validator::extend('match_calculated_age', function ($attribute, $value, $parameters, $validator) {
        // dd($value);
        $data = $validator->getData();
        $calculatedAge = now()->diffInYears($data['birthdate']);
        if (!Carbon::parse($data['birthdate'])->isPast()) {
          $calculatedAge--;
        } 
        return $calculatedAge == $value;
      });

      $rules = [
        'first_name'  => 'required',
        'last_name'   => 'required',
        'middle_name' => 'required',
        'birthdate'   => 'required|date',
        'age'         => 'required|numeric|match_calculated_age'
      ];

      $errorMessages = [
        'first_name.required'       => 'The first name is required.',
        'last_name.required'        => 'The last name is required.',
        'middle_name.required'      => 'The middle name is required.',
        'birthdate.required'        => 'The birthdate is required.',
        'birthdate.date'            => 'The birthdate must be date.',
        'age.required'              => 'The age is required.',
        'age.numeric'               => 'The age must be numeric.',
        'age.match_calculated_age'  => 'The age must be matched on the calculated age on your birthdate.',
      ];

      

      // Validation of Each Row
      $firstIteration = true;
      $collectionError['col'] = [];
      $collectionToBeStored = [];
      $collectionAlreadyExits['col'] = [];
      foreach ($theCollection->toArray() as $index => $item) {
        if ($firstIteration) {
            $firstIteration = false;
        }else{
          if(is_numeric($item[4])){
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[4]);
            $formattedDate = $date->format('Y-m-d');
          }else if( empty($item[4])){
            $formattedDate = null;
          }else{
            try {
              $date = Carbon::parse($item[4]);
              $formattedDate = $date->format('Y-m-d');
            } catch (\Exception $e) {
              $formattedDate = $item[4];
            }
          }

          $tempItem= [
              'first_name'  => $item[1] ?? null,
              'last_name'   => $item[2] ?? null,
              'middle_name' => $item[3] ?? null,
              'birthdate'   => $formattedDate,
              'age'         => $item[5] ?? null,
          ];
          
          $data = Validator::make($tempItem, $rules, $errorMessages);
          if ($data->fails()) {
            $errors = $data->errors();
            // Get Value
            $itemDataWithErrors = [];
            $counter = 0;
            foreach ($tempItem as $key => $value) {
              if ($errors->has($key)) {
                  $itemDataWithErrors[$key] = $value;
                  $counter++;
              }
            }

            $errorData = [
              $index + 1,
              $data->errors()->messages(),
              $itemDataWithErrors
            ];
            array_push($collectionError['col'], $errorData);
          }else{
            $student = Student::where([
              ['first_name', '=', $tempItem['first_name']],
              ['last_name', '=', $tempItem['last_name']],
              ['middle_name', '=', $tempItem['middle_name']],
              ['birthdate', '=', $tempItem['birthdate']],
            ])->get();
            if(count($student) == 0){
              array_push($collectionToBeStored, $tempItem);
            }else{
              array_push($collectionAlreadyExits['col'], $tempItem);
            }
          }
          
        }
      }
      if(!empty($collectionError['col'])){
        // dd($collectionError['col']);
        return redirect(route('student.index'))
            ->withErrors('error')
            ->with('sample', $collectionError)
            ->withInput();
      }else if(!empty($collectionAlreadyExits['col'])){
        if(empty($collectionToBeStored)){
          return redirect(route('student.index'))
            ->with('allExisting', 'existed')
            ->withInput();
        }else{
          session(['collectionToBeStored' => $collectionToBeStored]);
          return redirect(route('student.index'))
            ->with('existing', $collectionAlreadyExits)
            ->withInput();
        }
        
      }else{
        // If Excel has no Error in Validation
        foreach ($collectionToBeStored as $item) {
          Student::create($item);
        }

        return redirect(route('student.index'))->with('success', 'The file has been imported to the list successfully');
      }
      
    }

    // Store Not existing data to DB
    public function saveSessionData (){
      
      

      return redirect(route('student.index'))->with('success', 'The file has been imported to the list successfully');
    }

    public function deleteSessions (){
      session()->forget('collectionToBeStored');
      session()->forget('allExisting');
      session()->forget('existing');
      return redirect(route('student.index'));
    }
    
    // Export Student List to Excel File Type
    public function excelExport() 
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }



    // Student File Code
    public function fileList(Student $student) 
    {
      return view('student.uploadfiles', ['student'=> $student]);
    }

    public function getUploadedFiles ($studID){
      $studFiles = File::where('student_id', $studID)->get();
      return DataTables::of($studFiles)
          ->addColumn('action', function($file){
              $downBtn = '<a href="../downloadFile/'.$file->id.'" class="btn btn-success btn-sm"><i class="fa fa-cloud-download"></i></a>';
              $delBtn = '<button type="button" value=' . $file->id . ' class="btn btn-danger btn-sm btnDel" data-toggle="modal" data-target="#deleteConfim"><i class="fa fa-trash"></i></button>';
              $viewBtn = '<button type="button" value=' . $file->id . ' class="btn btn-secondary btn-sm btnDel" data-toggle="modal" data-target="#modalViewFile"><i class="fa fa-eye"></i></button>';
              return $downBtn . $delBtn;
          })
          ->addColumn('filename', function($file){
            $parts = explode('_', $file->name);
            return end($parts);
          })
          ->addColumn('view', function($file){
            return 'storage/' . $file->name;
          })
          ->make(true);
    }

    public function uploadFile(Request $request, Student $student) 
    {

      $data = Validator::make($request->all(), [
        'file_upload' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120',
      ]);

      if ($data->fails()) {
        return redirect(route('student.fileList', ['student'=>$student]))
          ->withErrors($data)
          ->withInput();
      }

      $file = $request->file('file_upload');
      $fileName = time() . '_' . $file->getClientOriginalName();
      $fileExtension = $file->getClientOriginalExtension();
      $path = $file->storeAs('public', $fileName);






















      
      $newFile = new File();
      $newFile->name = $fileName;
      $newFile->path = $path;
      $newFile->extension = $fileExtension;
      $newFile->student_id = $student->id;
      $newFile->save();

      return redirect(route('student.fileList', ['student'=>$student]))->with('success', 'The file has been uploaded successfully');
    }

    public function delFile($studID, $fileID){
      $file = File::find($fileID);
      if (Storage::disk('public')->exists($file->name)) {
        Storage::disk('public')->delete($file->name);
      } 
      $file->delete();
      $student = Student::find($studID);
      return redirect(route('student.fileList', ['student'=>$student]))->with('success', 'The file has been delete successfully');
    }

    public function bulkFileDel($studID, $file){
      $files = explode(',', $file);
  
      foreach($files as $file){
        $file=File::where('id',$file)->delete();
      }
      $student = Student::find($studID);
      return redirect(route('student.fileList', ['student'=>$student]))->with('success', 'The details of all selected files has been deleted successfully.');
    }

    public function downloadFile($fileID)
    {
      $file = File::find($fileID);
      return response()->download(storage_path('app/' . $file->path));
    }

}
