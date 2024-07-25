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
                        <div class="card-tools text-end">
                            <button id="delete-all" class="btn btn-danger delete-button">Delete All</button>
                            <button id="activate-all" class="btn btn-success">Activate Selected</button>
                            <button id="deactivate-all" class="btn btn-warning">Deactivate Selected</button>
                            <a href="{{ route('admin.brand.create') }}" class="text-dark btn btn-primary">Add Brands</a>
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
                                        <th>#</th>
                                        <th>ID</th>
                                        <th>Actions</th>
                                        <th>Brand Name</th>
                                        <th>Image</th>
                                        <th>Created At</th>
                                        <th>Status</th>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
    var BrandTable = $('#brand-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.brand') }}",
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
            { data: 'brand_name', name: 'brand_name' },
            {
                data: 'image', name: 'image', render: function (data, type, row) {
                    return '<img src="' + data + '" alt="Brand Image" width="70">';
                }
                , orderable: false, searchable: false
            },
            { data: 'created_at', name: 'created_at' },
            { data: 'status', name: 'status' },
        ],
        order: [[1, 'asc']],
        drawCallback: function(settings) {
            // Attach event listener to the dynamically generated checkboxes
            $('.status-toggle').on('click', function() {
                var brandId = $(this).data('id');
                var status = $(this).is(':checked') ? 1 : 0;
                updateStatus(brandId, status);
            });
        }
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
            success: function(data) {
                if (data.success) {
                    console.log('Status Updated !!');
                } else {
                    alert('Failed to update status.');
                }
                
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
});

        
</script>
@endsection