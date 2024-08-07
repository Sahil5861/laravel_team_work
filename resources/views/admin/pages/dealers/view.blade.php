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
                            Dashboard - <span class="fw-normal">Dealers</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-11">
                                <h5 class="card-title">Dealers</h5>
                            </div>
                            <div class="col-lg-1">
                                <a href="{{route('admin.dealers')}}" class="text-dark btn btn-info text-white">Back</a>  
                            </div>
                        </div>
                        
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
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
                            <div class="col-md-6 ml md-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Contact Persons</h3>
                                    </div>
                                </div>
                                
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Brands</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importForm" action="{{route('admin.brand.import')}}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="csv_file">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control" required value="{{old('csv_file')}}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" form="importForm" class="btn btn-primary">Import</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        var dealersTable = $('#dealers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.dealers') }}",
                data: function (d) {
                    d.status = $('#status').val();
                }
            },
            columns: [
                {
                    data: null,
                    name: 'select',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<input type="checkbox" class="select-row" value="' + row.id + '">';
                    }
                },
                { data: 'id', name: 'id' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'business_name', name: 'business_name' },
                { data: 'business_email', name: 'business_email' },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'view', name: 'view', orderable: false, searchable: false },
            ],

            order: [[1, 'asc']],
            drawCallback: function (settings) {
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
    });


</script>
@endsection