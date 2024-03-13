@extends('layouts.enrollmentLayout')

@section('content')
    
<style>
      #roleCont{
          overflow-y: auto;
          max-height: 250px;
          overflow: hidden;
      }
      
      #roleCont:hover{
          overflow-y: scroll;
      }
    </style>

    <script>
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $(document).ready(function(){

        $('#roleForm').one('submit', function(e){
          e.preventDefault();

          var permissions = [];
          $('input[name=permission]:checked').map(function() {
            permissions.push($(this).val());
          });

          $('#perStore').val(permissions);

          $(this).submit();
        });
      });
    </script>
        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Edit Role</h3>
              </div>

            </div>
            <div class="clearfix"></div>
                        <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Role Form <small>Fill up the form to edit the role.</small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form action="{{route('role.update', ['role'=>$role])}}" method="POST" id="roleForm" data-parsley-validate class="form-horizontal form-label-left">
                      @csrf
                      @method('post')
  
                      <input type="text" name="permissions" id="perStore" hidden>
                      <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="name">Role Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                          <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{$role->name}}">
                          @error('name') 
                            <span class="text-danger">{{$message}}</span>
                          @enderror
                        </div>
                      </div>

                      <div class="item form-group justify-content-center pt-3">
                        <div class="col-md-7 col-sm-7">
                          <h4>Permissions: <small>Check the boxes to assigned permission to the role.</small></h4>
                          <ul class="to_do" id="roleCont">
                              @foreach ($permissions as $permission)
                                <li>
                                  <p>
                                    @php
                                        $count = false;
                                        foreach ($assignedPermissions as $assignedPer){
                                          if($assignedPer->permission_id == $permission->id){
                                            $count = true;
                                          }
                                        }
                                    @endphp 
                                    @if($count)
                                      <input type="checkbox" class="flat" value = "{{$permission->id}}" name="permission" id="{{$permission->id}}" checked>
                                    @else
                                      <input type="checkbox" class="flat" value = "{{$permission->id}}" name="permission" id="{{$permission->id}}">
                                    @endif
                                      <label for="{{$permission->id}}">{{$permission->name}}</label>
                                  </p>
                                </li>
                              @endforeach
                            </ul>
                          </div>
                        </div>

                      <div class="ln_solid"></div>
                      <div class="item form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                          <a href="{{route('role.index')}}" class="btn btn-secondary">Back to Role List</a>
                          <button type="submit" class="btn btn-success">Update Role</button>
                        </div>
                      </div>
  
                    </form>
                  </div>
                </div>
              </div>
            </div>
        <!-- /page content -->
@endsection
