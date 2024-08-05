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
                            Dashboard - <span class="fw-normal">Add Role</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title fw-bold">Add Role</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.role.create.post')}}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="name" id="title" class="form-control">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>
                            <button type="submit" class="btn btn-primary">Add Role</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection