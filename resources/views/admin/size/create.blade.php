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
                            Dashboard - <span class="fw-normal">Add Size</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="content">
                <form action="{{ route('admin.size.create.post') }}" method="POST">
                    @csrf
                    <div class="row my-3">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>                            
                        </div>
                        <div class="col-lg-1 d-flex align-items-center justify-content-center">
                            <div class="border border-light" style="height: 100%; width: 1px;"></div>
                        </div>
                        <div class="col-lg-5">
                            <label for="short_name" class="form-label">Short Name</label>
                            <input type="text" name="short_name" id="short_name" class="form-control" value="{{ old('short_name') }}">                            
                            @error('short_name')
                                    <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Size</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
