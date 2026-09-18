
<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		
		<div class="divTitleText" style="width:250px; float: left;">
			 	Part 2: Nationality and Immigration Status
		</div>
		<p style="font: 9px 'Verdana'; font-size: 10px;"><strong>Note:</strong>  Please answer the following questions honestly. If you are appointed, documentary evidence will be sought to confirm your answers. Your answers may additionally be checked against UK Immigration and Nationality records.</p>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="font-size: 12px; margin-bottom:0px; ">
			<col width="86"><col width="200"><col width="86"><col width="200">
			<tbody>
				<tr>
					<td><strong>Town of Birth:</strong></td>
					<td>{{$DBSApplication->birth_town}}</td>
					<td><strong>Country of Birth:</strong></td>
					<td>{{$DBSApplication->birth_country_fullName}}</td>
				</tr>
				<tr>
					<td><strong>Nationality at Birth:</strong></td>
					<td>{{$DBSApplication->birth_nationality}}</td>
					<td><strong>Present Nationality:</strong> (if different)</td>
					<td>{{$BPSSApplication->present_nationality}}</td>
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
			<col width="510"><col width="110">
			<tbody>
				<tr>
					<td><strong>Do you have a National Identity Card</strong></td>
					<td>
						Yes
						@if(isset($BPSSApplication->national_identity_card) && $BPSSApplication->national_identity_card)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;No
						@if(!isset($BPSSApplication->national_identity_card) || !$BPSSApplication->national_identity_card)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
					</td>
				</tr>
				<tr>
					<td><strong>Do you have a Passport <span style="color: #FF0000">if multiple country passports list all</span></strong></td>
					<td>
						@if(isset($DBSApplication->supporting_passport) && strlen($DBSApplication->supporting_passport) > 0 && strlen($DBSApplication->supporting_passport_country) > 0)
            				Yes <img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />&nbsp;&nbsp;&nbsp;No <img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
            			@else
							Yes
							@if(isset($BPSSApplication->passport) && $BPSSApplication->passport)
								<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
							@else 
								<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
							@endif
							&nbsp;&nbsp;&nbsp;No
							@if(!isset($BPSSApplication->passport) || !$BPSSApplication->passport)
								<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
							@else 
								<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
							@endif
						@endif
					</td>
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px;">
			<col width="150"><col width="210"><col width="60"><col width="152">
			<tbody>

				@if(isset($DBSApplication->supporting_passport) && strlen($DBSApplication->supporting_passport) > 0 && strlen($DBSApplication->supporting_passport_country) > 0)
					<tr>
						<td><strong>Passport No.</strong></td>
						<td>@if(isset($DBSApplication->supporting_passport) && strlen($DBSApplication->supporting_passport)>0){{$DBSApplication->supporting_passport}}@else &nbsp; @endif</td>
						<td style="line-height: 12px;"><strong>Country of Issue:</strong></td>
						<td>{{$DBSApplication->supporting_passport_country_fullName}}</td>
					</tr>
				@endif
				@if(isset($BPSSApplication->passports) && count($BPSSApplication->passports) >0)
					@foreach ($BPSSApplication->passports as $key => $passport)
						<tr>
							<td><strong>Passport No.</strong></td>
							<td>@if(isset($passport->passport_number) && strlen($passport->passport_number)>0){{$passport->passport_number}}@else &nbsp; @endif</td>
							<td style="line-height: 12px;"><strong>Country of Issue:</strong></td>
							<td>{{$passport->passport_country_fullName}}</td>
						</tr>
					@endforeach
				@endif
				<tr>
					<td><strong>National Identity Card No:</strong></td>
					<td>{{$BPSSApplication->national_identity_card_number}}</td>
					<td style="line-height: 12px;"><strong>Country of Issue:</strong></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><strong>UK Driving Licence No:</strong></td>
					<td>@if(isset($DBSApplication->supporting_dln) && strlen($DBSApplication->supporting_dln)>0){{$DBSApplication->supporting_dln}}@else &nbsp; @endif</td>
					<td style="line-height: 12px;"><strong>Country of Issue:</strong></td>
					<td>@if(isset($DBSApplication->supporting_dln) && strlen($DBSApplication->supporting_dln)>0) United Kingdom @else &nbsp; @endif</td>
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px; ">
			<col width="510"><col width="110">
			<tbody>
				<tr>
					<td><strong>Do you hold dual citizenship?</strong></td>
					<td>
						Yes
						@if(isset($BPSSApplication->dual_citizenship) && $BPSSApplication->dual_citizenship)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;No
						@if(!isset($BPSSApplication->dual_citizenship) || !$BPSSApplication->dual_citizenship)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="2" style="line-height: 14px;">
						<strong>Please give details of dual citizenship/nationality status:</strong>
						@if(isset($BPSSApplication->dual_citizenship_details) && strlen($BPSSApplication->dual_citizenship_details) > 0) <br />{{$BPSSApplication->dual_citizenship_details}}@endif
					</td>
					
				</tr>
				<tr>
					<td><strong>Have you ever possessed any other nationality or citizenship?</strong></td>
					<td>
						Yes
						@if(isset($BPSSApplication->former_nationality) && $BPSSApplication->former_nationality)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;No
						@if(!isset($BPSSApplication->former_nationality) || !$BPSSApplication->former_nationality)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="2" style="line-height: 14px;">
						<strong>Former Nationality:</strong>
						@if(isset($BPSSApplication->former_nationality_details) && strlen($BPSSApplication->former_nationality_details) > 0){{$BPSSApplication->former_nationality_details}}@endif
					</td>
					
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px;">
			<col width="160"><col width="60"><col width="210"><col width="40"><col width="78">
			<tbody>
				<tr>
					<td style="line-height: 14px; vertical-align: middle;"><strong>If British naturalised please provide Naturalisation Certificate:</strong></td>
					<td style="line-height: 14px; vertical-align: middle;"><strong>Number:</strong></td>
					<td style="line-height: 14px; vertical-align: middle;">{{$BPSSApplication->naturalisation_certificate_number}}</td>
					<td style="line-height: 14px; vertical-align: middle;"><strong>Date:</strong></td>
					<td style="line-height: 14px; vertical-align: middle;">
						@if(isset($BPSSApplication->naturalisation_certificate_date) && strlen($BPSSApplication->naturalisation_certificate_date) == 10){{\Carbon\Carbon::parse($BPSSApplication->naturalisation_certificate_date)->format('d/m/Y')}}@endif
					</td>
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px; ">
			<col width="510"><col width="110">
			<tbody>
				<tr>
					<td><strong>Are you subject to immigration control?</strong></td>
					<td>
						Yes
						@if(isset($BPSSApplication->subject_to_immigration_control) && $BPSSApplication->subject_to_immigration_control)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;No
						@if(!isset($BPSSApplication->subject_to_immigration_control) || !$BPSSApplication->subject_to_immigration_control)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
					</td>
				</tr>
				
			</tbody>
		</table>
		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px;">
			<col width="120"><col width="500">
			<tbody>
				<tr>
					<td style="vertical-align: middle;">
						<strong>If YES please specify:</strong>
					</td>
					<td style="vertical-align: middle; line-height: 14px;">{{$BPSSApplication->subject_to_immigration_control_details}}</td>
					
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px; ">
			<col width="510"><col width="110">
			<tbody>
				<tr>
					<td><strong>Are you lawfully resident in the UK?</strong></td>
					<td>
						Yes
						@if(isset($BPSSApplication->lawfully_resident_in_uk) && $BPSSApplication->lawfully_resident_in_uk)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;No
						@if(!isset($BPSSApplication->lawfully_resident_in_uk) || !$BPSSApplication->lawfully_resident_in_uk)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
					</td>
				</tr>
				
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px; ">
			<col width="510"><col width="110">
			<tbody>
				<tr>
					<td><strong>Are there any restrictions on your continued residence in the UK?</strong></td>
					<td>
						Yes
						@if(isset($BPSSApplication->continued_residence_restrictions) && $BPSSApplication->continued_residence_restrictions)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;No
						@if(!isset($BPSSApplication->continued_residence_restrictions) || !$BPSSApplication->continued_residence_restrictions)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
					</td>
				</tr>
				
			</tbody>
		</table>
		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px;">
			<col width="120"><col width="500">
			<tbody>
				<tr>
					<td style="vertical-align: middle;">
						<strong>If YES please specify:</strong>
					</td>
					<td style="vertical-align: middle; line-height: 14px;">{{$BPSSApplication->continued_residence_restrictions_details}}</td>
					
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px; ">
			<col width="510"><col width="110">
			<tbody>
				<tr>
					<td><strong>Are there any restrictions on your continued freedom to take employment in the UK?</strong></td>
					<td>
						Yes
						@if(isset($BPSSApplication->freedom_to_take_employment) && $BPSSApplication->freedom_to_take_employment)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;No
						@if(!isset($BPSSApplication->freedom_to_take_employment) || !$BPSSApplication->freedom_to_take_employment)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
					</td>
				</tr>
				
			</tbody>
		</table>
		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px;">
			<col width="120"><col width="500">
			<tbody>
				<tr>
					<td style="vertical-align: middle;">
						<strong>If YES please specify:</strong>
					</td>
					<td style="vertical-align: middle; line-height: 14px;">{{$BPSSApplication->freedom_to_take_employment_details}}</td>
					
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px;">
			<col width="340"><col width="280">
			<tbody>
				<tr>
					<td style="vertical-align: middle;">
						<strong>If applicable, please state your Home Office / Port reference number:</strong>
					</td>
					<td style="vertical-align: top; line-height: 14px;">{{$BPSSApplication->ho_port_reference_number}}</td>
					
				</tr>
			</tbody>
		</table>


	</div>
</page>
