<li class="{{ Route::getCurrentRoute()->getName() == "channels" ? "active" : "" }}"><a href="{{route('channels')}}">Channels</a></li>
<li class="{{ Route::getCurrentRoute()->getName() == "draws" ? "active" : "" }}"><a href="{{route('draws')}}">Draws</a></li>
<li class="{{ Route::getCurrentRoute()->getName() == "centers" ? "active" : "" }}"><a href="{{route('centers')}}">Centers</a></li>
<li class="{{ Route::getCurrentRoute()->getName() == "declare-ngo" ? "active" : "" }}"><a href="{{route('declare-ngo')}}">Declare NGO</a></li>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Reports <span class="caret"></span></a>
	<ul class="dropdown-menu" role="menu">
		<li><a href="{{route('reports-declarations')}}">Declarations</a></li>
		<li><a href="{{route('reports-paid-transactions')}}">Paid Transactions</a></li>
		<li><a href="{{route('reports-donations')}}">Donations</a></li>
	</ul>
</li>
<li class="{{ Route::getCurrentRoute()->getName() == "general-settings" ? "active" : "" }}"><a href="{{route('general-settings')}}">Settings</a></li>
