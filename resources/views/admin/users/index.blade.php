@extends('admin.layout.app')
@section('title', 'Users')
@section('content')

<div class="nk-content ">
                    <div class="container wide-xl">
                        <div class="nk-content-inner">
                          
                            <div class="nk-content-body">
                                <div class="nk-content-wrap">
                                   <div class="nk-block-head nk-block-head-sm">
                                      <div class="nk-block-between">
                                         <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">Users List</h3>
                                            <div class="nk-block-des text-soft">
                                               <p>You have total {{$users->total()}} users.</p>
                                            </div>
                                         </div>
                                         @if (session('success'))
                                         <div class="alert alert-success">
                                             {{ session('success') }}
                                             <button type="button" class="close ml-3" data-dismiss="alert" aria-label="Close">
                                               <span aria-hidden="true">&times;</span>
                                             </button>
                                         </div>
                                         @endif
                                         <a href="{{ url('admin/users/add') }}" class="btn btn-primary">Add New User</a>
                                      </div>
                                   </div>
                                   <div class="nk-block">
                                      <div class="card card-bordered card-stretch">
                                         <div class="card-inner-group">
                                            <div class="card-inner position-relative card-tools-toggle">
                                               <div class="card-title-group">
                                                  <div class="card-tools">
                                                   
                                                  </div>
                                                  <div class="card-tools me-n1">
                                                     <ul class="btn-toolbar gx-1">
                                                        <li><a href="#" class="btn btn-icon search-toggle toggle-search" data-target="search"><em class="icon ni ni-search"></em></a></li>
                                                       
                                                     </ul>
                                                  </div>
                                               </div>
                                               <div class="card-search search-wrap" data-search="search">
                                                  <div class="card-body">
                                                     <div class="search-content"><a href="#" class="search-back btn btn-icon toggle-search" data-target="search"><em class="icon ni ni-arrow-left"></em></a><input type="text" class="form-control border-transparent form-focus-none" placeholder="Search by name"><button class="search-submit btn btn-icon"><em class="icon ni ni-search"></em></button></div>
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="card-inner p-0">
                                               <div class="nk-tb-list nk-tb-ulist">
                                                  <div class="nk-tb-item nk-tb-head">
                                                   
                                                     <div class="nk-tb-col"><span class="sub-text fw-bold">#</span></div>
                                                     <div class="nk-tb-col"><span class="sub-text fw-bold">User</span></div>
                                                     <div class="nk-tb-col tb-col-sm"><span class="sub-text fw-bold">Email</span></div>
                                                     <div class="nk-tb-col tb-col-md"><span class="sub-text fw-bold">Pin</span></div>
                                                     <div class="nk-tb-col tb-col-xl"><span class="sub-text fw-bold">Status</span></div>
                                                     <div class="nk-tb-col tb-col-xl"><span class="sub-text fw-bold">Joining Date</span></div>
                                                     <div class="nk-tb-col text-end"><span class="sub-text fw-bold">Actions</span></div>
                                                  </div>
                                                  @foreach($users as $user)
                                                  <div class="nk-tb-item">
                                                   <div class="nk-tb-col tb-col-sm"><span class="sub-text">{{ $user->id }}</span></div>
                                                    
                                                     <div class="nk-tb-col">
                                                        <a href="{{ url('admin/user/'.$user->id) }}">
                                                           <div class="user-card">
                                                            @if(isset($user->user_profile_photo))
                                                             <div class="user-avatar sm bg-indigo">
                                                               <img src="{{ asset('storage/uploaded_files/png/'.$user->user_profile_photo->file_name) }}" alt="Image Description">
                                                             </div>
                                                            @else
                                                            <div class="user-avatar sm bg-primary"><span>
                                                                 {{ implode('', array_map(function ($word) {
                                                                     return strtoupper($word[0]); // Convert to uppercase (optional)
                                                                  }, explode(' ', $user->user_basic_information->full_name ?? 'N/A'))) }}
                                                                  </span>
                                                               </div>
                                                               @endif
                                                              <div class="user-name"><span class="tb-lead">{{ $user->user_basic_information->full_name ?? 'N/A'}} <span class="dot dot-success d-xl-none ms-1"></span></span></div>
                                                           </div>
                                                        </a>
                                                     </div>
                                                    
                                                     <div class="nk-tb-col tb-col-sm"><span class="sub-text">{{ $user->email }}</span></div>
                                                     <div class="nk-tb-col tb-col-md"><span class="sub-text">{{ $user->pin }}</span></div>
                                                     
                                                     <div class="nk-tb-col tb-col-xl">
                                                      @if($user->status == '1')
                                                      <span class="tb-status text-success">Active</span>
                                                      @else
                                                      <span class="tb-status text-danger">Inactive</span>
                                                      @endif
                                                     </div>
                                                     <div class="nk-tb-col tb-col-xl"><span class="sub-text">@if($user->created_at){{ date_format($user->created_at,'d/m/Y')}} @endif</span></div>

                                                     <div class="nk-tb-col nk-tb-col-tools">
                                                        <ul class="gx-1">
                                                           {{-- <li class="nk-tb-action-hidden"><a href="#" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Details"><em class="icon ni ni-eye-fill"></em></a></li>
                                                           <li class="nk-tb-action-hidden"><a href="#" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Send Email"><em class="icon ni ni-mail-fill"></em></a></li>
                                                           <li class="nk-tb-action-hidden"><a href="#" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Suspend"><em class="icon ni ni-cross-fill-c"></em></a></li> --}}
                                                           <li>
                                                              <div class="drodown">
                                                                 <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                 <div class="dropdown-menu dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                       <li><a href="{{ url('admin/user/'.$user->id) }}"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                       <li><a href="{{ url('admin/user/update/'.$user->id) }}"><em class="icon ni ni-edit"></em><span>Edit</span></a></li>
                                                                       <li><a href="{{ url('admin/user/update-password/'.$user->id) }}"><em class="icon ni ni-lock-alt"></em><span>Change Password</span></a></li>
                                                                       @if($user->status == '1')
                                                                       <li><a href="{{ url('admin/user/change-status/'.$user->id.'?status=0') }}" onclick="return confirm('Are you sure you want to suspend this account?');" title="Suspend Account"><em class="icon ni ni-na"></em><span>Suspend Account</span></a></li>
                               
                                                                       @else
                                                                       <li><a href="{{ url('admin/user/change-status/'.$user->id.'?status=1') }}" onclick="return confirm('Are you sure you want to active this account?');" title="Active Account"><em class="icon ni ni-shield-check"></em><span>Active Account</span></a></li>
                                                                         {{-- <li><a href="{{ url('admin/account_status/'.$user->id.'?status=3') }}" onclick="return confirm('Are you sure you want to delete this account?');" title="Suspend Account"><em class="icon ni ni-trash"></em><span>Delete Account</span></a></li> --}}
                                                                       @endif 
                                                                    </ul>
                                                                 </div>
                                                              </div>
                                                           </li>
                                                        </ul>
                                                     </div>
                                                  </div>
                                                  @endforeach   
                                               </div>
                                            </div>
                                            <div class="card-inner">
                                             {{$users->render()}}
                                            </div>
                                         </div>
                                      </div>
                                   </div>
                                </div>
                              
                             </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->

@endsection
@section('script')


@endsection