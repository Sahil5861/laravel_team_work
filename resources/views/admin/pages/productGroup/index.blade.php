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
                        <div class="card-tools text-end" style="display: flex; align-items:center; justify-content: space-between;">
                            <div class="btns">
                                <a href="{{ route('admin.grouprelation.create') }}" class="text-dark btn btn-primary">+ Add Product Groups</a>
                                <button class="btn btn-danger" id="delete-selected">Delete Selected</button>
                            </div>
                            
                            <div class="dropdown">
                                <a href="#" class="text-body" data-bs-toggle="dropdown">
                                    <i class="ph-list"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item" data-toggle="modal"
                                        data-target="#importModal">Import Brands </a>
                                    <a href="{{route('admin.grouprelation.export')}}" class="dropdown-item">Export Brands </a>
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

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Product Groups</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importForm" action="{{route('admin.grouprelation.import')}}" method="POST"
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
        var ProductGroupTable = $('#group-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.grouprelation') }}",
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
        drawCallback: function(settings) {
            console.log('yes')
            // Attach event listener to the dynamically generated checkboxes

            $('#select-all').on('click', function () {
                var isChecked = this.checked;
                $('#group-table .select-row').each(function (){
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
                            url: "{{ route('admin.grouprelation.deleteSelected') }}",
                            method: 'DELETE',
                            data: { selected_product_groups: selectedIds },
                            success: function(response) {
                                ProductGroupTable.ajax.reload(); // Refresh the page
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
                        'Please select at least one Product Group to delete.',
                        'error'
                    );
                }
            })
            $('.status-toggle').on('click', function() {
                var groupId = $(this).data('id');
                var status = $(this).is(':checked') ? 1 : 0;
                updateStatus(groupId, status);
            });
        }



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
            success: function(data) {
                if (data.success) {
                    console.log('Status Updated !!');
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
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
});
</script>
@endsection
