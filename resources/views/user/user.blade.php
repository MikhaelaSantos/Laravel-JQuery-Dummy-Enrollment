@extends('layouts.enrollmentLayout')

@section('content')
    
<style>
  .custom-cursor-pointer {
    cursor: pointer;
  }

  #users-table td {
    border-bottom: solid 1px #000;
    padding: 2px;
    padding-left: 20px;
    color: #000;
    text-transform: capitalize;
  }

  #users-table .samTd{
    text-transform: uppercase;
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
      $('input:checkbox[class=userCheckBox]').not(this).prop('checked', this.checked);
    });

    $('input:checkbox[class=userCheckBox]').on('click', function(){   
      var checkboxTotal = $('input:checkbox[class=userCheckBox]').length;
      var checkboxCheckedTotal = $("input:checkbox[class=userCheckBox]:checked").length;

      if(checkboxTotal != checkboxCheckedTotal){
        $('#bulkCheck').prop('checked', false);
      }else{
        $('#bulkCheck').prop('checked', true);
      }
    });

    // Delete Eventlisteners
    var users = [];

    $( ".btnDel" ).on( "click", function() {
      users = $(this).val();
    });
    
    $( "#delProceed" ).on( "click", function() {
      window.location.href = `/user/delete/${users}`;
    });

    // Action Apply
    $( "#bulkApply" ).on( "click", function() {

      $("input:checkbox[class=userCheckBox]:checked").each(function(){
        users.push($(this).val());
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
        case "role":
          $('#bulkMsg').html("");
          $('#modalBody').html("");
          $('#footerBulkDel').html("");

          if(users.length == 0){
            $('#bulkMsg').append("There is no selected users.");  
            $('#footerBulkDel').append(
              "<button type='button' class='btn btn-danger btn-sm closeBulk'>Close</button>"
            )
            $('#delConfimDel').modal('show');
          }else{
            $('#modalRole').modal('show');
          }
          break;
        case "delete":
          $('#bulkMsg').html("");
          $('#modalBody').html("");
          $('#footerBulkDel').html("");
          if(users.length == 0){
            $('#bulkMsg').append("There is no selected users.");  
            $('#footerBulkDel').append(
              "<button type='button' class='btn btn-danger btn-sm closeBulk'>Close</button>"
            )
          }else{
            $('#bulkMsg').html("");
            $('#modalBody').html("");
            $('#footerBulkDel').html("");
            $('#bulkMsg').append("Are you sure you want to delete all the selected users?");
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
        window.location.href = `/user/bulkDel/${users}`;
      });

      });

    // Delay of Submit Form
    $('#formRole').one('submit', function(e){   
      e.preventDefault();

      $('#userStore').val(users);
      $('#roleStore').val($('#roleSel').val());

      $(this).submit();
    });

    // View All Roles and its Permissions
    $( ".viewAll" ).on( "click", function() {
      var userID = $(this).attr("id");
      $('#viewRolesModal').modal('show');

      $('#users-table').DataTable().destroy();
      
      var url = "{{ route('user.getUserRolesPer', ['userId' => ':userID']) }}";
      url = url.replace(':userID', userID);

      // Roles and Permission Display
      var groupColumn = 0;
      var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: url,
            type: 'GET'
        },
        columns: [
          // { data: 'id', name: 'id' },
          { data: 'role_name', name: 'role_name' },
          { data: 'permission_name', name: 'permission_name' },
        ],
        columnDefs: [
            { targets: groupColumn, visible: false }
        ],
        drawCallback: function (settings) {
            var api = this.api();
            var rows = api.rows({ page: 'current' }).nodes();
            var last = null;

            api.column(groupColumn, { page: 'current' })
                .data()
                .each(function (group, i) {
                    if (last !== group) {
                        $(rows)
                            .eq(i)
                            .before(
                                '<tr class="group bg-primary"><td class="text-white fs-6 fw-bold p-1 border border-0 samTd" colspan="5">' +
                                    group +
                                    '</td></tr>'
                            );
                        last = group;
                    }
                });
        }
      });

      // $(document).on('click', '.samTd', function() {
      //     sample = $(this).closest('tr').nextAll(':has(.odd):first').val();
      //     alert(sample);

      // });
      
      // // Second Try
      // var table = $('#users-table').DataTable({
      //   processing: true,
      //   serverSide: true,
      //   ajax: {
      //       url: url,
      //       type: 'GET'
      //   },
      //   columns: [
      //     // { data: 'id', name: 'id' },
      //     { data: 'role_name', name: 'role_name' },
      //     { data: 'permission_name', name: 'permission_name' },
      //   ],
      //   columnDefs: [
      //       { targets: groupColumn, visible: false }
      //   ],
      //   rowGroup: {
      //     dataSrc: 1,
      //     startRender: function (rows, group) {
      //         // Create a new <tr> for additional content before the group
      //       var additionalRow = $('<tr class="additional-row"><td colspan="8">Additional Content</td></tr>');

      //         // Append the additional row before the group header
      //         rows.nodes().to$().before(additionalRow);

      //         // Create the group header row
      //         var groupRow = $('<tr/>').append('<td colspan="8">' + group + '</td>');

      //         return groupRow;
      //         return $('<tr/>').append('<td colspan="8">' + group +' sample</td>');
      //     }
      //   }
      // });

    });
  
    // View All Permissions
    $( "#viewPer" ).on( "click", function() {
          var roleIDs = $('#roleSel').val();
          $('#sample').modal('show');

          $('#roles-table').DataTable().destroy();
          var url = "{{ route('user.getSelectedRolesPer', ['roles' => ':roles']) }}";
          url = url.replace(':roles', roleIDs);

          var groupColumn = 0;
          var table = $('#roles-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                type: 'GET'
            },
            columns: [
              { data: 'role_name', name: 'role_name' },
              { data: 'permission_name', name: 'permission_name' },
            ],
            columnDefs: [
                { targets: groupColumn, visible: false }
            ],
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function (group, i) {
                        if (last !== group) {
                            $(rows)
                                .eq(i)
                                .before(
                                    '<tr class="group bg-primary"><td class="text-white fs-6 fw-bold p-1 border border-0 samTd" colspan="5">' +
                                        group +
                                        '</td></tr>'
                                );

                            last = group;
                        }
                    });
            }
          });

        });
  });
</script>
        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Users</h3>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>List of Users</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      <div class="row">
                          <div class="col-sm-12">
                            <div class="card-box table-responsive">
                    <p class="text-muted font-13 m-b-30">
                      Listed Below are all the Users. Your can tamper the details on each users.
                    </p>
                    @if(session()->has('success'))
                        <div class="alert alert-success" id="successMsg">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    <div>
                    @if(\Auth::user()->hasAnyPermission(['update-user', 'delete-user']))
                    <div class="row">
                      <div class="col-4">
                        <div class="row">
                          <div class="col-6">
                            <select class="form-select form-select-sm" aria-label="Small select example" id="bulkSel">
                              <option selected value="">Bulk Action</option>
                              @can('update-user')
                              <option value="role">Assign Role</option>
                              @endcan
                              @can('delete-user')
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
                    </div>
                    @endif

                    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th style="width: 50px">
                            <input type="checkbox" id="bulkCheck" >
                           </th>
                          <th style="width:50px">ID</th>
                          <th>Name</th>
                          <th>Role</th>
                          @if(\Auth::user()->hasAnyPermission(['update-user', 'delete-user']))
                          <th style="width:350px">Actions</th>
                          @endif
                        </tr>
                      </thead>

                      <tbody>
                        @foreach ($users as $user)
                          <tr>
                            <td>
                              <input type="checkbox" class="userCheckBox" value="{{$user->id}}">
                             </td>
                            <td>{{$user->id}}</td>
                            <td>{{$user->name}}</td>
                            <td>
                              @php
                                  $getRoles = $user->getRoleNames()->toArray();
                                  if(count($getRoles) <= 4){
                                    $maxCount = count($getRoles);
                                  }else{
                                    $maxCount = 4;
                                  }
                                  $counter = 0;
                              @endphp

                              @if (!empty($getRoles))
                                @foreach($getRoles as $getRole)
                                  <span class="badge text-bg-primary p-1">{{ $getRole }}</span>
                                  @php
                                    $counter = $counter + 1;
                                    if ($counter == $maxCount) {
                                        break;
                                    }
                                  @endphp
                                @endforeach
                                @if (count($getRoles) > $maxCount)
                                  <span class="badge text-bg-primary p-1" id="{{$user->id}}"> + {{ count($getRoles)-$maxCount }}</span>
                                @endif
                                <span class="badge text-bg-secondary p-1 custom-cursor-pointer viewAll" id="{{$user->id}}">see more...</span>
                              @endif
                            </td>
                            @if(\Auth::user()->hasAnyPermission(['update-user', 'delete-user']))
                            <td>
                              @can('update-user')
                              <a href="{{route('user.edit', ['user'=>$user])}}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                              @endcan

                              @can('delete-user')
                              <button type="button" value={{$user->id}} class="btn btn-danger btnDel" data-toggle="modal" data-target="#deleteConfim">
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
                <h6 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this user?</h6>
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
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Bulk Delete -->

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

        <!-- Modal Bulk Assign Role -->
        <div class="modal fade" id="modalRole" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Role Assignment Form</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p class="text-danger">Instruction: Select the role you want to set on all the selected users.</p>
                <form id="formRole" action="{{route('user.bulkRole')}}" method="post" data-parsley-validate class="form-horizontal form-label-left">
                  @csrf
                  <input type="text" name="users" id="userStore" hidden>
                  <input type="text" name="roles" id="roleStore" hidden>
                  
                  <div class="item form-group">
                    <label for="role" class="col-form-label col-md-3 col-sm-3 label-align">User Role</label>
                    <div class="col-md-6 col-sm-6 ">
                      <select id="roleSel" class="form-control selectpicker border border-secondary-subtle" multiple aria-label="size 3 select example" data-size="3">
                        @foreach($allRoles as $role)
                          <option value="{{$role->id}}">{{$role->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <span for="role" class="d-flex align-items-center fs-5 custom-cursor-pointer" id="viewPer"><i class="fa fa-eye"></i></span>
                  </div>
              <div class="modal-footer">
                    <button id="submitForm" class="btn btn-success" type="submit">Assign Role</button>
                </div>
              
              </form>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Assign Role -->

        <!-- Modal for View Permissions -->
        <div class="modal fade" id="sample" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="titleModal">Role's Permissions</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="bodyModal">

                <table id="roles-table" class="display" style="width:100%">
                  <thead>
                      <tr>
                        <th>Role</th>
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
      </div>
        <!-- End Modal for View Permissions -->

        <!-- Modal for View Roles -->
          <div class="modal fade" id="viewRolesModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="titleModal">User Roles and Permissions</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="bodyModal">

                  <table id="users-table" class=" table-hover" style="width:100%">
                    <thead>
                        <tr>
                          {{-- <th>ID</th> --}}
                          {{-- <th>User</th> --}}
                          <th>Role</th>
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
        </div>
        <!-- End Modal for View Roles -->

        
        <!-- /page content -->
@endsection

