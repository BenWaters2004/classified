
<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		<div class="divTitleText" style="width:215px; float: left;">
			 	Address History - Previous Address
		</div>

		@if(isset($DBSApplication->previousAddresses) && count($DBSApplication->previousAddresses) >0)
			@foreach ($DBSApplication->previousAddresses as $key => $previousAddress)
					<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
						<col width="180"><col width="210"><col width="210">
						<tbody>
							<tr>
								<td rowspan="2"><strong>Previous Address [Number, Street]:</strong></td>
								<td colspan="2">{{$previousAddress->previous_address_line_1}}&nbsp;</td>
							</tr>
							<tr>
								<td colspan="2">{{$previousAddress->previous_address_line_2}}&nbsp;</td>
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
				@endforeach
		@else
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
				<col width="180"><col width="210"><col width="210">
				<tbody>
					<tr>
						<td rowspan="2"><strong>Previous Address [Number, Street]:</strong></td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td><strong>Post Town:</strong>&nbsp;</td>
						<td><strong>Postcode:</strong>&nbsp;</td>
						<td><strong>County:</strong>&nbsp;</td>
					</tr>
					<tr>
						<td><strong>Country:</strong> &nbsp;</td>
						<td colspan="2"><strong>Resident From:</strong> &nbsp;</td>
					</tr>
				</tbody>
			</table>
		@endif
	</div>
</page>
