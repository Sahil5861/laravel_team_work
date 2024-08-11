@extends('layout.base')
@section('title', 'Admin - Create Plan')

{{-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> --}}

@section('content')
<div class="page-content">
    @include('layout.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="row w-100 px-3">
                        <h4 class="page-title mb-0 col-lg-11">
                            Dashboard - <span class="fw-normal">Plan</span>
                        </h4>
                        <div class="col-lg-1 my-3">
                            <a href="{{ route('admin.plan') }}" class="text-dark btn btn-info text-white">Back</a>
                        </div>  
                    </div>
                </div>
            </div>
            <div class="content">
                <form action="{{ route('admin.plan.create.post') }}" method="post">
                    @csrf
                    <h3 class="mb-4">Add Plans</h3>
                    <div class="row my-2">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name">Plan Name</label>
                                <input type="text" id="name" name="name" class="form-control"
                                    placeholder="Enter Plan Name" value="{{ old('name') }}" autofocus>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror                                
                            </div>
                            <div class="mb-3">
                                <label for="price">Price</label>
                                <input type="text" id="price" name="price" class="form-control"
                                    placeholder="Enter Price" value="{{ old('price') }}"
                                    pattern="\d+(\.\d{1,6})?"
                                    title="Please enter a valid number with up to 6 decimal places">
                                @error('price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror                                
                            </div>
                            <div class="mb-3">
                                <label for="special_price">Special Price</label>
                                <input type="text" id="special_price" name="special_price"
                                    class="form-control" placeholder="Enter Special Price"
                                    value="{{ old('special_price') }}" pattern="\d+(\.\d{1,6})?"
                                    title="Please enter a valid number with up to 6 decimal places">
                                @error('special_price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror                                
                            </div>
                        </div>
                        <div class="col-lg-1 d-flex align-items-center justify-content-center">
                            <div class="border border-light" style="height: 100%; width: 1px;"></div>
                        </div>
                        <div class="col-lg-5">
                            <div class="mb-3">
                                <label for="image">Select Image</label>
                                <input type="file" name="image" id="image" value="{{ old('image') }}" class="form-control">
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror                                
                            </div>
                            <div class="mb-3">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="date" id="expiry_date" name="expiry_date"
                                    class="form-control" value="{{ old('expiry_date') }}">
                                @error('expiry_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror                                
                            </div>
                            <div class="mb-3">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control" placeholder="Enter Description">{{ old('description') }}</textarea>
                                <script>
                                    CKEDITOR.replace( 'description' );
                                </script>
                                @error('description')
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
                {{-- <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="mb-0">Add New Plan</h5>
                                <div class="hstack gap-2 ms-auto">
                                    <a href="{{ route('admin.plan') }}" class="text-dark btn btn-dark">Back</a>
                                </div>
                            </div>
                            <div class="collapse show">
                                <div class="card-body">
                                    <form action="{{ route('admin.plan.create.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-lg-6">
                                                <label for="name">Plan Name</label>
                                                <input type="text" id="name" name="name" class="form-control"
                                                    placeholder="Enter Plan Name" value="{{ old('name') }}" autofocus>
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-6">
                                                <label for="image">Select Image</label>
                                                <input type="file" name="image" id="image" value="{{ old('image') }}" class="form-control">
                                                @error('image')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>


                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-4">
                                                <label for="price">Price</label>
                                                <input type="text" id="price" name="price" class="form-control"
                                                    placeholder="Enter Price" value="{{ old('price') }}"
                                                    pattern="\d+(\.\d{1,6})?"
                                                    title="Please enter a valid number with up to 6 decimal places">
                                                @error('price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="special_price">Special Price</label>
                                                <input type="text" id="special_price" name="special_price"
                                                    class="form-control" placeholder="Enter Special Price"
                                                    value="{{ old('special_price') }}" pattern="\d+(\.\d{1,6})?"
                                                    title="Please enter a valid number with up to 6 decimal places">
                                                @error('special_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            <div class="col-lg-4">
                                                <label for="expiry_date">Expiry Date</label>
                                                <input type="date" id="expiry_date" name="expiry_date"
                                                    class="form-control" value="{{ old('expiry_date') }}">
                                                @error('expiry_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="description">Description</label>
                                                <textarea id="description" name="description" class="form-control"
                                                    placeholder="Enter Description">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-4">
                                                <button type="submit" class="btn btn-primary">Create</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection