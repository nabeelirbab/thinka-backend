@extends('admin.layout.app')
@section('title', 'Edit Relation Type')
@section('content')

<div class="nk-content ">
    <div class="container wide-xl ">
        <div class="nk-content-inner">
          <div class="nk-content-body ">
            <div class="nk-content-wrap">
            <div class="row d-flex justify-center" style="margin: 3.3rem 0rem 2rem 0">
                <div class="col-lg-6 ">
                    <div class="card card-bordered h-100">
                      <div class="card-inner">
                        <div class="card-head">
                          <h5 class="card-title">Update Relation Type</h5>
                        </div>
                        <form action="#" method="post">
                          <div class="form-group">
                            <label class="form-label" for="cf-full-name">Name</label>
                            <input type="text" class="form-control" id="cf-full-name" name="name" value="{{$relation->name}}">
                        </div>
                          <div class="form-group">
                            <label class="form-label" for="cf-email-address">Symbol</label>
                            <input type="text" class="form-control" id="cf-email-address" name="symbol" value="{{$relation->symbol}}">
                        </div>
                          <div class="form-group">
                            <label class="form-label" for="cf-phone-no">Default Impact</label>
                            <input type="text" class="form-control" id="cf-phone-no" name="default_impact" value="{{$relation->default_impact}}">
                        </div>
                          <div class="form-group">
                            <label class="form-label" for="cf-subject">Relevance</label>
                            <input type="text" class="form-control" id="cf-subject" name="relevance" value="{{$relation->relevance}}">
                        </div>
                          <div class="form-group">
                            <label class="form-label" for="cf-default-textarea">Description</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control form-control-sm" id="cf-default-textarea" name="description" placeholder="Write description">
                                    {{$relation->description}}
                                </textarea>
                            </div>
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