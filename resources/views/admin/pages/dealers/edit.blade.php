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
<div class="page-content">
    @include('layout.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="page-header">
                <div class="page-header-content d-lg-flex">
                    <div class="row w-100 px-3">
                        <h4 class="page-title mb-0 col-lg-11">
                            Dashboard - <span class="fw-normal">Dealers</span>
                        </h4>
                        <div class="col-lg-1 my-3">
                            <a href="{{route('admin.dealers')}}" class="text-dark btn btn-info text-white">Back</a>  
                        </div>  
                    </div>
                </div>
            </div>
            <div class="content">
                <form action="{{route('admin.dealers.create.post')}}" method="POST" enctype="multipart/form-data">
                    @csrf  
                    <h3 class="mb-4">Add Dealer's Data</h3>
                    <div class="row my-2">
                        <div class="col-lg-6 mb-3">
                            <div class="mb-3">
                                <label for="name">Dealer Name</label>
                                <input type="text" id="name" name="name" class="form-control text-white" placeholder="Enter Dealer's Name"  autofocus value="{{old('name')}}" >
                                <span>
                                    @error('name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>
                            </div>
                            <div class="mb-3">
                                <label for="name">Dealer Email</label>
                                <input type="email" id="email" name="email" class="form-control  text-white" placeholder="Enter Dealer's Email"  value="{{old('email')}}">
                                <span>
                                    @error('email')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>
                            </div>
                            <div class="mb-3">
                                <label for="name">Dealer Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control text-white" placeholder="Enter Dealer's Phone Number"  value="{{old('phone')}}" >
                                <span>
                                    @error('phone')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>                                
                            </div>
                        </div>
                        <div class="col-lg-1 d-flex align-items-center justify-content-center">
                            <div class="border border-light" style="height: 100%; width: 1px;"></div>
                        </div>
                        <div class="col-lg-5 mb-3">
                            
                            <div class="mb-3">
                                {{-- <label for="name">Country</label>
                                <input type="text" id="country" name="country" class="form-control text-white" placeholder="Enter Dealer's Contry" value="{{old('country')}}" >
                                <span>
                                    @error('country')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>  --}}
                                <label for="country">Country</label>
                                <select id="country" name="country" class="form-control text-white">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <span>
                                    @error('country')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>                                                                
                            </div>
                            <div class="mb-3">
                                <label for="name">State</label>
                                <select id="state" class="form-control text-white">
                                    <option value="">Select State</option>
                                </select>
                                <span>
                                    @error('state')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>                                
                            </div>
                            <div class="mb-3">
                                <label for="name">City</label>
                                <select id="city" class="form-control text-white">
                                    <option value="">Select City</option>
                                </select>
                                <span>
                                    @error('city')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>                        
                            </div>
                            
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="mb-3">
                                <label for="authenticated">Is Authenticated</label>
                                <select name="authenticated" id="authenticated" class="form-control text-white">
                                    <option value="1"{{$dealer->authenticated == 1 ? 'selected' : ''}}>Yes</option>
                                <option value="0"{{$dealer->authenticated == 0 ? 'selected' : ''}}>No</option>                                
                                </select>
                            </div>
                            <div class="mb-3"style="display: none" id="gst_number_group">
                                <label for="gst_no">GSTI Number</label>
                                <input type="text" id="gst_no" name="gst_no" class="form-control" {{old('gst_no')}} >
                                <span>
                                    @error('gst_no')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>                                
                            </div>                            
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="contact_person_id">Contact Person</label>
                                <select name="contact_person_id" id="contact_person_id" class="form-control text-white">
                                    <option value="" disabled>--Select Contact Person--</option>
                                    @foreach ($contactPersons as $person)
                                        <option value="{{$person->id}}" {{$person->dealers_id == $dealer->id ? 'selected' : ''}}>{{$person->name}}</option>
                                    @endforeach
                                </select>
                                <span>
                                    @error('contact_person_id')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>                                
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class=" col-lg-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>                

                <div class="row">
                    <div class="col-lg-12">
                        {{--  Form --}}
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="mb-0">Edit Your Dealers</h5>
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
                                    <form action="{{route('admin.dealers.edit.post',$dealer->id)}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" id="id" value="{{$dealer->id}}">
                                        <div class="row mb-3">
                                            <div class="col-lg-4">
                                                <label for="name">Dealer Name</label>
                                                <input type="text" id="name" name="name" class="form-control" value="{{$dealer->business_name}}"  autofocus value="{{old('name')}}">
                                                <span>
                                                    @error('name')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="name">Dealer Email</label>
                                                <input type="email" id="email" name="email" class="form-control" value="{{$dealer->business_email}}">
                                                <span>
                                                    @error('email')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="name">Dealer Phone</label>
                                                <input type="text" id="phone" name="phone" class="form-control" value="{{$dealer->phone_number}}" value="{{old('phone')}}" >
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
                                                <input type="text" id="city" name="city" class="form-control" value="{{$dealer->city}}"  autofocus {{old('city')}} >
                                                <span>
                                                    @error('city')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="name">State</label>
                                                <input type="text" id="state" name="state" class="form-control" value="{{$dealer->state}}" autofocus {{old('state')}} >
                                                <span>
                                                    @error('state')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="name">Country</label>
                                                <input type="text" id="country" name="country" class="form-control" value="{{$dealer->country}}"  autofocus {{old('contry')}} >
                                                <span>
                                                    @error('contry')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-4">
                                                <label for="contact_person_id">Contact Person</label>
                                                <select name="contact_person_id" id="contact_person_id" class="form-control">
                                                    <option value="">--Select Contact Person--</option>
                                                    @foreach ($contactPersons as $item)
                                                        <option value="{{$item->id}}" {{$item->id == $dealer->contact_person_id ? 'selected' : ''}}>{{$item->name}}</option>
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
                                                    <option value="1"
                                                    {{$dealer->authenticated == 1 ? 'selected' : ''}}>Yes</option>
                                                    <option value="0"
                                                    
                                                    {{$dealer->authenticated == 0 ? 'selected' : ''}}>No</option>
                                                </select>
                                                <span>
                                                    @error('authenticated')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-lg-4" id="gst_number_group" style="display:none;">
                                                <label for="gst_no">GSTI Number</label>
                                                <input type="text" id="gst_no" name="gst_no" class="form-control" value="{{$dealer->GST_number}}"  autofocus {{old('gst_no')}} >
                                                <span>
                                                    @error('gst_no')
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

        </div>
    </div>
</div>

    

    <script>
        $(document).ready(function() {
        // Function to toggle GST number field
        function toggleGSTField() {
            var isAuthenticated = $('#authenticated').val();
            if (isAuthenticated == '1') {
                $('#gst_number_group').show();
            } else {
                $('#gst_number_group').hide();
            }
        }
    
        // Initial check on page load
        toggleGSTField();
    
        // Event listener for change in select box
        $('#authenticated').change(function() {
            toggleGSTField();
        });
    });
    
    </script>
@endsection