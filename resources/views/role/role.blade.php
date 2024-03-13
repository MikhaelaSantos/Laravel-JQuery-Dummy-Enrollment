@extends('layouts.enrollmentLayout')

@section('content')
<style>
  .custom-cursor-pointer {
    cursor: pointer;
  }
</style>

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
    var role;

    $( ".btnDel" ).on( "click", function() {
      role = $(this).val();
    });
    
    $( "#delProceed" ).on( "click", function() {
      window.location.href = `/role/delete/${role}`;
    });

    $( "#bulkCheck" ).on( "change", function() {
      $('.studCheckBox').not(this).prop('checked', this.checked);
    });

    $( "#bulkDel" ).on( "click", function() {
      var roles = [];

      $("input:checkbox[class=perCheckBox]:checked").each(function(){
        roles.push($(this).val());
      });

      if(roles.length == 0){
        $('#bulkMsg').html("");
        $('#footerBulkDel').html("");
        $('#bulkMsg').append("There is no selected role.");  
        $('#footerBulkDel').append(
          "<button type='button' class='btn btn-danger btn-sm closeBulk'>Close</button>"
        );
      }else{
        $('#bulkMsg').html("");
        $('#footerBulkDel').html("");
        $('#bulkMsg').append("Are you sure you want to delete all the selected roles?");
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
        window.location.href = `/role/bulkDel/${roles}`;
      });

      $('#delConfimDel').modal('show');
    });

    // View All Roles and its Permissions
    $( ".viewAll" ).on( "click", function() {
      var roleID = $(this).attr("id");
      // alert(roleID);
      $('#viewRolesModal').modal('show');

      $('#permissions-table').DataTable().destroy();
      
      var url = "{{ route('role.getPer', ['role' => ':role']) }}";
      url = url.replace(':role', roleID);

      var table = $('#permissions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: url,
            type: 'GET'
        },
        columns: [
          { data: 'name', name: 'name' }
        ],
      });
    });

  });
</script>
        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Roles</h3>
              </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>List of Roles</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      <div class="row">
                          <div class="col-sm-12">
                            <div class="card-box table-responsive">
                    <p class="text-muted font-13 m-b-30">
                      @if(\Auth::user()->hasAnyPermission(['update-role', 'delete-role']))
                      Listed Below are all the roles. Your can tamper the details on each role.
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
                            @can('create-role')
                            <div class="col-6">
                              <a href="{{route('role.create')}}" class="btn btn-outline-primary btn-sm ps-4 pe-4 fs-6"><i class="fa fa-plus"></i> Add Role </a>
                            </div>
                            @endcan
                            @can('delete-role')
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
                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th style="width: 50px">
                            <input type="checkbox" id="bulkCheck" >
                           </th>
                          <th style="width:50px">ID</th>
                          <th>Roles</th>
                          <th>Permissions</th>
                          @if(\Auth::user()->hasAnyPermission(['update-role', 'delete-role']))
                          <th style="width:350px">Actions</th>
                          @endif
                        </tr>
                      </thead>

                      <tbody>
                        @foreach ($roles as $role)
                          <tr>
                            <td>
                              <input type="checkbox" class="perCheckBox" value="{{$role->id}}">
                             </td>
                            <td>{{$role->id}}</td>
                            <td>{{$role->name}}</td>
                            <td>
                              @php
                                $counter = 0;
                              @endphp
                              @foreach ($assignedPermissions as $assignedPer)
                                @if($role->id == $assignedPer->id)
                                  <span class="bg-primary rounded-2 badge">{{$assignedPer->permission_name}}</span>
                                  @php
                                    $counter = $counter + 1;
                                    if ($counter == 4) {
                                        break;
                                    }
                                  @endphp
                                @endif
                              @endforeach
                              @php
                                $totalCount = 0;
                                foreach ($assignedPermissions as $assignedPer) {
                                  if($role->id == $assignedPer->id){
                                    $totalCount = $totalCount + 1;
                                  }
                                }
                              @endphp
                              @if ($totalCount > 4)
                                <span class="badge text-bg-secondary p-1 custom-cursor-pointer viewAll" id="{{$role->id}}"> + {{ $totalCount-4}} see more...</span>
                              @endif
                            </td>
                            @if(\Auth::user()->hasAnyPermission(['update-role', 'delete-role']))
                            <td>
                              @can('update-role')
                              <a href="{{route('role.edit', ['role'=>$role])}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                              @endcan
                              @can('delete-role')
                              <button type="button" value={{$role->id}} class="btn btn-danger btnDel btn-sm" data-toggle="modal" data-target="#deleteConfim">
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
                <h6 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this role?</h6>
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

        <!-- Modal for View Roles -->
        <div class="modal fade" id="viewRolesModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="titleModal">Role Permissions</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="bodyModal">

                <table id="permissions-table" class=" table-hover" style="width:100%">
                  <thead>
                      <tr>
                        {{-- <th>ID</th> --}}
                        {{-- <th>User</th> --}}
                        {{-- <th>Role</th> --}}
                        <th>Permissions</th>
                      </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                {{-- <button type="button" class="btn btn-primary">Understood</button> --}}
              
            </div>
          </div>
        </div>
      <!-- End Modal for View Roles -->

        <!-- /page content -->
@endsection
