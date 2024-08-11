<!-- Main navbar -->
<div class="navbar navbar-dark navbar-expand-lg navbar-static border-bottom border-bottom-white border-opacity-10">
	<div class="container-fluid">
		<div class="d-flex d-lg-none me-2">
			<button type="button" class="navbar-toggler sidebar-mobile-main-toggle rounded-pill">
				<i class="ph-list"></i>
			</button>
		</div>

		<div class="navbar-brand flex-1 flex-lg-0">
			<a href="index.html" class="d-inline-flex align-items-center">
				<img src="{{asset('assets/images/logo_icon.svg')}}" alt="">
				{{-- <img src="{{asset('assets/images/logo_text_light.svg')}}" class="d-none d-sm-inline-block h-16px ms-3" alt=""> --}}
				<h3 class="d-none d-sm-inline-block h-16px ms-3 text-white title">{{env('COMPANY_NAME')}}</h3>
			</a>
		</div>
		<ul class="nav flex-row justify-content-end order-1 order-lg-2">
			<li class="nav-item nav-item-dropdown-lg dropdown ms-lg-2">
				<a href="#" class="navbar-nav-link align-items-center rounded-pill p-1" data-bs-toggle="dropdown">
					<div class="status-indicator-container">
						<img src="{{asset('assets/images/demo/users/face.png')}}" class="w-32px h-32px rounded-pill"
							alt="">
						<span class="status-indicator bg-success"></span>
					</div>
					<span
						class="d-none d-lg-inline-block mx-lg-2 text-capitalize">{{ auth()->user()->name ?? 'Victoria' }}</span>
				</a>

				<div class="dropdown-menu dropdown-menu-end">
					<a href="#" class="dropdown-item">
						<i class="ph-user-circle me-2"></i>
						My profile
					</a>
					<div class="dropdown-divider"></div>
					<a href="{{route('logout')}}" class="dropdown-item">
						<i class="ph-sign-out me-2"></i>
						Logout
					</a>
				</div>
			</li>
		</ul>
	</div>
</div>
<!-- /main navbar -->



<!-- Demo config -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="demo_config">
	<div class="position-absolute top-50 end-100 visible">
		<button type="button" class="btn btn-primary btn-icon translate-middle-y rounded-end-0"
			data-bs-toggle="offcanvas" data-bs-target="#demo_config">
			<i class="ph-gear"></i>
		</button>
	</div>

	<div class="offcanvas-header border-bottom py-0">
		<h5 class="offcanvas-title py-3">Theme Color</h5>
		<button type="button" class="btn btn-light btn-sm btn-icon border-transparent rounded-pill"
			data-bs-dismiss="offcanvas">
			<i class="ph-x"></i>
		</button>
	</div>

	<div class="offcanvas-body">
		<div class="fw-semibold mb-2">Color mode</div>
		<div class="list-group mb-3">
			<label class="list-group-item list-group-item-action form-check border-width-1 rounded mb-2">
				<div class="d-flex flex-fill my-1">
					<div class="form-check-label d-flex me-2">
						<i class="ph-sun ph-lg me-3"></i>
						<div>
							<span class="fw-bold">Light theme</span>
							<div class="fs-sm text-muted">Set light theme or reset to default</div>
						</div>
					</div>
					<input type="radio" class="form-check-input cursor-pointer ms-auto" name="main-theme" value="light"
						checked>
				</div>
			</label>

			<label class="list-group-item list-group-item-action form-check border-width-1 rounded mb-2">
				<div class="d-flex flex-fill my-1">
					<div class="form-check-label d-flex me-2">
						<i class="ph-moon ph-lg me-3"></i>
						<div>
							<span class="fw-bold">Dark theme</span>
							<div class="fs-sm text-muted">Switch to dark theme</div>
						</div>
					</div>
					<input type="radio" class="form-check-input cursor-pointer ms-auto" name="main-theme" value="dark">
				</div>
			</label>
		</div>
	</div>
</div>

<!-- /demo config -->