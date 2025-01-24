<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="description" content="Thinka">
    <meta name="author" content="Thinka">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="keywords" content="Thinka">
   
    <title>Thinka - @yield('title')</title>
  
    @include('admin.includes.head-css')

    @yield('styling')
</head>

<body>
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
        @include('admin.includes.sidebar')
        <!-- wrap @s -->
        <div class="nk-wrap ">
        @include('admin.includes.mainheader')
        <!-- Main Content-->
        @yield('content')

        <!-- End Page -->
        @include('admin.includes.footer')
    </div>
     <!-- wrap @e -->
</div>
    @include('admin.includes.modal')
</div>

    @include('admin.includes.footer-script')
    @yield('script')
</body>

</html>
