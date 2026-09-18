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
					<td style="background-color: #CCC;"><strong>Security Declaration</strong></td>
				</tr>
				<tr>
					<td>
						<p style="font: 9px 'Verdana'; font-size: 12px; line-height: 16px;font-style: italic;"><span style=" font-weight: bold; ">I declare that the information I have given on this form is true and complete to the best of my knowledge and belief.</span><br /><span class="text-red">I understand that any false information or deliberate omission in the information I have given on this form may disqualify me for employment in connection with Government contracts.</span></p>

						<p style="font: 9px 'Verdana'; font-size: 12px; line-height: 16px;"><span class="text-red" style=" font-weight: bold; font-style: italic;">I give permission to {{ env('APP_COMPANY_NAME') }} to confirm factual information from previous/current employer(s), covering the last <span style=" font-weight: bold; font-style: italic;">5 (permanent employee) / 3 (contractor/temporary worker)</span> years of my employment, that I have disclosed in part 3 of this form. I undertake to notify any material changes in the information I have given to the HR or Security Branch concerned.</span> </p>
						
						<table class="tableBlock" cellspacing="0" cellpadding="0" style="font-size: 12px; margin-bottom:0px; ">
							<col width="150"><col width="90"><col width="350">
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
					</td>
					</tr>
				</tbody>
		</table>

		
	</div>
	<page_footer>
		<div class="formReference"><strong>FORM</strong> PLY-SEC-FRM-002D REV. 2 [FEB-18]</div>
	</page_footer>
</page>
