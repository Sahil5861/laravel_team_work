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
                            Dashboard - <span class="fw-normal">Product Groups</span>
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
                                <h5 class="mb-0">Add New Product Group</h5>
                                <div class="hstack gap-2 ms-auto">
                                    <a href="{{route('admin.grouprelation')}}" class="text-dark btn btn-dark">Back</a>
                                </div>
                            </div>
                            <div class="collapse show">
                                <div class="card-body">
                                    <form action="{{route('admin.grouprelation.create.post')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-lg-6">
                                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Category Name"  autofocus {{old('name')}}>
                                                <span>
                                                    @error('name')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class=" col-lg-4">
                                                <button type="submit" class="btn btn-primary">Create</button>
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