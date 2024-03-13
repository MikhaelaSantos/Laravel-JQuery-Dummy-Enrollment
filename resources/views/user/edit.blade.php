@extends('layouts.enrollmentLayout')

@section('content')
    
<style>
  .custom-cursor-pointer {
    cursor: pointer;
  }

  #roles-table td {
    border-bottom: solid 1px #000;
    padding: 2px;
    padding-left: 20px;
    color: #000;
    text-transform: capitalize;
  }

  #roles-table .samTd{
    text-transform: uppercase;
  }

</style>
        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Edit User</h3>
              </div>

            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>User Form <small>Fill up the form to edit the user account.</small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form action="{{route('user.update', ['user' => $user])}}" method="POST" id="userForm" data-parsley-validate class="form-horizontal form-label-left">
                      @csrf
                      @method('post')
  
                      <input type="text" name="roles" id="roleStore" hidden>
                      <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="name" >Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                          <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{$user->name}}">
                          @error('name') 
                            <span class="text-danger">{{$message}}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="last_name">Email <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{$user->email}}">
                          @error('email') 
                            <span class="text-danger">{{$message}}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="item form-group">
                        <label for="password" class="col-form-label col-md-3 col-sm-3 label-align">Password <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 ">
                          <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password">
                          @error('password') 
                            <span class="text-danger">{{$message}}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="item form-group">
                        <label for="password_confirmation" class="col-form-label col-md-3 col-sm-3 label-align">Confirm Password <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 ">
                          <input id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation">
                        </div>
                      </div>

                      @php
                        $userRoles = $user->roles;
                        if(!empty($userRoles[0])){
                          $firstUserRole = $userRoles->first()->id;
                        }else{
                          $firstUserRole = null;
                        }
                      @endphp
                      <div class="item form-group">
                        <label for="role" class="col-form-label col-md-3 col-sm-3 label-align">User Role</label>
                        <div class="col-md-6 col-sm-6 ">
                          <select id="roleSel" class="form-control selectpicker border border-secondary-subtle" multiple aria-label="size 3 select example" data-size="3">
                            @foreach($roles as $role)
                              @php
                                $selRole = false;
                                foreach($userRoles as $userRole){
                                  if($role->id == $userRole->id){
                                    $selRole = true;
                                  }
                                }
                              @endphp
                              <option value="{{$role->id}}" {{ $selRole ? 'selected' : '' }}>{{$role->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <span for="role" class="d-flex align-items-center fs-5 custom-cursor-pointer" id="viewPer"><i class="fa fa-eye"></i></span>
                      </div>

                      <div class="ln_solid"></div>
                      <div class="item form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                          <a href="{{route('user.index')}}" class="btn btn-secondary text-center">Back to User List</a>
                          <button class="btn btn-primary" type="reset">Reset</button>
                          <button type="submit" class="btn btn-success">Update User</button>
                        </div>
                      </div>
                    
                    </form>
                  </div>
                </div>
              </div>
            </div>
        <!-- Modal for View Roles -->
        <div class="modal fade" id="viewRolesModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="titleModal">User Roles and Permissions</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="bodyModal">

                <table id="roles-table" class=" table-hover" style="width:100%">
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
      <!-- End Modal for View Roles -->

        <!-- /page content -->
        
    <script>
      // Your tooltip initialization here
      $(document).ready(function () {
        $( "#viewPer" ).on( "click", function() {
          var roleIDs = $('#roleSel').val();
          $('#viewRolesModal').modal('show');

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

        $('#userForm').one('submit', function(e){   
          e.preventDefault();

          $('#roleStore').val($('#roleSel').val());

          $(this).submit();
        });
      });
    </script>
@endsection
