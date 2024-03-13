@extends('layouts.enrollmentLayout')

@section('content')
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
@endsection    