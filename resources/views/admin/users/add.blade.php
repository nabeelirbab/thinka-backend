@extends('admin.layout.app')
@section('title', 'Add User')
@section('content')

<div class="nk-content ">
    <div class="container wide-xl ">
        <div class="nk-content-inner">
          <div class="nk-content-body ">
            <div class="nk-content-wrap">
              @if ($errors->any())
              <div class="alert alert-danger">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
              @endif
            <div class="row d-flex justify-center" style="margin: 3.3rem 0rem 2rem 0">
                <div class="col-lg-6 ">
                    <div class="card card-bordered h-100">
                      <div class="card-inner">
                        <div class="card-head">
                          <h5 class="card-title">Add User</h5>
                        </div>
                        <form action="#" method="post">
                          @csrf <!-- Laravel CSRF Token -->
                          <div class="form-group">
                              <label class="form-label" for="username">Username</label>
                              <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}">
                          </div>
                          <div class="form-group">
                              <label class="form-label" for="first_name">First Name</label>
                              <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}">
                          </div>
                          <div class="form-group">
                              <label class="form-label" for="last_name">Last Name</label>
                              <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}">
                          </div>
                          <div class="form-group">
                              <label class="form-label" for="email">Email</label>
                              <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                          </div>
                          <div class="form-group">
                              <label class="form-label" for="password">Password</label>
                              <input type="text" class="form-control" id="password" name="password">
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="password">Confirm Password</label>
                            <input type="text" class="form-control" id="password" name="password_confirmation">
                        </div>
                          <div class="form-group text-center">
                              <button type="submit" class="btn btn-lg btn-primary">Add</button>
                          </div>
                      </form>
                      
                      </div>
                    </div>
                  </div>
               </div>
            </div>
          </div>
        </div>
    </div>
</div>

@endsection
@section('script')


@endsection