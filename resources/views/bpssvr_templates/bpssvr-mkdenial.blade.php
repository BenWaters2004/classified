<page>

	<div id="divPageBox">

		<div class="divLogo">
			@if (env('APP_ENV') == 'production')
				<img class="imgLogo" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/logoPDF_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.png" />
			@else
				<img class="imgLogo" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/logoPDF_demo.png" />
			@endif
		</div>

		@if (strlen($securitymatrixImagePath) > 0)
			<img style="width:670px;" src="{{$securitymatrixImagePath}}" />
		@else
			<p class="text-red" style="font-style: italic; text-align: center; font-size: 13px; margin-top:10px;">No MK Denial file linked to this user!</p>
		@endif
		
	</div>
	<page_footer>
		<div class="formReference"><strong>FORM</strong> PLY-SEC-FRM-002D REV. 2 [FEB-18]</div>
	</page_footer>
</page>
