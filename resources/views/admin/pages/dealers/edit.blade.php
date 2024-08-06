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
        <button type="button" class="btn btn-info text-white">
            <a href="{{route('admin.dealers')}}" class="text-white link">Back</a>
        </button><br><br>
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