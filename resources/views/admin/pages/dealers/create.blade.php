@extends('layout.base')
@section('title', 'Admin-products-category')
<style>
    .w-45{
        width: 45%;
    }
</style>
@section('content')
<div class="page-content">
    @include('layout.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="page-header page-header-light shadow">
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
                {{--  Form --}}
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
                                    <option value="1"
                                    @if(old('authenticated') == 0)
                                        selected
                                    @endif>Yes</option>
                                <option value="0"
                                    @if(old('authenticated') == 0)
                                        selected
                                    @endif>No</option>                                
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
                    </div>
                    <h3 class="mb-4">Add a Contact Person</h3>
                    <div class="row my-2">
                        <div class="col-lg-6 mb-3">
                            <div class="mb-3">
                                <label for="contact_name">Name</label>
                                <input type="text" id="contact_name" name="contact_name" class="form-control text-white" placeholder="Username"  autofocus value="{{old('name')}}">
                                <span>
                                    @error('contact_name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>                                
                            </div>
                            <div class="mb-3">
                                <label for="contact_phone">Phone Number</label>
                                <input type="text" id="contact_phone" name="contact_phone" class="form-control text-white" placeholder="Enter Phone Number" value="{{old('phone')}}" >
                                <span>
                                    @error('contact_phone')
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
                                <label for="contact_email">Email</label>
                                <input type="email" id="contact_email" name="contact_email" class="form-control text-white" placeholder="Email Id" value="{{old('email')}}">
                                <span>
                                    @error('contact_email')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>                                
                            </div>
                            <div class="mb-3">
                                <label for="pass1">Create Password</label>
                                <input type="password" id="password" name="password" class="form-control text-white" placeholder="Create Password" value="{{old('pass1')}}">
                                <span>
                                    @error('password')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </span>                                
                            </div>
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
<script>
    $(document).ready(function() {
    
    // function for dynamic dropdown
    $('#country').change(function (){
        var countryId = $(this).val();
        $('#state').html('<option value="">Select State</option>');
        $('#city').html('<option value="">Select City</option>');

        if (countryId) {
            $.ajax({
                url: '/states/' + countryId,
                type: 'GET',
                success: function (data){
                    $.each(data, function (key, value){
                        $('#state').append('<option value="'+ value.id +'">' +value.name+ '</option>');
                    })
                }
            });
        }
    })

    $('#state').change(function (){
        var stateId  = $(this).val();
        $('#city').html('<option value="">Select City</option>');

        if (stateId) {
            $.ajax({
                url: '/cities/' + stateId ,
                type: 'GET',
                success: function (data){
                    $.each(data, function (key, value){
                        $('#city').append('<option value="'+ value.id +'">' +value.name+ '</option>');
                    })
                }
            });
        }
    })

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