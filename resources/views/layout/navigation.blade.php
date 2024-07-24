<div class="navbar navbar-dark bg-dark px-lg-0">
    <div class="container-fluid container-boxed position-relative">
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav gap-3 flex-column flex-lg-row">
                <li class="nav-item">
                    <a href="#" class="nav-link rounded">
                        Home
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle rounded" id="documentsDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Documents
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="documentsDropdown">
                        <li><a href="#" class="dropdown-item">Documents</a></li>
                        <li><a href="#" class="dropdown-item">Transactions</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle rounded" id="productsDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Products
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="productsDropdown">
                        <li><a href="#" class="dropdown-item">Categories</a></li>
                        <li><a href="#" class="dropdown-item">Brands</a></li>
                        <li><a href="#" class="dropdown-item">Product Group Relations</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        Manage Users
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle rounded" id="masterDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Master
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="masterDropdown">
                        <li><a href="{{ route('role.index') }}" class="dropdown-item">Manage Roles</a></li>
                        <li><a href="{{ route('colour.index') }}" class="dropdown-item">Manage Colours</a></li>
                        <li><a href="{{ route('size.index') }}" class="dropdown-item">Manage Sizes</a></li>
                        <li class="dropdown-submenu">
                            <a href="#" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">Manage Examples</a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('role.index') }}" class="dropdown-item">Example</a></li>
                                <li><a href="#" class="dropdown-item">Example 2</a></li>
                            </ul>
                        </li>
                        <li><a href="{{ route('blogs.index') }}" class="dropdown-item">Manage Blogs</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Add Bootstrap CSS and JS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
