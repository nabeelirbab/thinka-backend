@extends('admin.layout.app')
@section('title', 'User Information')
@section('content')

<div class="nk-content ">
    <div class="container wide-xl ">
        <div class="nk-content-inner">
          <div class="nk-content-body ">
            <div class="nk-content-wrap">
                <div class="nk-block-head">
                  <div class="nk-block-between g-3">
                    <div class="nk-block-head-content">
                      <h3 class="nk-block-title page-title">User Information</h3>
                    </div>
                  </div>
                </div>
                <div class="nk-block">
                    <div class="card card-bordered">
                    <div class="card-aside-wrap">
                    <div class="card-content">
                        <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                            <li class="nav-item">
                                <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true"><em class="icon ni ni-user-circle"></em><span>Personal</span></a>
                            </li>
                        
                        </ul><!-- .nav-tabs -->
                        <div class="card-inner tab-pane fade active show" id="personal" role="tabpanel" aria-labelledby="personal-tab" style="">
                            <div class="nk-block">
                                <div class="profile-ud-list">
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">First Name</span>
                                            <span class="profile-ud-value">{{$user->user_basic_information->first_name}}</span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Last Name</span>
                                            <span class="profile-ud-value">{{$user->user_basic_information->last_name}}</span>
                                        </div>
                                    </div>
                                   
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Mobile Number</span>
                                            <span class="profile-ud-value">86519799173</span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Email Address</span>
                                            <span class="profile-ud-value">{{$user->email}}</span>
                                        </div>
                                    </div>
                                </div><!-- .profile-ud-list -->
                            </div><!-- .nk-block -->
                            <div class="nk-block">
                                <div class="nk-block-head nk-block-head-line">
                                    <h6 class="title overline-title text-base">Additional Information</h6>
                                </div><!-- .nk-block-head -->
                                <div class="profile-ud-list">
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Country</span>
                                                                    <span class="profile-ud-value">Australia</span>
                                                                 </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">State</span>
                                                                    <span class="profile-ud-value">Tasmania</span>
                                                                </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">City</span>
                                                                    <span class="profile-ud-value"></span>
                                                                </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Joining Date</span>
                                            <span class="profile-ud-value">{{ date_format($user->created_at,'d/m/Y')}}</span>
                                        </div>
                                    </div>
                                </div><!-- .profile-ud-list -->
                            </div><!-- .nk-block -->
                            <div class="nk-divider divider md"></div>
                           
                        </div><!-- .card-inner -->
                     
                    </div><!-- .card-content -->
                   
                    </div><!-- .card-aside-wrap -->
                    </div><!-- .card -->
                    </div>
            </div>
        </div>
      </div>
  </div>
</div>

@endsection
@section('script')


@endsection