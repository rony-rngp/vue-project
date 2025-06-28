<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />


    <link rel="icon" type="image/x-icon" href="{{ asset('admin/assets') }}/img/favicon/favicon.ico" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin/assets') }}/vendor/fonts/boxicons.css" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets') }}/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('admin/assets') }}/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('admin/assets') }}/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('admin/assets') }}/vendor/libs/apex-charts/apex-charts.css" />

   <link rel="stylesheet" href="{{ asset('admin/assets') }}/vendor/css/pages/page-auth.css" />

    <!-- Page CSS -->
    <style>
        .border-bottom{
            border-bottom: 1px solid #E4E6E8;
        }
    </style>
    <!-- Helpers -->
    <script src="{{ asset('admin/assets') }}/vendor/js/helpers.js"></script>
    <script src="{{ asset('admin/assets') }}/js/config.js"></script>


    @vite('resources/js/app.js')
    @inertiaHead
    @routes
</head>
<body>
@inertia


<script src="{{ asset('admin/assets') }}/vendor/libs/jquery/jquery.js"></script>
<script src="{{ asset('admin/assets') }}/vendor/libs/popper/popper.js"></script>
<script src="{{ asset('admin/assets') }}/vendor/js/bootstrap.js"></script>
<script src="{{ asset('admin/assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ asset('admin/assets') }}/vendor/js/menu.js"></script>
<!-- Main JS -->
<script src="{{ asset('admin/assets') }}/js/main.js"></script>

</body>
</html>
