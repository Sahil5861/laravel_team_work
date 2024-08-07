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
                            Dashboard - <span class="fw-normal">Add Brand</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title fw-bold">Add Brand</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.brand.create.post')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="name">Brand Name</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        placeholder="Enter Brand Name" autofocus {{old('name')}}>
                                    <span>
                                        @error('name')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-lg-6">
                                    <label for="image">Select Image</label>
                                    <input type="file" name="image" id="image" class="form-control"
                                        placeholder="Choose Image" {{old('image')}}>
                                </div>
                                <br><br>
                            </div>
                            <div class="row mb-3">
                                <div class=" col-lg-4">
                                    <button type="submit" class="btn btn-primary">Create </button>
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