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

                <!-- Login form -->
                <form class="login-form" action="{{ route('login') }}" method="POST">
                    @csrf <!-- CSRF Token -->
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
                                    <img src="{{ asset('assets/images/logo_icon.svg') }}" class="h-48px" alt="">
                                </div>
                                <h5 class="mb-0">Login to your account</h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="text" class="form-control" name="email" placeholder="john@doe.com">
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-user-circle text-muted"></i>
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
                                        placeholder="•••••••••••">
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-lock text-muted"></i>
                                    </div>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <label class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" checked>
                                    <span class="form-check-label">Remember</span>
                                </label>

                                <a href="login_password_recover.html" class="ms-auto">Forgot password?</a>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">Sign in</button>
                            </div>

                            <div class="text-center text-muted content-divider mb-3">
                                <span class="px-2">or sign in with</span>
                            </div>

                            <div class="text-center mb-3">
                                <button type="button"
                                    class="btn btn-outline-primary btn-icon rounded-pill border-width-2"><i
                                        class="ph-facebook-logo"></i></button>
                                <button type="button"
                                    class="btn btn-outline-pink btn-icon rounded-pill border-width-2 ms-2"><i
                                        class="ph-dribbble-logo"></i></button>
                                <button type="button"
                                    class="btn btn-outline-secondary btn-icon rounded-pill border-width-2 ms-2"><i
                                        class="ph-github-logo"></i></button>
                                <button type="button"
                                    class="btn btn-outline-info btn-icon rounded-pill border-width-2 ms-2"><i
                                        class="ph-twitter-logo"></i></button>
                            </div>

                            <div class="text-center text-muted content-divider mb-3">
                                <span class="px-2">Don't have an account?</span>
                            </div>

                            <div class="mb-3">
                                <a href="{{ route('register') }}" class="btn btn-teal w-100">Register</a>
                            </div>

                            <span class="form-text text-center text-muted">By continuing, you're confirming that you've
                                read our <a href="#">Terms &amp; Conditions</a> and <a href="#">Cookie Policy</a></span>
                        </div>
                    </div>
                </form>
                <!-- /login form -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /inner content -->

    </div>
    <!-- /main content -->

</div>
<!-- /page content -->
@endsection
