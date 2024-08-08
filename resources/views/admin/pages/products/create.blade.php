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
                            Dashboard - <span class="fw-normal">Add Product</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title fw-bold">Add Product</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.product.create.post') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" step="0.01" name="price" id="price" class="form-control"
                                            value="{{ old('price') }}" required>
                                        @error('price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image</label>
                                        <input type="file" name="image" id="image" class="form-control" required>
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="4"
                                            required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 border-left">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select name="category_id" id="category_id" class="form-control" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="brand_id" class="form-label">Brand</label>
                                        <select name="brand_id" id="brand_id" class="form-control" required>
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="product_group_id" class="form-label">Product Group</label>
                                        <select name="product_group_id" id="product_group_id" class="form-control"
                                            required>
                                            <option value="">Select Product Group</option>
                                            @foreach($productGroups as $group)
                                                <option value="{{ $group->id }}">{{ $group->products_group_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('product_group_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="offer_price" class="form-label">Offer Price</label>
                                        <input type="number" step="0.01" name="offer_price" id="offer_price"
                                            class="form-control" value="{{ old('offer_price') }}">
                                        @error('offer_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="offer_expiry" class="form-label">Offer Expiry</label>
                                        <input type="date" name="offer_expiry" id="offer_expiry" class="form-control"
                                            value="{{ old('offer_expiry') }}">
                                        @error('offer_expiry')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3 border p-3">
                                        <label for="additional_images" class="form-label">Additional Images</label>
                                        <input type="file" name="additional_images[]" id="additional_images"
                                            class="form-control" accept="image/*" multiple>
                                        <button type="button" id="add_images"
                                            class="btn btn-primary mt-2 mb-3">Add</button>
                                        @error('additional_images')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <div id="additionalImagesPreview" class="d-flex flex-wrap"></div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    document.getElementById('add_images').addEventListener('click', function () {
        const input = document.getElementById('additional_images');
        const previewContainer = document.getElementById('additionalImagesPreview');

        if (input.files.length === 0) {
            return; // No files selected
        }

        previewContainer.innerHTML = ''; // Clear previous previews

        const dataTransfer = new DataTransfer();

        for (const file of input.files) {
            dataTransfer.items.add(file);

            const reader = new FileReader();

            reader.onload = function (e) {
                const imgDiv = document.createElement('div');
                imgDiv.classList.add('position-relative', 'me-2', 'mb-2');

                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('img-thumbnail');
                img.style.width = '80px';
                img.style.height = '80px';
                img.style.objectFit = 'cover';
                img.alt = 'Additional Image';

                const deleteBtn = document.createElement('span');
                deleteBtn.innerHTML = '&times;';
                deleteBtn.classList.add('position-absolute', 'top-0', 'end-0', 'bg-danger', 'text-white', 'rounded-circle', 'cursor-pointer');
                deleteBtn.style.cursor = 'pointer';
                deleteBtn.style.display = 'flex';
                deleteBtn.style.justifyContent = 'center';
                deleteBtn.style.alignItems = 'center';
                deleteBtn.style.width = '20px';
                deleteBtn.style.height = '20px';

                deleteBtn.addEventListener('click', function () {
                    imgDiv.remove();

                    // Remove file from DataTransfer object
                    const updatedFiles = Array.from(dataTransfer.files).filter(f => f !== file);
                    const newDataTransfer = new DataTransfer();
                    updatedFiles.forEach(f => newDataTransfer.items.add(f));
                    input.files = newDataTransfer.files;
                });

                imgDiv.appendChild(img);
                imgDiv.appendChild(deleteBtn);
                previewContainer.appendChild(imgDiv);
            };

            reader.readAsDataURL(file);
        }

        // Update the input field with the selected files
        input.files = dataTransfer.files;
    });

</script>
@endsection