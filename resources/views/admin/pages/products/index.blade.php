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
                        <h5 class="card-title">Admin/Product</h5>
                        <div class="card-tools text-end" style="display: flex; align-items:center; justify-content: space-between;">
                            <div class="btns">
                                <a href="{{ route('admin.products.create') }}" class="text-dark btn btn-primary">+ Add Product</a>
                                <button class="btn btn-danger" id="delete-selected">Delete Selected</button>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="text-body" data-bs-toggle="dropdown">
                                    <i class="ph-list"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#"class="dropdown-item">Import Categories</a>
                                    <a href="#"class="dropdown-item">Export Categories</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-bordered text-center" id="products-table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>S.no</th>
                                        <th>Actions</th>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>created At</th>
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
    var ProductsTable = $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.products') }}",
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
            { data: 'action', name: 'action'},
            {
                data: 'image', name: 'image', render: function (data, type, row) {
                    return '<img src="' + data + '" alt="Brand Image" width="70">';
                }
                , orderable: false, searchable: false
            },
            { data: 'name', name: 'name', orderable: false, searchable: false  },
            { data: 'price', name: 'price', orderable: false, searchable: false  },
            { data: 'created_at', name: 'created_at' },
            { data: 'status', name: 'status' },
        ],
        order: [[1, 'asc']],
        drawCallback: function(settings) {

            $('#select-all').on('click', function () {
                var isChecked = this.checked;
                $('#products-table .select-row').each(function (){
                    $(this).prop('checked', isChecked);
                });
            });

            $('#delete-selected').on('click', function (){
                var selectedIds = $('.select-row:checked').map(function(){
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
                            url: "{{ route('admin.products.deleteSelected') }}",
                            method: 'DELETE',
                            data: { selected_products: selectedIds },
                            success: function(response) {
                                ProductsTable.ajax.reload(); // Refresh the page
                                Swal.fire(
                                    'Deleted!',
                                    response.success,
                                    'success'
                                );
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong.',
                                    'error'
                                );
                            }
                        });
                        }
                    })

                    // if (confirm('Do You Want to Delete the Selected Categories')) {
                        
                    // }
                }
                else{
                    Swal.fire(
                        'Error!',
                        'Please select at least one Product to delete.',
                        'error'
                    );
                }
            })

            // Attach event listener to the dynamically generated checkboxes
            $('.status-toggle').on('click', function() {
                var productsId = $(this).data('id');
                var status = $(this).is(':checked') ? 1 : 0;
                updateStatus(productsId, status);
            });
        }
    });

    function updateStatus(productsId, status) {
        $.ajax({
            url: `{{ url('admin/products/update-status') }}/${productsId}`,
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify({ status: status }),
            success: function(data) {
                if (data.success) {
                    // console.log('Status Updated !!');
                    Swal.fire(
                        'Updated!',
                        'Status Updated',
                        'success'
                    );
                    ProductsTable.ajax.reload(); // Refresh the page
                } else {
                    alert('Failed to update status.');
                }
                
            },
            error: function(error) {
                Swal.fire(
                'Error!',
                'Unabel To update Status.',
                'error'
            );
            }
        });
    }
});

</script>
@endsection
