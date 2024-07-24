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
                            Dashboard - <span class="fw-normal">User</span>
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
                        <!-- <h5 class="card-title">User</h5> -->
                        <div class="card-tools text-end">
                            <button id="delete-all" class="btn btn-danger my-1">Delete All</button>
                            <button id="activate-all" class="btn btn-success my-1">Activate Selected</button>
                            <button id="deactivate-all" class="btn btn-warning my-1">Deactivate Selected</button>
                            <a href="{{ route('users.create') }}" class="btn btn-primary my-1">Add User</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered text-center" id="users-table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>ID</th>
                                        <th class="text-center">Actions</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Role</th>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        var usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('users.index') }}",
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
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'role', name: 'role' },
                { data: 'created_at', name: 'created_at' },
            ],
            order: [[0, 'asc']],
        });

        // Select/Deselect all checkboxes
        $('#select-all').on('click', function () {
            var isChecked = this.checked;
            $('#users-table .select-row').each(function () {
                $(this).prop('checked', isChecked);
            });
        });

        // Bulk delete
        $('#delete-all').on('click', function () {
            var ids = getSelectedIds();
            if (ids.length === 0) {
                alert('No users selected.');
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover these users!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('users.deleteSelected') }}",
                        method: 'POST',
                        data: { ids: ids },
                        success: function (response) {
                            Swal.fire(
                                'Deleted!',
                                response.success,
                                'success'
                            ).then(() => usersTable.ajax.reload());
                        }
                    });
                }
            });
        });

        // Bulk activate
        $('#activate-all').on('click', function () {
            var ids = getSelectedIds();
            if (ids.length === 0) {
                alert('No users selected.');
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to activate the selected users!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, activate!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('users.activateSelected') }}",
                        method: 'POST',
                        data: { ids: ids },
                        success: function (response) {
                            Swal.fire(
                                'Activated!',
                                response.success,
                                'success'
                            ).then(() => usersTable.ajax.reload());
                        }
                    });
                }
            });
        });

        // Bulk deactivate
        $('#deactivate-all').on('click', function () {
            var ids = getSelectedIds();
            if (ids.length === 0) {
                alert('No users selected.');
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to deactivate the selected users!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, deactivate!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('users.deactivateSelected') }}",
                        method: 'POST',
                        data: { ids: ids },
                        success: function (response) {
                            Swal.fire(
                                'Deactivated!',
                                response.success,
                                'success'
                            ).then(() => usersTable.ajax.reload());
                        }
                    });
                }
            });
        });

        function getSelectedIds() {
            var ids = [];
            $('.select-row:checked').each(function () {
                ids.push($(this).val());
            });
            return ids;
        }
    });
</script>

@endsection