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
		<div class="formReference">FORM PLY-SEC-FRM-002A REV. [Sept-18]</div>

		<div class="divLogo">
			@if (env('APP_ENV') == 'production')
				<img class="imgLogo" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/logoPDF_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.png" />
			@else
				<img class="imgLogo" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/logoPDF_demo.png" />
			@endif
		</div>
		<div class="mainTitle">Baseline Personnel Security Standard</div>
		<div class="mainSubtitle">Pre-employment &amp; Revalidation check for employees and sub-contractors</div>

		<div class="textCenter">The information provided in this booklet will be used to request and satisfy the current requirements of personnel security<br />For {{ env('APP_COMPANY_NAME') }}</div>

		<table class="tableBlock" cellspacing="0" cellpadding="0">
			<col width="324"><col width="300">
			<tbody>
				<tr>
					<td><strong>Name:</strong> {{$DBSApplication->forename}} @if(strlen($DBSApplication->middlename)>0){{$DBSApplication->middlename}}@endif {{$DBSApplication->presentSurname}}<br />
					@if(strlen($BPSSApplication->known_as_name)>0)<strong>Known as Name:</strong> {{$BPSSApplication->known_as_name}}@else &nbsp;<br /> @endif

					</td>
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
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" /> - Initial Clearance&nbsp;&nbsp;&nbsp;
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" /> - Initial Clearance&nbsp;&nbsp;&nbsp;
						@endif
						@if($BPSSApplication->employement_type == 'employee' && $BPSSApplication->form_type == 'revalidation')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" /> - Revalidation
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" /> - Revalidation
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
						<strong>Site Clearance Required for:</strong><br />
						@if(strlen($DBSApplication->organisationName)>0){{$DBSApplication->organisationName}}@endif
					</td>
					<td>
						<strong>Contractor</strong><br />
						@if($BPSSApplication->employement_type == 'contractor' && $BPSSApplication->form_type == 'initial_clearance')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" /> - Initial Clearance&nbsp;&nbsp;&nbsp;
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" /> - Initial Clearance&nbsp;&nbsp;&nbsp;
						@endif
						@if($BPSSApplication->employement_type == 'contractor' && $BPSSApplication->form_type == 'revalidation')
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox_checked.png" /> - Revalidation
						@else 
							<img class="checkbox" src="{{ env('APP_DOCUMENT_ROOT') }}/public/images/checkbox.png" /> - Revalidation
						@endif
						<br />
						<strong>Start Date:</strong>
						@if($BPSSApplication->employement_type == 'contractor' && strlen($BPSSApplication->start_date) == 10)
							{{\Carbon\Carbon::parse($BPSSApplication->start_date)->format('d/m/Y')}}
						@endif
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
			<strong>Completing your BPSS Clearance:</strong><br />
				Ensure that you have thoroughly read the notes before completing each part. Please ensure you answer all questions asked, you answer honestly and to the best of your ability.<br />
				If you have resided outside of England and Wales in the last 12 months you will be required to submit and supply your own 'Criminal Record Check'. Failure to comply with the requirements for the Baseline Security Standard may result in the delay in processing your application. If you require any assistance when completing please contact {{ env('APP_CONTACT_TEAM') }} ({{ env('APP_CONTACT_EMAIL') }}).
		</div>

		<div class="borderDiv" style="font-size:9px; line-height: 12px;">
			<span style="font-size: 10px; font-weight: bold;">Data Protection Act (2018):</span>
				This booklet asks you to supply “Sensitive Personal Data” as defined by the Data Protection Act 2018. You will be supplying this data to the appropriate HR or Security authority where it will be processed exclusively for the purpose of a check against the UK’s Immigration and Nationality records and the National Collection of Criminal Records. The HR or Security authority will protect the information that you provide and will ensure that it is not passed to anyone who is not authorised to see it. By signing the declaration on this booklet, you are explicitly consenting for the data you provide to be processed in the manner described above. If you have any concerns, about any of the questions or what we will do with the information you provide, please contact the person who issued this form for further information.<br />
			<span style="font-size: 10px; font-weight: bold;">Data Protection Act (2018):</span>
				We will only use the data you provide to complete the BPSS security clearance process, we will not share your data with anyone outside of the Security or HR function. On completion of clearance the data you provide for your DBS and BPSS applications will be securely wiped from our servers.
		</div>

		<div class="fullWidthDiv" style="margin-top: -10px;">
			<p style="font-size: 10px; line-height: 11px;">{{ env('APP_COMPANY_NAME') }} has various contracts with Governments and with companies which require it to have custody of, or access to, information that carries security classifications. The company has a duty to protect these assets and this obligation extends to its employees and contractors. You are required to provide certain information during the clearance process, so that your suitability for being granted access to protected assets can be assessed.</p>
			<p style="font-size: 10px; margin-top: -5px;  line-height: 11px;">All employees / contractors will be asked to provide information for this Baseline Standard. Note: If you are appointed, documentary evidence may be sought to confirm your answers. Your answers may, additionally, be checked against UK immigration and nationality records, and any unspent criminal record check through the means of a Basic Disclosure. By signing the declaration on this form, you are explicitly consenting for the data you provide to be processed in the manner described above. If you have any concerns, about any of the questions or what we will do with the information you provide, please contact the person who issued this form for further information. <span style="color:#FF0000; font-style: italic;">You are required to notify any material changes in the information you have given to the relevant HR or Security function concerned.</span></p>
		</div>

	</div>
</page>

