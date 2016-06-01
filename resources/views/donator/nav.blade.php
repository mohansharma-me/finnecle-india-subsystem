<li class="{{ Route::getCurrentRoute()->getName() == "channels" ? "active" : "" }}"><a href="{{route('channels')}}">Channels</a></li>
<li class="{{ Route::getCurrentRoute()->getName() == "draws" ? "active" : "" }}"><a href="{{route('draws')}}">Draws</a></li>
<li class="{{ Route::getCurrentRoute()->getName() == "centers" ? "active" : "" }}"><a href="{{route('centers')}}">Centers</a></li>
