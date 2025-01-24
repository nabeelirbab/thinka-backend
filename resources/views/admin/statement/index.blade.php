@extends('admin.layout.app')
@section('title', 'Support')
@section('content')

<div class="nk-content ">
                    <div class="container wide-xl">
                        <div class="nk-content-inner">
                          
                            <div class="nk-content-body">
                                <div class="nk-content-wrap">
                                   <div class="nk-block-head nk-block-head-sm">
                                      <div class="nk-block-between">
                                         <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">Statement Types List</h3>
                                            <div class="nk-block-des text-soft">
                                               <p>total {{$statements->total()}}.</p>
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
                                                     <div class="nk-tb-col"><span class="sub-text fw-bold">Description</span></div>
                                                     <div class="nk-tb-col tb-col-md"><span class="sub-text fw-bold">Explanation</span></div>
                                                     <div class="nk-tb-col tb-col-md"><span class="sub-text fw-bold">Color</span></div>
                                                     <div class="nk-tb-col tb-col-xl"><span class="sub-text fw-bold">Support Label</span></div>
                                                     <div class="nk-tb-col tb-col-xl"><span class="sub-text fw-bold">Counter Label</span></div>
                                                     <div class="nk-tb-col tb-col-xl"><span class="sub-text fw-bold">Action</span></div>

                                                  </div>
                                                  @foreach($statements as $statement)
                                                  <div class="nk-tb-item">
                                                   <div class="nk-tb-col tb-col-sm"><span class="sub-text">{{ $statement->id }}</span></div>
                                                    
                                                     <div class="nk-tb-col tb-col-sm"><span class="sub-text">{{ $statement->description }}</span></div>
                                                     <div class="nk-tb-col tb-col-md" style="width:30%"><span class="sub-text">{{ $statement->explanation }}</span></div>
                                                     <div class="nk-tb-col tb-col-xl"><span  style="color: {{$statement->color}}">{{ $statement->color }}</span></div> 
                                                     <div class="nk-tb-col tb-col-md"><span class="sub-text">{{ $statement->support_label }}</span></div>
                                                     <div class="nk-tb-col tb-col-md"><span class="sub-text">{{ $statement->counter_label }}</span></div>
                                                     <div class="nk-tb-col tb-col-md"><a href="{{ url('admin/statement-type/'.$statement->id) }}" class="btn btn-success btn-sm">Edit</a></div>


                                                  </div>
                                                  @endforeach   
                                               </div>
                                            </div>
                                            <div class="card-inner">
                                             {{$statements->render()}}
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