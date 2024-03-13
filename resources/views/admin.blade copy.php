<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="images/favicon.ico" type="image/ico" />

    <title>Enrollment</title>

    <!-- Bootstrap -->
    <link href="{{ asset('vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="{{ asset('vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ asset('css/custom.min.css') }}" rel="stylesheet">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="/" class="site_title"><i class="fa fa-mortar-board"></i> <span>Enrollment</span></a>
            </div>

            <div class="clearfix"></div>

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                {{-- <h3>General</h3> --}}
                <ul class="nav side-menu">
                  @can('read-subject')
                  <li><a href="/"><i class="fa fa-home"></i> Dashboard</a></li>
                  <li><a><i class="fa fa-book"></i>Subjects <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{route('subject.index')}}">Subject List</a></li>
                      @can('create-subject')
                      <li><a href="{{route('subject.create')}}">Create Subject</a></li>
                      @endcan
                    </ul>
                  </li>
                  @endcan
                  @can('read-student')
                  <li><a><i class="fa fa-group"></i> Students <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{route('student.index')}}">Student List</a></li>
                      @can('create-student')
                      <li><a href="{{route('student.create')}}">Create Student</a></li>
                      @endcan
                    </ul>
                  </li>
                  @endcan
                  @can('read-user')
                  <li><a><i class="fa fa-user"></i>Users <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{route('user.index')}}">User List</a></li>
                      @can('create-user')
                      <li><a href="{{route('user.create')}}">Create User</a></li>
                      @endcan
                    </ul>
                  </li>
                  <li><a><i class="fa fa-shield"></i>Authorizations <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @can('read-permission')
                      <li><a href="{{route('permission.index')}}">Permissions</a></li>
                      @endcan
                      @can('read-role')
                      <li><a href="{{route('role.index')}}">Roles</a></li>
                      @endcan
                    </ul>
                  </li>
                  @endcan
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
              <div class="nav toggle pb-2">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <nav class="nav navbar-nav">
                <ul class=" navbar-right ">
                  <li class="nav-item dropdown open mr-5 mt-1" style="padding-left: 15px;">
                    <a href="javascript:;" class="user-link dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
                      {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
                      <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out pull-right"></i> Log Out
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>

                    </div>
                  </li>
           </ul>
  
                </ul>
              </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="row d-flex justify-content-around mt-3" style="display: inline-block;">
            <div class="top_tiles">
              <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 ">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-book"></i></div>
                  <div class="count">{{$subjects}}</div>
                  <h4 class="p-2 m-0 text-secondary">Total Subjects</h4>
                  <p><a href="{{route('subject.index')}}">Go to Subject List <i class="fa fa-long-arrow-right"></i></a><p>
                </div>
              </div>
              
              <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 ">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-group"></i></div>
                  <div class="count">{{$students}}</div>
                  <h4 class="p-2 m-0 text-secondary">Total Students</h4>
                  <p><a href="{{route('student.index')}}">Go to Student List <i class="fa fa-long-arrow-right"></i></a><p>
                </div>
              </div>

              <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 ">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-pencil-square-o"></i></div>
                  <div class="count">{{$enrolled}}</div>
                  <h4 class="p-2 m-0 text-secondary">Total Enrolled Students</h4>
                </div>
              </div>

              
              <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 @can('read-user')'' @else invisible @endcan" >
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-user"></i></div>
                  <div class="count">{{$users}}</div>
                  <h4 class="p-2 m-0 text-secondary">Total Users</h4>
                  <p><a href="{{route('user.index')}}">Go to User List <i class="fa fa-long-arrow-right"></i></a></p>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
        <!-- /page content -->
        
        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('vendors/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom Theme Scripts -->
    <script src="{{ asset('js/custom.min.j') }}s"></script>
	
  </body>
</html>
