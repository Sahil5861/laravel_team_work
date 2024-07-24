@extends('layout.base')
@section('title', 'Admin-products-category')

<style>
    .img{
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;align-items: center;
        overflow: hidden;

    }
</style>
@section('content')
    <div class="content">
        <button type="button" class="btn btn-dark">
            <a href="{{route('admin.grouprelation')}}" class="text-dark">Back</a>
        </button><br><br>
        <h3></h3>
        <div class="row">
            {{-- <div class="col-lg-4">
                <div class="img bg-secondary">
                    <img src="{{asset($category->image)}}" alt="image" width="100%" height="100%">
                </div>
            </div> --}}
            <div class="col-lg-12">
                {{--  Form --}}
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="mb-0">Edit Your Category</h5>
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
                            <form action="{{route('admin.grouprelation.edit.post', $productsgroup->id)}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{$productsgroup->id}}">
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <label for="name">Product Group Name</label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Category Name" value="{{$productsgroup->products_group_name}}">
                                        <span>
                                            @error('name')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </span>
                                    </div>
                                </div>

                                <div class="row mb-3">
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