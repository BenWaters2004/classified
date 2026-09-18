<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		
		<div class="divTitleText" style="width:175px; float: left;">
			 	Criminal Record Declaration
		</div>
		

		<div class="fullWidthDiv" style="font-size:12px; line-height: 12px;">
			<p><strong>Guidance Note:</strong> The Company has Government contracts, some or all of which require them to to hold material or information, which is the property of the Government. The company has a duty to protect these assets while in its possession and this obligation extends to its employees and agents. Since you are or may become such a person please complete the following sections.</p>
			<p>Please answer the following questions honestly. In addition to your self-declaration below, a check against the National Collection of Criminal Records will be undertaken and documentary evidence sought to confirm your answers in the form of Police Act Disclosure which will be paid for by {{ env('APP_COMPANY_NAME') }}. By signing the declaration in part 5 you are giving us permission to do so.</p>
			<p>The information you give will be treated in strict confidence.</p>
		</div>

		<br />&nbsp;<br />
		<div class="fullWidthDiv"  style="font-size: 12px; line-height: 12px;">
			<p>Have you ever been convicted or found guilty by a court of any offence in any country (excluding parking but including all motoring offences even where a spot fine has been administrated by the police) or have you ever been put on probation (probation orders are now called community rehabilitation orders) or absolutely/conditionally discharged or bound over after being charged with any offence or is there any action pending against you? You need not declare convictions which are "spent" under the Rehabilitation of Offenders Act (1974).</p>
		</div>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style=" margin-top: 0px;">
			<col width="60"><col width="60"><col width="480">
			<tbody>
				<tr>
					<td>
						@if($BPSSApplication->convicted_by_court)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
						&nbsp; Yes
					</td>

					<td>
						@if($BPSSApplication->convicted_by_court === 0)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
						&nbsp; No
					</td>

					<td>
						<span style="color:#FF0000; font-style: italic;">(<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" /> as applicable - if yes, please give details on the following page)</span>
					</td>
				</tr>
			</tbody>
		</table>


		<div class="fullWidthDiv" style="font-size: 12px; line-height: 12px;">
			<p>Have you ever been convicted by a Court Martial or sentenced to detention or dismissal whilst serving in the Armed Forces of the UK or any Commonwealth or foreign country? You need not declare convictions which are "spent" under the Rehabilitation of Offenders Act (1974).</p>
		</div>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style=" margin-top: 0px;">
			<col width="60"><col width="60"><col width="480">
			<tbody>
				<tr>
					<td>
						@if($BPSSApplication->convicted_by_court_martial)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
						&nbsp; Yes
					</td>

					<td>
						@if($BPSSApplication->convicted_by_court_martial === 0)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
						&nbsp; No
					</td>

					<td>
						<span style="color:#FF0000; font-style: italic;">(<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" /> as applicable - if yes, please give details on the following page)</span>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="fullWidthDiv" style="font-size: 12px; line-height: 12px;">
			<p>Do you know of any other matters in your background which might cause your reliability or suitability to have access to government assets to be called into question?</p>
		</div>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style=" margin-top: 0px;">
			<col width="60"><col width="60"><col width="480">
			<tbody>
				<tr>
					<td>
						@if($BPSSApplication->background_reliability)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
						&nbsp; Yes
					</td>

					<td>
						@if($BPSSApplication->background_reliability === 0)
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" />
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" />
						@endif
						&nbsp; No
					</td>

					<td>
						<span style="color:#FF0000; font-style: italic;">(<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" /> as applicable - if yes, please give details on the following page)</span>
					</td>
				</tr>
			</tbody>
		</table>

		<br />&nbsp;<br />&nbsp;
		<div class="fullWidthDiv" style="font-size: 12px; line-height: 12px;">
			<p>If you answered YES to any of the questions on this form, please give details bellow:</p>
		</div>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
			<col width="650">
			<tbody>
				<tr>
					<td>
						<strong>Details: </strong><span style="color:#FF0000; font-style: italic;">please list bellow if applicable</span>
					</td>
				</tr>
				<tr>
					<td style="font-size: 12px;">
						@if(strlen($BPSSApplication->convictions_details)>0){{$BPSSApplication->convictions_details}}<br />&nbsp;@else
						<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
						@endif
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</page>
