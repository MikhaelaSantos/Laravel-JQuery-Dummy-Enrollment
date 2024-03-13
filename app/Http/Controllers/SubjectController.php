<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Support\Facades\Session;
use Validator;
use App\Http\Requests\StorePostRequest;

class SubjectController extends Controller
{
  public function __construct(){
    $this->middleware('auth');
  }
  
  public function index(){
    $subjects = Subject::all();
    return view('subject.subject', ['subjects' => $subjects]);
  }

  public function create(){
    return view('subject.create');
  }

  public function save(Request $request){

    $data = Validator::make($request->all(), [
      'subject_name' => 'required'
    ]);

    if ($data->fails()) {
      return redirect(route('subject.create'))
                  ->withErrors($data)
                  ->withInput();
    }
    
    $newSubject = Subject::create($data->validated());

    return redirect(route('subject.index'))->with('success', 'The subject "'.$newSubject->subject_name.'" has been created successfully.');
  }

  public function edit(Subject $subject){
    return view('subject.edit', ['subject' => $subject]);
  }

  public function update(Request $request, Subject $subject){

    $data = Validator::make($request->all(), [
      'subject_name' => 'required|unique:subjects'
    ]);

    if ($data->fails()) {
      return redirect(route('subject.edit', ['subject' => $subject]))
                  ->withErrors($data)
                  ->withInput();
    }

    $subject->update($data->validated());

    return redirect(route('subject.index'))->with('success', 'The subject "'.$subject->subject_name.'" has been updated successfully.');
  }

  public function delete($subject){
    $subject=Subject::where('id',$subject)->delete();
    return redirect(route('subject.index'))->with('success', 'Subject details has been deleted successfully.');
  }

  public function bulkDel($subject){
    $subjects = explode(',', $subject);

    foreach($subjects as $subject){
      $subject=Subject::where('id',$subject)->delete();
    }
    return redirect(route('subject.index'))->with('success', 'Subject details has been deleted successfully.');
  }


}
