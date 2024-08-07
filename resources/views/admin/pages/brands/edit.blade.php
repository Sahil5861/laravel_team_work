@extends('layout.base')

@section('content')
<div class="page-content">
    @include('layout.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="d-flex">
                        <h4 class="page-title mb-0">
                            Dashboard - <span class="fw-normal">Edit Brand</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title fw-bold">Edit Brand</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{route('admin.brand.edit.post', $brand->id)}}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$brand->id}}">
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="name">Brand Name</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        placeholder="Enter Category Name" value="{{$brand->brand_name}}">
                                </div>
                                <div class="col-lg-2">
                                    <small>Current Image</small>
                                    <img src="{{asset($brand->image)}}" alt="image" width="80px" height="80px">
                                </div>
                                <div class="col-lg-4">
                                    <label for="image">Update Image</label>
                                    <input type="file" name="image" id="image" placeholder="Choose Image"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class=" col-lg-4">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection