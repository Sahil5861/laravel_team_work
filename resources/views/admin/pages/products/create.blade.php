@extends('layout.base')

@section('content')
<div class="content">
    <button type="button" class="btn btn-dark">
        <a href="{{route('admin.grouprelation')}}" class="text-dark">Back</a>
    </button><br><br>
    <h3></h3>
    <div class="row">
        <div class="col-lg-12">
            {{--  Form --}}
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="mb-0">Add New Product </h5>
                    <div class="hstack gap-2 ms-auto">
                        <a class="text-body" data-card-action="collapse">
                            <i class="ph-caret-down"></i>
                        </a>
                        <a class="text-body" data-card-action="reload">
                            <i class="ph-arrows-clockwise"></i>
                        </a>
                        <a class="text-body" data-card-action="remove">
                            <i class="ph-x"></i>
                        </a>
                    </div>
                </div>

                <div class="collapse show">
                    <div class="card-body">
                        <form action="{{route('admin.grouprelation.create.post')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter Product Name"  autofocus>
                                    <span>
                                        @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-lg-3">
                                    <input type="file" id="image" name="image" class="form-control" placeholder="Choose Image">
                                    <span>
                                        @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-lg-3">
                                    <input type="text" name="amount" id="amount"  class="form-control" placeholder="Product Price">
                                    <span>
                                        @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </span>
                                </div>
                                
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <select name="brand" id="brand" class="form-control">
                                        <option value="">Select Brand Name</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{$brand->id}}">{{$brand->brand_name}}</option>
                                        @endforeach
                                    </select>
                                    <span>
                                        @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-lg-4">
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Select Category Name</option>
                                        @foreach ($categories as $category)
                                            <option value="{{$brand->id}}">{{$category->category_name}}</option>
                                        @endforeach
                                    </select>
                                    <span>
                                        @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-lg-4">
                                    <select name="product_group" id="product_group" class="form-control">
                                        <option value="">Select Product Group Name</option>
                                        @foreach ($productgroups as $productgroup)
                                            <option value="{{$productgroup->id}}">{{$productgroup->product_group_name}}</option>
                                        @endforeach
                                    </select>
                                    <span>
                                        @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <textarea name="desc" id="desc" cols="10" rows="5" class="form-control">

                                    </textarea>
                                </div>
                            </div>
                                <div class=" col-lg-4">
                                    <button type="submit" class="btn btn-primary">Create </button>
                                </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection