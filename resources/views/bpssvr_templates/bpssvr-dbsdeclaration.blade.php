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
					<td style="background-color: #CCC;"><strong>DBS Declaration</strong></td>
				</tr>
				<tr>
					<td>
						<div class="fullWidthDiv" style="font-size:12px; line-height: 12px;">
							<p><strong>Privacy Policy - basics check declaration:</strong><br />I have read the Basic DBS Check Processing Privacy Policy <a href="https://www.gov.uk/government/publications/dbs-privacy-policies" target="_blank">https://www.gov.uk/government/publications/dbs-privacy-policies</a> and I understand how DBS will process my personal data.</p>
						</div>

						<table class="tableBlock" cellspacing="0" cellpadding="0" style=" margin-top: 0px;">
							<col width="400"><col width="200">
							<tbody>
								<tr>
									<td style="color:#FF0000;">Applicant must consent by typing I CONFIRM in box A</td>

									<td>A: I CONFIRM</td>

								</tr>
							</tbody>
						</table>


						<div class="fullWidthDiv" style="font-size: 12px; line-height: 12px;">
							<p>By confirming your acceptance of the below declaration, you are giving us consent to receive an e-result regarding your basic DBS application. If consent is not given you have the option to complete a basic check via <a href="www.gov.uk/DBS" target="_blank">www.gov.uk/DBS</a>.</p>
						</div>

						<div class="fullWidthDiv" style="font-size: 12px; line-height: 12px;">
							<p><strong>Consent to obtain basic check electronic result</strong><br />I consent to the DBS providing an electronic result directly to the responsible organisation that has submitted my application. I understand that an electronic result contains a message that indicates either the certificate does not contain criminal record information or to await certificate which will indicate that my certificate contains criminal record information. In some cases the responsible organisation may provide this information directly to my employer prior to me receiving my certificate.<br />I understand if I do not consent to an electronic result being issued to the responsible organisation submitting my application that I must not proceed with this application and I should apply directly to DBS <a href="https://www.gov.uk/request-copy-criminal-record" target="_blank">Request a basic DBS check - GOV.UK (www.gov.uk)</a> I understand that to withdraw my consent whilst my application is in progress I must contact the DBS helpline 03000 200 190. My application will then be withdrawn.</p>
						</div>

						<table class="tableBlock" cellspacing="0" cellpadding="0" style=" margin-top: 0px;">
							<col width="400"><col width="200">
							<tbody>
								<tr>
									<td style="color:#FF0000;">Applicant must consent by typing I AGREE in box B</td>

									<td>B: I AGREE</td>

								</tr>
							</tbody>
						</table>

						<div class="fullWidthDiv" style="font-size: 12px; line-height: 12px;">
							<p>As the applicant you must explicitly confirm that you have provided complete and true information in support of this application.</p>
						</div>

						<div class="fullWidthDiv" style="font-size: 12px; line-height: 12px;">
							<p><strong>Declaration By Applicant</strong><br />I have provided complete and true information in support of the application, and I understand that knowingly making a false statement for this purpose is a criminal offence.</p>
						</div>

						<table class="tableBlock" cellspacing="0" cellpadding="0" style=" margin-top: 0px;">
							<col width="400"><col width="200">
							<tbody>
								<tr>
									<td style="color:#FF0000;">Applicant must consent by typing I CONFIRM in box C</td>

									<td>C: I CONFIRM</td>

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
