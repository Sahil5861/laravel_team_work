<!-- Create User Form -->
@extends('layout.base')

@section('content')
<div class="page-content">
    @include('layout.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="row w-100 px-3">
                        <h4 class="page-title mb-0 col-lg-11">
                            Dashboard - <span class="fw-normal">Users</span>
                        </h4>
                        <div class="col-lg-1 my-3">
                            <a href="{{route('admin.user')}}" class="text-dark btn btn-info text-white">Back</a>  
                        </div>  
                    </div>
                </div>
            </div>

            <div class="content">

                <form action="{{route('admin.user.create.post')}}" method="post">
                    @csrf
                    <h3 class="mb-4">Add User</h3>
                    <div class="row my-2">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" autofocus value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror                                
                            </div>
                            <div class="mb-3">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror                                
                            </div>
                            <div class="mb-3">
                                <label for="role_id">Role</label>
                                <select name="role_id" class="form-control text-white">
                                    <option value="">--Select Role--</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror                                
                            </div>
                        </div>
                        <div class="col-lg-1 d-flex align-items-center justify-content-center">
                            <div class="border border-light" style="height: 100%; width: 1px;"></div>
                        </div>
                        <div class="col-lg-5">
                            <div class="mb-3">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror                                
                            </div>
                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror                                
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class=" col-lg-4">
                            <button type="submit" class="btn btn-primary">Create </button>
                        </div>
                    </div>
                </form>
                {{-- <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Add User</h5>
                    </div>

                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{route('admin.user.create.post')}}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="role_id">Role</label>
                                <select name="role_id" class="form-control">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Add User</button>
                            </div>
                        </form>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection
