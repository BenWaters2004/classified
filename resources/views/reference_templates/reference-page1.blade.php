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
		<div class="mainTitle">Reference For {{$userDetails->firstName}} {{$userDetails->lastName}}</div>

		<table class="tableBlock" cellspacing="0" cellpadding="0" style="font-size: 12px; margin-bottom:20px; ">
			<col width="30"><col width="280"><col width="300">
			<tbody>
				<tr>
					<td align="center">1</td>
					<td><strong>Name of candidate</strong></td>
					<td>{{$referenceDetails->reference_nameOfCandidate}}</td>
				</tr>
				<tr>
					<td align="center">2</td>
					<td><strong>Date of birth of candidate</strong></td>
					<td>{{$referenceDetails->reference_dobCandidate}}</td>
				</tr>
				<tr>
					<td align="center">3</td>
					<td><strong>Are you related to the subject? If so, please state your relationship.</strong></td>
					<td>
						@if($referenceDetails->reference_relationYesNo)
							Yes
						@else
							No
						@endif
						<br />{{$referenceDetails->reference_relationCandidate}}
					</td>
				</tr>
				<tr>
					<td align="center">4</td>
					<td><strong>Over what period have you know the subject?</strong></td>
					<td>
						From: {{\Carbon\Carbon::parse($referenceDetails->reference_preriodKnownFrom)->format('d/m/Y')}}<br />
						To: {{\Carbon\Carbon::parse($referenceDetails->reference_periodKnownTo)->format('d/m/Y')}}
					</td>
				</tr>
				<tr>
					<td align="center">5</td>
					<td><strong>Please state the nature and depth of your acquaintance:</strong></td>
					<td>{{$referenceDetails->reference_natureOfAq}}</td>
				</tr>
				<tr>
					<td align="center">6</td>
					<td><strong>Do you believe the subject to be strictly honest, conscientious and discreet?</strong></td>
					<td>{{$referenceDetails->reference_subjectHonest}}</td>
				</tr>
				<tr>
					<td align="center">7</td>
					<td>
						<span><strong>Please answer yes or no. If yes, please give more details. Do you know of any factor concerning the subject which might cause his / her fitness for employment on sensitive work to be questioned? Is so, please give details.</strong></span><br /><br />
						<span style="font-style: italic; color: #ff0000;">(Among the factors which are relevant are significant financial difficulties, abuse of alcohol or drugs, an extravagant mode of living or signs of mental or physical illness which may impair judgement or reliability.)</span>
					</td>
					<td>{{$referenceDetails->reference_factorsConcerning}}</td>
				</tr>
				
				
			</tbody>
		</table>
		
		<div style="text-align: center; color: #ff0000; font-weight: bold;"><p>The above answers are correct to the best of my knowledge and belief</p></div>
		<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:20px; font-size: 12px; margin-bottom:20px; ">
			<col width="180"><col width="455">
			<tbody>
				<tr>
					<td>Name</td>
					<td>{{$referenceDetails->referenceDetails_fullName}}</td>
				</tr>
				<tr>
					<td>Signature</td>
					<td><span style="font-style: italic;">Completed online</span></td>
				</tr>
				<tr>
					<td>Date</td>
					<td>{{\Carbon\Carbon::parse($referenceDetails->createdOn)->format('d/m/Y')}}</td>
				</tr>
				<tr>
					<td>Contact Address</td>
					<td>{{$referenceDetails->referenceDetails_contactAddress}}</td>
				</tr>
				<tr>
					<td>Telephone Number</td>
					<td>{{$referenceDetails->referenceDetails_contactTelephome}}</td>
				</tr>
				<tr>
					<td>Email</td>
					<td>{{$referenceDetails->referenceDetails_contactEmail}}</td>
				</tr>


				
			</tbody>
		</table>

		<div style="text-align: center; font-style: italic;"><p>Important: Data Protection Act (2018). This form contains “personal” data as defined by the Data Protection Act 2018. It has been supplied to the appropriate HR or Security authority exclusively for the purpose of the Baseline Personnel Security Standard. The HR or Security authority must protect the information provided and ensure that it is not passed to anyone who is not authorised to see it.</p></div>

		
	</div>
	<page_footer>
		<div class="formReference"><strong>FORM</strong> Version 1.2 [MAR-23]</div>
	</page_footer>
</page>


