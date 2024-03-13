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
                <h3>{{$subject->subject_name}} Enrolled Students</h3>
              </div>

            </div>
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2 id="target">List of Enrolled Students
                        <small>Below are the details of the students enrolled in the subject.</small>
                    </h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    @if($students != null)
                    <div class="d-flex justify-content-center">
                      <div class="col-md-9 col-sm-9 ">
                        <ul class="to_do">
                          @foreach ($students as $student)
                            <li>
                              <p>{{$student->first_name}} {{$student->last_name}}</p>
                            </li>
                          @endforeach
                        </ul>
                    @else
                        <h2>No Enrolled Students</h2>
                    @endif
                        <div class="ln_solid"></div>
                        <div class="item form-group">
                          <div class="col-md-6 col-sm-6 offset-md-3">
                            <a href="{{route('subject.index')}}" class="btn btn-success text-center">Back to Subject List</a>
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
