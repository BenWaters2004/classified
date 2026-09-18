
<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		<div class="divTitleText" style="width:120px;">
			General Information
		</div>
		<span style="color: #FF0000; font-size: 11px;">PLEASE USE CAPITAL LETTERS WHEN COMPLETING THIS FORM</span>
		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:0; font-size: 12px; ">
			<col width="80"><col width="150"><col width="60"><col width="150"><col width="30"><col width="60">
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
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;Female
						@if($DBSApplication->gender == 'female')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="6"><strong>Mother's Maiden Last Name:</strong> {{$BPSSApplication->mother_maiden_last_name}}</td>
				</tr>
				<tr>
					<td colspan="6"><strong>UK National Insurance Number:</strong> {{strtoupper($DBSApplication->supporting_nino)}}</td>
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
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;No
						@if(!isset($DBSApplication->otherNames) || count($DBSApplication->otherNames) ==0)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
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

		<div style="clear: both;width: 670px; height: 30px;">&nbsp;</div>
		<div class="divTitleText" style="width:200px; float: left;">
			Contractor Company Information
		</div>

		<p style="font: 9px 'Verdana'; font-size: 16px; font-weight: bold; line-height: 20px; margin-bottom: 5px;">For Facilities / Contractors:</p>
		<p style="font: 9px 'Verdana'; font-size: 12px;  line-height: 16px;  font-style: italic; margin-bottom: 5px;"><span style="font-weight: bold;">Note: Contractors</span> to complete this page.</p>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="font-size: 12px; margin-bottom:0px; margin-top: 5px;">
			<col width="180"><col width="450">
			<tbody>
				<tr>
					<td style="vertical-align: top;"><strong>Name of Company:</strong></td>
					<td>{{$BPSSApplication->contractor_name_of_company}}</td>
				</tr>
				<tr>
					<td style="vertical-align: top;"><strong>Address of Company:</strong></td>
					<td>{{$BPSSApplication->contractor_address_of_company}}</td>
				</tr>
				<tr>
					<td style="vertical-align: top;"><strong>Number of years with the company:</strong></td>
					<td>{{$BPSSApplication->contractor_number_of_years_with_company}}</td>
				</tr>
				<tr>
					<td style="vertical-align: top;"><strong>Name of Agency:</strong> <span class="text-red">(If applicable)</span></td>
					<td>{{$BPSSApplication->contractor_name_of_agency}}</td>
				</tr>
				<tr>
					<td style="vertical-align: top;"><strong>Address of Agency:</strong></td>
					<td>{{$BPSSApplication->contractor_address_of_agency}}</td>
				</tr>
				<tr>
					<td style="vertical-align: top;"><strong>Agency Contact:</strong></td>
					<td>{{$BPSSApplication->contractor_agency_contact}}</td>
				</tr>
				<tr>
					<td style="vertical-align: top;"><strong>Contact:</strong></td>
					<td>{{$BPSSApplication->contractor_utas_contact}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</page>
