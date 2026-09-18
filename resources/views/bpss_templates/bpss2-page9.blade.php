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
						<td style="background-color: #CCC;"><strong>Personal Referee</strong><br />&nbsp;</td>
						<td><strong>Period Known From:</strong> @if(isset($reference1->date_from) && strlen($reference1->date_from) == 10)<br />{{\Carbon\Carbon::parse($reference1->date_from)->format('d/m/Y')}}@endif</td>
						<td><strong>Period Known To:</strong> @if(isset($reference1->date_to) && strlen($reference1->date_to) == 10)<br />{{\Carbon\Carbon::parse($reference1->date_to)->format('d/m/Y')}}@endif</td>
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
						<td style="background-color: #CCC;"><strong>Personal Referee</strong><br />&nbsp;</td>
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
						<td style="background-color: #CCC;"><strong>Personal Referee</strong><br />&nbsp;</td>
						<td><strong>Period Known From:</strong> @if(isset($reference2->date_from) && strlen($reference2->date_from) == 10)<br />{{\Carbon\Carbon::parse($reference2->date_from)->format('d/m/Y')}}@endif</td>
						<td><strong>Period Known To:</strong> @if(isset($reference2->date_to) && strlen($reference2->date_to) == 10)<br />{{\Carbon\Carbon::parse($reference2->date_to)->format('d/m/Y')}}@endif</td>
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
						<td style="background-color: #CCC;"><strong>Personal Referee</strong><br />&nbsp;</td>
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

		<div class="divTitleText" style="width:120px; float: left;">
			Security Declaration
		</div>
		<p style="font: 9px 'Verdana'; font-size: 12px; line-height: 16px;font-style: italic;"><span style=" font-weight: bold; ">I declare that the information I have given on this form is true and complete to the best of my knowledge and belief.</span><br /><span class="text-red">I understand that any false information or deliberate omission in the information I have given on this form may disqualify me for employment in connection with Government contracts.</span></p>

		<p style="font: 9px 'Verdana'; font-size: 12px; line-height: 16px;"><span class="text-red" style=" font-weight: bold; font-style: italic;">I give permission to {{ env('APP_COMPANY_NAME') }} to confirm factual information from previous/current employer(s), covering the last <span style=" font-weight: bold; font-style: italic;">5 (permanent employee) / 3 (contractor/temporary worker)</span> years of my employment, that I have disclosed in part 3 of this form. I undertake to notify any material changes in the information I have given to the HR or Security Branch concerned.</span> </p>
		
		<table class="tableBlock" cellspacing="0" cellpadding="0" style="font-size: 12px; margin-bottom:0px; ">
			<col width="150"><col width="90"><col width="360">
			<tbody>
				<tr>
					<td><strong>Is Consent Provided:</strong></td>
					<td>
						@if(isset($BPSSApplication->is_consent_provided) && $BPSSApplication->is_consent_provided)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" /> YES
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" /> No
						@endif
					</td>
					<td><strong>Consented Responsible Body Email:</strong> <a href="mailto:{{$BPSSApplication->consent_responsible_body_email}}">{{$BPSSApplication->consent_responsible_body_email}}</a></td>
				</tr>
				<tr>
					<td><strong>Date Signed:</strong></td>
					<td colspan="2">{{\Carbon\Carbon::parse($BPSSApplication->completedDate)->format('d/m/Y')}}</td>
				</tr>
			</tbody>
		</table>

	</div>
</page>
