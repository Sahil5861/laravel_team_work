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
                            Dashboard - <span class="fw-normal">Edit Product</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title fw-bold">Edit Product</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.product.edit.post', $product->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="{{ old('name', $product->name) }}">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" step="0.01" name="price" id="price" class="form-control"
                                            value="{{ old('price', $product->price) }}">
                                        @error('price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-lg-2">
                                                @if($product->image)
                                                    <div class="img mb-2">
                                                        <img src="{{ asset($product->image) }}" alt="Current Image"
                                                            style="max-width: 100%; height: auto;">
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-lg-10">
                                                <label for="image">Update Image</label>
                                                <input type="file" name="image" id="image" placeholder="Choose Image"
                                                    class="form-control">
                                                @error('image')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description"
                                            class="form-control">{{ old('description', $product->description) }}</textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 border-left">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select name="category_id" id="category_id" class="form-control">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="brand_id" class="form-label">Brand</label>
                                        <select name="brand_id" id="brand_id" class="form-control">
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->brand_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="product_group_id" class="form-label">Product Group</label>
                                        <select name="product_group_id" id="product_group_id" class="form-control">
                                            <option value="">Select Product Group</option>
                                            @foreach($productGroups as $group)
                                                <option value="{{ $group->id }}" {{ old('product_group_id', $product->product_group_id) == $group->id ? 'selected' : '' }}>
                                                    {{ $group->products_group_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('product_group_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="offer_price" class="form-label">Offer Price</label>
                                        <input type="number" step="0.01" name="offer_price" id="offer_price"
                                            class="form-control"
                                            value="{{ old('offer_price', $product->offer_price) }}">
                                        @error('offer_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="offer_expiry" class="form-label">Offer Expiry</label>
                                        <input type="date" name="offer_expiry" id="offer_expiry" class="form-control"
                                            value="{{ old('offer_expiry', $product->offer_expiry ? $product->offer_expiry->format('Y-m-d') : '') }}">
                                        @error('offer_expiry')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection