<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @vite('default')
    <title>index.CMS</title>
</head>
<body class="bg-gray-100">
<div class="flex flex-col h-screen justify-center items-center">
    <div class="flex items-center justify-center mb-10">
        <span class="self-center text-xl font-semibold whitespace-nowrap">index.CMS</span>
    </div>
    <div class="w-1/2">
        @yield('content')
    </div>
</div>

</body>
</html>
