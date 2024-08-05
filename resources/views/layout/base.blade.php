<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Limitless - Responsive Web Application Kit by Eugene Kopyov</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Global stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/inter/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/phosphor/styles.min.css') }}">
    <link rel="stylesheet" href="{{ asset('full/assets/css/ltr/all.min.css') }}">


    <!-- /global stylesheets -->
    <style>

    </style>
</head>

<body>
    @include('layout.header')
    {{-- @include('layout.header')
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    <script>
        alert("{{ session('success') }}");
    </script>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    <script>
        alert("{{ session('error') }}");
    </script>
    @endif --}}
    @yield('content')

    @include('layout.footer')

    <!-- Core JS files -->
    <script src="{{ asset('assets/js/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/demo/demo_configurator.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script src="{{ asset('assets/js/vendor/visualization/d3/d3.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/visualization/d3/d3_tooltip.js') }}"></script>
    <script src="{{ asset('assets/demo/pages/dashboard.js') }}"></script>
    <script src="{{ asset('assets/demo/charts/pages/dashboard/streamgraph.js') }}"></script>
    <script src="{{ asset('assets/demo/charts/pages/dashboard/sparklines.js') }}"></script>
    <script src="{{ asset('assets/demo/charts/pages/dashboard/lines.js') }}"></script>
    <script src="{{ asset('assets/demo/charts/pages/dashboard/areas.js') }}"></script>
    <script src="{{ asset('assets/demo/charts/pages/dashboard/donuts.js') }}"></script>
    <script src="{{ asset('assets/demo/charts/pages/dashboard/bars.js') }}"></script>
    <script src="{{ asset('assets/demo/charts/pages/dashboard/progress.js') }}"></script>
    <script src="{{ asset('assets/demo/charts/pages/dashboard/heatmaps.js') }}"></script>
    <script src="{{ asset('assets/demo/charts/pages/dashboard/pies.js') }}"></script>
    <script src="{{ asset('assets/demo/charts/pages/dashboard/bullets.js') }}"></script>
    <!-- /theme JS files -->

    <!-- Additional JS files -->
    <script src="{{ asset('assets/js/vendor/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/forms/selects/bootstrap_multiselect.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/uploaders/fileinput/plugins/sortable.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/uploaders/fileinput/fileinput.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/notifications/sweet_alert.min.js') }}"></script>
    <script src="{{ asset('full/assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/demo/pages/table_elements.js') }}"></script>
    <script src="{{ asset('assets/demo/pages/datatables_basic.js') }}"></script>
    <!-- /additional JS files -->

    <script src="{{ asset('assets/js/vendor/tables/datatables/datatables.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <!-- Include Moment.js -->
    <script src="{{ asset('assets/demo/pages/extra_sweetalert.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/script.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script>
        $(document).ready(function () {
            // Description toggle for blogs
            var blogTable = $('#blogs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('blogs.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'title', name: 'title' },
                    { data: 'author', name: 'author' },
                    {
                        data: 'description',
                        name: 'description',
                        render: function (data, type, full, meta) {
                            var shortDescription = data.length > 50 ? data.substring(0, 50) + '...' : data;
                            return '<span class="short-description">' + shortDescription + '</span>' +
                                '<span class="full-description" style="display:none;">' + data + '</span>' +
                                '<button class="btn btn-sm btn-link toggle-description">Show More</button>';
                        }
                    },
                    {
                        data: 'image',
                        name: 'image',
                        render: function (data, type, full, meta) {
                            return data ? '<img src="{{ asset('images') }}/' + data + '" class="img-fluid" style="height:80px; width:80px; object-fit:cover;" alt="' + full.title + '">' : 'No Image';
                        }
                    },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'asc']],
            });

            // Toggle full description
            $('#blogs-table tbody').on('click', '.toggle-description', function () {
                var tr = $(this).closest('tr');
                var row = blogTable.row(tr);
                var $description = tr.find('.full-description');
                var $shortDescription = tr.find('.short-description');

                $description.toggle();
                $shortDescription.toggle();

                $(this).text(function (i, text) {
                    return text === "Show More" ? "Show Less" : "Show More";
                });

                row.invalidate().draw(false);
            });



        });
    </script>

</body>

</html>