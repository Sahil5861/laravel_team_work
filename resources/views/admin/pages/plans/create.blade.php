@extends('layout.base')
@section('title', 'Admin - Create Plan')

@section('content')
<div class="page-content">
    @include('layout.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="d-flex">
                        <h4 class="page-title mb-0">
                            Dashboard - <span class="fw-normal">Plan</span>
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
                        {{-- Form --}}
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection