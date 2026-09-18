
<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		
		<div class="divTitleText" style="width:280px; float: left;">
			 	Address History Continues- Previous Address
		</div>

		@if(isset($DBSApplication->previousAddresses) && count($DBSApplication->previousAddresses) >1)
			@foreach ($DBSApplication->previousAddresses as $key => $previousAddress)
				@if($key != 0)
					<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
						<col width="180"><col width="210"><col width="210">
						<tbody>
							<tr>
								<td rowspan="2"><strong>Previous Address [Number, Street]:</strong></td>
								<td colspan="2">{{$previousAddress->previous_address_line_1}}</td>
							</tr>
							<tr>
								<td colspan="2">{{$previousAddress->previous_address_line_2}}</td>
							</tr>
							<tr>
								<td><strong>Post Town:</strong> {{$previousAddress->previous_address_town}}</td>
								<td><strong>Postcode:</strong> {{$previousAddress->previous_address_postcode}}</td>
								<td><strong>County:</strong> {{$previousAddress->previous_address_county}}</td>
							</tr>
							<tr>
								<td><strong>Country:</strong> {{$previousAddress->previous_address_country_fullName}}</td>
								<td colspan="2"><strong>Resident From:</strong> {{\Carbon\Carbon::parse($previousAddress->previous_address_from)->format('d/m/Y')}}</td>
							</tr>
						</tbody>
					</table>
				@endif
			@endforeach
		
		@endif

	</div>
</page>
