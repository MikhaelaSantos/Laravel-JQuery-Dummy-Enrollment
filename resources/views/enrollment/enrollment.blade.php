@extends('layouts.enrollmentLayout')

@section('content')
  <script>
      $(document).ready(function(){
        setTimeout(function() {
        $('#successMsg').fadeOut('fast');
          }, 3000);
      });    
  </script>
        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Enrollment Form for {{$student->first_name}} {{$student->last_name}}</h3>
              </div>

            </div>
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2 id="target">List of Subjects
                      <small>Listed below are your enrolled subjects.</small>
                    </h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    @if(session()->has('success'))
                        <div class="alert alert-success" id="successMsg">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    <div class="d-flex justify-content-center">
                      <div class="col-md-9 col-sm-9 ">
                        <ul class="to_do">
                          @foreach ($enrolled as $enroll)
                            <li>
                              <p>{{$enroll->subject_name}}</p>
                            </li>
                          @endforeach
                        </ul>

                        <div class="ln_solid"></div>
                        <div class="item form-group">
                          <div class="col-md-6 col-sm-6 offset-md-3">
                            @can('update-student')
                            <a href="{{route('enrollment.edit', ['student'=>$student])}}" class="btn btn-primary text-center">Edit Enrollment</a>
                            @endcan
                            <a href="{{route('student.index')}}" class="btn btn-secondary  text-center">Back to Student List</a>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
        <!-- /page content -->
@endsection
