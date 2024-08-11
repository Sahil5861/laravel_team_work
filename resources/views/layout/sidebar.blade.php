<!-- Main sidebar -->
<div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- Sidebar header -->
        <div class="sidebar-section">
            <div class="sidebar-section-body d-flex justify-content-center">
                <h5 class="sidebar-resize-hide flex-grow-1 my-auto">Navigation</h5>

                <div>
                    <button type="button"
                        class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
                        <i class="ph-arrows-left-right"></i>
                    </button>

                    <button type="button"
                        class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-mobile-main-toggle d-lg-none">
                        <i class="ph-x"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- /sidebar header -->

        <!-- Main navigation -->
        <div class="sidebar-section">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
                <li class="nav-item-header pt-0">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Main</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="ph-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.user') }}"
                        class="nav-link {{ request()->routeIs('admin.user') ? 'active' : '' }}">
                        <i class="ph-user"></i>
                        <span>Manage Users</span>
                    </a>
                </li>

                <li class="nav-item nav-item-submenu">
                    <a href="#"
                        class="nav-link {{ request()->routeIs('admin.role') || request()->routeIs('admin.colour') || request()->routeIs('admin.size') || request()->routeIs('admin.dealers') || request()->routeIs('admin.contactPersons') || request()->routeIs('admin.plan') || request()->routeIs('blogs.index') ? 'active' : '' }}">
                        <i class="ph-layout"></i>
                        <span>Master</span>
                    </a>
                    <ul class="nav-group-sub collapse">
                        <li class="nav-item nav-item-submenu">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('admin.dealers') || request()->routeIs('admin.contactPersons') ? 'active' : '' }}">
                                <i class="ph-layout"></i>
                                <span>Dealers</span>
                            </a>
                            <ul class="nav-group-sub collapse">
                                <li class="nav-item">
                                    <a href="{{ route('admin.dealers') }}"
                                        class="nav-link {{ request()->routeIs('admin.dealers') ? 'active' : '' }}">
                                        <i class="ph-layout"></i>
                                        <span>Manage Dealers</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.contactPersons') }}"
                                        class="nav-link {{ request()->routeIs('admin.contactPersons') ? 'active' : '' }}">
                                        <i class="ph-layout"></i>
                                        <span>Manage Contact Persons</span>
                                    </a>
                                </li>
                            </ul>
                        </li>                        
                        <li class="nav-item">
                            <a href="{{ route('admin.colour') }}"
                                class="nav-link {{ request()->routeIs('admin.colour') ? 'active' : '' }}">
                                <i class="ph-layout"></i>
                                <span>Manage Colours</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.size') }}"
                                class="nav-link {{ request()->routeIs('admin.size') ? 'active' : '' }}">
                                <i class="ph-layout"></i>
                                <span>Manage Sizes</span>
                            </a>
                        </li>

                        
                        <li class="nav-item">
                            <a href="{{ route('admin.plan') }}"
                                class="nav-link {{ request()->routeIs('admin.plan') ? 'active' : '' }}">
                                <i class="ph-layout"></i>
                                <span>Manage Plans</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('blogs.index') }}"
                                class="nav-link {{ request()->routeIs('blogs.index') ? 'active' : '' }}">
                                <i class="ph-layout"></i>
                                <span>Manage Blogs</span>
                            </a>
                        </li>
                    </ul>
                </li>


                <li class="nav-item nav-item-submenu">
                    <a href="#"
                        class="nav-link {{ request()->routeIs('admin.brand') || request()->routeIs('admin.category') || request()->routeIs('admin.grouprelation') || request()->routeIs('admin.product') ? 'active' : '' }}">
                        <i class="ph-layout"></i>
                        <span>Manage Products</span>
                    </a>
                    <ul class="nav-group-sub collapse">
                        <li class="nav-item">
                            <a href="{{ route('admin.brand') }}"
                                class="nav-link {{ request()->routeIs('admin.brand') ? 'active' : '' }}">
                                <i class="ph-layout"></i>
                                <span>Brands</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.category') }}"
                                class="nav-link {{ request()->routeIs('admin.category') ? 'active' : '' }}">
                                <i class="ph-layout"></i>
                                <span>Categories</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.grouprelation') }}"
                                class="nav-link {{ request()->routeIs('admin.grouprelation') ? 'active' : '' }}">
                                <i class="ph-layout"></i>
                                <span>Product Relation</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.product') }}"
                                class="nav-link {{ request()->routeIs('admin.product') ? 'active' : '' }}">
                                <i class="ph-layout"></i>
                                <span>Product</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item nav-item-submenu">
                    <a href="#"
                        class="nav-link {{ request()->routeIs('admin.gallery') || request()->routeIs('admin.gallery.images') ? 'active' : '' }}">
                        <i class="ph-layout"></i>
                        <span>Gallery</span>
                    </a>
                    <ul class="nav-group-sub collapse">
                        <li class="nav-item">
                            <a href="{{ route('admin.gallery') }}"
                                class="nav-link {{ request()->routeIs('admin.gallery') ? 'active' : '' }}">
                                <i class="ph-image"></i>
                                <span>Image Gallery</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- /main navigation -->
    </div>
    <!-- /sidebar content -->
</div>
<!-- /main sidebar -->