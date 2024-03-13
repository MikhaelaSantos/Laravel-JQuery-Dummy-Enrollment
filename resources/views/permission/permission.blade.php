@extends('layouts.enrollmentLayout')

@section('content')

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

    // For Bulk Checkbox
    $( "#bulkCheck" ).on( "change", function() {
      $('input:checkbox[class=perCheckBox]').not(this).prop('checked', this.checked);
    });

    $('input:checkbox[class=perCheckBox]').on('click', function(){   
      var checkboxTotal = $('input:checkbox[class=perCheckBox]').length;
      var checkboxCheckedTotal = $("input:checkbox[class=perCheckBox]:checked").length;

      if(checkboxTotal != checkboxCheckedTotal){
        $('#bulkCheck').prop('checked', false);
      }else{
        $('#bulkCheck').prop('checked', true);
      }
    });

    // Delete Eventlistener
    var permission;

    $(document).on("click", ".btnDel", function() {
        permission = $(this).val();
    });
    
    $( "#delProceed" ).on( "click", function() {
      window.location.href = `/permission/delete/${permission}`;
    });

    $( "#bulkCheck" ).on( "change", function() {
      $('.studCheckBox').not(this).prop('checked', this.checked);
    });

    $( "#bulkDel" ).on( "click", function() {
      var permissions = [];

      $("input:checkbox[class=perCheckBox]:checked").each(function(){
        permissions.push($(this).val());
      });

      if(permissions.length == 0){
        $('#bulkMsg').html("");
        $('#footerBulkDel').html("");
        $('#bulkMsg').append("There is no selected permission.");  
        $('#footerBulkDel').append(
          "<button type='button' class='btn btn-danger btn-sm closeBulk'>Close</button>"
        );
      }else{
        $('#bulkMsg').html("");
        $('#footerBulkDel').html("");
        $('#bulkMsg').append("Are you sure you want to delete all the selected permissions?");
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
        window.location.href = `/permission/bulkDel/${permissions}`;
      });

      $('#delConfimDel').modal('show');
    });

  });
</script>
        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Permissions</h3>
              </div>

            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>List of Permissions</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      <div class="row">
                          <div class="col-sm-12">
                            <div class="card-box table-responsive">
                    @can('update-user')
                    <p class="text-muted font-13 m-b-30">
                      @if(\Auth::user()->hasAnyPermission(['update-permission', 'delete-permission']))
                      Listed Below are all the permissions. Your can tamper the details on each permission.
                      @endif
                    </p>
                    @if(session()->has('success'))
                        <div class="alert alert-success" id="successMsg">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    <div>
                      <div class="row">
                        <div class="col-4">
                          <div class="row">
                            @can('create-permission')
                            <div class="col-6">
                              <a href="{{route('permission.create')}}" class="btn btn-outline-primary btn-sm p-1"><i class="fa fa-plus"></i> Add Permission </a>
                            </div>
                            @endcan
                            @can('delete-permission')
                            <div class="col-6">
                              <button type="button" class="btn btn-outline-danger btn-sm ps-4 pe-4" id="bulkDel">
                                <span class="fs-6">Bulk Delete</span>
                              </button>
                            </div>
                            @endcan
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
                          <th>Permissions</th>
                          @if(\Auth::user()->hasAnyPermission(['update-permission', 'delete-permission']))
                          <th style="width:350px">Actions</th>
                          @endif
                        </tr>
                      </thead>

                      <tbody>
                        @foreach ($permissions as $permission)
                          <tr>
                            <td>
                              <input type="checkbox" class="perCheckBox" value="{{$permission->id}}">
                             </td>
                            <td>{{$permission->id}}</td>
                            <td>{{$permission->name}}</td>
                            @if(\Auth::user()->hasAnyPermission(['update-permission', 'delete-permission']))
                            <td>
                              @can('update-permission')
                              <a href="{{route('permission.edit', ['permission'=>$permission])}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                              @endcan
                              @can('delete-permission')
                              <button type="button" value={{$permission->id}} class="btn btn-danger btn-sm btnDel" data-toggle="modal" data-target="#deleteConfim">
                                <i class="fa fa-trash"></i>
                              </button>
                              @endcan
                            </td>
                            @endif
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
                <h6 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this permission?</h6>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="delProceed">Proceed</button>
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
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Bulk Delete -->

        <!-- /page content -->
@endsection
