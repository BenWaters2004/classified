
<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		
		<div class="divTitleText" style="width:210px; float: left;">
			 	Address History - Current Address
		</div>
		<div style="width:500px; font:bold 19px 'Times New Roman'; color: #FF0000; float:left;">Please provide addresses to cover the last 5 years</div>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:5px; font-size: 12px; margin-bottom:12px; ">
			<col width="180"><col width="210"><col width="210">
			<tbody>
				<tr>
					<td rowspan="2"><strong>Current Addres [Number, Street]:</strong></td>
					<td colspan="2">{{$DBSApplication->address_line_1}}</td>
				</tr>
				<tr>
					<td colspan="2">{{$DBSApplication->address_line_2}}</td>
				</tr>
				<tr>
					<td><strong>Post Town:</strong> {{$DBSApplication->address_town}}</td>
					<td><strong>Postcode:</strong> {{$DBSApplication->address_postcode}}</td>
					<td><strong>County:</strong> {{$DBSApplication->address_county}}</td>
				</tr>
				<tr>
					<td><strong>Country:</strong> {{$DBSApplication->address_country_fullName}}</td>
					<td colspan="2"><strong>Resident From:</strong> {{\Carbon\Carbon::parse($DBSApplication->current_address_from)->format('d/m/Y')}}</td>
				</tr>
			</tbody>
		</table>

	</div>
</page>
