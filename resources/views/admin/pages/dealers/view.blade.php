@extends('layout.base')

@section('content')
<div class="page-content">
    @include('layout.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="row w-100">
                        <div class="col-lg-11">
                            <h4 class="page-title mb-0">
                                Dashboard - <span class="fw-normal text-primary">{{$dealer->business_name}}</span>
                            </h4>
                        </div>
                        <div class="col-lg-1">
                            <a href="{{route('admin.dealers')}}" class="text-dark btn btn-info text-white m-3">Back</a>  
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Dealer</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped table-borderless">
                                            <tr>
                                                <th>Dealer Name</th>
                                                <td>{{$dealer->business_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>Dealer Email</th>
                                                <td class="text-primary">{{$dealer->business_email}}</td>
                                            </tr>
                                            <tr>
                                                <th>Dealer Phone</th>
                                                <td>{{$dealer->phone_number}}</td>
                                            </tr>
                                            <tr>
                                                <th>Address</th>
                                                <td>{{$dealer->city}}, {{$dealer->state}}, {{$dealer->country}}</td>
                                           </tr>
                                           <tr>
                                            
                                                <th>GST Number</th>
                                                <td class="text-primary">{{$dealer->GST_number ? $dealer->GST_number : "----------"}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <h3 class="col-lg-10">Contact Persons</h3>
                                            <div class="col-lg-2">
                                                <a href="#" class="text-dark btn btn-info text-white" data-toggle="modal" data-target="#addModal"> + Add</a>  
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <table class="table table-striped table-borderless" id="persons-table">
                                            <thead>
                                                <tr>
                                                    {{-- <th>Status</th> --}}
                                                    <th>Action</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone Number</th>
                                                    <th>Password</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add Contact Persons</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.dealers.view.create.post', $dealer->id)}}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="text" id="role" name="role" class="form-control bg-dark text-white" value="3" hidden>
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <label for="dealer_id">Dealer Name</label>
                            <input type="text" id="dealer_id" name="dealer_id" class="form-control bg-dark text-white" value="{{$dealer->business_name}}" readonly>
                        </div>
                        <div class="col-lg-6">
                            <label for="name">Contact Person Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Username"  autofocus value="{{old('name')}}">
                            <span>
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </span>
                        </div>
                        
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <label for="email">Conatct Person Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email Id" value="{{old('email')}}">
                            <span>
                                @error('email')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </span>
                        </div>
                        <div class="col-lg-6">
                            <label for="phone">Contact Person Phone</label>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter Phone Number" value="{{old('phone')}}" >
                            <span>
                                @error('phone')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <label for="pass1">Create Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Create Password" value="{{old('pass1')}}">
                            <span>
                                @error('password')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </span>
                        </div>
                        <div class="col-lg-6">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Create Password" value="{{old('pass2')}}">
                            <span>
                                @error('password_confirmation')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Create </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        var personsTable = $('#persons-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.dealers.viewdata', ['id' => $dealer->id])}}",
                data: function (d) {
                    d.status = $('#status').val();
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'real_password', name: 'real_password' },
            ],
            order: [[0, 'asc']],
            drawCallback: function (settings) {  
                
                $('.set-primary-button').on('click', function (e){
                    e.preventDefault();
                    var userId = $(this).data('id');
                    var dealerId = "{{ $dealer->id }}";

                    console.log(userId, dealerId);
                    

                    $.ajax({
                        url: "{{ route('admin.dealers.view.contact.setprimary')}}", // Update with your route
                        method: 'POST',
                        data: {
                        _token: "{{ csrf_token() }}",
                        user_id: userId,
                        dealer_id: dealerId
                        },
                    success: function(response) {
                    if (response.success) {
                        // Reload the DataTable to reflect changes
                        alert('Primary set');
                        
                        personsTable.ajax.reload();
                    } else {
                        alert('An error occurred.');
                        }
                    },
                });

                });
                    

                    
                $('#select-all').on('click', function () {
                    var isChecked = this.checked;
                    $('#dealers-table .select-row').each(function () {
                        $(this).prop('checked', isChecked);
                    });
                });

                $('#delete-selected').on('click', function () {
                    var selectedIds = $('.select-row:checked').map(function () {
                        return this.value;
                    }).get();

                    if (selectedIds.length > 0) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: "{{ route('admin.dealers.deleteSelected') }}",
                                    method: 'DELETE',
                                    data: { selected_dealers: selectedIds },
                                    success: function (response) {
                                        BrandTable.ajax.reload(); // Refresh the page
                                        Swal.fire(
                                            'Deleted!',
                                            response.success,
                                            'success'
                                        );
                                    },
                                    error: function (xhr) {
                                        Swal.fire(
                                            'Error!',
                                            'Something went wrong.',
                                            'error'
                                        );
                                    }
                                });
                            }
                        })


                    }
                    else {
                        Swal.fire(
                            'Error!',
                            'Please select at least one brand to delete.',
                            'error'
                        );
                    }
                })
                $('.status-toggle').on('click', function () {
                    var dealerId = $(this).data('id');
                    var status = $(this).is(':checked') ? 1 : 0;
                    updateStatus(dealerId, status);
                });
            }
        });

        $('#status').on('change', function () {
            dealersTable.ajax.reload();
        });

        $(document).ready(function () {
            $('#export-dealers').on('click', function () {
                var status = $('#status').val();
                var url = "{{ route('admin.dealers.export') }}";
                if (status) {
                    url += "?status=" + status;
                }
                window.location.href = url;
            });
        });

    });


        function updateStatus(brandId, status) {
            $.ajax({
                url: `{{ url('admin/dealer/update-status') }}/${brandId}`,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify({ status: status }),
                success: function (data) {
                    if (data.success) {
                        // console.log('Status Updated !!');
                        Swal.fire(
                            'Updated!',
                            'Status Updated',
                            'success'
                        );
                        // alert('Status Updated !!');

                        // location.reload(); // Refresh the page
                        BrandTable.ajax.reload();
                    } else {
                        alert('Failed to update status.');
                    }

                },
                error: function (error) {
                    console.error('Error:', error);
                }
            });
        }


</script>
@endsection