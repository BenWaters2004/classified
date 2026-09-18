@extends('layout.default')

@section('title', "Application FAQ's")

@section('sidebar')
@endsection

@section('content')
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
      <!-- general form elements -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-size: 36px !important;">Common problems with clearance</h3>
        </div>
        <!-- /.box-header -->
        <div class="row">
        	<div class="col-md-12">
		        <div class="box-body">
		        	<strong>Quick Reference</strong>
		        	<ul>
		        		<li>Not all answers on the system have been answered correctly</li>
		        		<li>The DBS Declaration was not completed / not correctly completed with ‘I agree’ or ‘I confirm’ as instructed</li>
		        		<li>Incorrectly showing ‘Employee’ and not ‘Contractor’ or vice versa</li>
		        		<li>Incorrectly showing ‘Not Living’ lawfully in the UK</li>
		        		<li>Not completed the full 3 or 5-years’ work history</li>
		        		<li>Personal or employee reference contact details not being correct, or the reference is slow to respond</li>
		        		<li>Gathering the correct ID required and also ensuring any utility bills are from the last 3 months, and if a driving licence is used, the address matches the utility bill.</li>
		        	</ul>
		        </div>
		    </div>
		</div>
        <div class="row">
        	<div class="col-md-12">
		        <div class="box-body">
	              <div class="box-group" id="accordion">
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" style="text-decoration: none;">
	                        The DBS Declaration 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse1" class="panel-collapse collapse">
	                    <div class="box-body">
	                      You – the candidate – will be asked at the start of your DBS check from BIT asking you to agree that we, as an organisation, are given consent from you, to process your personal information. We cannot progress with your application until this is received back from you, completed appropriately. Commonly, candidates don’t complete the respective boxes with the correct statement, for example will write ‘I CONFIRM’ when the correct statement is ‘I AGREE’, then we are required to not let you proceed untill this has been corrected.
	                    </div>
	                  </div>
	                </div>
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse2" style="text-decoration: none;">
	                        Infomation Provided within ClassifIeD 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse2" class="panel-collapse collapse">
	                    <div class="box-body">
	                      It is paramount that you complete all the questions in the ClassifIeD system to the best of your knowledge and do not miss bits out. We are required to go through each area of the application in detail. If we then need to get in touch with you to gather more information, this obviously slows things down considerably.
	                    </div>
	                  </div>
	                </div>

	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse3" style="text-decoration: none;">
	                        What if im asked to change something 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse3" class="panel-collapse collapse">
	                    <div class="box-body">
	                      If we get in touch with you and ask you to correct or check something, then please visit <a href=https://classified.getclassified.co.uk/">https://classified.getclassified.co.uk/</a> and using the same details you used to register, log back in and update what is required.
	                    </div>
	                  </div>
	                </div>
	                
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse4" style="text-decoration: none;">
	                        Who are BluescreenIT (BIT) 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse4" class="panel-collapse collapse">
	                    <div class="box-body">
	                      BIT are the contractor who support your company with their BPSS clearances. You may also here them reffered to by BIT Group/BIT Security/BluescreenIT.
	                    </div>
	                  </div>
	                </div>
	                
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse5" style="text-decoration: none;">
	                        What is ClassifIeD
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse5" class="panel-collapse collapse">
	                    <div class="box-body">
	                      ClassifIeD is the name of the website Bluescreen IT (BIT) uses to process BPSS clearances.
	                    </div>
	                  </div>
	                </div>
	                
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse6" style="text-decoration: none;">
	                        Why am I being asked to do this again (if you have already) 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse6" class="panel-collapse collapse">
	                    <div class="box-body">
	                      If you have been recruited through an agency, it is likely they will do their own checks on you as an individual. Our BPPS screening is required by your company and is irrespective of what clearance you have done with any other organisation. Even if you have recently done a DBS or BPSS check, we will still need to complete our own. Deapending on your employers policies, you may be asked to complete this again every 3-5 years.
	                    </div>
	                  </div>
	                </div>
	                
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse7" style="text-decoration: none;">
	                        What is BPSS? 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse7" class="panel-collapse collapse">
	                    <div class="box-body">
	                      BPSS checks are seen by the government as an important precaution to confirm the identity of an individual (employee or contractor) and their rights to work in the UK. These types of checks are often required when securing government contracts / government related work providing a level of assurance as to those individuals trustworthiness, honestly, integrity and values required for the job position. BPSS checks mitigate the risks associated with individuals working with potentially sensitive information.
	                    </div>
	                  </div>
	                </div>
	                
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse8" style="text-decoration: none;">
	                        Why is BPSS necessary? 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse8" class="panel-collapse collapse">
	                    <div class="box-body">
	                      A BPSS check is requested by your employer. If you would like more information on why you have been asked to complete this, you will need to contact your HR representative/Security Controller.
	                    </div>
	                  </div>
	                </div>
	                
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse9" style="text-decoration: none;">
	                        What information is included in the check?  
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse9" class="panel-collapse collapse">
	                    <div class="box-body">
	                      	A BPSS check consists of verification made up of the following 4 parts (RICE):<br />
							<strong>R</strong> - ight to work– Nationality and Immigration Status (including an entitlement to undertake the work in question.<br />
							<strong>I</strong> - dentity– ID Data check (electronic identity authentication- name, address, aliases, links, accounts, etc).<br />
							<strong>C</strong> - riminal Records– Search for unspent convictions only (Basic Disclosure).<br />
							<strong>E</strong> - mployment history check– Confirmation of past 3 years employment (minimum) history / activity.<br />
							In addition, candidates are required to disclose any significant periods spent abroad (6 months or more in the past 3 years). 
	                    </div>
	                  </div>
	                </div>
	                
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse10" style="text-decoration: none;">
	                        What happens if I dont pass my clearance? 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse10" class="panel-collapse collapse">
	                    <div class="box-body">
	                      This will come down to your company and their own HR policies and procedures. Unfortunately, we are not in a position to answer this on their behalf.
	                    </div>
	                  </div>
	                </div>
	                

	              </div>
	            </div>
        	</div>
      	</div>

       	<div class="box-header with-border">
          <h3 class="box-title" style="font-size: 36px !important;">Frequently asked questions</h3>
        </div>
        <div class="row">
        	<div class="col-md-12">
		        <div class="box-body">
	              <div class="box-group" id="accordion">
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse11" style="text-decoration: none;">
	                        What are DBS / BPSS checks? 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse11" class="panel-collapse collapse">
	                    <div class="box-body">
	                      <table style="width: 100%; text-align: center;" class="table table-striped">
	                      	<tr>
	                      		<th style="text-align: center; width: 50%;">DBS</th>
	                      		<th style="text-align: center; width: 50%;">BPSS</th>
	                      	</tr>
	                      	<tr>
	                      		<td>DBS stands for Disclosure and Barring Service – and will check your background for any criminal activity or police charges.</td>
	                      		<td>BPSS Stands for Baseline Personnel Security Standard – it is the standard pre-employment screening for employees working in Government departments. It replaced its predecessor – the basic check- in 2006.</td>
	                      	</tr>
	                      </table>
	                    </div>
	                  </div>
	                </div>
	              </div>

	              <div class="box-group" id="accordion">
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse12" style="text-decoration: none;">
	                        What is checked in a DBS / BPSS check? 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse12" class="panel-collapse collapse">
	                    <div class="box-body">
	                      <table style="width: 100%; text-align: center;" class="table table-striped">
	                      	<tr>
	                      		<th style="text-align: center; width: 50%;">DBS</th>
	                      		<th style="text-align: center; width: 50%;">BPSS</th>
	                      	</tr>
	                      	<tr>
	                      		<td>Any criminal convictions will likely be disclosed. Spent convictions are unlikely to be disclosed on this level of DBS check.</td>
	                      		<td>ID verification, Right to Work in the UK. A 3 year work history and a criminal record check.</td>
	                      	</tr>
	                      </table>
	                    </div>
	                  </div>
	                </div>
	              </div>

	              <div class="box-group" id="accordion">
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse13" style="text-decoration: none;">
	                        How long does a DBS / BPSS check usually take? 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse13" class="panel-collapse collapse">
	                    <div class="box-body">
	                      <table style="width: 100%; text-align: center;" class="table table-striped">
	                      	<tr>
	                      		<th style="text-align: center; width: 50%;">DBS</th>
	                      		<th style="text-align: center; width: 50%;">BPSS</th>
	                      	</tr>
	                      	<tr>
	                      		<td>This varies – it can take 2 hours and on other occasions, it can take more than 2 weeks.</td>
	                      		<td>This depends on the level of information provided and how well the candidate completes the application form, how long it takes for reference checks to come back; assuming all the information is presented in a proper manner and the DBS check doesn’t encounter any issues, it can take a matter of days.</td>
	                      	</tr>
	                      </table>
	                    </div>
	                  </div>
	                </div>
	              </div>

	              <div class="box-group" id="accordion">
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse14" style="text-decoration: none;">
	                        How long does a DBS / BPSS check last? 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse14" class="panel-collapse collapse">
	                    <div class="box-body">
	                      <table style="width: 100%; text-align: center;" class="table table-striped">
	                      	<tr>
	                      		<th style="text-align: center; width: 50%;">DBS</th>
	                      		<th style="text-align: center; width: 50%;">BPSS</th>
	                      	</tr>
	                      	<tr>
	                      		<td>There is no time frame on a DBS – they are only as accurate as the day they are printed – it is down to individual organisations to dictate when they need to be renewed.</td>
	                      		<td>This will likely be down to the end user/employer – but often, it will be a 3 year period.</td>
	                      	</tr>
	                      </table>
	                    </div>
	                  </div>
	                </div>
	              </div>

	              <div class="box-group" id="accordion">
	                <div class="panel box box-primary">
	                  <div class="box-header with-border">
	                    <h4 class="box-title">
	                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse15" style="text-decoration: none;">
	                        What happens if I fail my DBS / BPSS 
	                      </a>
	                    </h4>
	                  </div>
	                  <div id="collapse15" class="panel-collapse collapse">
	                    <div class="box-body">
	                      <table style="width: 100%; text-align: center;" class="table table-striped">
	                      	<tr>
	                      		<th style="text-align: center; width: 50%;">DBS</th>
	                      		<th style="text-align: center; width: 50%;">BPSS</th>
	                      	</tr>
	                      	<tr>
	                      		<td>The final decision on what happens in that eventually will fall to the end customer.</td>
	                      		<td>The final decision on what happens in that eventually will fall to the end customer.</td>
	                      	</tr>
	                      </table>
	                    </div>
	                  </div>
	                </div>
	              </div>

	            </div>
        	</div>
      	</div>

      <!-- /.box -->
	</div>
</div>

@endsection


@section('pageCSS')
 
@endsection

@section('pageJavascript')
@include('applicant.dbsValidations')
<script type="text/javascript">

</script>
@endsection
