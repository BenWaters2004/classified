
<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		<div class="divTitleText" style="width:170px;">
			 	Part 1: General Information
		</div>
		<!-- <span style="color: #FF0000; font-size: 11px;">PLEASE USE CAPITAL LETTERS WHEN COMPLETING THIS FORM</span> -->
		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:0; font-size: 12px; ">
			<col width="60"><col width="150"><col width="60"><col width="150"><col width="40"><col width="70">
			<tbody>
				<tr>
					<td rowspan="2"><strong>Present Surname:</strong></td>
					<td rowspan="2">{{$DBSApplication->presentSurname}}</td>
					<td><strong>Forename:</strong></td>
					<td>{{$DBSApplication->forename}}</td>
					<td rowspan="2"><strong>Title:</strong></td>
					<td rowspan="2">{{$DBSApplication->userTitle}}</td>
				</tr>
				<tr>
					<td><strong>Middle name[s]:</strong></td>
					<td>@if(strlen($DBSApplication->middlename)>0){{$DBSApplication->middlename}}@else &nbsp; @endif</td>
				</tr>

				<tr>
					<td><strong>Date of Birth:</strong></td>
					<td>{{\Carbon\Carbon::parse($DBSApplication->dob)->format('d/m/Y')}}</td>
					<td colspan="4">
						<strong>Gender:</strong>
						Male
						@if($DBSApplication->gender == 'male')
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;Female
						@if($DBSApplication->gender == 'female')
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
					</td>
				</tr>
				<!-- <tr>
					<td colspan="6"><strong>Mother's Maiden Last Name:</strong> {{$BPSSApplication->mother_maiden_last_name}}</td>
				</tr> -->
				<tr>
					<td colspan="6"><strong>UK National Insurance Number:</strong> {{$DBSApplication->supporting_nino}}</td>
				</tr>

			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="font-size: 12px; ">
			<col width="500"><col width="126">
			<tbody>
				<tr>
					<td><strong>Are you, have you ever been, or were you at birth known by a different name?</strong></td>
					<td>
						Yes
						@if(isset($DBSApplication->otherNames) && count($DBSApplication->otherNames) >0)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;No
						@if(!isset($DBSApplication->otherNames) || count($DBSApplication->otherNames) ==0)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
					</td>
				</tr>
			</tbody>
		</table>

		@if(isset($DBSApplication->otherNames) && count($DBSApplication->otherNames) >0)
			@foreach ($DBSApplication->otherNames as $key => $otherName)
				<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:20px; ">
					<col width="144"><col width="145"><col width="144"><col width="145">
					<tbody>
						<tr>
							<td><strong>Previous Surname:</strong></td>
							<td colspan="3">{{$otherName->other_surname}}</td>
						</tr>
						<tr>
							<td><strong>Previous Forename:</strong></td>
							<td colspan="3">{{$otherName->other_forename}}</td>
						</tr>
						<tr>
							<td><strong>Previous Middle name[s]:</strong></td>
							<td colspan="3">{{$otherName->other_middlename}}</td>
						</tr>
						<tr>
							<td><strong>Used From:</strong></td>
							<td>{{\Carbon\Carbon::parse($otherName->dateFrom)->format('d/m/Y')}}</td>
							<td><strong>Used To:</strong></td>
							<td>{{\Carbon\Carbon::parse($otherName->dateTo)->format('d/m/Y')}}</td>
						</tr>
					</tbody>
				</table>
			@endforeach
		@else
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:12px; ">
				<col width="144"><col width="145"><col width="144"><col width="145">
				<tbody>
					<tr>
						<td><strong>Previous Surname:</strong></td>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td><strong>Previous Forename:</strong></td>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td><strong>Previous Middle name[s]:</strong></td>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td><strong>Used From:</strong></td>
						<td>&nbsp;</td>
						<td><strong>Used To:</strong></td>
						<td>&nbsp;</td>
					</tr>

				</tbody>
			</table>
		@endif


		<div class="divTitleText" style="width:200px; float: left;">
			 	Address History- Current Address
		</div>
		<!-- <div style="width:100px; font:bold 19px 'Times New Roman'; color: #FF0000; float:left;">Please provide addresses to cover the last 5 years</div> -->

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:12px; ">
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


		<div class="divTitleText" style="width:210px; float: left;">
			 	Address History- Previous Address
		</div>

		@if(isset($DBSApplication->previousAddresses) && count($DBSApplication->previousAddresses) >0)
			@foreach ($DBSApplication->previousAddresses as $key => $previousAddress)
				@if($key == 0)
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
