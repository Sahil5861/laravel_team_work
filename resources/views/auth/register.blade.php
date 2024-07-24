@extends('layout.base')

@section('content')

<!-- Page content -->
<div class="page-content">
    <!-- Main content -->
    <div class="content-wrapper">
        <!-- Inner content -->
        <div class="content-inner">
            <!-- Content area -->
            <div class="content d-flex justify-content-center align-items-center">
                <!-- Registration form -->
                <form class="login-form" action="{{ route('register') }}" method="POST">
                    @csrf <!-- CSRF Token -->
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
                                    <img src="{{ asset('assets/images/logo_icon.svg') }}" class="h-48px" alt="">
                                </div>
                                <h5 class="mb-0">Create account</h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="text" class="form-control" name="name" placeholder="Your Name...">
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-user-circle text-muted"></i>
                                    </div>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone No.</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="tel" class="form-control" name="phone" placeholder="Your Number...">
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-phone text-muted"></i>
                                    </div>
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Your email</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="email" class="form-control" name="email" placeholder="Your Email...">
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-at text-muted"></i>
                                    </div>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Your Password...">
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-lock text-muted"></i>
                                    </div>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Confirm Password...">
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-lock text-muted"></i>
                                    </div>
                                    @error('password_confirmation')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Register</button>
                            <div class="text-center text-muted content-divider my-3">
                                <span class="px-2">Or</span>
                            </div>
                            <a href="{{ route('login') }}" class="btn btn-teal w-100">Already have an account?</a>
                        </div>
                    </div>
                </form>
                <!-- /registration form -->
            </div>
            <!-- /content area -->
        </div>
        <!-- /inner content -->
    </div>
    <!-- /main content -->
</div>
<!-- /page content -->

@endsection
