@extends('layout.base')
@section('title', 'Admin-products-category')

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
                        <h5 class="mb-0">Add New Product Group</h5>
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
                                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Category Name"  autofocus>
                                        <span>
                                            @error('name')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </span>
                                    </div>
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
@endsection