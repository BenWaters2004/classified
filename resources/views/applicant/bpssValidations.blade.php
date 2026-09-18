<script type="text/javascript">



$(document).ready(function() {
	$('#errorMessageBlock').hide();

	//---------------------------------------------------------------------------//
    var employement_type = $( "#employement_type option:selected" ).val();
 	//if (employement_type == 'employee'){
	// 	$("#contractorBlock").hide();
	// } else if (employement_type == 'contractor'){
	// 	$("#contractorBlock").show();
	// }

	var selfemployment = $( "#selfemployment option:selected" ).val();
	if (selfemployment == 1){
		$("#selfemploymentOptionsBlock").show();
	} else{
		$("#selfemploymentOptionsBlock").hide();
	}
	check_selfemployment_documentation_option();

	var national_identity_card = $( "#national_identity_card option:selected" ).val();
    if (national_identity_card == 1){
		$("#nationalIdentityCardBlock").show();
	} else {
		$("#nationalIdentityCardBlock").hide();
	}

	var lawfully_resident_in_uk = $("#lawfully_resident_in_uk option:selected").val();
    if (lawfully_resident_in_uk == 0){
		$("#validateLawfullyResidentWarning").show();
	} else{
		$("#validateLawfullyResidentWarning").hide();
	}

	var passport = $( "#passport option:selected" ).val();
    if (passport == 1){
		$("#addNewPassportBlock").show();
	} else {
		$("#addNewPassportBlock").hide();
	}

	if($("#passportsListBlock").attr('show-flag') == 'true'){
		$("#passport option:selected").removeAttr("selected");
		$('passport').find('option').each(function() {
	        if($(this).val() == '1') {
	            $(this).attr('selected', 'selected');
	        }
	    });

		$("#passportsListBlock").show();
		$("#addNewPassportBlock").show();
	} 

	var dual_citizenship = $( "#dual_citizenship option:selected" ).val();
    if (dual_citizenship == 1){
		$("#dualCitizenshipBlock").show();
	} else {
		$("#dualCitizenshipBlock").hide();
	}

	var former_nationality = $( "#former_nationality option:selected" ).val();
    if (former_nationality == 1){
		$("#formerNationalityBlock").show();
	} else {
		$("#formerNationalityBlock").hide();
	}

	var subject_to_immigration_control = $( "#subject_to_immigration_control option:selected" ).val();
    if (subject_to_immigration_control == 1){
		$("#immigrationControlBlock").show();
	} else {
		$("#immigrationControlBlock").hide();
	}

	var continued_residence_restrictions = $( "#continued_residence_restrictions option:selected" ).val();
    if (continued_residence_restrictions == 1){
		$("#residenceRestrictionsBlock").show();
	} else {
		$("#residenceRestrictionsBlock").hide();
	}

	var freedom_to_take_employment = $( "#freedom_to_take_employment option:selected" ).val();
    if (freedom_to_take_employment == 1){
		$("#freedomToTakeRmploymentBlock").show();
	} else {
		$("#freedomToTakeRmploymentBlock").hide();
	}

	if($('input[name=school_data]:checked').val() == '1'){
		$("#schoolDataBlock").show();
	} else {
		$("#schoolDataBlock").hide();
	}

	if($('input[name=college_data]:checked').val() == '1'){
		$("#collegeDataBlock").show();
	} else {
		$("#collegeDataBlock").hide();
	}

	if($('input[name=university_data]:checked').val() == '1'){
		$("#universityDataBlock").show();
	} else {
		$("#universityDataBlock").hide();
	}

	var unemployment = $( "#unemployment option:selected" ).val();
    if (unemployment == 1){
		$("#unemploymentBlock").show();
		$("#addNewUnemploymentBlock").show();
	} else {
		$("#unemploymentBlock").hide();
		$("#addNewUnemploymentBlock").hide();
	}


	//---------------------------------------------------------------------------//
});

$('#start_date, #naturalisation_certificate_date, #employment_date_from, #employment_date_to, #se_accountant_known_from, #se_accountant_known_to, #unemployment_date_from, #unemployment_date_to, #school_date_from, #school_date_to, #college_date_from, #college_date_to, #university_date_from, #university_date_to, #reference1_date_from, #reference1_date_to, #reference2_date_from, #reference2_date_to, #date_issued').datepicker({
    autoclose: true,
    format: "dd/mm/yyyy",

});


function checkDateFormat(dateDtring) {
    var pattern = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
    return pattern.test(dateDtring);
}


$(window).click(function(e) {
    var user_dob = $( "#user_dob" ).val();
	if(typeof user_dob != 'undefined' && user_dob.length == 10){
		validate_user_dob(user_dob);
	}

});


//STEP 1 Start
$('.submitStep1').on('click', function() {
    validateStep1Form();
});

function validateStep1Form(){
	$('.submitStep1').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;

	var mother_maiden_last_name = $( "#mother_maiden_last_name" ).val();
	validate_mother_maiden_last_name(mother_maiden_last_name);

	var employement_type = $( "#employement_type option:selected" ).val();
	set_employement_type(employement_type);

	var starte_date = $( "#start_date" ).val();
	validate_starte_date(starte_date);


	// if (employement_type == 'contractor'){
	// 	var subcontractor_company_name = $( "#subcontractor_company_name" ).val();
	// 	validate_subcontractor_company_name(subcontractor_company_name);
	// }
}

function checkErrorBlock(){
	if ($('.submitStep1').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}

//---------------------------------------------------------------------------//
$("#mother_maiden_last_name").focusout(function() {
	var mother_maiden_last_name = $( "#mother_maiden_last_name" ).val();
	validate_mother_maiden_last_name(mother_maiden_last_name);
});

function validate_mother_maiden_last_name(mother_maiden_last_name){
	if(mother_maiden_last_name.length == 0){
		$("#mother_maiden_last_name").parent().addClass('has-error');
		$("#mother_maiden_last_name").next('.help-block').text('Cannot be blank');
		$("#mother_maiden_last_name").next('.help-block').show();
		$('.submitStep1').attr('type','button');
	} else {
		$("#mother_maiden_last_name").parent().removeClass('has-error');
		$("#mother_maiden_last_name").next('.help-block').hide();
	}
	//checkErrorBlock();
}

//---------------------------------------------------------------------------//
$("#employement_type").change(function() {
	var employement_type = $( "#employement_type option:selected" ).val();
	set_employement_type(employement_type);
});

function set_employement_type(employement_type){
	if (employement_type == 'employee' || employement_type.length == 0){
		$("#contractorBlock").hide();
	} else if (employement_type == 'contractor'){
		$("#contractorBlock").show();
	}

	//checkErrorBlock();
}


//---------------------------------------------------------------------------//
$("#starte_date").focusout(function() {
	var starte_date = $( "#starte_date" ).val();
	validate_starte_date(starte_date);
	
});

function validate_starte_date(starte_date){
	var starte_date_raw = starte_date.split('/');
	var starte_date_formatted = new Date(starte_date[2],starte_date[1]-1,starte_date[0]);
	// if  (starte_date.length == 0){
	// 	$("#starte_date").parent().parent().addClass('has-error');
	// 	$("#start_date_error_block").text('Please enter a date.');
	// 	$("#start_date_error_block").show();
	// 	$('.submitStep1').attr('type','button');
	// }else if (starte_date.length != 10 || !checkDateFormat(starte_date) ){
	if (starte_date.length > 0 && (starte_date.length != 10 || !checkDateFormat(starte_date)) ){
		$("#starte_date").parent().parent().addClass('has-error');
		$("#start_date_error_block").text('Date format is invalid.');
		$("#start_date_error_block").show();
		$('.submitStep1').attr('type','button');
	}else{
		$("#starte_date").parent().parent().removeClass('has-error');
		$("#submitStep1").hide();
	}

	
}

//---------------------------------------------------------------------------//
$("#subcontractor_company_name").focusout(function() {
	var subcontractor_company_name = $( "#subcontractor_company_name" ).val();
	validate_subcontractor_company_name(subcontractor_company_name);
});

function validate_subcontractor_company_name(subcontractor_company_name){
	if(subcontractor_company_name.length == 0){
		$("#subcontractor_company_name").parent().addClass('has-error');
		$("#subcontractor_company_name").next('.help-block').text('Cannot be blank');
		$("#subcontractor_company_name").next('.help-block').show();
		$('.submitStep1').attr('type','button');
	} else {
		$("#subcontractor_company_name").parent().removeClass('has-error');
		$("#subcontractor_company_name").next('.help-block').hide();
	}
	//checkErrorBlock();
}

//---------------------------------------------------------------------------//
//STEP 1 END


//STEP 1 Extra Start
//---------------------------------------------------------------------------//

$('.submitStep1Extra').on('click', function() {
    validateStep1ExtraForm();
});

function validateStep1ExtraForm(){
	$('.submitStep1Extra').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;

}


//---------------------------------------------------------------------------//
$("#selfemployment").change(function() {
	check_selfemployment();
});
function check_selfemployment(){
	var selfemployment = $( "#selfemployment option:selected" ).val();
	if (selfemployment == 1){
		$("#selfemploymentOptionsBlock").show();
	} else{
		$("#selfemploymentOptionsBlock").hide();
	}

	check_selfemployment_documentation_option();
}

//---------------------------------------------------------------------------//
$("#selfemployment_documentation_option").change(function() {
	check_selfemployment_documentation_option();
});
function check_selfemployment_documentation_option(){
	$("#selfemploymentDocumentationBlock").hide();
	$("#selfemploymentAccountantBlock").hide();
	var selfemployment = $( "#selfemployment option:selected" ).val();
	if (selfemployment == 1){
		var selfemployment_documentation_option = $( "#selfemployment_documentation_option option:selected" ).val();
		if (selfemployment_documentation_option == 'self_assessment'){
			$("#selfemploymentAccountantBlock").hide();
			$("#selfemploymentDocumentationBlock").show();

			//remove required
			$('#se_accountant_known_from').removeAttr('required');
			$('#se_accountant_known_to').removeAttr('required');
			$('#se_accountant_name').removeAttr('required');
			$('#se_accountant_address').removeAttr('required');
			$('#se_accountant_town').removeAttr('required');
			$('#se_accountant_postcode').removeAttr('required');
			$('#se_accountant_email').removeAttr('required');
			$('#se_accountant_contact_number').removeAttr('required');

		} else if (selfemployment_documentation_option == 'accountant'){
			$("#selfemploymentDocumentationBlock").hide();
			$("#selfemploymentAccountantBlock").show();

			//add required
			$('#se_accountant_known_from').attr('required', 'required');
			$('#se_accountant_known_to').attr('required', 'required');
			$('#se_accountant_name').attr('required', 'required');
			$('#se_accountant_address').attr('required', 'required');
			$('#se_accountant_town').attr('required', 'required');
			$('#se_accountant_postcode').attr('required', 'required');
			$('#se_accountant_email').attr('required', 'required');
			$('#se_accountant_contact_number').attr('required', 'required');
		}
	}

}

//---------------------------------------------------------------------------//
$("#se_accountant_known_from").focusout(function() {
	
	validate_se_accountant_known_from();
	
});

function validate_se_accountant_known_from(){
	var se_accountant_known_from = $( "#se_accountant_known_from" ).val();
	var user_dob = $( "#validate_dob" ).val();
	var user_dob_raw = user_dob.split('-');
	var user_dob_formatted = new Date(user_dob_raw[0],user_dob_raw[1]-1,user_dob_raw[2]);

	var date_limit = new Date('1900','00','01');
	var today = new Date();

	var se_accountant_known_from_formatted = new Date(se_accountant_known_from.split("/").reverse().join("-"));

	if  (se_accountant_known_from.length == 0){
		$("#se_accountant_known_from").parent().parent().addClass('has-error');
		$("#se_accountant_known_from_error_block").text('Please enter a date between now and your date of birth');
		$("#se_accountant_known_from_error_block").show();
		$('.submitStep2').attr('type','button');
	}else if (se_accountant_known_from.length != 10 || !checkDateFormat(se_accountant_known_from) ){
		$("#se_accountant_known_from").parent().parent().addClass('has-error');
		$("#se_accountant_known_from_error_block").text('Date format is invalid.');
		$("#se_accountant_known_from_error_block").show();
		$('.submitStep2').attr('type','button');
	}else{
		$("#se_accountant_known_from").parent().parent().removeClass('has-error');
		$("#se_accountant_known_from_error_block").hide();

		if(se_accountant_known_from_formatted < date_limit){
			$("#se_accountant_known_from").parent().parent().addClass('has-error');
			$("#se_accountant_known_from_error_block").text('Date cannot be before 01 January 1900.');
			$("#se_accountant_known_from_error_block").show();
			$('.submitStep2').attr('type','button');
		} else if(se_accountant_known_from_formatted > today){
			$("#se_accountant_known_from").parent().parent().addClass('has-error');
			$("#se_accountant_known_from_error_block").text('Date cannot be in the future.');
			$("#se_accountant_known_from_error_block").show();
			$('.submitStep2').attr('type','button');
		} else if(se_accountant_known_from_formatted < user_dob_formatted){
			$("#se_accountant_known_from").parent().parent().addClass('has-error');
			$("#se_accountant_known_from_error_block").text('Please enter a date between now and your date of birth.');
			$("#se_accountant_known_from_error_block").show();
			$('.submitStep2').attr('type','button');
		}
	}

	
}

//---------------------------------------------------------------------------//
$("#se_accountant_known_to").focusout(function() {
	validate_se_accountant_known_to();
	
});

function validate_se_accountant_known_to(){
	var se_accountant_known_from = $( "#se_accountant_known_from" ).val();
	var se_accountant_known_to = $( "#se_accountant_known_to" ).val();

	var user_dob = $( "#validate_dob" ).val();
	var user_dob_raw = user_dob.split('-');
	var user_dob_formatted = new Date(user_dob_raw[0],user_dob_raw[1]-1,user_dob_raw[2]);

	var date_limit = new Date('1900','00','01');
	var today = new Date();

	var se_accountant_known_to_formatted = new Date(se_accountant_known_to.split("/").reverse().join("-"));
	var se_accountant_known_from_formatted = new Date(se_accountant_known_from.split("/").reverse().join("-"));

	if  (se_accountant_known_to.length == 0){
		$("#se_accountant_known_to").parent().parent().addClass('has-error');
		$("#se_accountant_known_to_error_block").text('Please enter a date between now and your date of birth');
		$("#se_accountant_known_to_error_block").show();
		$('.submitStep2').attr('type','button');
	}else if (se_accountant_known_to.length != 10 || !checkDateFormat(se_accountant_known_to) ){
		$("#se_accountant_known_to").parent().parent().addClass('has-error');
		$("#se_accountant_known_to_error_block").text('Date format is invalid.');
		$("#se_accountant_known_to_error_block").show();
		$('.submitStep2').attr('type','button');
	}else{
		$("#se_accountant_known_to").parent().parent().removeClass('has-error');
		$("#se_accountant_known_to_error_block").hide();

		if(se_accountant_known_to_formatted < date_limit){
			$("#se_accountant_known_to").parent().parent().addClass('has-error');
			$("#se_accountant_known_to_error_block").text('Date cannot be before 01 January 1900.');
			$("#se_accountant_known_to_error_block").show();
			$('.submitStep2').attr('type','button');
		} else if(se_accountant_known_to_formatted > today){
			$("#se_accountant_known_to").parent().parent().addClass('has-error');
			$("#se_accountant_known_to_error_block").text('Date cannot be in the future.');
			$("#se_accountant_known_to_error_block").show();
			$('.submitStep2').attr('type','button');
		} else if(se_accountant_known_to_formatted < user_dob_formatted){
			$("#se_accountant_known_to").parent().parent().addClass('has-error');
			$("#se_accountant_known_to_error_block").text('Please enter a date between now and your date of birth.');
			$("#se_accountant_known_to_error_block").show();
			$('.submitStep2').attr('type','button');
		} else if(se_accountant_known_to_formatted < se_accountant_known_from_formatted){
			$("#se_accountant_known_to").parent().parent().addClass('has-error');
			$("#se_accountant_known_to_error_block").text('Period Known To cannot be before Period Known From.');
			$("#se_accountant_known_to_error_block").show();
			$('.submitStep2').attr('type','button');
		}
	}

	
}

//---------------------------------------------------------------------------//




//---------------------------------------------------------------------------//
//STEP 1 Extra END


//STEP 2 Start
//---------------------------------------------------------------------------//

$('.submitStep2').on('click', function() {
    validateStep2Form();
});

function validateStep2Form(){
	$('.submitStep2').attr('type','submit');
	$('#errorMessageBlock').hide();
	//var lawfully_resident_in_uk = $("#lawfully_resident_in_uk option:selected").val();
	//validate_lawfully_resident(optionselected);

	var formValidate = true;	
}

//---------------------------------------------------------------------------//
$("#lawfully_resident_in_uk").change(function() {
	var lawfully_resident_in_uk = $("#lawfully_resident_in_uk option:selected").val();
	validate_lawfully_resident(lawfully_resident_in_uk);
});
function validate_lawfully_resident(optionselected){
	if (optionselected == 0){
		$("#validateLawfullyResidentWarning").show();
	} else{
		$("#validateLawfullyResidentWarning").hide();
	}
}

function checkErrorBlock(){
	if ($('.submitStep2').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}


//---------------------------------------------------------------------------//
$("#national_identity_card").change(function() {
	var national_identity_card = $( "#national_identity_card option:selected" ).val();
	set_national_identity_card_block(national_identity_card);
});

function set_national_identity_card_block(national_identity_card){
	if (national_identity_card == 1){
		$("#nationalIdentityCardBlock").show();
	} else{
		$("#nationalIdentityCardBlock").hide();
	}
}

//---------------------------------------------------------------------------//
$("#passport").change(function() {
	var passport = $( "#passport option:selected" ).val();
	set_passport_block(passport);
});
function set_passport_block(passport){
	if (passport == 1){
		$("#addNewPassportBlock").show();
	} else{
		$("#addNewPassportBlock").hide();
	}
}
//---------------------------------------------------------------------------//
$(document).on("click", '#save_passport_details', function() {
	var BPSSApplicationID = "<?php if(isset($BPSSApplication->id) && $BPSSApplication->id > 0 ) echo $BPSSApplication->id; else echo'0'; ?>";
	save_passport_details(BPSSApplicationID);
});
function save_passport_details(BPSSApplicationID){
	var passport_number = $( "#passport_number" ).val();
	var country_of_issue = $("#country_of_issue option:selected").val();
	var country_of_issue_name = $("#country_of_issue option:selected").text();

	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/addPassportDetails", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSApplicationID, "passport_number":passport_number, "country_of_issue":country_of_issue},
        success: function(result){
        	var saveResult = JSON.parse(result);
        	if(saveResult.status == 1){
        		var passportLine = '<div class="row" id="passport_block_'+saveResult.passportID+'"><div class="col-md-4">';
        		passportLine += passport_number;
        		passportLine += '</div>';
        		passportLine += '<div class="col-md-4">';
        		passportLine += country_of_issue_name;
        		passportLine += '</div>';
        		passportLine += '<div class="col-md-2" style=" padding-left:0;">';
        		passportLine += '<button id="remove_passport_block_'+saveResult.passportID+'" type="button" passport-id="'+saveResult.passportID+'" class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>';
        		passportLine += '</div>';
        		passportLine += '<hr />';
        		passportLine += '</div>';
        		$('#passportsListBlock').append(passportLine);
      		} else {
      			alert('Error: Please try again!');
      		}
        },
        error: function(result){
          alert('Error: Please try again!');
        },
	});
}

//---------------------------------------------------------------------------//

$(document).on("click", '[id^="remove_passport_block_"]', function() {
	var passportID = $(this).attr('passport-id');
	var BPSSApplicationID = "<?php if(isset($BPSSApplication->id) && $BPSSApplication->id > 0 ) echo $BPSSApplication->id; else echo'0'; ?>";
    remove_passport_details(BPSSApplicationID, passportID);
});
function remove_passport_details(BPSSApplicationID, passportID){
	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/removePassportDetails", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSApplicationID, "passportID":passportID},
        success: function(result){
        	var deleteResult = JSON.parse(result);
        	if(deleteResult.status == 1){
        		$("#passport_block_"+passportID).remove();
      		} else {
      			alert('Error: Please try again!');
      		}
        },
        error: function(result){
          alert('Error: Please try again!');
        },
	});
}


//---------------------------------------------------------------------------//
$("#dual_citizenship").change(function() {
	var dual_citizenship = $( "#dual_citizenship option:selected" ).val();
	set_dual_citizenship_block(dual_citizenship);
});

function set_dual_citizenship_block(dual_citizenship){
	if (dual_citizenship == 1){
		$("#dualCitizenshipBlock").show();
	} else{
		$("#dualCitizenshipBlock").hide();
	}
}

//---------------------------------------------------------------------------//
$(document).on("click", '#save_citizenshipCountry_details', function() {
	var BPSSApplicationID = "<?php if(isset($BPSSApplication->id) && $BPSSApplication->id > 0 ) echo $BPSSApplication->id; else echo'0'; ?>";
	save_citizenshipCountry_details(BPSSApplicationID);
});
function save_citizenshipCountry_details(BPSSApplicationID){
	var country_of_nationality = $("#country_of_nationality option:selected").val();
	var country_of_nationality_name = $("#country_of_nationality option:selected").text();

	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/addCitizenshipCountry", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSApplicationID, "country_of_nationality":country_of_nationality},
        success: function(result){
        	var saveResult = JSON.parse(result);
        	if(saveResult.status == 1){
        		var countryLine = '<div class="row" id="citizenshipCountry_block_'+saveResult.countryID+'"><div class="col-md-4">';
        		countryLine += country_of_nationality_name;
        		countryLine += '</div>';
        		countryLine += '<div class="col-md-2" style=" padding-left:0;">';
        		countryLine += '<button id="remove_citizenshipCountry_block_'+saveResult.countryID+'" type="button" citizenshipCountry-id="'+saveResult.countryID+'" class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>';
        		countryLine += '</div>';
        		countryLine += '</div>';
        		$('#dualCitizenshipAjaxBlock').append(countryLine);
      		} else {
      			alert('Error: Please try again!');
      		}
        },
        error: function(result){
          alert('Error: Please try again!');
        },
	});
}

//---------------------------------------------------------------------------//

$(document).on("click", '[id^="remove_citizenshipCountry_block_"]', function() {
	var citizenshipCountry = $(this).attr('citizenshipCountry-id');
	var BPSSApplicationID = "<?php if(isset($BPSSApplication->id) && $BPSSApplication->id > 0 ) echo $BPSSApplication->id; else echo'0'; ?>";
    remove_citizenshipCountry_details(BPSSApplicationID, citizenshipCountry);
});
function remove_citizenshipCountry_details(BPSSApplicationID, citizenshipCountry){
	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/removeCitizenshipCountry", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSApplicationID, "citizenshipCountry":citizenshipCountry},
        success: function(result){
        	var deleteResult = JSON.parse(result);
        	if(deleteResult.status == 1){
        		$("#citizenshipCountry_block_"+citizenshipCountry).remove();
      		} else {
      			alert('Error: Please try again!');
      		}
        },
        error: function(result){
          alert('Error: Please try again!');
        },
	});
}

//---------------------------------------------------------------------------//
$("#former_nationality").change(function() {
	var former_nationality = $( "#former_nationality option:selected" ).val();
	set_former_nationality_block(former_nationality);
});

function set_former_nationality_block(former_nationality){
	if (former_nationality == 1){
		$("#formerNationalityBlock").show();
	} else{
		$("#formerNationalityBlock").hide();
	}
}

//---------------------------------------------------------------------------//
$("#subject_to_immigration_control").change(function() {
	var subject_to_immigration_control = $( "#subject_to_immigration_control option:selected" ).val();
	set_subject_to_immigration_control_block(subject_to_immigration_control);
});

function set_subject_to_immigration_control_block(subject_to_immigration_control){
	if (subject_to_immigration_control == 1){
		$("#immigrationControlBlock").show();
	} else{
		$("#immigrationControlBlock").hide();
	}
}

//---------------------------------------------------------------------------//
$("#continued_residence_restrictions").change(function() {
	var continued_residence_restrictions = $( "#continued_residence_restrictions option:selected" ).val();
	set_continued_residence_restrictions_block(continued_residence_restrictions);
});

function set_continued_residence_restrictions_block(continued_residence_restrictions){
	if (continued_residence_restrictions == 1){
		$("#residenceRestrictionsBlock").show();
	} else{
		$("#residenceRestrictionsBlock").hide();
	}
}

//---------------------------------------------------------------------------//
$("#freedom_to_take_employment").change(function() {
	var freedom_to_take_employment = $( "#freedom_to_take_employment option:selected" ).val();
	set_freedom_to_take_employment_block(freedom_to_take_employment);
});

function set_freedom_to_take_employment_block(freedom_to_take_employment){
	if (freedom_to_take_employment == 1){
		$("#freedomToTakeRmploymentBlock").show();
	} else{
		$("#freedomToTakeRmploymentBlock").hide();
	}
}



//---------------------------------------------------------------------------//
//STEP 2 END



//STEP 3 Start
//---------------------------------------------------------------------------//

$('.submitStep3').on('click', function() {
    validateStep3Form();
});

function validateStep3Form(){
	$('.submitStep3').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;
	validate_employment_history();
	validate_reference1_period();
	validate_reference2_period();
}

function validate_employment_history(){
	//check if any employment history exists
	check_employment_set
	if($("#check_employment_set").val() == 1){
		$("#employment_error_block").removeClass('has-error');
		$("#employment_error_block").hide();
	} else {
			$("#employment_error_block").addClass('has-error');
		$("#employment_error_block").show();
		$('.submitStep3').attr('type','button');
	}
	//checkErrorBlock();
}

function checkErrorBlock(){
	if ($('.submitStep3').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}


//---------------------------------------------------------------------------//
$('#school_data').click(function() {
	if($('input[name=school_data]:checked').val() == '1'){
		$("#schoolDataBlock").show();
	} else {
		$("#schoolDataBlock").hide();
	}
});

//---------------------------------------------------------------------------//
$('#college_data').click(function() {
	if($('input[name=college_data]:checked').val() == '1'){
		$("#collegeDataBlock").show();
	} else {
		$("#collegeDataBlock").hide();
	}
});

//---------------------------------------------------------------------------//
$('#university_data').click(function() {
	if($('input[name=university_data]:checked').val() == '1'){
		$("#universityDataBlock").show();
	} else {
		$("#universityDataBlock").hide();
	}
});


//---------------------------------------------------------------------------//
$(document).on("click", '#save_employment_details', function() {
	var BPSSApplicationID = "<?php if(isset($BPSSApplication->id) && $BPSSApplication->id > 0 ) echo $BPSSApplication->id; else echo'0'; ?>";
	save_employment_details(BPSSApplicationID);	
});
function save_employment_details(BPSSApplicationID){
	var validForm = true;
	var employment_date_from = $( "#employment_date_from" ).val();
	var employment_date_to = $( "#employment_date_to" ).val();
	if($("#referee_allow_contact").is(':checked')){
		var referee_allow_contact = 1;
	}else{
		var referee_allow_contact = 0;
	}
	if($("#p60_enclosed").is(':checked')){
		var p60_enclosed = 1;
	}else{
		var p60_enclosed = 0;
	}
	var company_name = $( "#company_name" ).val();
	var email_address = $( "#email_address" ).val();
	var company_address_line = $( "#company_address_line" ).val();
	var company_address_town = $( "#company_address_town" ).val();
	var company_address_postcode = $( "#company_address_postcode" ).val();
	if(employment_date_from.length < 10){
		$("#employment_date_from_error_block").addClass('text-red');
		$("#employment_date_from_error_block").show();
		validForm = false;
	} else {
		$("#employment_date_from_error_block").removeClass('text-red');
		$("#employment_date_from_error_block").hide();
	}

	if(employment_date_to.length < 10){
		$("#employment_date_to_error_block").addClass('text-red');
		$("#employment_date_to_error_block").show();
		validForm = false;
	} else {
		$("#employment_date_to_error_block").removeClass('text-red');
		$("#employment_date_to_error_block").hide();
	}

	if(company_name.length < 1){
		$("#company_name_error_block").addClass('text-red');
		$("#company_name_error_block").show();
		validForm = false;
	} else {
		$("#company_name_error_block").removeClass('text-red');
		$("#company_name_error_block").hide();
	}
	if(validForm){
		$.ajax({
			url: "<?php echo env('APP_URL'); ?>" + "applicant/addEmploymentDetails", 
	        method: "POST",
	        data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSApplicationID, "employment_date_from":employment_date_from, "employment_date_to":employment_date_to, "referee_allow_contact":referee_allow_contact, "p60_enclosed":p60_enclosed, "company_name":company_name, "email_address":email_address, "company_address_line":company_address_line, "company_address_town":company_address_town, "company_address_postcode":company_address_postcode},
	        success: function(result){
	        	var saveResult = JSON.parse(result);
	        	if(saveResult.status == 1){
	        		var employmentLine = '<div class="row" id="employment_block_'+saveResult.employmentID+'">';
	        		employmentLine += '<div class="col-md-12">';

	        		employmentLine += '<div class="row">';
	        		employmentLine += '<div class="col-md-4">';
	        		employmentLine += 'Period Covered: '+employment_date_from+' - '+ employment_date_to;
	        		employmentLine += '</div>';
	        		employmentLine += '<div class="col-md-4">';
	        		employmentLine += 'Company Name: '+company_name;
	        		employmentLine += '</div>';
	        		employmentLine += '<div class="col-md-4">';
	        		employmentLine += 'Email: '+email_address;
	        		employmentLine += '</div>';
	        		employmentLine += '</div>';

	        		employmentLine += '<div class="row">';
	        		employmentLine += '<div class="col-md-12">';
	        		employmentLine += 'Address: '+company_address_line;
	        		employmentLine += '</div>';
	        		employmentLine += '</div>';

	        		employmentLine += '<div class="row">';
	        		employmentLine += '<div class="col-md-3">';
	        		employmentLine += 'Town: '+company_address_town;
	        		employmentLine += '</div>';
	        		employmentLine += '<div class="col-md-3">';
	        		employmentLine += 'Postcode: '+company_address_postcode;
	        		employmentLine += '</div>';
	        		employmentLine += '<div class="col-md-4">';
	        		employmentLine += 'Allow referee contact: '; if(referee_allow_contact == '1') {employmentLine += 'No';} else {employmentLine += 'Yes';}
	        		employmentLine += ' | P60 enclosed: '; if(p60_enclosed == '1') {employmentLine += 'Yes';} else {employmentLine += 'No';}
	        		employmentLine += '</div>';
	        		employmentLine += '<div class="col-md-2" style=" padding-left:0;">';
	        		employmentLine += '<button id="remove_employment_block_'+saveResult.employmentID+'" type="button" employment-id="'+saveResult.employmentID+'" class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>';
	        		employmentLine += '</div>';
	        		employmentLine += '</div>';

	        		employmentLine += '<hr />';
	        		employmentLine += '</div>';

	        		$('#employmentBlock').append(employmentLine);
	        		//clear fields
	        		$( "#employment_date_from" ).val('');
	        		$( "#employment_date_to" ).val('');
	        		$('#referee_allow_contact').attr('checked', false);
	        		$('#p60_enclosed').attr('checked', false);
	        		$( "#company_name" ).val('');
	        		$( "#email_address" ).val('');
	        		$( "#company_address_line" ).val('');
	        		$( "#company_address_town" ).val('');
	        		$( "#company_address_postcode" ).val('');
	        		window.location.href = "<?php echo env('APP_URL'); ?>" + "applicant/bpssStep3";
	      		} else {
	      			alert('Error: Please try again!');
	      		}
	        },
	        error: function(result){
	          alert('Error: Please try again!');
	        },
		});
	}
}



//---------------------------------------------------------------------------//

$(document).on("click", '[id^="remove_employment_block_"]', function() {
	var employmentID = $(this).attr('employment-id');
	var BPSSApplicationID = "<?php if(isset($BPSSApplication->id) && $BPSSApplication->id > 0 ) echo $BPSSApplication->id; else echo'0'; ?>";
    remove_employment_details(BPSSApplicationID, employmentID);
});
function remove_employment_details(BPSSApplicationID, employmentID){
	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/removeEmploymentDetails", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSApplicationID, "employmentID":employmentID},
        success: function(result){
        	var deleteResult = JSON.parse(result);
        	if(deleteResult.status == 1){
        		$("#employment_block_"+employmentID).remove();
      		} else {
      			alert('Error: Please try again!');
      		}
    	window.location.href = "<?php echo env('APP_URL'); ?>" + "applicant/bpssStep3";
        },
        error: function(result){
          alert('Error: Please try again!');
        },
	});
}

//---------------------------------------------------------------------------//
$("#unemployment").change(function() {
	var unemployment = $( "#unemployment option:selected" ).val();
	set_unemployment_block(unemployment);
});
function set_unemployment_block(unemployment){
	if (unemployment == 1){
		$("#unemploymentBlock").show();
		$("#addNewUnemploymentBlock").show();
	} else{
		$("#unemploymentBlock").hide();
		$("#addNewUnemploymentBlock").hide();

	}
}

//---------------------------------------------------------------------------//
$(document).on("click", '#save_unemployment_details', function() {
	var BPSSApplicationID = "<?php if(isset($BPSSApplication->id) && $BPSSApplication->id > 0 ) echo $BPSSApplication->id; else echo'0'; ?>";
	save_unemployment_details(BPSSApplicationID);
});
function save_unemployment_details(BPSSApplicationID){
	var unemployment_date_from = $( "#unemployment_date_from" ).val();
	var unemployment_date_to = $( "#unemployment_date_to" ).val();

	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/addUnemploymentDetails", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSApplicationID, "unemployment_date_from":unemployment_date_from, "unemployment_date_to":unemployment_date_to},
        success: function(result){

        	var saveResult = JSON.parse(result);
        	if(saveResult.status == 1){
        		var unemploymentLine = '<div class="row" id="unemployment_block_'+saveResult.unemploymentID+'">';
        		unemploymentLine += '<div class="col-md-6">';
        		unemploymentLine += 'Period Covered: '+unemployment_date_from+' - '+ unemployment_date_to;
        		unemploymentLine += '</div>';
        		unemploymentLine += '<div class="col-md-2" style=" padding-left:0;">';
        		unemploymentLine += '<button id="remove_unemployment_block_'+saveResult.unemploymentID+'" type="button" unemployment-id="'+saveResult.unemploymentID+'" class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>';
        		unemploymentLine += '</div>';

        		unemploymentLine += '<hr />';
        		unemploymentLine += '</div>';

        		$('#unemploymentBlock').append(unemploymentLine);
        		//clear fields
        		$( "#unemployment_date_from" ).val('');
        		$( "#unemployment_date_to" ).val('');
      		} else {
      			alert('Error: Please try again!');
      		}
        },
        error: function(result){
          alert('Error: Please try again!');
        },
	});
}



//---------------------------------------------------------------------------//

$(document).on("click", '[id^="remove_unemployment_block_"]', function() {
	var unemploymentID = $(this).attr('unemployment-id');
	var BPSSApplicationID = "<?php if(isset($BPSSApplication->id) && $BPSSApplication->id > 0 ) echo $BPSSApplication->id; else echo'0'; ?>";
    remove_unemployment_details(BPSSApplicationID, unemploymentID);
});
function remove_unemployment_details(BPSSApplicationID, unemploymentID){
	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/removeUnemploymentDetails", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSApplicationID, "unemploymentID":unemploymentID},
        success: function(result){
        	var deleteResult = JSON.parse(result);
        	if(deleteResult.status == 1){
        		$("#unemployment_block_"+unemploymentID).remove();
      		} else {
      			alert('Error: Please try again!');
      		}
        },
        error: function(result){
          alert('Error: Please try again!');
        },
	});
}

$("#reference1_date_to").focusout(function() {
	validate_reference1_period();
});

function validate_reference1_period(){
	var today = new Date();

	var year  = new Date().getFullYear();
   	var month = new Date().getMonth();
   	var day   = new Date().getDate();
   	var pastdate  = new Date(year - 5, month, day);

   	var reference1_date_from = $( "#reference1_date_from" ).val();
	var reference1_date_from_raw = reference1_date_from.split('/');
	var reference1_date_from_formatted = new Date(reference1_date_from_raw[2],reference1_date_from_raw[1]-1,reference1_date_from_raw[0]);

	var reference1_date_to = $( "#reference1_date_to" ).val();
	var reference1_date_to_raw = reference1_date_to.split('/');
	var reference1_date_to_formatted = new Date(reference1_date_to_raw[2],reference1_date_to_raw[1]-1,reference1_date_to_raw[0]);

	var knownPeriod1 = Math.floor((reference1_date_to_formatted-reference1_date_from_formatted) / (365.25 * 24 * 60 * 60 * 1000));
	if(knownPeriod1 < 5){
		$("#reference1_error_block").show();
		$('.submitStep3').attr('type','button');
	} else {
		$("#reference1_error_block").hide();
	}

}

$("#reference2_date_to").focusout(function() {
	validate_reference2_period();
});

function validate_reference2_period(){
	var today = new Date();

	var year  = new Date().getFullYear();
   	var month = new Date().getMonth();
   	var day   = new Date().getDate();
   	var pastdate  = new Date(year - 5, month, day);

	var reference2_date_from = $( "#reference2_date_from" ).val();
	var reference2_date_from_raw = reference2_date_from.split('/');
	var reference2_date_from_formatted = new Date(reference2_date_from_raw[2],reference2_date_from_raw[1]-1,reference2_date_from_raw[0]);

	var reference2_date_to = $( "#reference2_date_to" ).val();
	var reference2_date_to_raw = reference2_date_to.split('/');
	var reference2_date_to_formatted = new Date(reference2_date_to_raw[2],reference2_date_to_raw[1]-1,reference2_date_to_raw[0]);

	var knownPeriod2 = Math.floor((reference2_date_to_formatted-reference2_date_from_formatted) / (365.25 * 24 * 60 * 60 * 1000));
	if(knownPeriod2 < 5){
		$("#reference2_error_block").show();
		$('.submitStep3').attr('type','button');
	} else {
		$("#reference2_error_block").hide();
	}

}




//---------------------------------------------------------------------------//
//STEP 3 END


//STEP 4 Start
//---------------------------------------------------------------------------//

$('.submitStep4').on('click', function() {
    validateStep4Form();
});

function validateStep4Form(){
	$('.submitStep4').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;

	check_police_act_disclosure();
	validate_declaration_consent();
}

function checkErrorBlock(){
	if ($('.submitStep4').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}


//---------------------------------------------------------------------------//
// function check_police_act_disclosure(){
// 	if(!$("#consent_responsible_body_submitting_dbs_application").is(':checked') || !$("#electronic_notification_state").is(':checked') || !$("#read_and_understood_dbs_Statement").is(':checked')){
// 		$("#police_disclosure_error").show();
// 		$('.submitStep4').attr('type','button');
// 	} else {
// 		$("#police_disclosure_error").hide();
// 	}
// }

//---------------------------------------------------------------------------//
$("#is_consent_provided").change(function() {
	validate_declaration_consent();
	
});

//---------------------------------------------------------------------------//
function validate_declaration_consent(){
	var is_consent_provided = $("#is_consent_provided option:selected").val();
	if(is_consent_provided == 1){
		$("#consent_provided_error").hide();
	} else {
		$( "#consent_provided_error" ).show();
		$('.submitStep4').attr('type','button');
	}
}

//---------------------------------------------------------------------------//
//STEP 4 END


//STEP 5 Start
//---------------------------------------------------------------------------//

$('.submitStep5').on('click', function() {
    validateStep5Form();
});

function validateStep5Form(){
	$('.submitStep5').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;

	validate_convicted_by_court();
	validate_convicted_by_court_martial();
	validate_background_reliability();

}
//---------------------------------------------------------------------------//
function validate_convicted_by_court(){
	if( ($("#convicted_by_court_yes").is(':checked') || $("#convicted_by_court_no").is(':checked'))){
		$("#convicted_by_court_error").hide();
	} else {
		$( "#convicted_by_court_error" ).show();
		$('.submitStep5').attr('type','button');
	}
}

function validate_convicted_by_court_martial(){
	if( ($("#convicted_by_court_martial_yes").is(':checked') || $("#convicted_by_court_martial_no").is(':checked'))){
		$("#convicted_by_court_martial_error").hide();
	} else {
		$( "#convicted_by_court_martial_error" ).show();
		$('.submitStep5').attr('type','button');
	}
}

function validate_background_reliability(){
	if( ($("#background_reliability_yes").is(':checked') || $("#background_reliability_no").is(':checked'))){
		$("#background_reliability_error").hide();
	} else {
		$( "#background_reliability_error" ).show();
		$('.submitStep5').attr('type','button');
	}
}


function checkErrorBlock(){
	if ($('.submitStep5').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}


//---------------------------------------------------------------------------//
//STEP 5 END


//STEP 6 Start
//---------------------------------------------------------------------------//

$('.submitStep6').on('click', function() {
    validateStep6Form();
});

function validateStep6Form(){
	$('.submitStep6').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;

}

function checkErrorBlock(){
	if ($('.submitStep6').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}


//---------------------------------------------------------------------------//
//STEP 6 END






//ADMIN SUBMISSION START
//---------------------------------------------------------------------------//
$('.submitToDBS').on('click', function() {
    validateSubmitToDBS();
});

function validateSubmitToDBS(){
	$('.submitToDBS').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;

	confirm_2_documents_or_further_details();
	
	confirm_entered_password();

	if ($('.submitToDBS').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}

//---------------------------------------------------------------------------//
function confirm_2_documents_or_further_details(){
	var further_details = $( "#admin_further_evidence_details" ).val();
	if( ($("#admin_confirm_valid_nino").is(':checked') && $("#admin_confirm_valid_dln").is(':checked')) || ($("#admin_confirm_valid_nino").is(':checked') && $("#admin_confirm_valid_passport").is(':checked')) || ($("#admin_confirm_valid_passport").is(':checked') && $("#admin_confirm_valid_dln").is(':checked')) || further_details.length > 0 ){
		$("#admin_minimum_one_box_error_block").hide();
	} else{
		$("#admin_minimum_one_box_error_block").show();
		$('.submitToDBS').attr('type','button');
	    
	}
}

//---------------------------------------------------------------------------//
function confirm_entered_password(){
	var validation_password = $( "#submit_to_dbs_validation_password" ).val();

	if(validation_password.length <= 0 ){
		$("#submit_to_dbs_validation_password").parent().addClass('has-error');
		$("#validation_password_error_block").show();
		$('.submitToDBS').attr('type','button');
	} else{
		$("#submit_to_dbs_validation_password").parent().removeClass('has-error');
	    $("#validation_password_error_block").hide();
		
	}
}
//---------------------------------------------------------------------------//
//ADMIN SUBMISSION END

</script>