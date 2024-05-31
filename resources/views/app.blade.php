<?php
$tPath = app()->environment('local') ? '' : '/public';
?>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <title>TOkoKU</title>
        <link rel="icon" type="image/png" href="{{ asset($tPath.'img/icon/shop.png') }}" />
        @vite('resources/css/app.css')
        @inertiaHead
    </head>
    <body class="select-none h-screen">
        <script>
            window.env = {
                baseUrl: "{{ env('VUE_APP_BASE_URL') }}"
            };
            </script>
        @inertia
        @vite('resources/js/app.js')
    </body>
</html>