@extends('layout.base')
<style>
    .image-container {
    position: relative;
    display: inline-block;
    margin: 10px;
    height: 100px;
    width: 100px;
    overflow: hidden;
}

.image {
    height: 100%; width: 100%;
    object-fit: cover;
    display: block;
    transition: filter 0.3s ease;
}

.image-container:hover .image {
    filter: brightness(40%);
}

.copy-btn, .view-btn {
    position: absolute;
    top: 20%;
    left: 80%;
    transform: translate(-50%, -50%);
    display: none;
    padding: 10px 15px;
    background: none;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    opacity: 0.8;
}
.view-btn{
    top: 50%;
}
.copy-btn i, .view-btn i{
    font-size: 1.5rem;
}

.image-container:hover .copy-btn , .image-container:hover .view-btn{
    display: block;
}

.copy-btn:hover, .view-btn:hover {
    opacity: 1;
}

/* Full-screen overlay */
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); /* Dark background */
    display: none; /* Hidden by default */
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.overlay img {
    max-width: 90%;
    max-height: 90%;
}

.close-btn {
    position: absolute;
    top: 20%;
    right: 20px;
    padding: 10px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 3rem;
}

</style>
@section('content')
<div class="page-content">
    @include('layout.sidebar')
    <div class="content-wrapper">
        <div class="content-inner">
            <div class="page-header page-header-light shadow">
                <div class="page-header-content d-lg-flex">
                    <div class="row w-100">
                        <div class="col-lg-11">
                            <h4 class="page-title mb-0">
                                Gallery - <span class="fw-normal text-primary">{{$folder->name}}s</span>
                            </h4>
                        </div>
                        <div class="col-lg-1">
                            <a href="{{route('admin.gallery')}}" class="text-dark btn btn-info text-white m-3">Back</a>  
                        </div>
                    </div>
                </div>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif


            @if (session('success'))
            <script>
                alert('{{ session('success') }}');
            </script>
            @endif
            <div class="row w-100">
                <div class="col-lg-9 col-md-6">
                    <form action="{{route('admin.gallery.upload.images')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="folder_id" value="{{$folder->id}}" hidden>
                        <div class="row w-100 mb-3 p-3">
                            <div class="col-lg-8 col-md-10">
                                <input type="file" class="form-control" name="images[]" id="image" multiple accept="image/*">
                                <span>
                                    {{-- @error('images*')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror --}}
                                </span>
                            </div>
                            <div class="col-lg-4 col-md-2">
                                <button type="submit" class="btn btn-info p-1">Upload</button>
                            </div>
                        </div>  
                    </form>
                </div>
                <div class="col-lg-3 col-md-6 p-3">
                    {{-- <input type="number" id="limit" value="12" min="1" class="form-control" placeholder="Enter number of images per page"> --}}
                    <select id="limit" class="form-control bg-dark text-white">
                        {{-- @if ($images && $images->count())
                            <option value="{{$images->count()}}" selected>--Select Page Per view</option>
                        @endif --}}
                        {{-- <option value="3">3</option>
                        <option value="2">2</option> --}}
                        <option value="1">1</option>

                        <!-- Add more options as needed -->
                    </select>
                    <button id="loadMoreBtn" class="btn btn-primary my-3">Load More</button>
                    
                </div>
            </div>
            <div class="container-fluid w-100">
                <div class="row gap-1">
                    @if (!$images)
                        <p>No Images</p>
                    @else
                    <div id="imageGallery"></div>
                    
                    {{-- <div id="paginationLinks"></div> --}}
                    @endif
                    
                </div>
            </div>
            <div id="imageOverlay" class="overlay">
                <span class="close-btn" onclick="closeOverlay()">&times;</span>
                <img id="overlayImage" src="" alt="Image" width="400px">
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let offset = 0;
    // let limit = $('#limit').val()
    function loadmoreimages(){
        $.ajax({
            type: 'GET',
            url: '{{ route('admin.gallery.image', $folder->id) }}',
            data: {
                offset: offset,
                limit: $('#limit').val(),
            },
            success : function(images){
                if (images.length > 0) {
                    let imageHtml = '';
                    images.forEach(image =>{
                        imageHtml += `
                        <div class="col-lg-3 image-container">
                            <img src="{{ asset('') }}${image.image_path}" class="image" alt="${image.image_path}">
                            <button class="copy-btn" onclick="copyImagePath('${image.image_path}')" data-toggle="tooltip" data-placement="top" title="Copy Path"><i class="ph ph-copy"></i></button>
                            <button class="view-btn" onclick="viewImage('{{ asset('') }}${image.image_path}')" data-toggle="tooltip" data-placement="top" title="View Image"><i class="ph ph-eye"></i></button>
                        </div>
                        
                        `;
                    });
                    $('#imageGallery').append(imageHtml);
                    offset += $('#limit').val();

                }
            }
        })
    }
    // function fetchImages(limit = 12) {
    //     $.ajax({
    //         url: '{{ route('admin.gallery.image', $folder->id) }}', // Update this to your route
    //         type: 'GET',
    //         data: { limit: limit },
    //         success: function(response) {
    //             const images = response.images;
    //             const pagination = response.pagination;

    //             let imageHtml = '';
    //             images.forEach(image => {
    //                 imageHtml += `
    //                     <div class="col-lg-3 image-container">
    //                         <img src="{{ asset('') }}${image.image_path}" class="image" alt="${image.image_path}">
    //                         <button class="copy-btn" onclick="copyImagePath('${image.image_path}')" data-toggle="tooltip" data-placement="top" title="Copy Path"><i class="ph ph-copy"></i></button>
    //                         <button class="view-btn" onclick="viewImage('{{ asset('') }}${image.image_path}')" data-toggle="tooltip" data-placement="top" title="View Image"><i class="ph ph-eye"></i></button>
    //                     </div>
    //                 `;
    //             });

    //             $('#imageGallery').html(imageHtml);
    //             // $('#paginationLinks').html(pagination);
    //         }
    //     });
    // }

    $(document).ready(function() {
        loadmoreimages(); // Load initial set of images

         $('#loadMoreBtn').on('click', function() {
            loadMoreImages();
        });
    });


    // $(document).ready(function (){
    //     fetchImages($('#limit').val());

    //     $('#limit').on('change', function() {
    //         fetchImages($(this).val());
    //     });

    // });
    function copyImagePath(path) {
    // Create a temporary input to hold the text to copy
    const tempInput = document.createElement('input');
    tempInput.value = path;
    document.body.appendChild(tempInput);
    
    // Select the text field
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // For mobile devices

    // Copy the text inside the text field
    document.execCommand("copy");

    // Remove the temporary input
    document.body.removeChild(tempInput);

    Swal.fire({
        title: 'Copied!',
        text: 'The image path has been copied to clipboard.',
        icon: 'success',
        confirmButtonText: 'OK'
    });

    // Optionally, show a message to the user
}
    function viewImage(imagePath) {
        console.log('Image Path:', imagePath); // Check the path
        var path = imagePath;
        const overlay = document.getElementById('imageOverlay');
        const overlayImage = document.getElementById('overlayImage');
        overlayImage.src = path; // Set the correct image path
        overlay.style.display = 'flex'; // Show the overlay
    }


    function closeOverlay() {
        document.getElementById('imageOverlay').style.display = 'none';
    }



</script>

{{-- <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add Brand Images </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.dealers.view.create.post', $folder->id)}}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <label for="name">Contact Person Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Username"  autofocus value="{{old('name')}}">
                            <span>
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </span>
                        </div>
                        
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Create </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> --}}


@endsection