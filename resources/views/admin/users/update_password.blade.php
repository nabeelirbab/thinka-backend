@extends('admin.layout.app')
@section('title', 'Edit Relation Type')
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
                          <h5 class="card-title">Update Password</h5>
                        </div>
                        <form action="{{ url('admin/user/update-password/'.$id) }}" method="post">
                          <div class="form-group">
                            <label class="form-label" for="cf-full-name">New Password</label>
                            <input type="text" class="form-control" name="password">
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="cf-email-address">Confirm Password</label>
                            <input type="text" class="form-control"  name="password_confirmation">
                          </div>
                          <div class="form-group text-center"><button type="submit" class="btn btn-lg btn-primary">Update</button></div>
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