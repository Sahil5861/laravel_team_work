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
                            Dashboard - <span class="fw-normal">Products</span>
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
                <h3></h3>
                <div class="row">
                    <div class="col-lg-12">
                        {{--  Form --}}
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="mb-0">Add New Product </h5>
                                <div class="hstack gap-2 ms-auto">
                                    <a href="{{route('admin.products')}}" class="text-dark btn btn-dark">Back</a>                
                                </div>
                            </div>
            
                            <div class="collapse show">
                                <div class="card-body">
                                    <form action="{{route('admin.products.create.post')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-lg-6">
                                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Product Name"value="{{old('name')}}"  autofocus>
                                                <span>
                                                    @error('name')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-3">
                                                <input type="file" id="image" name="image" class="form-control" placeholder="Choose Image" value="{{old('image')}}">
                                                <span>
                                                    @error('image')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-3">
                                                <input type="text" name="amount" id="amount"  class="form-control" placeholder="Product Price" value="{{old('amount')}}">
                                                <span>
                                                    @error('amount')
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
                                                        <option value="{{$brand->id}}" {{old('brand') == $brand->id ? 'selected' : ''}}>{{$brand->brand_name}}</option>
                                                    @endforeach
                                                </select>
                                                <span>
                                                    @error('brand')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <select name="category" id="category" class="form-control">
                                                    <option value="">Select Category Name</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{$category->id}}" {{old('category') == $category->id ? 'selected':''}}>{{$category->category_name}}</option>
                                                    @endforeach
                                                </select>
                                                <span>
                                                    @error('category')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <select name="product_group" id="product_group" class="form-control">
                                                    <option value="">Select Product Group Name</option>
                                                    @foreach ($productgroups as $productgroup)
                                                        <option value="{{$productgroup->id}}" {{old('product_group') == $productgroup->id ? 'selected':''}}>{{$productgroup->products_group_name}}</option>
                                                    @endforeach
                                                </select>
                                                <span>
                                                    @error('product_group')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <textarea name="desc" id="desc" cols="10" rows="5" class="form-control" placeholder="Product Description"></textarea>
                                            </div>
                                            @error('desc')
                                                <span class="text-danger">{{$message}}</span>
                                            @enderror
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




        </div>
    </div>
</div>
@endsection