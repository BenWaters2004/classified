<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		
		<div class="divTitleText" style="width:175px; float: left;">
			 	Personal / Character Referee
		</div>
		

		@if(isset($reference1->id) && $reference1->id >0)
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px; ">
				<col width="118"><col width="240"><col width="240">
				<tbody>
					<tr>
						<td><strong>Personal Referee:</strong></td>
						<td><strong>Period Known From:</strong> @if(isset($reference1->date_from) && strlen($reference1->date_from) == 10){{\Carbon\Carbon::parse($reference1->date_from)->format('d/m/Y')}}@endif</td>
						<td><strong>Period Known To:</strong> @if(isset($reference1->date_to) && strlen($reference1->date_to) == 10){{\Carbon\Carbon::parse($reference1->date_to)->format('d/m/Y')}}@endif</td>
					</tr>
					<tr>
						<td colspan="3"><strong>Name: </strong> {{$reference1->referee_name}}</td>
					</tr>
					<tr>
						<td colspan="3"><strong>Address: </strong> {{$reference1->referee_address_line}}</td>
					</tr>
				</tbody>
			</table>
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
				<col width="311"><col width="311">
				<tbody>
					<tr>
						<td>
							<strong>Town:</strong> {{$reference1->referee_address_town}}<br />
						</td>
						<td>
							<strong>Postcode:</strong> {{$reference1->referee_address_postcode}}<br />
						</td>
					</tr>
					<tr>
						<td>
							<strong>Email:</strong> {{$reference1->referee_email}}<br />
						</td>
						<td>
							<strong>Contact Number:</strong> {{$reference1->referee_contact_number}}<br />
						</td>
					</tr>
				</tbody>
			</table>
		@else
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px; ">
				<col width="118"><col width="240"><col width="240">
				<tbody>
					<tr>
						<td><strong>Personal Referee:</strong></td>
						<td><strong>Period Known From:</strong>&nbsp;</td>
						<td><strong>Period Known To:</strong>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3"><strong>Name: </strong>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3"><strong>Address: </strong>&nbsp;</td>
					</tr>
				</tbody>
			</table>
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
				<col width="311"><col width="311">
				<tbody>
					<tr>
						<td>
							<strong>Town:</strong>&nbsp;<br />
						</td>
						<td>
							<strong>Postcode:</strong>&nbsp;<br />
						</td>
					</tr>
					<tr>
						<td>
							<strong>Email:</strong>&nbsp;<br />
						</td>
						<td>
							<strong>Contact Number:</strong>&nbsp;<br />
						</td>
					</tr>
				</tbody>
			</table>
		@endif

		@if(isset($reference2->id) && $reference2->id >0)
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
				<col width="118"><col width="240"><col width="240">
				<tbody>
					<tr>
						<td><strong>Personal Referee:</strong></td>
						<td><strong>Period Known From:</strong> @if(isset($reference2->date_from) && strlen($reference2->date_from) == 10){{\Carbon\Carbon::parse($reference2->date_from)->format('d/m/Y')}}@endif</td>
						<td><strong>Period Known To:</strong> @if(isset($reference2->date_to) && strlen($reference2->date_to) == 10){{\Carbon\Carbon::parse($reference2->date_to)->format('d/m/Y')}}@endif</td>
					</tr>
					<tr>
						<td colspan="3"><strong>Name: </strong> {{$reference2->referee_name}}</td>
					</tr>
					<tr>
						<td colspan="3"><strong>Address: </strong> {{$reference2->referee_address_line}}</td>
					</tr>
				</tbody>
			</table>
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
				<col width="311"><col width="311">
				<tbody>
					<tr>
						<td>
							<strong>Town:</strong> {{$reference2->referee_address_town}}<br />
						</td>
						<td>
							<strong>Postcode:</strong> {{$reference2->referee_address_postcode}}<br />
						</td>
					</tr>
					<tr>
						<td>
							<strong>Email:</strong> {{$reference2->referee_email}}<br />
						</td>
						<td>
							<strong>Contact Number:</strong> {{$reference2->referee_contact_number}}<br />
						</td>
					</tr>
				</tbody>
			</table>
		@else
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
				<col width="118"><col width="240"><col width="240">
				<tbody>
					<tr>
						<td><strong>Personal Referee:</strong></td>
						<td><strong>Period Known From:</strong>&nbsp;</td>
						<td><strong>Period Known To:</strong>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3"><strong>Name: </strong>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3"><strong>Address: </strong>&nbsp;</td>
					</tr>
				</tbody>
			</table>
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
				<col width="311"><col width="311">
				<tbody>
					<tr>
						<td>
							<strong>Town:</strong>&nbsp;<br />
						</td>
						<td>
							<strong>Postcode:</strong>&nbsp;<br />
						</td>
					</tr>
					<tr>
						<td>
							<strong>Email:</strong>&nbsp;<br />
						</td>
						<td>
							<strong>Contact Number:</strong>&nbsp;<br />
						</td>
					</tr>
				</tbody>
			</table>
		@endif

		<p style="font: 9px 'Verdana'; font-size: 13px; font-weight: bold; line-height: 16px;">*Please ensure you provide an email address for each Personal Referee. And that the individual is not related to you, the person that you have nominated must have known you well for 5 or more years.</p>

		<div class="divTitleText" style="width:240px; float: left;">
			Part 4: Police Act Disclosure Application
		</div>
		<p style="font: 9px 'Verdana'; font-size: 13px; line-height: 16px;"><span style=" font-weight: bold; font-style: italic;">The fourth element of the BPSS process is to externally check whether you have any unspent convictions that may prevent you working on sensitive programmes. {{ env('APP_COMPANY_NAME') }}</span> will submit an application to either the Disclosure & Barring Service (for England & Wales) or Disclosure Scotland (for Scotland) for a basic disclosure. A basic disclosure certificate will contain information about any unspent convictions held in your name or confirm that no such unspent convictions exist.</p>
		<p class="text-red" style="font: 9px 'Verdana'; font-size: 13px; font-weight: bold; line-height: 16px;">By signing the declaration in section 5 you are consenting to some or all of these statements.</p>
		
		<div style="width: 640px; padding-left: 30px; line-height: 16px; margin-bottom: 10px;">
			@if(isset($BPSSApplication->consent_responsible_body_submitting_dbs_application) && $BPSSApplication->consent_responsible_body_submitting_dbs_application)
				<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
			@else 
				<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
			@endif The Responsible Body will be submitting an application for a basic disclosure certificate on my behalf.
		</div>
		<div style="width: 640px; padding-left: 30px; line-height: 16px; margin-bottom: 10px;">
			@if(isset($BPSSApplication->electronic_notification_state) && $BPSSApplication->electronic_notification_state)
				<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
			@else 
				<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
			@endif The electronic notification will state either the "Certificate contains no information" or "Please wait to view the applicants certificate".
		</div>
		<div style="width: 640px; padding-left: 30px; line-height: 16px;">
			@if(isset($BPSSApplication->read_and_understood_dbs_Statement) && $BPSSApplication->read_and_understood_dbs_Statement)
				<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
			@else 
				<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
			@endif You have read and understood the DBS statement of fair processing, <a href="https://www.gov.uk/government/publications/dbs-privacy-policies-for-basic-checks" target="_blank">Privacy Policy for Applicants. UK Gov DBS Privacy Policies for Basic Checks</a>.
		</div>

		<p style="font: 9px 'Verdana'; font-size: 13px; line-height: 16px; margin-bottom: 10px;">I confirm that <span style=" font-weight: bold; font-style: italic;">{{ env('APP_COMPANY_NAME') }}</span> has confirmed that if either of the services they use to obtain my basic disclosure certificate contains information that a recruitment decision about me, will not be made until the content of that certificate is considered by <span style=" font-weight: bold; font-style: italic;">{{ env('APP_COMPANY_NAME') }}</span>. <span class="text-red" style=" font-weight: bold; font-style: italic;">The Responsible Body will only keep information regarding your application for clearance during the clearance process. All documentation will be securely destroyed as per the RO privacy policy (available direct from the Security Dept.).</span></p>

		<p style="font: 9px 'Verdana'; font-size: 13px; font-weight: bold; line-height: 16px;">The minimum requirement for obtaining a Basic Disclosure is over one month’s residency in the UK. If you have spent more than six months overseas in the last three years you will need to obtain an overseas criminal record certificate from the country or countries you were residing in.</p>

		<p style="font: 9px 'Verdana'; font-size: 13px; line-height: 16px;">Contact the Security Services who will be able to provide you with guidelines and advice on how to obtain an overseas criminal record certificate.</p>

	</div>
</page>
