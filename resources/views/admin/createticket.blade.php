@extends('customer.layout.app')
@section('title', 'Dashboard')
@section('content')

 <!-- content @s -->
                <div class="nk-content ">
                    <div class="container wide-xl">
                        <div class="nk-content-inner">
                           
                            <div class="nk-content-body">
                                <div class="nk-content-wrap">
                                    <div class="nk-block-head nk-block-head-lg">
                                        <div class="nk-block-head-sub"><a href="support.html" class="text-soft"><span>Contact</span></a></div>
                                        <div class="nk-block-head-content">
                                            <h2 class="nk-block-title fw-normal">How can we help?</h2>
                                            <div class="nk-block-des">
                                                <p>You can find all of the questions and answers abour secure your account</p>
                                            </div>
                                        </div>
                                    </div><!-- .nk-block-head -->
                                    <div class="nk-block mb-3">
                                        <div class="card card-bordered">
                                            <div class="card-inner">
                                                <form action="#" class="form-contact">
                                                    <div class="row g-4">
                                                        <div class="col-md-6">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="type" id="type-general" checked>
                                                                <label class="custom-control-label" for="type-general">A general enquiry</label>
                                                            </div>
                                                        </div><!-- .col -->
                                                        <div class="col-md-6">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="type" id="type-problem">
                                                                <label class="custom-control-label" for="type-problem">I have a problem need help</label>
                                                            </div>
                                                        </div><!-- .col -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label"><span>Category</span></label>
                                                                <div class="form-control-wrap">
                                                                    <select class="form-select" data-search="off" data-ui="lg">
                                                                        <option value="general">General</option>
                                                                        <option value="billing">Billing</option>
                                                                        <option value="technical">Technical</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div><!-- .col -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label"><span>Priority</span></label>
                                                                <div class="form-control-wrap">
                                                                    <select class="form-select" data-search="off" data-ui="lg">
                                                                        <option value="normal">Normal</option>
                                                                        <option value="important">Important</option>
                                                                        <option value="high">High Piority</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div><!-- .col -->
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="form-label">Describe the problem you have</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control form-control-lg" placeholder="Write your problem... ">
                                                                </div>
                                                            </div>
                                                        </div><!-- .col -->
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="form-label"><span>Give us the details </span><em class="icon ni ni-info text-gray"></em></label>
                                                                <p class="text-soft">If you’re experiencing a personal crisis, are unable to cope and need support send message. We will always try to respond to texters as quickly as possible.</p>
                                                                <div class="form-control-wrap">
                                                                    <div class="form-editor-custom">
                                                                        <textarea class="form-control form-control-lg no-resize" placeholder="Write your message"></textarea>
                                                                        <div class="form-editor-action">
                                                                            <a class="link collapsed" data-toggle="collapse" href="#filedown"><em class="icon ni ni-clip"></em> Attach file</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!-- .col -->
                                                        <div class="col-12">
                                                            <div class="choose-file">
                                                                <div class="form-group collapse" id="filedown">
                                                                    <div class="support-upload-box">
                                                                        <div class="upload-zone">
                                                                            <div class="dz-message" data-dz-message>
                                                                                <em class="icon ni ni-clip"></em>
                                                                                <span class="dz-message-text">Drag your file</span>
                                                                                <span class="dz-message-or"> or</span>
                                                                                <button class="btn btn-primary">Select</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!-- .col -->
                                                        <div class="col-12">
                                                            <a href="#" class="btn btn-primary">Send</a>
                                                        </div><!-- .col -->
                                                    </div><!-- .row -->
                                                </form><!-- .form-contact -->
                                            </div><!-- .card-inner -->
                                        </div><!-- .card -->
                                    </div><!-- .nk-block -->
                                </div>
                             
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->

@endsection
@section('script')


@endsection