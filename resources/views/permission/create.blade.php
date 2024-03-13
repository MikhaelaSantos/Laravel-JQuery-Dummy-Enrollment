@extends('layouts.enrollmentLayout')

@section('content')
    
        <!-- page content -->
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Create Permission</h3>
              </div>

            </div>
            <div class="clearfix"></div>
                        <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Permission Form <small>Fill up the form to create a new permission.</small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form action="{{route('permission.save')}}" method="POST" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                      @csrf
                      @method('post')
  
                      <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="name">Permission Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                          <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                          @error('name') 
                            <span class="text-danger">{{$message}}</span>
                          @enderror
                        </div>
                      </div>
                      <div class="ln_solid"></div>
                      <div class="item form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                          <a href="{{route('permission.index')}}" class="btn btn-secondary">Back to Permission List</a>
                          <button class="btn btn-primary" type="reset">Reset</button>
                          <button type="submit" class="btn btn-success">Create Permission</button>
                        </div>
                      </div>
  
                    </form>
                  </div>
                </div>
              </div>
            </div>
        <!-- /page content -->
@endsection