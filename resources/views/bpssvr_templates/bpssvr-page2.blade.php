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
			<col width="9"><col width="460"><col width="140"><col width="100">
			<tbody>
				<tr>
					<td colspan="3" style="background-color: #CCC;"><strong>Part 2a. Verification of Identity</strong></td>
				</tr>
				<tr>
					<td colspan="2" style="font-style: italic;"> <strong>Document</strong> </td>
					<td style="font-style: italic;"> <strong>Date of Issue</strong> </td>
				</tr>
				@if(isset($BPSSVR->passport_number) && strlen($BPSSVR->passport_number) > 0)
					<tr>
						<td style="text-align: center;">&nbsp;</td>
						<td>@if(strlen($BPSSVR->passport_number)>0) Passport ({{$BPSSVR->passport_number.': '.$BPSSVR->passport_country}})@else{{' '}}@endif</td>
						<td>@if(strlen($BPSSVR->passport_date_of_issue)==10 && date('Y-m-d', strtotime($BPSSVR->passport_date_of_issue)) != '1970-01-01') {{\Carbon\Carbon::parse($BPSSVR->passport_date_of_issue)->format('d/m/Y')}}@else{{' '}}@endif</td>
					</tr>
        		@endif


				@if(isset($BPSSVR->identity_documents) && count($BPSSVR->identity_documents) >0)
					@foreach ($BPSSVR->identity_documents as $key => $document)
						<tr>
							<td style="text-align: center;">{{($key+1)}}.</td>
							<td>@if(strlen($document->document_name)>0){{$document->document_name}}@else{{' '}}@endif</td>
							<td>@if(strlen($document->document_date_of_issue_name)==10){{\Carbon\Carbon::parse($document->document_date_of_issue_name)->format('d/m/Y')}}@else{{' '}}@endif</td>
						</tr>
					@endforeach
					@if(count($BPSSVR->identity_documents) < 2)
						<tr>
							<td style="text-align: center;">2.</td>
							<td>&nbsp;</td>
						<td>&nbsp;</td>
						</tr>
					@endif
					@if(count($BPSSVR->identity_documents) < 3)
						<tr>
							<td style="text-align: center;">3.</td>
							<td>&nbsp;</td>
						<td>&nbsp;</td>
						</tr>
					@endif
					@if(count($BPSSVR->identity_documents) < 4)
						<tr>
							<td style="text-align: center;">4.</td>
							<td>&nbsp;</td>
						<td>&nbsp;</td>
						</tr>
					@endif
				@else
					<tr>
						<td style="text-align: center;">1.</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align: center;">2.</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align: center;">3.</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align: center;">4.</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				@endif
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0">
			<col width="9"><col width="152"><col width="100"><col width="200"><col width="100">
			<tbody>
				<tr>
					<td colspan="5" style="background-color: #CCC;"><strong>Part 3: Reference Details</strong></td>
				</tr>
				<tr>
					<td colspan="2" style="font-style: italic; vertical-align: center;">Referee Name</td>
					<td style="font-style: italic; vertical-align: center;">Relationship</td>
					<td style="font-style: italic; vertical-align: center;">Address</td>
					<td style="font-style: italic; vertical-align: center;">Length of Association</td>
				</tr>
				@if(isset($reference1->id) && strlen($reference1->referee_name)>0)
					<tr>
						<td style="text-align: center;">1.</td>
						<td>@if(strlen($reference1->referee_name)>0){{$reference1->referee_name}}@else{{' '}}@endif</td>
						<td>@if(strlen($reference1->referee_relationship)>0){{$reference1->referee_relationship}}@else{{' '}}@endif</td>
						<td>@if(strlen($reference1->referee_address)>0){{$reference1->referee_address}}@else{{' '}}@endif</td>
						<td>@if(strlen($reference1->referee_length_of_association)>0){{$reference1->referee_length_of_association}}@else{{' '}}@endif</td>
					</tr>
				@else
					<tr>
						<td style="text-align: center;">1.</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				@endif

				@if(isset($reference2->id) && strlen($reference2->referee_name)>0)
					<tr>
						<td style="text-align: center;">2.</td>
						<td>@if(strlen($reference2->referee_name)>0){{$reference2->referee_name}}@else{{' '}}@endif</td>
						<td>@if(strlen($reference2->referee_relationship)>0){{$reference2->referee_relationship}}@else{{' '}}@endif</td>
						<td>@if(strlen($reference2->referee_address)>0){{$reference2->referee_address}}@else{{' '}}@endif</td>
						<td>@if(strlen($reference2->referee_length_of_association)>0){{$reference2->referee_length_of_association}}@else{{' '}}@endif</td>
					</tr>
				@else
					<tr>
						<td style="text-align: center;">2.</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				@endif

				@if(isset($reference3->id) && $reference3->referee_name)
					<tr>
						<td style="text-align: center;">3.</td>
						<td>@if(strlen($reference3->referee_name)>0){{$reference3->referee_name}}@else{{' '}}@endif</td>
						<td>@if(strlen($reference3->referee_relationship)>0){{$reference3->referee_relationship}}@else{{' '}}@endif</td>
						<td>@if(strlen($reference3->referee_address)>0){{$reference3->referee_address}}@else{{' '}}@endif</td>
						<td>@if(strlen($reference3->referee_length_of_association)>0){{$reference3->referee_length_of_association}}@else{{' '}}@endif</td>
					</tr>
				@else
					<tr>
						<td style="text-align: center;">3.</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				@endif
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
			<col width="8"><col width="284"><col width="8"><col width="285">
			<tbody>
				<tr>
					<td colspan="4" style="background-color: #CCC;"><strong>Part 3a. other Information</strong></td>
				</tr>
				<tr>
					<td>
						@if(isset($BPSSVR->verification_of_national_status_received) && $BPSSVR->verification_of_national_status_received)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
					</td>
					<td>Verification of Nationality Status Received</td>
					<td>
						@if(isset($BPSSVR->last_years_employemnt_confirmed) && $BPSSVR->last_years_employemnt_confirmed)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
					</td>
					<td>Last 3 years employment confirmed</td>
				</tr>

				<tr>
					<td>
						@if(isset($BPSSVR->academic_qualifications_received) && $BPSSVR->academic_qualifications_received)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
					</td>
					<td>Academic qualifications received</td>
					<td>
						@if(isset($BPSSVR->document_received_support_employment_history) && $BPSSVR->document_received_support_employment_history)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
					</td>
					<td>P60/P45 Received to support Employment History</td>
				</tr>

				<tr>
					<td>
						@if(isset($BPSSVR->denied_party_screening) && $BPSSVR->denied_party_screening)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
					</td>
					<td>Denied Party Screening</td>
					<td colspan="2">
						@if(isset($BPSSApplication->items_of_interest) && $BPSSApplication->items_of_interest)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> No Items of Interest <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> Items recorded see attached report
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> No Items of Interest <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Items recorded see attached report
						@endif
					</td>
				</tr>

				<tr>
					<td>
						@if(isset($BPSSVR->security_matrix) && $BPSSVR->security_matrix)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
					</td>
					<td>Security Matrix</td>
					<td colspan="2">
						@if(isset($BPSSVR->clearance_type) && $BPSSVR->clearance_type == 'bpss_clearance')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> BPSS Clearance <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> SC Clearance
						@elseif(isset($BPSSVR->clearance_type) && $BPSSVR->clearance_type == 'sc_clearance')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> BPSS Clearance <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> SC Clearance
						@else
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> BPSS Clearance <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> SC Clearance
						@endif
					</td>
				</tr>
			</tbody>
		</table>


		<table class="tableBlock" cellspacing="0" cellpadding="0">
			<col width="80"><col width="185"><col width="135"><col width="185">
			<tbody>
				<tr>
					<td colspan="4" class="text-red textCenter" style="font-size:10px;"><strong>I certify that in accordance with the requirements of the Baseline Personnel Security Standard: <br />I have personally examined the documents listed in 2 and 3 above and have satisfactorily established the details listed.</strong></td>
				</tr>
				<tr>
					<td><strong>Name:</strong></td>
					<td>@if(strlen($BPSSVR->admin_certify_name)>0){{$BPSSVR->admin_certify_name}}@else{{' '}}@endif</td>
					<td><strong>Post:</strong></td>
					<td>@if(strlen($BPSSVR->admin_certify_post)>0){{$BPSSVR->admin_certify_post}}@else{{' '}}@endif</td>
				</tr>
				<tr>
					<td><strong>Date:</strong></td>
					<td>@if(date("Y-m-d", strtotime($BPSSVR->admin_certify_date)) != '1970-01-01'){{\Carbon\Carbon::parse($BPSSVR->admin_certify_date)->format('d/m/Y')}}@else{{' '}}@endif</td>
					<td><strong>Telephone Number:</strong></td>
					<td>@if(strlen($BPSSVR->admin_certify_telephone_number)>0){{$BPSSVR->admin_certify_telephone_number}}@else{{' '}}@endif</td>
				</tr>
				<tr>
					<td colspan="4">
						<strong>Signature: </strong>{{date("Y-m-d H:i:s")}}
						@if(strlen($BPSSVR->adminSignaturePath)>0)
			            	<img class="imgSignature" src="{{ env('APP_DOCUMENT_ROOT') }}/public/uploads/user_signatures/{{$BPSSVR->adminSignaturePath}}">
			            @endif
					</td>
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0">
			<col width="140"><col width="70"><col width="155"><col width="220">
			<tbody>
				<tr>
					<td colspan="4" style="background-color: #CCC;"><strong>4. Criminal Record Check</strong></td>
				</tr>
				<tr>
					<td colspan="4">Recorded below are details of the external verification of unspent criminal record details using Disclosure Scotland or Disclosure Barring Service (DBS)</td>
				</tr>
				<tr style="font-size: 13px;">
					<td style="font-style: italic; padding-right: 0;"><strong>Type of Disclosure</strong></td>
					<td style="font-style: italic; padding-right: 0;"><strong>Date Issue</strong></td>
					<td style="font-style: italic; padding-right: 0;"><strong>Certificate Reference No</strong></td>
					<td style="font-style: italic; padding-right: 0;"><strong>Comments</strong></td>
				</tr>
				<tr style="font-size: 13px;">
					<td>
						@if(isset($BPSSVR->type_of_disclosure) && $BPSSVR->type_of_disclosure == 'basic')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> Basic <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Standard/Enhanced
						@elseif(isset($BPSSVR->type_of_disclosure) && $BPSSVR->type_of_disclosure == 'standard')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Basic <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> Standard/Enhanced
						@else
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Basic <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> Standard/Enhanced
						@endif
					</td>
					<td>@if(strlen($BPSSVR->disclosure_certificate_date) == 10){{\Carbon\Carbon::parse($BPSSVR->disclosure_certificate_date)->format('d/m/Y')}}@else{{' '}}@endif</td>
					<td>@if(strlen($BPSSVR->dbsResponse_int023_DisclosureNumber)>0){{$BPSSVR->dbsResponse_int023_DisclosureNumber}}@else{{' '}}@endif</td>
					<td>
						@if(isset($BPSSVR->disclosure_comments) && $BPSSVR->disclosure_comments == 'no_convictions_for_diclosure')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> No convictions for disclosure <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> See notes for further explanation
						@elseif(isset($BPSSVR->disclosure_comments) && $BPSSVR->disclosure_comments == 'disclosure_comments_see_notes')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> No convictions for disclosure <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton_selected.png" /> See notes for further explanation
						@else
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> No convictions for disclosure <br />
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/radioButton.png" /> See notes for further explanation
						@endif
					</td>
				</tr>
			</tbody>
		</table>


		
	</div>
	<page_footer>
		<div class="formReference"><strong>FORM</strong> PLY-SEC-FRM-002D REV. 2 [FEB-18]</div>
	</page_footer>
</page>