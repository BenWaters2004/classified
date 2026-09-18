<style type="text/css">
<!--
body{font-family: Calibri;}
#divPageBox{width:670px; height:1050px; border: 2px solid #081475; padding: 0 25px; }
.formReference{width:600px; text-align:right; padding-right:70px; margin-top:15px; font-size: 12px;}
.divLogo{width:670px; height:96px; padding: 0;  margin: 20px 0; }
.imgLogo{width:660px; height: 106px; border: none;}
.mainTitle{width:670px;text-align: center; font-size:29px; font-weight: bold; line-height:36px; color: #081475; margin: 20px 0 0 0; }
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
-->
</style>
<page>
	<div id="divPageBox">
		<div class="formReference">FORM PLY-SEC-FRM-002A REV. 3 [MAR-18]</div>

		<div class="divLogo">
			@if (env('APP_ENV') == 'production')
				<img class="imgLogo" src="{{ env('APP_URL') }}images/logoPDF_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.png" />
			@else
				<img class="imgLogo" src="{{ env('APP_URL') }}images/logoPDF_demo.png" />
			@endif
		</div>
		<div class="mainTitle">Baseline Personnel Security Standard</div>
		<div class="mainSubtitle">Pre-employment &amp; Revalidation check for employees and sub-contractors</div>

		<div class="textCenter">The information provided in this booklet will be used to request and satisfy the current requirements of personnel security<br />For {{ env('APP_COMPANY_NAME') }}</div>

		<table class="tableBlock" cellspacing="0" cellpadding="0">
			<col width="312"><col width="312">
			<tbody>
				<tr>
					<td><strong>Name:</strong> {{$DBSApplication->forename}} @if(strlen($DBSApplication->middlename)>0){{$DBSApplication->middlename}}@endif {{$DBSApplication->presentSurname}}</td>
					<td>
						<strong>Contact Telephone Number:</strong> @if(strlen($DBSApplication->contact_number)>0)+{{$DBSApplication->contact_number_country_code}} {{$DBSApplication->contact_number}}@endif<br />
						<strong>Contact Mobile Number:</strong> @if(strlen($DBSApplication->mobile_number)>0)+{{$DBSApplication->mobile_number_country_code}} {{$DBSApplication->mobile_number}}@endif
					</td>
				</tr>

				<tr>
					<td>
						<strong>Contact Email Address:</strong><br />
						@if(strlen($DBSApplication->application_email)>0){{$DBSApplication->application_email}}@endif
					</td>
					<td rowspan="2">
						<strong>Employee</strong><br />
						@if($BPSSApplication->employement_type == 'employee' && $BPSSApplication->form_type == 'initial_clearance')
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" /> - Initial Clearance&nbsp;&nbsp;&nbsp;
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" /> - Initial Clearance&nbsp;&nbsp;&nbsp;
						@endif
						@if($BPSSApplication->employement_type == 'employee' && $BPSSApplication->form_type == 'revalidation')
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" /> - Revalidation
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" /> - Revalidation
						@endif
						<br />
						<strong>Start Date:</strong>
						@if($BPSSApplication->employement_type == 'employee' && strlen($BPSSApplication->start_date) == 10)
							{{\Carbon\Carbon::parse($BPSSApplication->start_date)->format('d/m/Y')}}
						@endif
					</td>
				</tr>
				<tr>
					<td>
						<strong>Job Title:</strong>
						@if(strlen($DBSApplication->position_applied_for)>0){{$DBSApplication->position_applied_for}}@endif
					</td>
				</tr>
				<tr>
					<td>
						<strong>Location of Site you will be based at:</strong><br />
						@if(strlen($DBSApplication->organisationName)>0){{$DBSApplication->organisationName}}@endif
					</td>
					<td>
						<strong>Contractor</strong><br />
						@if($BPSSApplication->employement_type == 'contractor' && $BPSSApplication->form_type == 'initial_clearance')
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" /> - Initial Clearance&nbsp;&nbsp;&nbsp;
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" /> - Initial Clearance&nbsp;&nbsp;&nbsp;
						@endif
						@if($BPSSApplication->employement_type == 'contractor' && $BPSSApplication->form_type == 'revalidation')
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox_checked.png" /> - Revalidation
						@else 
							<img class="checkbox" src="{{ env('APP_URL') }}images/checkbox.png" /> - Revalidation
						@endif
						<br />
						<strong>Start Date:</strong>
						@if($BPSSApplication->employement_type == 'contractor' && strlen($BPSSApplication->start_date) == 10)
							{{\Carbon\Carbon::parse($BPSSApplication->start_date)->format('d/m/Y')}}
						@endif
						<br />
						<strong>Sub-Contractor Company Name:</strong>
						@if(strlen($BPSSApplication->subcontractor_company_name)>0){{$BPSSApplication->subcontractor_company_name}}@endif
					</td>
				</tr>
				<tr>
					<td>
						<strong>Registered Organisation Application Reference:</strong><br />
						@if(strlen($DBSApplication->id)>0)REF_{{$DBSApplication->id}}@endif
					</td>
					<td>
						<strong>DBS Application Form Reference:</strong><br />
						@if(strlen($DBSApplication->dbsResponse_int022_DBSApplicationFormReference)>0){{$DBSApplication->dbsResponse_int022_DBSApplicationFormReference}}@endif
					</td>
				</tr>
			</tbody>
		</table>

		<div class="borderDiv">
			<strong>How to Complete this Booklet:</strong><br />
				Ensure that you have thoroughly read the notes before completing each part.<br />
				Please answer all parts of this booklet applicable to you honestly and to the best of your ability.<br />
				Failure to comply with the requirements for the Baseline Security Standard may result in the delay in processing your application.<br />
				Please supply 3 forms of ID; please refer to Appendix 3 for types of ID that are acceptable.
		</div>

		<div class="borderDiv" style="font-size:9px; line-height: 12px;">
			<span style="font-size: 10px; font-weight: bold;">Data Protection Act (2018):</span>
				This booklet asks you to supply “Sensitive Personal Data” as defined by the Data Protection Act 2018. You will be supplying this data to the appropriate HR or Security authority where it will be processed exclusively for the purpose of a check against the UK’s Immigration and Nationality records and the National Collection of Criminal Records. The HR or Security authority will protect the information that you provide and will ensure that it is not passed to anyone who is not authorised to see it. By signing the declaration on this booklet, you are explicitly consenting for the data you provide to be processed in the manner described above. If you have any concerns, about any of the questions or what we will do with the information you provide, please contact the person who issued this form for further information.
		</div>

		<div class="fullWidthDiv" style="margin-top: -10px;">
			<p style="font-size: 10px;">{{ env('APP_COMPANY_NAME') }} has various contracts with Governments and with companies which require it to have custody of, or access to, information that carries security classifications. The company has a duty to protect these assets and this obligation extends to its employees and contractors. You are required to provide certain information during the clearance process, so that your suitability for being granted access to protected assets can be assessed.</p>
			<p style="font-size: 10px; margin-top: -5px;">All employees / contractors will be asked to provide information for this Baseline Standard. Note: If you are appointed, documentary evidence may be sought to confirm your answers. Your answers may, additionally, be checked against UK immigration and nationality records, and any unspent criminal record check through the means of a Basic Disclosure. By signing the declaration on this form, you are explicitly consenting for the data you provide to be processed in the manner described above. If you have any concerns, about any of the questions or what we will do with the information you provide, please contact the person who issued this form for further information. <span style="color:#FF0000; font-style: italic;">You are required to notify any material changes in the information you have given to the relevant HR or Security function concerned.</span></p>
		</div>

	</div>
</page>
