<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Default File
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Enrollment Routes
Route::get('/', [EnrollmentController::class, 'index'])->name('enrollment.index');

Route::group(['prefix' => 'subject', 'middleware' => ['subject.maintenance']], function () {
  Route::get('/', [SubjectController::class, 'index'])->name('subject.index');
  Route::get('/create', [SubjectController::class, 'create'])->name('subject.create');
  Route::post('/create', [SubjectController::class, 'save'])->name('subject.save');
  Route::get('/edit/{subject}', [SubjectController::class, 'edit'])->name('subject.edit');
  Route::post('/edit/{subject}', [SubjectController::class, 'update'])->name('subject.update');
  Route::get('/delete/{subject}', [SubjectController::class, 'delete'])->name('subject.delete');
  Route::get('/bulkDel/{subject}', [SubjectController::class, 'bulkDel'])->name('subject.bulkDel');
});

Route::group(['prefix' => 'student', 'middleware' => ['student.maintenance']], function () {
  Route::get('/', [StudentController::class, 'index'])->name('student.index');
  Route::get('/create', [StudentController::class, 'create'])->name('student.create');
  Route::post('/create', [StudentController::class, 'save'])->name('student.save');
  Route::get('/edit/{student}', [StudentController::class, 'edit'])->name('student.edit');
  Route::post('/edit/{student}', [StudentController::class, 'update'])->name('student.update');
  Route::get('/delete/{student}', [StudentController::class, 'delete'])->name('student.delete');
  Route::get('/bulkDel/{student}', [StudentController::class, 'bulkDel'])->name('student.bulkDel');
  Route::get('/export', [StudentController::class, 'excelExport'])->name('student.excelExport');
  Route::post('/import', [StudentController::class, 'excelImport'])->name('student.excelImport');
  Route::get('/fileList/{student}', [StudentController::class, 'fileList'])->name('student.fileList');
  Route::post('/uploadFile/{student}', [StudentController::class, 'uploadFile'])->name('student.uploadFile');
  Route::get('/getUploadedFiles/{student}', [StudentController::class, 'getUploadedFiles'])->name('student.getUploadedFiles');
  Route::get('/delFile/{student}/{file}', [StudentController::class, 'delFile'])->name('student.delFile');
  Route::get('/downloadFile/{file}', [StudentController::class, 'downloadFile'])->name('student.downloadFile');
  Route::get('/saveSessionData', [StudentController::class, 'saveSessionData'])->name('student.saveSessionData');
  Route::get('/deleteSessions', [StudentController::class, 'deleteSessions'])->name('student.deleteSessions');
  Route::get('/bulkFileDel/{student}/{file}', [StudentController::class, 'bulkFileDel'])->name('student.bulkFileDel');
});

Route::group(['prefix' => 'enrollment','middleware' => ['student.maintenance']], function () {
  Route::get('/{subject}', [EnrollmentController::class, 'students'])->name('enrollment.students');
  Route::get('/create/{student}', [EnrollmentController::class, 'create'])->name('enrollment.create');
  Route::post('/create/{student}', [EnrollmentController::class, 'enroll'])->name('enrollment.enroll');
  Route::get('/edit/{student}', [EnrollmentController::class, 'edit'])->name('enrollment.edit');
  Route::post('/edit/{student}', [EnrollmentController::class, 'editEnroll'])->name('enrollment.editEnroll');
  Route::get('/getSub', [EnrollmentController::class, 'getSub'])->name('enrollment.getSub');
  Route::post('/enroll', [EnrollmentController::class, 'enrollSub'])->name('enrollment.enrollSub');
});


Route::group(['prefix'=> 'user', 'middleware' => ['user.maintenance']], function () {
  Route::get('/', [UserController::class, 'index'])->name('user.index');
  Route::get('/create', [UserController::class, 'create'])->name('user.create');
  Route::post('/create', [UserController::class, 'save'])->name('user.save');
  Route::get('/edit/{user}', [UserController::class, 'edit'])->name('user.edit');
  Route::post('/edit/{user}', [UserController::class, 'update'])->name('user.update');
  Route::get('/delete/{user}', [UserController::class, 'delete'])->name('user.delete');
  Route::get('/bulkDel/{user}', [UserController::class, 'bulkDel'])->name('user.bulkDel');
  Route::post('/bulkRole', [UserController::class, 'bulkRole'])->name('user.bulkRole');
  Route::get('/getUserRolesPer/{userId}', [UserController::class, 'getUserRolesPer'])->name('user.getUserRolesPer');
  Route::get('/getSelectedRolesPer/{roles}', [UserController::class, 'getSelectedRolesPer'])->name('user.getSelectedRolesPer');
});

Route::group(['prefix'=> 'permission'], function () {
  Route::get('/', [PermissionController::class, 'index'])->name('permission.index');
  Route::get('/create', [PermissionController::class, 'create'])->name('permission.create');
  Route::post('/create', [PermissionController::class, 'save'])->name('permission.save');
  Route::get('/edit/{permission}', [PermissionController::class, 'edit'])->name('permission.edit');
  Route::post('/edit/{permission}', [PermissionController::class, 'update'])->name('permission.update');
  Route::get('/delete/{permission}', [PermissionController::class, 'delete'])->name('permission.delete');
  Route::get('/bulkDel/{permission}', [PermissionController::class, 'bulkDel'])->name('permission.bulkDel');
});

Route::group(['prefix'=> 'role', 'middleware' => ['role.maintenance']], function () {
  Route::get('/', [RoleController::class, 'index'])->name('role.index');
  Route::get('/create', [RoleController::class, 'create'])->name('role.create');
  Route::post('/create', [RoleController::class, 'save'])->name('role.save');
  Route::get('/edit/{role}', [RoleController::class, 'edit'])->name('role.edit');
  Route::post('/edit/{role}', [RoleController::class, 'update'])->name('role.update');
  Route::get('/delete/{role}', [RoleController::class, 'delete'])->name('role.delete');
  Route::get('/bulkDel/{role}', [RoleController::class, 'bulkDel'])->name('role.bulkDel');
  Route::get('/getPer/{role}', [RoleController::class, 'getPer'])->name('role.getPer');
});

