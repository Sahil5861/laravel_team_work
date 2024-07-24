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
            <a href="{{route('admin.category')}}" class="text-dark">Back</a>
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
                            <form action="{{route('admin.category.edit.post', $category->id)}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{$category->id}}">
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <label for="name">Category Name</label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Category Name" value="{{$category->category_name}}">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="name">Parent Category</label>
                                        <select name="parent_category" id="parent_category"  class="form-control">
                                            <option value="">select parent categoty (optional)</option>
                                            @if (!$categories->isEmpty())
                                                @foreach ($categories as $item)
                                                    <option value="{{$item->category_name}}"
                                                        @if ($item->parent_category == $category->parent_category)
                                                            selected
                                                        @endif
                                                        >{{$item->category_name}}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>

                                        {{-- <label for="status">Status</label>
                                        <input type="checkbox" name="status" class="form-control" id="status"> --}}

                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <small>Current Image</small>
                                        <img src="{{asset($category->image)}}" alt="image" width="80px" height="80px">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-10">
                                        <label for="image">Update Image</label>
                                        <input type="file" name="image" id="image" placeholder="Choose Image" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
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