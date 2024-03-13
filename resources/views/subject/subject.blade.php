@extends('layouts.enrollmentLayout')

@section('content')
        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Subjects</h3>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>List of Subjects</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      <div class="row">
                          <div class="col-sm-12">
                            <div class="card-box table-responsive">
                    <p class="text-muted font-13 m-b-30">
                      Listed Below are all the Subjects. Your can tamper the details on each subjects.
                    </p>
                    @if(session()->has('success'))
                        <div class="alert alert-success" id="successMsg">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    @can('delete-subject')
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
                    @endcan
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th style="width: 50px">
                            <input type="checkbox" id="bulkCheck" >
                           </th>
                          <th style="width:50px">ID</th>
                          <th>Name</th>
                          <th style="width:350px">Actions</th>
                        </tr>
                      </thead>

                      <tbody>
                        @foreach ($subjects as $subject)
                          <tr>
                            <td>
                              <input type="checkbox" class="subCheckBox" value="{{$subject->id}}">
                             </td>
                            <td>{{$subject->id}}</td>
                            <td>{{$subject->subject_name}}</td>
                            <td>
                              @can('update-subject')
                              <a href="{{route('subject.edit', ['subject'=>$subject])}}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                              @endcan
                              @can('delete-subject')
                              <button type="button" value={{$subject->id}} class="btn btn-danger btnDel" data-toggle="modal" data-target="#deleteConfim">
                                <i class="fa fa-trash"></i>
                              </button>
                              @endcan
                             <a href="{{route('enrollment.students', ['subject'=>$subject])}}" class="btn btn-secondary"><i class="fa fa-eye"></i></a>
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
              </div>
          </div>

        <!-- Modal Individual Delete -->
        <div class="modal fade bd-example-modal-sm" data-backdrop="static" data-keyboard="false" id="deleteConfim" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this subject?</h6>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="delProceed">Proceed</button>
                {{-- <a href="{{route('subject.delete', ['subject'=>$subject])}}" class="btn btn-primary btn-sm">Proceed</a> --}}
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Individual Delete -->

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
                {{-- <a href="{{route('subject.delete', ['subject'=>$subject])}}" class="btn btn-primary btn-sm">Proceed</a> --}}
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Bulk Delete -->
        
        <!-- /page content -->
        <script>
          $(document).ready(function(){
      
            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
            });
      
            setTimeout(function() {
                $('#successMsg').fadeOut('fast');
            }, 3000); 
      
            var subject;
      
            $( ".btnDel" ).on( "click", function() {
              subject = $(this).val();
            });
            
            $( "#delProceed" ).on( "click", function() {
              window.location.href = `/subject/delete/${subject}`;
            });
      
            // Bulk Checkbox
            $( "#bulkCheck" ).on( "change", function() {
              $('.subCheckBox').not(this).prop('checked', this.checked);
            });

            $('input:checkbox[class=subCheckBox]').on('click', function(){   
              var checkboxTotal = $('input:checkbox[class=subCheckBox]').length;
              var checkboxCheckedTotal = $("input:checkbox[class=subCheckBox]:checked").length;

              if(checkboxTotal != checkboxCheckedTotal){
                $('#bulkCheck').prop('checked', false);
              }else{
                $('#bulkCheck').prop('checked', true);
              }
            });


            // Bulk Delete
            $( "#bulkDel" ).on( "click", function() {
              var values = [];
      
              $("input:checkbox[class=subCheckBox]:checked").each(function(){
                values.push($(this).val());
              });
      
              if(values.length == 0){
                $('#bulkMsg').html("");
                $('#footerBulkDel').html("");
                $('#bulkMsg').append("There is no selected subject.");  
                $('#footerBulkDel').append(
                  "<button type='button' class='btn btn-danger btn-sm closeBulk'>Close</button>"
                );
              }else{
                $('#bulkMsg').html("");
                $('#footerBulkDel').html("");
                $('#bulkMsg').append("Are you sure you want to delete all the selected subjects?");
                $('#footerBulkDel').append(
                  "<button type='button' class='btn btn-danger btn-sm closeBulk'>Cancel</button>"+
                  "<button type='button' class='btn btn-primary btn-sm' id='bulkDelPro'>Proceed</button>"
                );
              }
      
              $('.closeBulk').on('click', function(){
                $('input[type=checkbox]').prop('checked', false);
                $('#delConfimDel').modal('hide');
              });
      
              // Delete Bulk Subjects
              $('#bulkDelPro').on('click', function(){      
                window.location.href = `/subject/bulkDel/${values}`;
              });
      
              $('#delConfimDel').modal('show');
            });
      
          });
        </script>
@endsection


