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
                    <a href="{{route('dashboard')}}" class="nav-link active">
                        <i class="ph-house"></i>
                        <span>
                            Dashboard
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="ph-layout"></i>
                        <span>Manage Users</span>
                    </a>
                </li>

                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link">
                        <i class="ph-layout"></i>
                        <span>Master</span>
                    </a>
                    <ul class="nav-group-sub collapse">
                        <li class="nav-item">
                            <a href="{{ route('role.index') }}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Roles</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.colour') }}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Colours</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.size') }}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Sizes</span>
                            </a>
                        </li>

                        {{-- <li class="nav-item nav-item-submenu">
                            <a href="#" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Examples</span>
                            </a>
                            <ul class="nav-group-sub collapse">
                                <li class="nav-item">
                                    <a href="{{ route('role.index') }}" class="nav-link">
                                        <i class="ph-layout"></i>
                                        <span>Example</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="ph-layout"></i>
                                        <span>Example 2</span>
                                    </a>
                                </li>
                            </ul>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('blogs.index') }}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Blogs</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.brand')}}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Brands</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.category')}}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Categories</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.grouprelation')}}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Product Relation</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.products')}}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Product</span>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link">
                        <i class="ph-layout"></i>
                        <span>Products</span>
                    </a>
                    <ul class="nav-group-sub collapse">
                        <li class="nav-item">
                            <a href="{{route('admin.brand')}}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Brands</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.category')}}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Categories</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.grouprelation')}}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Product Relation</span>
                            </a>
                        </li>
                        <div class="dropdown-divider"></div>
                        <li class="nav-item">
                            <a href="{{route('admin.products')}}" class="nav-link">
                                <i class="ph-layout"></i>
                                <span>Manage Product</span>
                            </a>
                        </li>
                    </ul>
                </li> --}}
            </ul>
        </div>
        <!-- /main navigation -->
    </div>
    <!-- /sidebar content -->
</div>
<!-- /main sidebar -->