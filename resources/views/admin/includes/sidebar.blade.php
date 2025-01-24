  <!-- sidebar @s -->
            <div class="nk-sidebar nk-sidebar-fixed is-dark " data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-menu-trigger">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                        <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                    </div>
                    <div class="nk-sidebar-brand" style="width:60%">
                        <a href="{{url('/')}}" class="logo-link nk-sidebar-logo">
                            <img class="logo-light logo-img" src="{{asset('frontend-assets/assets/logo/logo.png') }}" srcset="{{asset('frontend-assets/assets/logo/logo.png') }}" alt="logo">
                            <img class="logo-dark logo-img" src="{{asset('frontend-assets/assets/logo/logo.png') }}" srcset="{{asset('frontend-assets/assets/logo/logo.png') }}" alt="logo-dark">
                        </a>
                    </div>
                </div><!-- .nk-sidebar-element -->
                <div class="nk-sidebar-element nk-sidebar-body">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">
                                <li class="nk-menu-item">
                                    <a href="{{url('/admin')}}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-dashboard-fill"></em></span>
                                        <span class="nk-menu-text">Dashboard</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                             
                                <li class="nk-menu-item">
                                    <a href="{{url('admin/users')}}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                                        <span class="nk-menu-text">Users</span>
                                    </a>
                                 </li>

                                <li class="nk-menu-item">
                                    <a href="{{url('admin/statemnt-types')}}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                                        <span class="nk-menu-text">Statement Types</span>
                                    </a>
                                 </li>
                                 <li class="nk-menu-item">
                                    <a href="{{url('admin/relation-types')}}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                                        <span class="nk-menu-text">Relation Types</span>
                                    </a>
                                 </li>
                                 <!-- .nk-menu-item -->
                                {{-- <li class="nk-menu-item">
                                    <a href="{{url('dashboard/doc')}}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                                        <span class="nk-menu-text">Documentation</span>
                                    </a>
                                 </li><!-- .nk-menu-item --> --}}
                                <!-- <li class="nk-menu-item has-sub">
                                    <a href="{{url('customer/support')}}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-help-alt"></em></span>
                                        <span class="nk-menu-text">Support</span>
                                    </a>
                                
                                </li> --><!-- .nk-menu-item -->
                            </ul><!-- .nk-menu -->
                        </div><!-- .nk-sidebar-menu -->
                    </div><!-- .nk-sidebar-content -->
                </div><!-- .nk-sidebar-element -->
            </div>
            <!-- sidebar @e -->