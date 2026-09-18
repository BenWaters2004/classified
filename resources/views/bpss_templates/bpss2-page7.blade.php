
<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		<div style="width: 670px; height: 20px; clear: both;">&nbsp;</div> 

		@if(isset($BPSSApplication->employment_history) && count($BPSSApplication->employment_history) >2)
			@foreach ($BPSSApplication->employment_history as $key => $employemntHystoryBlock)
				@if($key != 0 && $key != 1)

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
						<td><strong>Period Covered:</strong></td>
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


		<p class="text-red" style="font: 9px 'Verdana'; font-size: 14px; line-height: 16px;">Please indicate below if you do <span style="text-decoration: underline; font-weight: bold;">NOT</span> wish us to contact any of the referee(s) listed above for employment history; where you do not wish us to contact the referee please provide an income tax summary to cover the period this will speed up the clearance process.</p>
		@if(isset($BPSSApplication->employment_history) && count($BPSSApplication->employment_history) >0)
		<div class="text-red" style="width:670px;"> 
			@foreach ($BPSSApplication->employment_history as $key => $employemntHystoryBlock)

				@if(isset($employemntHystoryBlock->referee_allow_contact) && $employemntHystoryBlock->referee_allow_contact)
					<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
				@else 
					<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
				@endif Referee {{$key+1}} @if(isset($employemntHystoryBlock->p60_enclosed) && $employemntHystoryBlock->p60_enclosed){{'(P60 enclosed)'}}@endif
				&nbsp;&nbsp;&nbsp;&nbsp;
			@endforeach
		</div>
		@endif

	</div>
</page>

