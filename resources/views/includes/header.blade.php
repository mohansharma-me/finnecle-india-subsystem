<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{route('index')}}">{{env('PROJECT')}}</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="{{ Route::getCurrentRoute()->getName() == "index" || Route::getCurrentRoute()->getName() == "dashboard" ? "active" : ""  }}"><a href="{{route('index')}}">{{Auth::check() ? 'Dashboard': 'Home'}}</a></li>
                @if(Auth::check() && view()->exists(Auth::user()->getRole('.nav')))
                    @include(Auth::user()->getRole('.nav'))
                @endif
            </ul>
            <ul class="nav navbar-nav navbar-right">
                @if(Auth::check())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">My Account <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Change Password</a></li>
                            <li><a href="#">Change E-mail</a></li>
                            <li class="divider"></li>
                            <li><a href="{{route('logout')}}">Logout</a></li>
                        </ul>
                    </li>
                @else
                    <li class="{{ Route::getCurrentRoute()->getName() == "forgot-password" ? "active" : "" }}"><a href="{{route('forgot-password')}}">Forgot Password ?</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>