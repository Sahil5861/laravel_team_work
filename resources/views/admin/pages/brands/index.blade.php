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
                            Dashboard - <span class="fw-normal">Brand</span>
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Brands</h5>
                        <div class="card-tools text-end"
                            style="display: flex; align-items:center; justify-content: space-between;">
                            <div class="btns">
                                {{-- If user wants to remove modal, and use a new page to add :
                                    1. Remove the add modal from the web page : --}}    
                                <a href="{{ route('admin.brand.create') }}" class="text-dark btn btn-primary" data-toggle="modal" data-target="#addModal">Add Brands</a>
                                {{-- <a href="{{ route('admin.brand.create') }}" class="text-dark btn btn-primary">Add Brands</a> --}}
                                <button class="btn btn-danger" id="delete-selected">Delete Selected</button>
                                <br><br>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="text-body" data-bs-toggle="dropdown">
                                    <i class="ph-list"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#importModal">Import Brands</a>
                                    <a href="#" class="dropdown-item" id="export-brands">Export Brands</a>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-bordered text-center" id="brand-table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>ID</th>
                                        <th>Actions</th>
                                        <th>Status</th>
                                        <th>Brand Name</th>
                                        <th>Image</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Add a New Brand</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.brand.create.post')}}" id="addForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="name">Brand Name</label>
                            <input type="text" id="name" name="name" class="form-control"
                                placeholder="Enter Brand Name" autofocus {{old('name')}}>
                            <span>
                                @error('name')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </span>
                        </div>
                        <div class="col-lg-6">
                            <label for="image">Select Image</label>
                            <input type="file" name="image" id="image" class="form-control"
                                placeholder="Choose Image" {{old('image')}}>
                        </div>
                        <br><br>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" form="addForm" class="btn btn-primary">+ Add</button>
            </div>
        </div>
    </div>
</div>
{{-- Add Modal End--}}

{{-- Import Modal --}}
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
                    <a class="btn btn-success csvSample" href="{{ route('sample-file-download-brand') }}">Download
                    Sample</a>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" form="importForm" class="btn btn-primary">Import</button>
            </div>
        </div>
    </div>
</div>
{{-- Import Modal End--}}


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        var BrandTable = $('#brand-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.brand') }}",
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
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'brand_name', name: 'brand_name' },
                {
                    data: 'image', name: 'image', render: function (data, type, row) {
                        return '<img src="' + data + '" alt="Brand Image" width="70">';
                    }
                    , orderable: false, searchable: false
                },
                { data: 'created_at', name: 'created_at' },
            ],

            order: [[1, 'asc']],
            drawCallback: function (settings) {
                $('#select-all').on('click', function () {
                    var isChecked = this.checked;
                    $('#brand-table .select-row').each(function () {
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
                                    url: "{{ route('admin.brand.deleteSelected') }}",
                                    method: 'DELETE',
                                    data: { selected_brands: selectedIds },
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
                    var brandId = $(this).data('id');
                    var status = $(this).is(':checked') ? 1 : 0;
                    updateStatus(brandId, status);
                });
            }



        });

        $('#status').on('change', function () {
            BrandTable.ajax.reload();
        });

        $(document).ready(function () {
            $('#export-brands').on('click', function () {
                var status = $('#status').val();
                var url = "{{ route('admin.brand.export') }}";
                if (status) {
                    url += "?status=" + status;
                }
                window.location.href = url;
            });
        });


        function updateStatus(brandId, status) {
            $.ajax({
                url: `{{ url('admin/brand/update-status') }}/${brandId}`,
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