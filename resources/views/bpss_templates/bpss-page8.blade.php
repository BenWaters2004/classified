 <page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		
		<div class="divTitleText" style="width:175px; float: left;">
			Part 5: Declaration
		</div>

		<p style="font: 9px 'Verdana'; font-size: 12px; line-height: 16px;font-style: italic;"><span style=" font-weight: bold; ">I declare that the information I have given on this form is true and complete to the best of my knowledge and belief.</span> <span class="text-red">I understand that any false information or deliberate omission in the information I have given on this form may disqualify me for employment in connection with Government contracts.</span></p>

		<p style="font: 9px 'Verdana'; font-size: 12px; line-height: 16px;">I give permission to {{ env('APP_COMPANY_NAME') }} to confirm factual information from previous/current employer(s), covering the last <span style=" font-weight: bold; font-style: italic;">5 (permanent employee) / 3 (contractor/temporary worker)</span> years of my employment, that I have disclosed in <span class="text-red" style=" font-weight: bold; font-style: italic;">Part 3</span> of this form<!-- , including a ‘Police Act Disclosure’ as mentioned in <span class="text-red" style=" font-weight: bold; font-style: italic;">Part 4</span>. <span style=" font-weight: bold; font-style: italic;">I undertake to notify any material changes in the information I have given in PART 2 to the HR or Security Branch concerned</span> --></p>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="font-size: 12px; margin-bottom:0px; ">
			<col width="150"><col width="90"><col width="360">
			<tbody>
				<tr>
					<td><strong>Is Consent Provided:</strong></td>
					<td>
						@if(isset($BPSSApplication->is_consent_provided) && $BPSSApplication->is_consent_provided)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" /> YES / <img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" /> No
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" /> YES / <img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" /> No
						@endif
					</td>
					<td><strong>Consented Responsible Body Email:</strong> <a href="mailto:{{$BPSSApplication->consent_responsible_body_email}}">{{$BPSSApplication->consent_responsible_body_email}}</a></td>
				</tr>
				<tr>
					<td><strong>Applicant Signature:</strong></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td><strong>Date Signed:</strong></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td><strong>Purpose of Check</strong></td>
					<td colspan="2">{{$BPSSApplication->purpose_of_check}}</td>
				</tr>
			</tbody>
		</table>

		<p style="font: 9px 'Verdana'; font-size: 16px; font-weight: bold; line-height: 20px;  font-style: italic;">ADDITIONAL SECURITY REQUIREMENTS</p>
		<p style="font: 9px 'Verdana'; font-size: 13px;line-height: 16px;  font-style: italic;">Have you held or currently hold a higher level security clearance through your current employment or a previous employer within the last 12 months, if so please give details below:</p>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="font-size: 12px; margin-bottom:0px; ">
			<col width="95"><col width="435"><col width="73">
			<tbody>
				<tr>
					<td style="font-style: italic;"><strong>Clearance Level</strong></td>
					<td style="font-style: italic; padding-right: 2px;"><strong>Where was it issued? Please give details of the company name and address</strong></td>
					<td style="font-style: italic;"><strong>Date Issued</strong></td>
				</tr>
				<tr>
					<td>
						@if(isset($BPSSApplication->clearance_level_sc) && $BPSSApplication->clearance_level_sc)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif SC&nbsp;&nbsp;&nbsp;&nbsp;
						@if(isset($BPSSApplication->clearance_level_dv) && $BPSSApplication->clearance_level_dv)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif DV
					</td>
					<td>{{$BPSSApplication->clearance_level_details}}</td>
					<td>
						@if(isset($BPSSApplication->date_issued) && strlen($BPSSApplication->date_issued) == 10){{\Carbon\Carbon::parse($BPSSApplication->date_issued)->format('d/m/Y')}}@endif
					</td>
				</tr>
			</tbody>
		</table>
		<div style="clear: both;width: 670px; height: 30px;">&nbsp;</div>
		<div class="divTitleText" style="width:275px; float: left;">
			Appendix 1: Contractor Company Information:
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
