<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-46601315-3"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-46601315-3');
    </script>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top" style="background-color: #155c8c">
        <div class="container">
            <div class="row">
                <br>
                <?php if (!strpos($_SERVER['HTTP_HOST'], 'asoprovida')) { ?>     
                <p align="center"><a href="<?php echo ENV('PATH_PUBLIC') ?>"><img src="<?php echo env('PATH_PUBLIC')?>img/sol-de-acuario-chico-isologo.png"></a></p>
                <?php } ?>
            </div>
        </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
