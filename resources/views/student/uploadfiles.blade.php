@extends('layouts.enrollmentLayout')

@section('content')
  <script>
      $(document).ready(function(){
        setTimeout(function() {
        $('#successMsg').fadeOut('fast');
          }, 3000);
        
        $('#uploadFileBtn').on('click', function(){
          $('#modalFileUpload').modal('show');
        });

        // If no file uploaded modal file upload display
        @if($errors->any())
          $('#modalFileUpload').modal('show');
        @endif

        // File Upload Datatable
        $('#uploadedFiles-table').DataTable().destroy();
        var url = `/student/getUploadedFiles/${$('.stuID').attr("id")}`;

        var table = $('#uploadedFiles-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                type: 'GET',
            },
            columns: [
                { 
                    data: 'id', 
                    name: 'id', 
                    orderable: false, 
                    render: function(data, type, row, meta) {
                        return '<input type="checkbox" class="fileCheckBox" value="'+data+'"">';
                    }
                },
                { data: 'id', name: 'id' },
                { data: 'filename', name: 'filename' },
                { data: 'action', name: 'action' },
            ],
        });


        // Delete Individual
        var fileID;
      
        $(document).on("click", ".btnDel", function() {
            fileID = $(this).val();
        });

        $(document).on("click", "#delProceed", function() {
          window.location.href = `/student/delFile/${$('.stuID').attr("id")}/${fileID}`;
        });


        // Bulk Checkbox
        $( "#bulkCheck" ).on( "change", function() {
          $('.fileCheckBox').not(this).prop('checked', this.checked);
        });

        $(document).on("click", "input:checkbox[class=fileCheckBox]", function() {
          var checkboxTotal = $('input:checkbox[class=fileCheckBox]').length;
          var checkboxCheckedTotal = $("input:checkbox[class=fileCheckBox]:checked").length;

          if(checkboxTotal != checkboxCheckedTotal){
            $('#bulkCheck').prop('checked', false);
          }else{
            $('#bulkCheck').prop('checked', true);
          }
        });


        // Bulk Delete 
        $(document).on("click", "#bulkDel", function() {
          var files = [];
      
          $("input:checkbox[class=fileCheckBox]:checked").each(function(){
            files.push($(this).val());
          });
          
          if(files.length == 0){
            $('#bulkMsg').html("");
            $('#footerBulkDel').html("");
            $('#bulkMsg').append("There is no selected file.");  
            $('#footerBulkDel').append(
              "<button type='button' class='btn btn-danger btn-sm closeBulk'>Close</button>"
            );
          }else{
            $('#bulkMsg').html("");
            $('#footerBulkDel').html("");
            $('#bulkMsg').append("Are you sure you want to delete all the selected files?");
            $('#footerBulkDel').append(
              "<button type='button' class='btn btn-danger btn-sm closeBulk'>Cancel</button>"+
              "<button type='button' class='btn btn-primary btn-sm' id='bulkDelPro'>Proceed</button>"
            );
          }
          
          $('.closeBulk').on('click', function(){
            $('input[type=checkbox]').prop('checked', false);
            $('#delConfimDel').modal('hide');
          });
      
          // Delete Bulk Files
          $('#bulkDelPro').on('click', function(){      
            window.location.href = `/student/bulkFileDel/${$('.stuID').attr("id")}/${files}`;
          });
          $('#delConfimDel').modal('show');
        });
      });    
  </script>
        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Files of {{$student->first_name}} {{$student->last_name}}</h3>
              </div>

            </div>
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <div class="row">
                      <div class="col">
                        <h2 id="target">List of Files
                          <small>Below are the list of uploaded files of the student.</small>
                        </h2>
                      </div>
                      <div class="col">
                        <div class="row">
                          <div class="col-6"></div>
                          <div class="col-6 d-flex justify-content-end">
                            <button type="button" class="btn btn-success btn-sm ps-4 pe-4" id="uploadFileBtn">
                              <span class="text-white"><i class="fa fa-cloud-upload"></i> Upload File</span>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    @if(session()->has('success'))
                        <div class="alert alert-success" id="successMsg">
                            {{ session()->get('success') }}
                        </div>
                    @endif

                      <div>
                        <div class="row">
                          <div class="col-4">
                            <div class="row">
                              <div class="col-6">
                                <button type="button" class="btn btn-outline-danger btn-sm ps-4 pe-4" id="bulkDel">
                                  <span class="fs-6">Bulk Delete</span>
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    <div class="d-flex justify-content-center">
                      <input type="text" id="{{$student->id}}" class="stuID" hidden>
                      <table id="uploadedFiles-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                          <tr>
                            <th style="width: 40px">
                              <input type="checkbox" id="bulkCheck" >
                             </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>


      <!-- Modal File Upload -->
      <div class="modal fade" id="modalFileUpload" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="staticBackdropLabel">File Upload Form</h1>
              <button type="button" class="btn-close closeExcelModal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>    
            <div class="modal-body">
              <p class="text-danger">Instruction: Upload your file here</p>
              <form id="formFileUpload" action="{{route('student.uploadFile', ['student' => $student])}}" method="post" enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">
                @csrf
                <input type="file" class="form-control @if($errors->any()) is-invalid @endif" name="file_upload" id="file_upload" value="{{ old('file_upload') }}">
                @if($errors->any())
                    <div>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li class="list-group-item"><span class="text-danger">{{$error}}</span></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
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
      <!-- End Modal File Upload -->

      <!-- Modal Individual Delete -->
      <div class="modal fade bd-example-modal-sm" data-backdrop="static" data-keyboard="false" id="deleteConfim" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this file?</h6>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="delProceed">Proceed</button>
            </div>
          </div>
        </div>
      </div>
      <!-- End Modal Individual Delete -->

      <!-- Modal View  -->
      <div class="modal fade" id="modalViewFile" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title m-0" id="staticBackdropLabel">File View</h5>
              <button type="button" class="btn-close closeExcelModal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>    
            <div class="modal-body">
              <h2>Sample</h2>
            </div>
          </div>
        </div>
      </div>
      <!-- End Modal View -->

      <!-- Modal Bulk Delete -->
      <div class="modal fade bd-example-modal-sm" data-backdrop="static" data-keyboard="false" id="delConfimDel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title" id="bulkMsg"></h6>
            </div>
            <div class="modal-footer" id="footerBulkDel">
              <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="delProceed">Proceed</button>
            </div>
          </div>
        </div>
      </div>
      <!-- End Modal Bulk Delete -->

        <!-- /page content -->
@endsection
