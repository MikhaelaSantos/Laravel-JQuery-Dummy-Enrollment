@extends('layouts.enrollmentLayout')

@section('content')
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $(document).ready(function(){

      $( "#resetBtn" ).on( "click", function() {
        $('input:checkbox').attr('checked',false);
      });

      $('#enrollForm').one('submit', function(e){
        e.preventDefault();

        var subjects = [];
        $('input[name=subject]:checked').map(function() {
          subjects.push($(this).val());
        });

        $('#subStore').val(subjects);

        $(this).submit();
      });

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
                      @can('create-student')
                      <small>Check the box on the desired subject/s you want to enroll.</small>
                      @endcan
                    </h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    @if(session()->has('message'))
                        <div class="alert alert-danger" id="successMsg">
                            {{ session()->get('message') }}
                        </div>
                    @endif
                    @can('create-student')
                      <form id="enrollForm" action="{{route('enrollment.enroll', ['student'=>$student])}}" method="post" data-parsley-validate class="form-horizontal form-label-left">
                        @csrf
                        <input type="text" name="subjects" id="subStore" hidden>
                        <div class="item form-group d-flex justify-content-center">
                            <div class="col-md-9 col-sm-9">
                              <ul class="to_do">
                                @foreach ($subjects as $subject)
                                  <li>
                                    <p>
                                      <input type="checkbox" class="flat" value = "{{$subject->id}}" name="subject" id="{{$subject->id}}">
                                      <label for="{{$subject->id}}">{{$subject->subject_name}}</label>
                                    </p>
                                  </li>
                                @endforeach
                              </ul>
                            </div>
                        </div>
                        
                        <div class="ln_solid"></div>
                        <div class="item form-group">
                          <div class="col-md-6 col-sm-6 offset-md-3">
                            <a href="{{route('student.index')}}" class="btn btn-secondary text-center">Back to Student List</a>
                            <button id="submitForm" class="btn btn-success" type="submit">Enroll Subjects</button>
                          </div>
                        </div>
                      
                      </form>

                    @else
                    <div>
                      <h2 class="text-center">No Enrolled Subject Yet</h2>
                      <div class="ln_solid"></div>
                        <div class="item form-group">
                          <div class="col-md-6 col-sm-6 offset-md-5">
                            <a href="{{route('student.index')}}" class="btn btn-secondary text-center">Back to Student List</a>
                          </div>
                        </div>
                    </div>
                    @endcan

                  </div>
                </div>
              </div>
            </div>
        <!-- /page content -->
@endsection