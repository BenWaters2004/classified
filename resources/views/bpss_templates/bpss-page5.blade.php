
<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		
		<div class="divTitleText" style="width:150px; float: left;">
			 	Part 3: Reference Details
		</div>
		<p style="font: 9px 'Verdana'; font-size: 12px;"><strong>Guidance Notes:</strong> <span style="color: #FF0000;">PLEASE PROVIDE DETAILS OF EMPLOYMENT / UNEMPLOYMENT / ACADEMIC HISTORY INCLUDING CONTACT DETAILS COVERING THE LAST 5 YEARS IN CHRONOLOGICAL ORDER.</span></p>
		<p style="font: 9px 'Verdana'; font-size: 12px; margin-top: -10px;">We will use the information below to contact your referees; please complete the questions below honestly and to the best of your ability. Your personal referees should not be relatives.</p>
		<p style="font: 9px 'Verdana'; font-size: 12px; margin-top: 0;">If you do NOT wish your current employer to be contacted please indicate on page 6; if you have selected for us NOT to contact your employer you will be requested to supply P60 for those periods.</p>
		<p style="font: 9px 'Verdana'; font-size: 9px; font-style: italic; line-height: 10px;">If you have been travelling outside of the United Kingdom you will be required to provide evidence of your whereabouts including dates for example: receipts, passport stamps, reference from fellow travellers, places worked etc.</p>


		<div class="divTitleText" style="width:660px; float: left;">
			Academic Period <span style="font-size: 12px; font-style: italic; font-weight: normal;"> if you have attended college/university in the last 5 years please give details below</span>
		</div>
		<table class="tableBlock" cellspacing="0" cellpadding="0" style="font-size: 12px; margin-bottom:0px; ">
			<col width="200"><col width="198"><col width="198">
			<tbody>
				<tr>
					<td>
						@if(isset($BPSSApplication->school_data) && $BPSSApplication->school_data)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif School Name: @if(isset($BPSSApplication->school_data) && $BPSSApplication->school_data) <br />{{$BPSSApplication->school_name}}@endif
					</td>
					<td>
						@if(isset($BPSSApplication->college_data) && $BPSSApplication->college_data)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif College Name: @if(isset($BPSSApplication->college_data) && $BPSSApplication->college_data) <br />{{$BPSSApplication->college_name}}@endif
					</td>
					<td>
						@if(isset($BPSSApplication->university_data) && $BPSSApplication->university_data)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif University Name: @if(isset($BPSSApplication->university_data) && $BPSSApplication->university_data) <br />{{$BPSSApplication->university_name}}@endif
					</td>
				</tr>
				<tr>
					<td><strong>Date From:</strong> @if(isset($BPSSApplication->school_date_from) && strlen($BPSSApplication->school_date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->school_date_from)->format('d/m/Y')}}@endif</td>
					<td><strong>Date From:</strong> @if(isset($BPSSApplication->college_date_from) && strlen($BPSSApplication->college_date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->college_date_from)->format('d/m/Y')}}@endif</td>
					<td><strong>Date From:</strong> @if(isset($BPSSApplication->university_date_from) && strlen($BPSSApplication->university_date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->university_date_from)->format('d/m/Y')}}@endif</td>
				</tr>
				<tr>
					<td><strong>Date To:</strong> @if(isset($BPSSApplication->school_date_to) && strlen($BPSSApplication->school_date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->school_date_to)->format('d/m/Y')}}@endif</td>
					<td><strong>Date To:</strong> @if(isset($BPSSApplication->college_date_to) && strlen($BPSSApplication->college_date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->college_date_to)->format('d/m/Y')}}@endif</td>
					<td><strong>Date To:</strong> @if(isset($BPSSApplication->university_date_to) && strlen($BPSSApplication->university_date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->university_date_to)->format('d/m/Y')}}@endif</td>
				</tr>
				<tr>
					<td><strong>Contact Name:</strong> @if(isset($BPSSApplication->school_contact_name) && strlen($BPSSApplication->school_contact_name) > 0) <br />{{$BPSSApplication->school_contact_name}}@endif</td>
					<td><strong>Contact Name:</strong> @if(isset($BPSSApplication->college_contact_name) && strlen($BPSSApplication->college_contact_name) > 0) <br />{{$BPSSApplication->college_contact_name}}@endif</td>
					<td><strong>Contact Email:</strong> @if(isset($BPSSApplication->university_contact_email) && strlen($BPSSApplication->university_contact_email) > 0) <br />{{$BPSSApplication->university_contact_email}}@endif</td>
				</tr>
				<tr>
					<td><strong>Contact Address:</strong> @if(isset($BPSSApplication->school_contact_address) && strlen($BPSSApplication->school_contact_address) > 0) <br />{{$BPSSApplication->school_contact_address}}@endif</td>
					<td><strong>Contact Address:</strong> @if(isset($BPSSApplication->college_contact_address) && strlen($BPSSApplication->college_contact_address) > 0) <br />{{$BPSSApplication->college_contact_address}}@endif</td>
					<td><strong>Contact Number:</strong> @if(isset($BPSSApplication->university_contact_number) && strlen($BPSSApplication->university_contact_number) > 0) <br />{{$BPSSApplication->university_contact_number}}@endif</td>
				</tr>
			</tbody>
		</table>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px; ">
			<col width="510"><col width="110">
			<tbody>
				<tr>
					<td><strong>Have you been unemployed in the last 5 years? if YES please give dates below:</strong></td>
					<td>
						Yes
						@if(isset($BPSSApplication->unemployment) && $BPSSApplication->unemployment && isset($BPSSApplication->unemployment_history) && count($BPSSApplication->unemployment_history) >0)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
						&nbsp;&nbsp;&nbsp;No
						@if(!isset($BPSSApplication->unemployment) || !$BPSSApplication->unemployment || !isset($BPSSApplication->unemployment_history) || count($BPSSApplication->unemployment_history) == 0)
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" />
						@endif
					</td>
				</tr>
			</tbody>
		</table>

		
		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:10px; ">
			<col width="200"><col width="198"><col width="198">
			<tbody>
				@if(!isset($BPSSApplication->unemployment) || !$BPSSApplication->unemployment || !isset($BPSSApplication->unemployment_history) || count($BPSSApplication->unemployment_history) ==0)

				<tr>
					<td style="line-height: 14px;">
						<strong>Date From:</strong> <br />
						<strong>Date To:</strong> <br />
					</td>
					<td style="line-height: 14px;">
						<strong>Date From:</strong> <br />
						<strong>Date To:</strong> <br />
					</td>
					<td style="line-height: 14px;">
						<strong>Date From:</strong> <br />
						<strong>Date To:</strong> <br />
					</td>
				</tr>
				@endif

				@if(isset($BPSSApplication->unemployment) && $BPSSApplication->unemployment)
					@if(isset($BPSSApplication->unemployment_history) && count($BPSSApplication->unemployment_history) >0)
					<tr>
						<td style="line-height: 14px;">
							<strong>Date From:</strong> @if(isset($BPSSApplication->unemployment_history[0]->date_from) && strlen($BPSSApplication->unemployment_history[0]->date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[0]->date_from)->format('d/m/Y')}}@endif <br />
							<strong>Date To:</strong> @if(isset($BPSSApplication->unemployment_history[0]->date_to) && strlen($BPSSApplication->unemployment_history[0]->date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[0]->date_to)->format('d/m/Y')}}@endif <br />
						</td>
						<td style="line-height: 14px;">
							<strong>Date From:</strong> @if(isset($BPSSApplication->unemployment_history[1]->date_from) && strlen($BPSSApplication->unemployment_history[1]->date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[1]->date_from)->format('d/m/Y')}}@endif <br />
							<strong>Date To:</strong> @if(isset($BPSSApplication->unemployment_history[1]->date_to) && strlen($BPSSApplication->unemployment_history[1]->date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[1]->date_to)->format('d/m/Y')}}@endif <br />
						</td>
						<td style="line-height: 14px;">
							<strong>Date From:</strong> @if(isset($BPSSApplication->unemployment_history[2]->date_from) && strlen($BPSSApplication->unemployment_history[2]->date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[2]->date_from)->format('d/m/Y')}}@endif <br />
							<strong>Date To:</strong> @if(isset($BPSSApplication->unemployment_history[2]->date_to) && strlen($BPSSApplication->unemployment_history[2]->date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[2]->date_to)->format('d/m/Y')}}@endif <br />
						</td>
					</tr>
					@endif

					@if(isset($BPSSApplication->unemployment_history) && count($BPSSApplication->unemployment_history) >3)
					<tr>
						<td style="line-height: 14px;">
							<strong>Date From:</strong> @if(isset($BPSSApplication->unemployment_history[3]->date_from) && strlen($BPSSApplication->unemployment_history[3]->date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[3]->date_from)->format('d/m/Y')}}@endif <br />
							<strong>Date To:</strong> @if(isset($BPSSApplication->unemployment_history[3]->date_to) && strlen($BPSSApplication->unemployment_history[3]->date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[3]->date_to)->format('d/m/Y')}}@endif <br />
						</td>
						<td style="line-height: 14px;">
							<strong>Date From:</strong> @if(isset($BPSSApplication->unemployment_history[4]->date_from) && strlen($BPSSApplication->unemployment_history[4]->date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[4]->date_from)->format('d/m/Y')}}@endif <br />
							<strong>Date To:</strong> @if(isset($BPSSApplication->unemployment_history[4]->date_to) && strlen($BPSSApplication->unemployment_history[4]->date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[4]->date_to)->format('d/m/Y')}}@endif <br />
						</td>
						<td style="line-height: 14px;">
							<strong>Date From:</strong> @if(isset($BPSSApplication->unemployment_history[5]->date_from) && strlen($BPSSApplication->unemployment_history[5]->date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[5]->date_from)->format('d/m/Y')}}@endif <br />
							<strong>Date To:</strong> @if(isset($BPSSApplication->unemployment_history[5]->date_to) && strlen($BPSSApplication->unemployment_history[5]->date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[5]->date_to)->format('d/m/Y')}}@endif <br />
						</td>
					</tr>
					@endif

					@if(isset($BPSSApplication->unemployment_history) && count($BPSSApplication->unemployment_history) >6)
					<tr>
						<td style="line-height: 14px;">
							<strong>Date From:</strong> @if(isset($BPSSApplication->unemployment_history[6]->date_from) && strlen($BPSSApplication->unemployment_history[6]->date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[6]->date_from)->format('d/m/Y')}}@endif <br />
							<strong>Date To:</strong> @if(isset($BPSSApplication->unemployment_history[6]->date_to) && strlen($BPSSApplication->unemployment_history[6]->date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[6]->date_to)->format('d/m/Y')}}@endif <br />
						</td>
						<td style="line-height: 14px;">
							<strong>Date From:</strong> @if(isset($BPSSApplication->unemployment_history[7]->date_from) && strlen($BPSSApplication->unemployment_history[7]->date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[7]->date_from)->format('d/m/Y')}}@endif <br />
							<strong>Date To:</strong> @if(isset($BPSSApplication->unemployment_history[7]->date_to) && strlen($BPSSApplication->unemployment_history[7]->date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[7]->date_to)->format('d/m/Y')}}@endif <br />
						</td>
						<td style="line-height: 14px;">
							<strong>Date From:</strong> @if(isset($BPSSApplication->unemployment_history[8]->date_from) && strlen($BPSSApplication->unemployment_history[8]->date_from) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[8]->date_from)->format('d/m/Y')}}@endif <br />
							<strong>Date To:</strong> @if(isset($BPSSApplication->unemployment_history[8]->date_to) && strlen($BPSSApplication->unemployment_history[8]->date_to) == 10){{\Carbon\Carbon::parse($BPSSApplication->unemployment_history[8]->date_to)->format('d/m/Y')}}@endif <br />
						</td>
					</tr>
					@endif
				@endif
			</tbody>
		</table>

		<div class="divTitleText" style="width:660px; float: left;">
			Employment Period: <span style="font-size: 12px; font-style: italic; font-weight: normal;"> please provide details of your employment covering the last 5 years</span>
		</div>

		@if(isset($BPSSApplication->employment_history) && count($BPSSApplication->employment_history) >0)
			@foreach ($BPSSApplication->employment_history as $key => $employemntHystoryBlock)
				@if($key == 0 || $key == 1)

					<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px; ">
						<col width="118"><col width="240"><col width="240">
						<tbody>
							<tr>
								<td><strong>Period Covered [{{$key+1}}]:</strong></td>
								<td><strong>Date From:</strong> @if(isset($employemntHystoryBlock->date_from) && strlen($employemntHystoryBlock->date_from) == 10){{\Carbon\Carbon::parse($employemntHystoryBlock->date_from)->format('d/m/Y')}}@endif</td>
								<td><strong>Date To:</strong> @if(isset($employemntHystoryBlock->date_to) && strlen($employemntHystoryBlock->date_to) == 10){{\Carbon\Carbon::parse($employemntHystoryBlock->date_to)->format('d/m/Y')}}@endif</td>
							</tr>
							<tr>
								<td colspan="3"><strong>Company Name: </strong> {{$employemntHystoryBlock->company_name}}</td>
							</tr>
							<tr>
								<td colspan="3"><strong>Email Address: </strong> {{$employemntHystoryBlock->email_address}}</td>
							</tr>
							<tr>
								<td colspan="3"><strong>Company Address: </strong> {{$employemntHystoryBlock->company_address_line}}</td>
							</tr>
						</tbody>
					</table>
					<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
						<col width="311"><col width="311">
						<tbody>
							<tr>
								<td>
									<strong>Town:</strong> {{$employemntHystoryBlock->company_address_town}}<br />
								</td>
								<td>
									<strong>Postcode:</strong> {{$employemntHystoryBlock->company_address_postcode}}<br />
								</td>
							</tr>
						</tbody>
					</table>
				@endif
			@endforeach
		@else
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:10px; font-size: 12px; margin-bottom:0px; ">
				<col width="418"><col width="90"><col width="90">
				<tbody>
					<tr>
						<td><strong>Period Covered [1]:</strong></td>
						<td><strong>Date From:</strong></td>
						<td><strong>Date To:</strong></td>
					</tr>
					<tr>
						<td colspan="3"><strong>Company Name: </strong></td>
					</tr>
					<tr>
						<td colspan="3"><strong>Email Address: </strong></td>
					</tr>
					<tr>
						<td colspan="3"><strong>Company Address: </strong></td>
					</tr>
				</tbody>
			</table>
			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
				<col width="311"><col width="311">
				<tbody>
					<tr>
						<td>
							<strong>Town:</strong> <br />
						</td>
						<td>
							<strong>Postcode:</strong> <br />
						</td>
					</tr>
				</tbody>
			</table>
		@endif

	</div>
</page>
