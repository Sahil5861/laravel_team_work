@extends('layout.base')
@section('title', 'Admin-products-category')

@section('content')
<div class="page-content">
    @include('layout.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="d-flex">
                        <h4 class="page-title mb-0">
                            Dashboard - <span class="fw-normal">conatct Persons</span>
                        </h4>
                        <a href="#page_header"
                            class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                            data-bs-toggle="collapse">
                            <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-lg-12">
                        {{--  Form --}}
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="mb-0">Add New Conatct Persons</h5>
                                <div class="hstack gap-2 ms-auto">
                                    <a href="{{route('admin.contactPersons')}}" class="text-dark btn btn-info text-white">Back</a>  
                                </div>
                            </div>
                            <div class="collapse show">
                                <div class="card-body">
                                    <form action="{{route('admin.contactPersons.create.post')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row mb-4">
                                            <div class="col-lg-4">
                                                <label for="name">Contact Person Name</label>
                                                <input type="text" id="name" name="name" class="form-control" placeholder="Username"  autofocus value="{{old('name')}}">
                                                <span>
                                                    @error('name')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="email">Conatct Person Email</label>
                                                <input type="email" id="email" name="email" class="form-control" placeholder="Email Id" value="{{old('email')}}">
                                                <span>
                                                    @error('email')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>

                                            <div class="col-lg-4">
                                                <label for="phone">Contact Person Phone</label>
                                                <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter Phone Number" value="{{old('phone')}}" >
                                                <span>
                                                    @error('phone')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            
                                            
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-3">
                                                <label for="role">Conatct Person Role</label>
                                                {{-- <input type="text" id="role" name="role" class="form-control bg-dark text-white" placeholder="Enter Dealer's Email" value="Contact Person" readonly> --}}
                                                <select name="role" id="role" class="form-control bg-dark text-white">
                                                    @foreach ($roles as $role)
                                                        <option value="{{$role->id}}" {{$role->id == 3 ? 'selected': 'disabled'}}>{{$role->name}}</option>
                                                    @endforeach
                                                </select>
                                                <span>
                                                    @error('role')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="pass1">Create Password</label>
                                                <input type="password" id="password" name="password" class="form-control" placeholder="Create Password" value="{{old('pass1')}}">
                                                <span>
                                                    @error('pass1')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="password_confirmation">Confirm Password</label>
                                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Create Password" value="{{old('pass2')}}">
                                                <span>
                                                    @error('pass2')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="dealer_id">Dealer</label>
                                                <select name="dealer_id" id="dealer_id" class="form-control">
                                                    <option value="">--Select Dealer--</option>
                                                    @foreach ($dealers as $dealer)
                                                        <option value="{{$dealer->id}}"
                                                            {{old('dealer_id') ==$dealer->id ? 'selected' : ''}}
                                                            >{{$dealer->business_name}}</option>
                                                    @endforeach
                                                </select>
                                                <span>
                                                    @error('dealer_id')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class=" col-lg-4">
                                                <button type="submit" class="btn btn-primary btn-block">Create </button>
                                            </div>
                                        </div>
        
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection