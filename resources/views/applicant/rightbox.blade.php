<div class="box-body">
	<p class="strong">If you have any issues with this application please contact us.</p>

	@if (strlen(env('APP_COMPANY_EMAIL'))>0)<p>Email:<br />{!! env('APP_COMPANY_EMAIL') !!}</p>@endif 
	@if (strlen(env('APP_COMPANY_PHONE'))>0)<p>Phone:<br />{!! env('APP_COMPANY_PHONE') !!}</p>@endif 
	@if (strlen(env('APP_POST_ADDRESS'))>0)<p>Post:<br />{!! env('APP_POST_ADDRESS') !!}</p>@endif 
</div>