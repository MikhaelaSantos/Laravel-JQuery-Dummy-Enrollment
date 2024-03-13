@extends('layouts.enrollmentLayout')

@section('content')

<style>
    div.dataTables_wrapper div.dataTables_filter input {
        width: 150px; /* Adjust the width as needed */
    }
</style>
    
<script>
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

    $(document).ready(function(){
      setTimeout(function() {
          $('#successMsg').fadeOut('fast');
      }, 3000);

      var student;
      var values = [];

      $( ".btnDel" ).on( "click", function() {
        student = $(this).val();
      });
      
      $( "#delProceed" ).on( "click", function() {
        window.location.href = `/student/delete/${student}`;
      });

      $( "#bulkCheck" ).on( "change", function() {
        $('input:checkbox[class=studCheckBox]').not(this).prop('checked', this.checked);
      });

      $( "#bulkApply" ).on( "click", function() {

        $("input:checkbox[class=studCheckBox]:checked").each(function(){
          values.push($(this).val());
        });

        
        switch($('#bulkSel').val()) {
          case "":
            $('#bulkMsg').html("");
            $('#modalBody').html("");
            $('#footerBulkDel').html("");
            $('#bulkMsg').append("There is no selected action.");  
            $('#footerBulkDel').append(
              "<button type='button' class='btn btn-danger btn-sm closeBulk'>Close</button>"
            )
            $('#delConfimDel').modal('show');
            break;
          case "enroll":
            $('#bulkMsg').html("");
            $('#modalBody').html("");
            $('#footerBulkDel').html("");

            var csrfVar = $('meta[name="csrf-token"]').attr('content');

            if(values.length == 0){
              $('#bulkMsg').append("There is no selected students.");  
              $('#footerBulkDel').append(
                "<button type='button' class='btn btn-danger btn-sm closeBulk'>Close</button>"
              )
              $('#delConfimDel').modal('show');
            }else{
              $('#modalEnroll').modal('show');
            }
            break;
          case "delete":
            $('#bulkMsg').html("");
            $('#modalBody').html("");
            $('#footerBulkDel').html("");
            if(values.length == 0){
              $('#bulkMsg').append("There is no selected students.");  
              $('#footerBulkDel').append(
                "<button type='button' class='btn btn-danger btn-sm closeBulk'>Close</button>"
              )
            }else{
              $('#bulkMsg').html("");
              $('#modalBody').html("");
              $('#footerBulkDel').html("");
              $('#bulkMsg').append("Are you sure you want to delete all the selected students?");
              $('#footerBulkDel').append(
                "<button type='button' class='btn btn-danger btn-sm closeBulk'>Cancel</button>"+
                "<button type='button' class='btn btn-primary btn-sm' id='bulkDelPro'>Proceed</button>"
              );
            }
            $('#delConfimDel').modal('show');
            break;
        }

        $('.closeBulk').on('click', function(){
          $('input[type=checkbox]').prop('checked', false);
          $('#delConfimDel').modal('hide');
          // $('#modalEnroll').modal('hide');
        });

        // Delete Bulk Students
        $('#bulkDelPro').on('click', function(){   
          window.location.href = `/student/bulkDel/${values}`;
        });

      });

      $('#formEnroll').one('submit', function(e){   
        e.preventDefault();

        var subjects = [];
        $("input:checkbox[class=subEnroll]:checked").each(function(){
          subjects.push($(this).val());
        });

        $('#studStore').val(values);
        $('#subStore').val(subjects);

        $(this).submit();
      });

      $('input:checkbox[class=studCheckBox]').on('click', function(){   

        var checkboxTotal = $('input:checkbox[class=studCheckBox]').length;
        var checkboxCheckedTotal = $("input:checkbox[class=studCheckBox]:checked").length;

        if(checkboxTotal != checkboxCheckedTotal){
          $('#bulkCheck').prop('checked', false);
        }else{
          $('#bulkCheck').prop('checked', true);
        }
      });


      // Import Excel
      $('#importExcelBtn').on('click', function(){
        $('#modalExcelImport').modal('show');
      });

      $('.closeExcelModal').on('click', function(){
        $('#studExcel').val(null);
        $('#errorCont').html("");
      });

      // If no excel file uploaded modal import excel display
      @if($errors->any())
        $('#errors-table').DataTable();
        $('#modalExcelImport').modal('show');
      @endif

      // If there is an existing entry in DB
      @if(session('existing') || session('allExisting'))
      $('#modalAlreadyExists').modal('show');
        $('#existing-table').DataTable();
      @endif

    });

  </script>

        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Students</h3>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>List of Students</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      <div class="row">
                          <div class="col-sm-12">
                            <div class="card-box table-responsive">
                    <p class="text-muted font-13 m-b-30">
                      Listed Below are all the Students. Your can tamper the details on each students.
                    </p>
                    @if(session()->has('success'))
                        <div class="alert alert-success" id="successMsg">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    <div>
                      @if (\Auth::user()->hasAnyPermission(['update-student', 'delete-student']))
                      <div class="row d-flex justify-content-between">
                        <div class="col-4">
                          <div class="row">
                            <div class="col-6">
                              <select class="form-select form-select-sm" aria-label="Small select example" id="bulkSel">
                                <option selected value="">Bulk Action</option>
                                @can('update-student')
                                <option value="enroll">Enroll</option>
                                @endcan
                                @can('delete-student')
                                <option value="delete">Delete</option>
                                @endcan
                              </select>
                            </div>
                            <div class="col">
                              <button type="button" class="btn btn-outline-secondary btn-sm ps-4 pe-4" id="bulkApply">
                                <span class="fs-6">Apply</span>
                              </button>
                            </div>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="row">
                            <div class="col-6">
                              @can('export-student')
                              <a href="{{route('student.excelExport')}}" class="btn btn-outline-success btn-sm ps-4 pe-4" id="exportExcelBtn">
                                <span><i class="fa fa-cloud-download"></i> Export</span>
                              </a>
                              @endcan
                            </div>
                            <div class="col-6">
                              @can('import-student')
                              <button type="button" class="btn btn-success btn-sm ps-4 pe-4" id="importExcelBtn">
                                <span><i class="fa fa-cloud-upload"></i> Import</span>
                              </button>
                              @endcan
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endif
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>
                            <input type="checkbox" id="bulkCheck" >
                           </th>
                          <th>ID</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Middle Name</th>
                          <th>Birthdate</th>
                          <th>Age</th>
                          <th>Actions</th>
                        </tr>
                      </thead>

                      <tbody>
                        @foreach ($students as $student)
                          <tr>
                            <td>
                              <input type="checkbox" class="studCheckBox" value="{{$student->id}}">
                             </td>
                            <td>{{$student->id}}</td>
                            <td>{{$student->first_name}}</td>
                            <td>{{$student->last_name}}</td>
                            <td>{{$student->middle_name}}</td>
                            <td>{{ $student->birthdate->format('F d, Y') }}</td>
                            <td>{{$student->age}}</td>
                            <td>
                              @can('update-student')
                              <a href="{{route('student.edit', ['student'=>$student])}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                              @endcan
                              @can('delete-student')
                              <button type="button" value={{$student->id}} class="btn btn-danger btn-sm btnDel" data-toggle="modal" data-target="#deleteConfim">
                                <i class="fa fa-trash"></i>
                              </button>
                              @endcan
                              <a href="{{route('enrollment.create', ['student'=>$student])}}" class="btn btn-secondary btn-sm"><i class="fa fa-mortar-board"></i></a>
                              <a href="{{route('student.fileList', ['student'=>$student])}}" class="btn btn-success btn-sm"><i class="fa fa-folder"></i></a>
                            </td>
                          </tr>   
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  </div>
              </div>
            </div>
          </div>

         <!-- Modal Individual Delete-->
         <div class="modal fade bd-example-modal-sm" data-backdrop="static" data-keyboard="false" id="deleteConfim" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this student details?</h6>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="delProceed">Proceed</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal -->

        <!-- Modal Bulk Action -->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="delConfimDel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h6 class="modal-title" id="bulkMsg"></h6>
              </div>
              <div class="modal-footer" id="footerBulkDel">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="delProceed">Proceed</button>
                {{-- <a href="{{route('subject.delete', ['subject'=>$subject])}}" class="btn btn-primary btn-sm">Proceed</a> --}}
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Bulk Action -->

        <!-- Modal Bulk Enroll -->
        <div class="modal fade" id="modalEnroll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Enrollment Form</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p class="text-danger">Instruction: Check the box of the subject you want to enroll the students</p>
                <form id="formEnroll" action="{{route('enrollment.enrollSub')}}" method="post" data-parsley-validate class="form-horizontal form-label-left">
                  @csrf
                  <input type="text" name="students" id="studStore" hidden>
                  <input type="text" name="subjects" id="subStore" hidden>
                  <div class="item form-group d-flex justify-content-center">
                      <div class="col-md-9 col-sm-9">
                        <ul class="to_do">
                          @foreach ($subjects as $subject)
                            <li style="background-color:#fff;" class="m-0 p-0">
                              <p>
                                <input type="checkbox" class="subEnroll" value = "{{$subject->id}}" id="{{$subject->id}}">
                                <label for="{{$subject->id}}" class="form-check-label fs-5 ps-1 ">{{$subject->subject_name}}</label>
                              </p>
                            </li>
                          @endforeach
                        </ul>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                    <button class="btn btn-primary" type="button" id="resetCheckbox">Reset</button>
                    <button id="submitForm" class="btn btn-success" type="submit">Enroll Subjects</button>
                </div>
              
              </form>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Bulk Enroll -->

        <!-- Modal Excel Import -->
        <div class="modal fade" id="modalExcelImport" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Excel Import Form</h1>
                <button type="button" class="btn-close closeExcelModal" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>    
              <div class="modal-body">
                <p class="text-danger">Instruction: Upload here your Excel File</p>
                <form id="formExcelImport" action="{{route('student.excelImport')}}" method="post" enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">
                  @csrf
                  <input type="file" class="form-control @if($errors->any()) is-invalid @endif" name="file" id="studExcel" value="{{ old('file') }}">
                  <div id="errorCont">
                    @if($errors->any())
                      @if($errors->has('file'))
                        <span class="text-danger mt-1">{{ $errors->first('file') }}</span>
                      @else
                        @if(session('sample'))
                          <h6 class="mt-3 text-danger">List of Issues <small>Solve the listed issues to import the file.</small></h6>
                          <table id="errors-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                              <tr>
                                <th>Row</th>
                                <th>Issue</th>
                                <th>Inputed Value</th>
                              </tr>
                            </thead>
      
                            <tbody>
                              @foreach(session('sample')['col'] as $item)
                                @foreach($item[1] as $key => $errors)
                                  @foreach($errors as $err)
                                    <tr>
                                      <td>{{ $item[0] }}</td>
                                      <td>{{$err}}</td>
                                      <td>{{ $item[2][$key] }}</td>
                                    </tr>
                                  @endforeach 
                                @endforeach
                              @endforeach
                            </tbody>
                          </table>
                        @endif
                      @endif
                    @endif
                  </div>
              </div>
              <div class="modal-footer">
                    <button type="button" class="btn btn-danger closeExcelModal" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    <button id="submitForm" class="btn btn-primary" type="submit">Import Excel</button>
                </div>
              
              </form>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Excel Import -->

        <!-- Modal Existing Data on Imported Excel -->
        <div class="modal fade" id="modalAlreadyExists" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Imported File Status</h1>
              </div>    
              <div class="modal-body">
                @if(session('existing'))
                  <h6>List of Existing Students</h6>
                  <p class="text-danger">This list won't be added to the student because they are already existed.</p>
                  <table id="existing-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Middle Name</th>
                        <th>Birthdate</th>
                        <th>Age</th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach(session('existing')['col'] as $item)
                        <tr>
                          <td>{{ $item['first_name'] }}</td>
                          <td>{{ $item['last_name'] }}</td>
                          <td>{{ $item['middle_name'] }}</td>
                          <td>{{ $item['birthdate']}}</td>
                          <td>{{ $item['age'] }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                @endif
                @if(session('allExisting'))
                  <h6 class="text-danger">All data in the list already exists; therefore, no additional data will be added to the Student List.</h6>
                @endif
              </div>
              <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-danger closeExcelModal" data-bs-dismiss="modal" aria-label="Close">Cancel</button> --}}
                    <a href="{{route('student.deleteSessions')}}" class="btn btn-danger">Cancel</a>
                    @if(session('existing'))
                    <a href="{{route('student.saveSessionData')}}" class="btn btn-primary">Proceed Import Excel</a>
                    @endif
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Existing Data on Imported Excel -->


        <!-- /page content -->
@endsection
