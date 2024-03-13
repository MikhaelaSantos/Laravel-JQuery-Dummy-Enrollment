@extends('layouts.enrollmentLayout')

@section('content')
<script>
  $(document).ready(function(){
    $( "#birthdate" ).on( "change", function() {
      var birthDate = new Date($(this).val());
      var currentDate = new Date();
      
      // Age Computation
      var age = currentDate.getFullYear() - birthDate.getFullYear();

      // Check if Birthday not yet Occured this Year
      if (currentDate.getMonth() < birthDate.getMonth() || (currentDate.getMonth() === birthDate.getMonth() && currentDate.getDate() < birthDate.getDate())) {
                age--;
      }

      $("#age").val(age);
    });
  });
</script>

        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Edit Student</h3>
              </div>

            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Student Form <small>Edit the display details.</small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form action="{{route('student.update', ['student'=>$student])}}" method="POST" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                      @csrf
                      @method('post')
  
                      <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first_name">First Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                          <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{$student->first_name}}">
                          @error('first_name') 
                            <span class="text-danger">{{$message}}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="last_name">Last Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                          <input type="text" id="last_name" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{$student->last_name}}">
                          @error('last_name') 
                            <span class="text-danger">{{$message}}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="item form-group">
                        <label for="middle_name" class="col-form-label col-md-3 col-sm-3 label-align">Middle Name <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 ">
                          <input id="middle_name" class="form-control @error('middle_name') is-invalid @enderror" type="text" name="middle_name"  value="{{$student->middle_name}}">
                          @error('middle_name') 
                            <span class="text-danger">{{$message}}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="item form-group">
                        <label for="birthdate" class="col-form-label col-md-3 col-sm-3 label-align">Birthdate <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 ">
                          <input id="birthdate" class="form-control @error('birthdate') is-invalid @enderror" type="date" name="birthdate" value="{{ $student->birthdate->format('Y-m-d') }}">
                          @error('birthdate') 
                            <span class="text-danger">{{$message}}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="item form-group">
                        <label for="age" class="col-form-label col-md-3 col-sm-3 label-align">Age <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 ">
                          <input id="age" class="form-control @error('age') is-invalid @enderror" type="number" name="age" value="{{$student->age}}" readonly>
                          @error('age') 
                            <span class="text-danger">{{$message}}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="ln_solid"></div>
                      <div class="item form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                          <a href="{{route('student.index')}}" class="btn btn-secondary text-center">Back to Student List</a>
                          <button class="btn btn-primary" type="reset">Reset</button>
                          <button type="submit" class="btn btn-success">Update Student</button>
                        </div>
                      </div>
                    
                    </form>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <!-- /page content -->
@endsection
