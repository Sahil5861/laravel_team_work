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
                            Dashboard - <span class="fw-normal">Products Group</span>
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
                        <h5 class="card-title">Admin/Product Groups</h5>
                        <div class="card-tools text-end"
                            style="display: flex; align-items:center; justify-content: space-between;">
                            <div class="btns">
                                <a href="{{ route('admin.grouprelation.create') }}" class="text-dark btn btn-primary">+
                                    Add Product Groups</a>
                                <button class="btn btn-danger" id="delete-selected">Delete Selected</button>
                                <select name="status" id="status" class="form-control mt-3">
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
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#importModal">Import Product Groups</a>
                                    <a href="#" class="dropdown-item" id="export-product">Export Product Groups</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-bordered text-center" id="group-table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>ID</th>
                                        <th>Actions</th>
                                        <th>Product Group Name</th>
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
<script>
    $(document).ready(function () {
        var ProductGroupTable = $('#group-table').DataTable({
            processing: true,
            serverSide: true,

            ajax: {
                url: "{{ route('admin.grouprelation') }}",
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
                { data: 'products_group_name', name: 'products_group_name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
            ],
            order: [[1, 'asc']],
            drawCallback: function (settings) {
                console.log('yes')
                // Attach event listener to the dynamically generated checkboxes

                $('#select-all').on('click', function () {
                    var isChecked = this.checked;
                    $('#group-table .select-row').each(function () {
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
                                    url: "{{ route('admin.grouprelation.deleteSelected') }}",
                                    method: 'DELETE',
                                    data: { selected_product_groups: selectedIds },
                                    success: function (response) {
                                        ProductGroupTable.ajax.reload(); // Refresh the page
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

                        // if (confirm('Do You Want to Delete the Selected Categories')) {

                        // }
                    }
                    else {
                        Swal.fire(
                            'Error!',
                            'Please select at least one Product Group to delete.',
                            'error'
                        );
                    }
                })
                $('.status-toggle').on('click', function () {
                    var groupId = $(this).data('id');
                    var status = $(this).is(':checked') ? 1 : 0;
                    // updateStatus(groupId, status);
                });
            }



        });
        $('#status').on('change', function () {
            ProductGroupTable.ajax.reload();
        });

        $(document).ready(function () {
        // Existing code...

        $('#export-product').on('click', function () {
            var status = $('#status').val();
            var url = "{{ route('admin.grouprelation.export') }}";
            if (status) {
                url += "?status=" + status;
            }
            window.location.href = url;
        });
    });

        function updateStatus(groupId, status) {
            $.ajax({
                url: `{{ url('admin/group-relation/update-status') }}/${groupId}`,
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