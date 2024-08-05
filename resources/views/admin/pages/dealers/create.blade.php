@extends('layout.base')
@section('title', 'Admin-products-category')

@section('content')
<div class="page-content">
    @include('layout.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="d-flex">
                        <h4 class="page-title mb-0">
                            Dashboard - <span class="fw-normal">Dealers</span>
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
                        {{--  Form --}}
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="mb-0">Add New Dealer</h5>
                                <div class="hstack gap-2 ms-auto">
                                    <a href="{{route('admin.dealers')}}" class="text-dark btn btn-info text-white">Back</a>  
                                </div>
                            </div>
                            <div class="collapse show">
                                <div class="card-body">
                                    <form action="{{route('admin.dealers.create.post')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-lg-4">
                                                <label for="name">Dealer Name</label>
                                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Dealer's Name"  autofocus {{old('name')}}>
                                                <span>
                                                    @error('name')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="name">Dealer Email</label>
                                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter Dealer's Email"  autofocus {{old('email')}}>
                                                <span>
                                                    @error('email')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="name">Dealer Phone</label>
                                                <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter Dealer's Phone Number"  autofocus {{old('phone')}} >
                                                <span>
                                                    @error('phone')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-4">
                                                <label for="name">City</label>
                                                <input type="text" id="city" name="city" class="form-control" placeholder="Enter Dealer's City"  autofocus {{old('city')}} >
                                                <span>
                                                    @error('city')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="name">State</label>
                                                <input type="text" id="state" name="state" class="form-control" placeholder="Enter Dealer's State"  autofocus {{old('state')}} >
                                                <span>
                                                    @error('state')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="name">Country</label>
                                                <input type="text" id="country" name="country" class="form-control" placeholder="Enter Dealer's Contry"  autofocus {{old('contry')}} >
                                                <span>
                                                    @error('contry')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-lg-4">
                                                <label for="contact_person_id">Contact Person Id</label>
                                                <select name="contact_person_id" id="contact_person_id" class="form-control">
                                                    <option value="">--Select Contact Person--</option>
                                                    @foreach ($contactPersons as $item)
                                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                                    @endforeach
                                                </select>
                                                <span>
                                                    @error('contry')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>

                                            <div class="col-lg-4">
                                                <label for="authenticated">Is Authenticated</label>
                                                <select name="authenticated" id="authenticated" class="form-control">
                                                    <option value="">--Select--</option>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                                <span>
                                                    @error('authenticated')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="gst_no">GSTI Number</label>
                                                <input type="text" id="gst_no" name="gst_no" class="form-control" placeholder="Enter Dealer's GST Number"  autofocus {{old('gst_no')}} >
                                                <span>
                                                    @error('gst_no')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
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