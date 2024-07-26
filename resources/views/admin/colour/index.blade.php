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
                            Dashboard - <span class="fw-normal">Colour</span>
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
                        <h5 class="card-title">Colour</h5>
                        <div class="card-tools text-end" style="display: flex; align-items:center; justify-content: space-between;">
                            <div class="btns">
                                <a href="{{ route('colour.create') }}" class="text-dark btn btn-primary">Add
                                    Colors</a>
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
                                        data-target="#importModal">Import Colors </a>
                                    <a href="{{route('colours.export')}}" class="dropdown-item">Export Colors</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table id="colour-table" class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>ID</th>
                                        <th class="text-center">Actions</th>
                                        <th>Name</th>
                                        <th>Short Name</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- This will be populated by DataTables -->
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
                <h5 class="modal-title" id="importModalLabel">Import Colors</h5>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {
        // Initialize DataTable
        var ColourTable = $('#colour-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('colour.index') }}",
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
                { data: 'short_name', name: 'short_name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'status', name: 'status' },
            ],
            order: [[1, 'asc']],
        });

        // Select/Deselect all checkboxes
        $('#select-all').on('click', function () {
            var isChecked = this.checked;
            $('#colour-table .select-row').each(function () {
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
                            url: '{{ route("colours.bulkDelete") }}',
                            method: 'POST',
                            data: { ids: ids },
                            success: function (response) {
                                ColourTable.ajax.reload();
                                Swal.fire(
                                    'Deleted!',
                                    response.success,
                                    'success'
                                );
                            },
                            error: function (xhr) {
                                console.log(xhr.responseText);
                            }
                        });
                    }
                });
            } else {
                Swal.fire(
                    'No Rows Selected',
                    'Please select at least one colour to delete.',
                    'warning'
                );
            }
        });

        // Activate selected rows
        $('#activate-all').on('click', function () {
            var ids = [];
            $('.select-row:checked').each(function () {
                ids.push($(this).val());
            });

            if (ids.length > 0) {
                $.ajax({
                    url: '{{ route("colours.bulkStatusUpdate") }}',
                    method: 'POST',
                    data: { ids: ids, status: 'active' },
                    success: function (response) {
                        ColourTable.ajax.reload();
                        Swal.fire(
                            'Activated!',
                            response.success,
                            'success'
                        );
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            } else {
                Swal.fire(
                    'No Rows Selected',
                    'Please select at least one colour to activate.',
                    'warning'
                );
            }
        });

        // Deactivate selected rows
        $('#deactivate-all').on('click', function () {
            var ids = [];
            $('.select-row:checked').each(function () {
                ids.push($(this).val());
            });

            if (ids.length > 0) {
                $.ajax({
                    url: '{{ route("colours.bulkStatusUpdate") }}',
                    method: 'POST',
                    data: { ids: ids, status: 'inactive' },
                    success: function (response) {
                        ColourTable.ajax.reload();
                        Swal.fire(
                            'Deactivated!',
                            response.success,
                            'success'
                        );
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            } else {
                Swal.fire(
                    'No Rows Selected',
                    'Please select at least one colour to deactivate.',
                    'warning'
                );
            }
        });
    });
</script>

@endsection