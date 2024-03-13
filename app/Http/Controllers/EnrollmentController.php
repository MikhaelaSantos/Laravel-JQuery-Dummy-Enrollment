<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{

  public function __construct(){
      $this->middleware('auth');
  }

  public function index(){
    $subjects = Subject::all()->count();
    $students = Student::all()->count();
    $enrolled = DB::select('SELECT COUNT(student_id) as total FROM enrollments INNER JOIN subjects ON enrollments.subject_id = subjects.id Group By student_id');
    $users = User::all()->count();

    return view('admin', ['users' => $users, 'subjects' => $subjects, 'students' => $students, 'enrolled' => count($enrolled)]);
  }

  public function create(Student $student){
    $subjects = Subject::all();
    $enrolled = DB::select('SELECT * FROM subjects INNER JOIN enrollments ON subjects.id = enrollments.subject_id WHERE student_id = ?', [$student->id]);

    if($enrolled == null){
      return view('enrollment.create', ['student' => $student, 'subjects' => $subjects]);
    }else{
      return view('enrollment.enrollment', ['student' => $student, 'enrolled' => $enrolled]);
    }
  }

  public function enroll(Request $request, Student $student){

    if($request->subjects != null){
      $subjects = explode(',',$request->subjects);
      for($x=0; $x<count($subjects); $x++){
        DB::table('enrollments')->insert([
          [
              'subject_id' => $subjects[$x],
              'student_id' => $student->id
          ],
        ]);
      }
  
      return redirect(route('enrollment.create', ['student'=>$student]))->with('success', 'Your now enrolled!'); 
    }else{
      return redirect(route('enrollment.create', ['student'=>$student]))->with('message', 'There is no selected subject to be enrolled.'); 
    }
  }

  public function edit(Student $student){
    $subjects = Subject::all();
    $enrolled = DB::select('SELECT * FROM subjects INNER JOIN enrollments ON subjects.id = enrollments.subject_id WHERE student_id = ?', [$student->id]);

    return view('enrollment.edit', ['student' => $student, 'subjects' => $subjects, 'enrolled' => $enrolled]);
  }

  public function editEnroll(Request $request, Student $student){
    // dd($request);
    $del=Enrollment::where('student_id',$student->id)->delete();

    if($request->subjects != null){
      $subjects = explode(',',$request->subjects);
      for($x=0; $x<count($subjects); $x++){
        DB::table('enrollments')->insert([
          [
              'subject_id' => $subjects[$x],
              'student_id' => $student->id
          ],
        ]);
      }
    }else{
      return redirect(route('enrollment.edit', ['student'=>$student]))->with('message', 'There is no selected subject to be enrolled.');
    }
    

    return redirect(route('enrollment.create', ['student'=>$student]))->with('success', 'Your now enrolled!');
  }

  public function students(Subject $subject){
    $students = DB::select('SELECT * FROM students INNER JOIN enrollments ON students.id = enrollments.student_id WHERE subject_id = ?', [$subject->id]);

    return view('enrollment.student', ['students' => $students, 'subject' => $subject]);
  }

  public function getSub(){
    $subjects = Subject::all();
    return response()->json($subjects);
  }

  public function enrollSub (Request $request){

    $students = explode(',',$request->students);
    $subjects = explode(',',$request->subjects);

    for($x=0; $x<count($students); $x++){
      $del=Enrollment::where('student_id',$students[$x])->delete();

      for($z=0; $z<count($subjects); $z++){
        DB::table('enrollments')->insert([
          [
              'subject_id' => $subjects[$z],
              'student_id' => $students[$x]
          ],
        ]);
      }
    }

    return redirect(route('student.index'))->with('success', 'All the students has been enrolled');

  }
}
