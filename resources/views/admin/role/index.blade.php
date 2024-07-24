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
                            Dashboard - <span class="fw-normal">Role</span>
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
                        <h5 class="card-title">Role</h5>
                        <div class="card-tools text-end">
                            <button id="delete-all" class="btn btn-danger delete-button">Delete All</button>
                            <button id="activate-all" class="btn btn-success">Activate Selected</button>
                            <button id="deactivate-all" class="btn btn-warning">Deactivate Selected</button>
                            <a href="{{ route('role.create') }}" class="btn btn-primary">Add Role</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table id="role-table" class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>ID</th>
                                        <th class="text-center">Actions</th>
                                        <th>Name</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Status</th>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {
        // Initialize DataTable
        var roleTable = $('#role-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('role.index') }}",
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
                { data: 'name', name: 'name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'status', name: 'status' },
            ],
            order: [[1, 'asc']],
        });

        // Select/Deselect all checkboxes
        $('#select-all').on('click', function () {
            var isChecked = this.checked;
            $('#role-table .select-row').each(function () {
                $(this).prop('checked', isChecked);
            });
        });

        // Delete selected rows
        $('#delete-all').on('click', function () {
            var ids = [];
            $('.select-row:checked').each(function () {
                ids.push($(this).val());
            });

            if (ids.length > 0) {
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
                            url: '{{ route("roles.bulkDelete") }}',
                            method: 'POST',
                            data: { ids: ids },
                            success: function (response) {
                                roleTable.ajax.reload();
                                Swal.fire(
                                    'Deleted!',
                                    response.success,
                                    'success'
                                );
                            },
                            error: function (response) {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            } else {
                Swal.fire(
                    'No Selection!',
                    'Please select at least one role to delete.',
                    'warning'
                );
            }
        });

        // Activate/Deactivate selected rows
        $('#activate-all, #deactivate-all').on('click', function () {
            var status = $(this).attr('id') === 'activate-all' ? 'active' : 'inactive';
            var ids = [];
            $('.select-row:checked').each(function () {
                ids.push($(this).val());
            });

            if (ids.length > 0) {
                $.ajax({
                    url: '{{ route("roles.bulkStatusUpdate") }}',
                    method: 'POST',
                    data: { ids: ids, status: status },
                    success: function (response) {
                        roleTable.ajax.reload();
                        Swal.fire(
                            'Updated!',
                            'Selected roles have been updated.',
                            'success'
                        );
                    },
                    error: function (response) {
                        Swal.fire(
                            'Error!',
                            'Something went wrong.',
                            'error'
                        );
                    }
                });
            } else {
                Swal.fire(
                    'No Selection!',
                    'Please select at least one role to update.',
                    'warning'
                );
            }
        });

        // Toggle status
        $(document).on('change', '.status-toggle', function () {
            var roleId = $(this).data('id');
            var status = $(this).is(':checked') ? 'active' : 'inactive';

            $.ajax({
                url: '{{ route("role.toggleStatus", "") }}/' + roleId,
                method: 'POST',
                data: { status: status },
                success: function (response) {
                    Swal.fire(
                        'Updated!',
                        response.success,
                        'success'
                    );
                },
                error: function (response) {
                    Swal.fire(
                        'Error!',
                        'Something went wrong.',
                        'error'
                    );
                }
            });
        });
    });
</script>
@endsection
