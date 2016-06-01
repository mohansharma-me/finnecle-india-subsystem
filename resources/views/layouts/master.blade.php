<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Home') - {{env('PROJECT')}}</title>

    <link href="<?=URL::to('css/pace.css')?>" rel="stylesheet">
    <script src="<?=URL::to('js/pace.min.js')?>"></script>

    <!-- Bootstrap -->
    <link href="<?=URL::to('css/lumen.bootstrap.min.css')?>" rel="stylesheet">
    <link href="<?=URL::to('css/style.css')?>" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="<?=URL::to('js/html5shiv.min.js')?>"></script>
    <script src="<?=URL::to('js/respond.min.js')?>"></script>
    <![endif]-->

    @stack('head')
</head>
<body>

    @include('includes.header')

    <div class="container">
        @if(session('master_msg'))
            <div class="alert alert-{{ session('master_flag') ? 'success' : 'danger' }}">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{session('master_msg')}}
            </div>
        @endif
        @yield('content')
    </div>

    <script src="<?=URL::to('js/jquery.min.js')?>"></script>
    <script src="<?=URL::to('js/bootstrap.min.js')?>"></script>
    @stack('footer')
</body>
</html>