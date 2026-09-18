<style type="text/css">
<!--
body{font-family: Calibri;}
#divPageBox{width:670px; height:1050px; padding: 0 25px; }
.formReference{text-align:right; margin-top:15px; margin-right:25px; font-size: 12px;}
.divLogo{width:670px; height:40px; padding: 0;  margin: 20px 0 0 0; }
.imgLogo{width:200px; height: 32px; border: none;}
.imgProfile{}

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

		<table class="tableBlock" cellspacing="0" cellpadding="0">
			<col width="660">
			<tbody>
				<tr>
					<td style="background-color: #CCC;"><strong>Criminal Record Declaration</strong></td>
				</tr>
				<tr>
					<td>
						<div class="fullWidthDiv" style="font-size:12px; line-height: 12px;">
							<p><strong>Guidance Note:</strong> The Company has Government contracts, some or all of which require the company to hold material or information, which is the property of the Government. The company has a duty to protect these assets while in its possession and this obligation extends to its employees and agents. Since you are or may become such a person please complete the following sections.</p>
							<p>Please answer the following questions honestly. In addition to your self-declaration below, a check against the National Collection of Criminal Records will be undertaken and documentary evidence sought to confirm your answers in the form of Police Act Disclosure which will be paid for by {{ env('APP_COMPANY_NAME') }}. By signing the declaration in part 5 you are giving us permission to do so.</p>
							<p>The information you give will be treated in strict confidence.</p>
						</div>


						<div class="fullWidthDiv"  style="font-size: 12px; line-height: 12px;">
							<p>Have you ever been convicted or found guilty by a court of any offence in any country (excluding parking but including all motoring offences even where a spot fine has been administrated by the police) or have you ever been put on probation (probation orders are now called community rehabilitation orders) or absolutely/conditionally discharged or bound over after being charged with any offence or is there any action pending against you? You need not declare convictions which are "spent" under the Rehabilitation of Offenders Act (1974).</p>
						</div>

						<table class="tableBlock" cellspacing="0" cellpadding="0" style=" margin-top: 0px;">
							<col width="60"><col width="60"><col width="470">
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
							<col width="60"><col width="60"><col width="470">
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
							<col width="60"><col width="60"><col width="470">
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

		
						<div class="fullWidthDiv" style="font-size: 12px; line-height: 12px;">
							<p>If you answered YES to any of the questions on this form, please give details bellow:</p>
						</div>

						<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
							<col width="640">
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

						

						</td>
					</tr>
				</tbody>
		</table>
	</div>
	<page_footer>
		<div class="formReference"><strong>FORM</strong> PLY-SEC-FRM-002D REV. 2 [FEB-18]</div>
	</page_footer>
</page>
