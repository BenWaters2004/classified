<page>

	<div id="divPageBox">

		<div class="divLogo">
			@if (env('APP_ENV') == 'production')
				<img class="imgLogo" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/logoPDF_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.png" />
			@else
				<img class="imgLogo" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/logoPDF_demo.png" />
			@endif
		</div>

		<table class="tableBlock" cellspacing="0" cellpadding="0">
			<col width="655">
			<tbody>
				<tr>
					<td class="text-red" style="font-weight: bold; text-align: center;">
						<span style="font-size: 28px;">APPROVAL FOR ACCESS</span><br />
						<span style="font-size: 15px; margin-top:20px;">(To be completed by the Site Security Controller)</span>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="text-red" style="font-style: italic; text-align: center; font-size: 13px; margin-top:10px;">I certify that in accordance with the requirements of Baseline Security Personnel Standard that I, the undersigned have personally examined the documents provided as evidence and am satisfied meets the BPSS requirements as specified by the cabinet office.</p>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="border: none;">
			<col width="325"><col width="325">
			<tbody>
				<tr style="border: none;">
					<td style="font-weight: bold; text-align: center; font-size: 36px; border: none;">
						Approved 
						@if(isset($BPSSVR->status) && $BPSSVR->status == 'approved')
							<span style="margin: 15px 0 0 20px;"><img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" style="width:32px; height: 32px;" /></span>
						@else
							<span style="margin: 15px 0 0 20px;"><img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png"" style="width:32px; height: 32px;" /></span>
						@endif
					</td>
					<td style="font-weight: bold; text-align: center; font-size: 36px; border: none;">
						Denied 
						@if(isset($BPSSVR->status) && $BPSSVR->status == 'denied')
							<span style="margin: 15px 0 0 20px;"><img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" style="width:32px; height: 32px;" /></span>
						@else
							<span style="margin: 15px 0 0 20px;"><img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" style="width:32px; height: 32px;" /></span>
						@endif
					</td>
				</tr>
			</tbody>
		</table>

		


		<table class="tableBlock" cellspacing="0" cellpadding="0">
			<col width="140"><col width="180"><col width="65"><col width="200">
			<tbody>
				<tr>
					<td style="background-color: #CCC;"><strong>Name:</strong></td>
					<td>@if(strlen($BPSSVR->admin_approval_name)>0){{$BPSSVR->admin_approval_name}}@else{{' '}}@endif</td>
					<td style="background-color: #CCC;"><strong>Title:</strong></td>
					<td>@if(strlen($BPSSVR->admin_approval_title)>0){{$BPSSVR->admin_approval_title}}@else{{' '}}@endif</td>
				</tr>
				<tr>
					<td style="background-color: #CCC;"><strong>Signature:</strong></td>
					<td>{{date("Y-m-d H:i:s")}}</td>
					<td style="background-color: #CCC;"><strong>Date:</strong></td>
					<td>@if(date("Y-m-d", strtotime($BPSSVR->admin_approval_date)) != '1970-01-01'){{\Carbon\Carbon::parse($BPSSVR->admin_approval_date)->format('d/m/Y')}}@else{{' '}}@endif</td>
				</tr>
				<tr>
					<td colspan="4">
						<strong>Notes: </strong>@if(strlen($BPSSVR->admin_approval_notes)>0){{$BPSSVR->admin_approval_notes}}@else&nbsp;@endif
						<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #CCC; padding-top: 20px;">
						@if(isset($BPSSVR->employement_type) && $BPSSVR->employement_type == 'employee')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" />
						@else
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" />
						@endif
						<strong>Employee (10 Years)</strong>&nbsp;&nbsp;
						@if(isset($BPSSVR->employement_type) && $BPSSVR->employement_type == 'contractor')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" />
						@else
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" />
						@endif
						<strong>Contractor (3 Years)</strong>
					</td>
					<td colspan="2" style="background-color: #CCC; padding-top: 20px;">
						@if(isset($BPSSVR->application_type) && $BPSSVR->application_type == 'initial_clearance')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" />
						@else
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" />
						@endif
						<strong>Initial Clearance</strong>&nbsp;&nbsp;
						@if(isset($BPSSVR->application_type) && $BPSSVR->application_type == 'revalidation')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" />
						@else
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" />
						@endif
						<strong>Revalidation</strong>
					</td>
				</tr>
				<tr>
					<td style="background-color: #CCC;"><strong>Site:</strong></td>
					<td>@if(strlen($BPSSVR->utas_site)>0){{$BPSSVR->utas_site}}@else{{' '}}@endif</td>
					<td style="background-color: #CCC;"><strong>Known Cargo:</strong></td>
					<td>
						@if(isset($BPSSVR->known_cargo) && strtolower($BPSSVR->known_cargo) == 'yes')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" />  No
						@elseif(isset($BPSSVR->known_cargo) && strtolower($BPSSVR->known_cargo) == 'no')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" />  No
						@else
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" />  No
						@endif
						<strong>Initial Clearance</strong>&nbsp;&nbsp;
					</td>
				</tr>
				<tr>
					<td style="background-color: #CCC;"><strong>Contractor Company:</strong></td>
					<td>@if(strlen($BPSSVR->contractor_company)>0){{$BPSSVR->contractor_company}}@else{{' '}}@endif</td>
					<td style="background-color: #CCC;"><strong>Position:</strong></td>
					<td>@if(strlen($BPSSVR->contractor_position)>0){{$BPSSVR->contractor_position}}@else{{' '}}@endif</td>
				</tr>
			</tbody>
		</table>

		<p class="text-red" style="font-style: italic; text-align: justify; font-size: 15px; margin-top:50px;">
			<strong>Important: Data Protection Act (2018) and GDPR.</strong> This form contains "personal data" as defined by the Data Protection Act 2018 and the General Data Protection Regulation (GDPR). It has been supplied to the appropriate HR or Security authority exclusively for the purpose of the Baseline Personnel Security Standard (BPSS). Information relating to the applicant will not be shared with any unauthorised personnel. Copies of ID provided will only be kept during the process of the BPSS and then securely destroyed on completion of this process.
		</p>

		

		
	</div>
	<page_footer>
		<div class="formReference"><strong>FORM</strong> PLY-SEC-FRM-002D REV. 2 [FEB-18]</div>
	</page_footer>
</page>
