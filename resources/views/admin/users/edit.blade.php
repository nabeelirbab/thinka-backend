@extends('admin.layout.app')
@section('title', 'Edit User')
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
                          <h5 class="card-title">Update User</h5>
                        </div>
                        <form action="#" method="post">
                          @csrf <!-- Laravel CSRF Token -->
                          <div class="form-group">
                              <label class="form-label" for="username">Username</label>
                              <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}">
                          </div>
                          <div class="form-group">
                              <label class="form-label" for="first_name">First Name</label>
                              <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->user_basic_information->first_name }}">
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="last_name">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ $user->user_basic_information->middle_name }}">
                        </div>
                          <div class="form-group">
                              <label class="form-label" for="last_name">Last Name</label>
                              <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->user_basic_information->last_name }}">
                          </div>
                          <div class="form-group">
                            <label class="form-label">Status</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="active" value="1" 
                                       {{ old('status', $user->status) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="inactive" value="0" 
                                       {{ old('status', $user->status) == 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="inactive">Inactive</label>
                            </div>
                        </div>
                          <div class="form-group text-center">
                              <button type="submit" class="btn btn-lg btn-primary">Update</button>
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