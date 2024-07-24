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
                            Dashboard - <span class="fw-normal">Category</span>
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
                <h3>Admin/Categories</h3>
                <button type="button" class="btn btn-primary">
                    <a href="{{ route('admin.category.create') }}" class="text-dark">Add Categories</a>
                </button>
                <br><br>
                <div class="table-responsive">
                    <table class="table table-bordered text-center table-hover" id="category-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Actions</th>
                                <th>Image</th>
                                <th>Category Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @if ($categories->isEmpty())
                                <tr>
                                    <td colspan="5">No Category Created</td>
                                </tr>
                            @else
                                @foreach ($categories as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn text-white" data-bs-toggle="dropdown"
                                                    aria-expanded="false" style="border: none;">
                                                    <i class="ph-list me-2"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ route('admin.category.edit', $item->id) }}" class="dropdown-item">
                                                        Edit
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a href="{{ route('admin.category.delete', $item->id) }}" class="dropdown-item">
                                                        Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td><img src="{{ asset($item->image) }}" alt="image" width="100px"></td>
                                        <td>{{ $item->category_name }}</td>
                                        <td>
                                            <label class="form-check form-switch form-check-reverse"
                                                style="display:flex; justify-content:space-between; width:50%;">
                                                <input type="checkbox" class="status-checkbox form-check-input"
                                                    data-id="{{ $item->id }}" {{ $item->status ? 'checked' : '' }}
                                                    id="status{{ $item->id }}" style="cursor:pointer;">
                                                @if ($item->status == 1)
                                                    <span class="text-success">Active</span>
                                                @else
                                                    <span class="text-danger">Inactive</span>
                                                @endif
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('hello!!');
        const checkboxes = document.querySelectorAll('.status-checkbox');
        console.log(checkboxes);
        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                console.log(checkbox);
                const categoryId = this.getAttribute('data-id');
                const status = this.checked ? 1 : 0;
                fetch(`{{ url('admin/category/update-status') }}/${categoryId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Status Updated !!');
                        location.reload();
                    } else {
                        alert('Failed to update status.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });
</script>
@endsection
