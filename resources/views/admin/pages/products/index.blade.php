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
                            Dashboard - <span class="fw-normal">Products</span>
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
                        <h5 class="card-title">Products</h5>
                        <div class="card-tools text-end"
                            style="display: flex; align-items:center; justify-content: space-between;">
                            <div class="btns">
                                <a href="{{ route('admin.product.create') }}" class="text-dark btn btn-primary">Add
                                    Product</a>
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
                                    <a href="#" class="dropdown-item" data-toggle="modal"
                                        data-target="#importModal">Import Products</a>
                                    <a href="#" class="dropdown-item" id="export-products">Export Products</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table id="product-table" class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>ID</th>
                                        <th class="text-center">Actions</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        <th>Price</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Product Group</th>
                                        <th>Description</th>
                                        <th>Offer Price</th>
                                        <th>Offer Expiry</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Updated Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables will populate this -->
                                </tbody>
                            </table>
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
                <h5 class="modal-title" id="importModalLabel">Import Products</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importForm" action="{{ route('admin.product.import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="csv_file">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control" required value="{{ old('csv_file') }}">
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
        var ProductTable = $('#product-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.product') }}",
                data: function (d) {
                    d.status = $('#status').val();
                },
                error: function (xhr, error, thrown) {
                    console.error('Ajax Error:', xhr.responseText);
                }
            },
            columns: [
                {
                    data: null, name: 'select', orderable: false, searchable: false, render: function (data, type, row) {
                        return '<input type="checkbox" class="select-row" value="' + row.id + '">';
                    }
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                {
                    data: 'image', name: 'image', render: function (data, type, row) {
                        return '<img src="' + data + '" alt="Product Image" width="70">';
                    }
                    , orderable: false, searchable: false
                },

                { data: 'price', name: 'price' },
                { data: 'category_name', name: 'category_name' },
                { data: 'brand_name', name: 'brand_name' },
                { data: 'product_group_name', name: 'product_group_name' },

                { data: 'description', name: 'description' },
                { data: 'offer_price', name: 'offer_price' },
                { data: 'offer_expiry', name: 'offer_expiry' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' },
            ],
            order: [[1, 'asc']],
            drawCallback: function (settings) {
                // Your callback code
            }
        });

        $('#status').on('change', function () {
            ProductTable.ajax.reload();
        });

        $('#export-products').on('click', function () {
            var status = $('#status').val();
            var url = "{{ route('admin.product.export') }}";
            if (status) {
                url += "?status=" + status;
            }
            window.location.href = url;
        });

        function updateStatus(groupId, status) {
            $.ajax({
                url: `{{ url('admin/product/update-status') }}/${groupId}`,
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
                        ProductGroupTable.ajax.reload();
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