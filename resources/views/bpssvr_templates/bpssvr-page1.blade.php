<style type="text/css">
<!--
body{font-family: Calibri;}
#divPageBox{width:670px; height:1050px; padding: 0 25px; }
.formReference{text-align:right; margin-top:15px; margin-right:25px; font-size: 12px;}
.divLogo{width:670px; height:40px; padding: 0;  margin: 20px 0 0 0; }
.imgLogo{width:200px; height: 32px; border: none;}
.imgProfile{}
.imgSignature{max-height: 30px;}

.mainTitle{width:670px;text-align: center; font-size:20px; font-weight: bold; line-height:36px; color: #081475; margin: 20px 0 0 0; }
.mainSubtitle{width:670px;text-align: center; font-size:14px; line-height:16px; color: #0070c0; margin: 0; font-style: italic;}
.textCenter{width:670px;text-align: center; font-size:12px; line-height:15px; margin: 20px 0 0 0;}

.tableBlock{width: 100%; margin: 20px 0px 0px 0px; font-size: 14px;}
.tableBlock, .tableBlock th, .tableBlock td {border: 1px solid black;}
.tableBlock th, .tableBlock td {padding: 5px; line-height: 20px;  vertical-align:middle; }

.divPagination{width:650px; margin: 5px 0; font: 16px 'Times New Roman'; font-weight: bold; padding:0px 20px 0 0; text-align: right;}
.divTitleText{width:660px; height: 27px; margin: 10px 0; font: 16px 'Calibri'; vertical-align:middle; background: #000080; padding:5px; color: #FFF;}

.checkbox{width: 16px; height: 16px;}
.borderDiv{width:650px; margin: 5px 0px; font: 11px 'Verdana'; padding:10px; line-height:18px; border: 2px solid #000000;}
.fullWidthDiv{width:670px; margin: 5px 0px; font: 11px 'Verdana'; padding:10px 0; line-height:18px;}
.text-red{color: #FF0000;}
td{vertical-align: top;}
-->
</style>
<page>
	<div id="divPageBox">

		<div class="divLogo">
			@if (env('APP_ENV') == 'production')
				<img class="imgLogo" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/logoPDF_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.png" />
			@else
				<img class="imgLogo" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/logoPDF_demo.png" />
			@endif
		</div>
		<div class="mainTitle">Baseline Personnel Security Standard – Verification Record</div>
		<div>
			<table class="tableBlock" cellspacing="0" cellpadding="0" align="center">
				<col width="180"><col width="160">
				<tbody>
					<tr>
						<td><strong>APPROVED ACCESS NO.</strong></td>
						<td>{{$BPSSVR->approved_access_no}}</td>
					</tr>
					<tr>
						<td><strong>EXPIRY:</strong></td>
						<td>{{\Carbon\Carbon::parse($BPSSVR->expiry_date)->format('d/m/Y')}}</td>
					</tr>
				</tbody>
			</table>
		</div>

		<table class="tableBlock" cellspacing="0" cellpadding="0">
			<col width="135"><col width="155"><col width="140"><col width="155">
			<tbody>
				<tr>
					<td colspan="4" style="background-color: #CCC;"><strong>Part 1. Employee / Contractor / Applicant Details</strong></td>
				</tr>
				<tr>
					<td>
						<strong>Present Surname:</strong>
					</td>
					<td>@if(strlen($BPSSVR->present_surname)>0){{$BPSSVR->present_surname}}@endif</td>
					<td>
						<strong>Present Forenames:</strong>
					</td>
					<td>@if(strlen($BPSSVR->present_forenames)>0){{$BPSSVR->present_forenames}}@endif</td>
				</tr>
				<tr>
					<td>
						<strong>Previous Surname:<br /><span class="text-red" style="font-style: italic;">If applicable</span></strong>
					</td>
					<td>
						@if(isset($otherNames) && count($otherNames) > 0)
			            	@foreach ($otherNames as $key => $otherName)
					          	@if($key>0)<br />@endif
						        {{$otherName->other_surname}}
					        @endforeach
					   	@endif
					</td>
					<td>
						<strong>Previous Forenames:<br /><span class="text-red" style="font-style: italic;">If applicable</span></strong>
					</td>
					<td>
						@if(isset($otherNames) && count($otherNames) > 0)
			            	@foreach ($otherNames as $key => $otherName)
					          	@if($key>0)<br />@endif
						        {{$otherName->other_forename}}
					        @endforeach
					   	@endif
					</td>
				</tr>
			</tbody>
		</table>
		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
			<col width="133"><col width="194"><col width="160"><col width="120">
			<tbody>
				<tr>
					<td rowspan="3">
						<strong>Address:</strong>
					</td>
					<td colspan="2">@if(strlen($BPSSVR->address_line_1)>0){{$BPSSVR->address_line_1}}@else{{' '}}@endif</td>
					<td rowspan="4">
						@if(@getimagesize(env('APP_DOCUMENT_ROOT').'/public/uploads/bpssvr_photos/'.$BPSSVR->photo_path))
							<img class="imgProfile" src="{{ env('APP_DOCUMENT_ROOT') }}/public/uploads/bpssvr_photos/{{$BPSSVR->photo_path}}" />

						@else
							No Photo Available
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="2">@if(strlen($BPSSVR->address_line_2)>0){{$BPSSVR->address_line_2}}@else{{' '}}@endif</td>
				</tr>
				<tr>
					<td colspan="2">
						@if(strlen($BPSSVR->address_town)>0){{$BPSSVR->address_town}}@endif
						@if(strlen($BPSSVR->address_county)>0){{' '.$BPSSVR->address_county}}@endif
						@if(strlen($BPSSVR->address_country_fullName)>0){{', '.$BPSSVR->address_country_fullName}}@endif
					</td>
				</tr>
				<tr>
					<td colspan="2"><strong>Contact Telephone Number:</strong></td>
					<td colspan="1">@if(strlen($BPSSVR->contact_number)>0){{$BPSSVR->contact_number}}@else{{' '}}@endif</td>
				</tr>

				
			</tbody>
		</table>


		<table class="tableBlock" cellspacing="0" cellpadding="0">
			<col width="203"><col width="203"><col width="203">
			<tbody>
				<tr>
					<td colspan="3" style="background-color: #CCC;"><strong>Part 2. Nationality and Immigration Status</strong></td>
				</tr>
				<tr>
					<td><strong>Date of Birth:</strong><br />@if(strlen($BPSSVR->dob) == 10){{\Carbon\Carbon::parse($BPSSVR->dob)->format('d/m/Y')}}@else{{' '}}@endif</td>
					<td><strong>Town of Birth:</strong><br />@if(strlen($BPSSVR->birth_town)>0){{$BPSSVR->birth_town}}@else{{' '}}@endif</td>
					<td><strong>Country of Birth:</strong><br />@if(strlen($BPSSVR->birth_country_fullName)>0){{$BPSSVR->birth_country_fullName}}@else{{' '}}@endif</td>
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
			<col width="115"><col width="225"><col width="85"><col width="160">
			<tbody>
				<tr>
					<td colspan="2">
						<strong>Nationality:</strong> @if(strlen($BPSSVR->present_nationality)>0){{$BPSSVR->present_nationality}}@else{{' '}}@endif
					</td>
					<td colspan="2"><strong>Dual Nationality:</strong> @if(strlen($BPSSVR->dual_nationalities)>0){{$BPSSVR->dual_nationalities}}@else{{' '}}@endif</td>
				</tr>
				<tr>
					<td colspan="4">
						<strong>Former Nationality:</strong> @if(strlen($BPSSVR->former_nationality_details)>0){{$BPSSVR->former_nationality_details}}@else{{' '}}@endif
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<strong>British Naturalisation Certificate No</strong> @if(strlen($BPSSVR->naturalisation_certificate_number)>0){{$BPSSVR->naturalisation_certificate_number}}@else{{' '}}@endif
					</td>
					<td colspan="2"><strong>Date Issued:</strong> @if(strlen($BPSSVR->naturalisation_certificate_date) == 10){{\Carbon\Carbon::parse($BPSSVR->naturalisation_certificate_date)->format('d/m/Y')}}@else{{' '}}@endif</td>
				</tr>
				<tr>
					<td colspan="2">
						<strong>Dual Citizenship</strong>
						@if(isset($BPSSVR->dual_citizenship) && $BPSSVR->dual_citizenship)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> Yes / 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> No
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Yes / 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> No
						@endif
					</td>
					<td colspan="2">
						<strong>Lawfully in the UK</strong>
						@if(isset($BPSSVR->lawfully_resident_in_uk) && $BPSSVR->lawfully_resident_in_uk)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> Yes / 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> No
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Yes / 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> No
						@endif
					</td>
				</tr>	
				<tr>
					<td>
						<strong>Restrictions on continued residence in the UK</strong><br />
						@if(isset($BPSSVR->continued_residence_restrictions) && $BPSSVR->continued_residence_restrictions)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> Yes / 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> No
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Yes / 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> No
						@endif
					</td>
					<td style="vertical-align: top;"><strong>Details:</strong> @if(strlen($BPSSVR->continued_residence_restrictions_details) > 0){{$BPSSVR->continued_residence_restrictions_details}}@else{{' '}}@endif</td>
					<td>
						<strong>Subject to immigration Control</strong><br />
						@if(isset($BPSSVR->subject_to_immigration_control) && $BPSSVR->subject_to_immigration_control)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> Yes / 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> No
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Yes / 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> No
						@endif
					</td>
					<td style="vertical-align: top;"><strong>Details:</strong> @if(strlen($BPSSVR->subject_to_immigration_control_details) > 0){{$BPSSVR->subject_to_immigration_control_details}}@else{{' '}}@endif</td>
				</tr>
				<tr>
					<td colspan="2">
						<strong>Restrictions on continued freedom to take employment in the UK?</strong><br />
						@if(isset($BPSSVR->freedom_to_take_employment) && $BPSSVR->freedom_to_take_employment)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> Yes / 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> No
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Yes / 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> No
						@endif
					</td>
					<td colspan="2" style="vertical-align: top;"><strong>Details:</strong> @if(strlen($BPSSVR->freedom_to_take_employment_details) > 0){{$BPSSVR->freedom_to_take_employment_details}}@else{{' '}}@endif</td>
				</tr>	
				<tr>
					<td colspan="4">
						<strong>Home Office / Port Reference Number:</strong> @if(strlen($BPSSVR->ho_port_reference_number) > 0){{$BPSSVR->ho_port_reference_number}}@else{{' '}}@endif
					</td>
				</tr>		
			</tbody>
		</table>

		
	</div>
	<page_footer>
		<div class="formReference"><strong>FORM</strong> PLY-SEC-FRM-002D REV. 2 [FEB-18]</div>
	</page_footer>
</page>


