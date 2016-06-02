<li class="{{ Route::getCurrentRoute()->getName() == "create-donation" ? "active" : "" }}"><a href="{{route('create-donation')}}">Create Donation Slip</a></li>
<li class="{{ Route::getCurrentRoute()->getName() == "donations" ? "active" : "" }}"><a href="{{route('donations')}}">Donations</a></li>
