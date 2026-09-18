<script type="text/javascript">



$(document).ready(function() {
	$('#errorMessageBlock').hide();

	//---------------------------------------------------------------------------//
    var purpose_of_check = $( "#user_purpose_of_check option:selected" ).val();
    if (purpose_of_check == 'employment'){
    	//$("#declarationByApplicantBlock").hide();
		$("#employmentSectorBlock, #declarationEmploymentBlock").show();
	}

	if($("#user_checkbox_previous_names").is(':checked')){
	    $("#otherNamesBlock").show();
	}

	var otherNamesSaved = $('[id^="other_names_block_"]').length;
	 $( "#skipOtherNamesValidation" ).val(otherNamesSaved);

	var user_supporting_paper_certificate = $( "#user_supporting_paper_certificate option:selected" ).val();
    if (user_supporting_paper_certificate == '1'){
    	$("#userPaperCertificateAddressOptionsBlock").show();
	} else {
		$("#userPaperCertificateAddressOptionsBlock").hide();
	}

	var newAddress = $('input[name=user_paper_certificate_different_address]:checked').val();
	if (newAddress == 1){
		$( "#currentAddressBlock" ).hide();
		$( "#newAddressBlock" ).show();

	}else {
		$( "#currentAddressBlock" ).show();
		$( "#newAddressBlock" ).hide();
	}
	//---------------------------------------------------------------------------//
});

$('#user_current_address_from, #user_previous_address_from, #user_previous_address_until, #user_dob, #user_other_names_from, #user_other_names_to, #user_supporting_dln_issue_date, #user_supporting_passport_date').datepicker({
    autoclose: true,
    format: "dd/mm/yyyy",

});


function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}

$(document).on("keypress keyup blur", ".onlyNumbers", function(event) {
//$(this).val($(this).val().replace(/[^\d].+/, ""));
    if ((event.which < 48 || event.which > 57) && event.which != 8  && event.which != 0 && event.which != 189 && event.which != 109 && event.which != 45) {//https://css-tricks.com/snippets/javascript/javascript-keycodes/
        event.preventDefault();
    }
});

function lettersAndNumbers(string) {
    var pattern = /^[a-zA-Z0-9 .-]+$/;
    return pattern.test(string);
}

function onlyLetters(string) {
    var pattern = /^[a-zA-Z]+$/;
    return pattern.test(string);
}
function onlyLettersAndHyphen(string) {
    var pattern = /^[a-zA-Z\-]+$/;
    return pattern.test(string);
}
function onlyLettersAndSpaces(string) {
    var pattern = /^[a-zA-Z\s]+$/;
    return pattern.test(string);
}
function validateNameStructure(string) {
    var pattern = /^[a-zA-Z]+$/;
    var pattern2 = /^([a-zA-Z][a-zA-Z '\-]*[a-zA-Z])+$/;
    return (pattern.test(string) || pattern2.test(string));
}
function onlyLettersAndNumbers(string) {
    var pattern = /^[a-zA-Z0-9]+$/;
    return pattern.test(string);
}
function onlyLettersNumbersAndSpace(string) {
    var pattern = /^[a-zA-Z0-9 ]+$/;
    return pattern.test(string);
}
function validateAddressLine(string) {
    var pattern = /^[a-zA-Z0-9 -]+$/;
    return pattern.test(string);
}



function validateNINO(nino){
	var pattern = /^[ABCEGHJKLMNOPRSTWXYZ][ABCEGHJKLMNPRSTWXYZ][0-9]{6}[A-D]{0,1}$/;
    return pattern.test(nino);
}

function validateTemporaryNINO(nino){
	var pattern = /^[Tt][Nn][0-9]{6}[MF\smf]{0,1}$/;
    return pattern.test(nino);
}

function validatePostcodeFormat(postcode){
	var pattern = /^([A-PR-UWYZ0-9][A-HK-Y0-9][AEHMNPRTVXY0-9]?[ABEHMNPRVWXY0-9]? {1,2}[0-9][ABD-HJLN-UW-Z]{2}|GIR 0AA)$/;
    return pattern.test(postcode);
}

function validateDriverLicence(licenceNumber){
	var pattern = /^[a-zA-Z9]{5}\d[0156]\d([0][1-9]|[12]\d|3[01])\d[a-zA-Z9]{2}\d[a-zA-Z]{2}$/;
    return pattern.test(licenceNumber.trim());
}

function validate_postcode(postcode){
	$.ajax({
	    url: "http://api.postcodes.io/postcodes/"+postcode+"/validate", 
	    method: "GET",
	    success: function(searchResult){
     		if (!searchResult.result){
     			$("#user_address_postcode").parent().addClass('has-error');
				$("#user_address_postcode").next('.help-block').text('The postcode does not appear to be valid.');
				$("#user_address_postcode").next('.help-block').show();
				$('.submitStep2').attr('type','button');
      	 	} else {
      	 		$("#user_address_postcode").parent().removeClass('has-error');
				$("#user_address_postcode").next('.help-block').hide();
      	 	}
	    },
	    error: function(result){
	    	//
	    },
	});
}

function validate_postcode_previous_address(previous_address_postcode){
	$.ajax({
	    url: "http://api.postcodes.io/postcodes/"+previous_address_postcode+"/validate", 
	    method: "GET",
	    success: function(searchResult){
     		if (!searchResult.result){
     			$("#user_previous_address_postcode").parent().addClass('has-error');
				$("#user_previous_address_postcode").next('.help-block').text('The postcode does not appear to be valid.');
				$("#user_previous_address_postcode").next('.help-block').show();
				$('.submitStep2').attr('type','button');
      	 	} else {
      	 		$("#user_previous_address_postcode").parent().removeClass('has-error');
				$("#user_previous_address_postcode").next('.help-block').hide();
      	 	}
	    },
	    error: function(result){
	    	//
	    },
	});
}

function validate_postcode_new_address(postcode){
	$.ajax({
	    url: "http://api.postcodes.io/postcodes/"+postcode+"/validate", 
	    method: "GET",
	    success: function(searchResult){
     		if (!searchResult.result){
     			$("#certificate_address_postcode").parent().addClass('has-error');
				$("#certificate_address_postcode").next('.help-block').text('The postcode does not appear to be valid.');
				$("#certificate_address_postcode").next('.help-block').show();
				$('.submitStep5').attr('type','button');
      	 	} else {
      	 		$("#certificate_address_postcode").parent().removeClass('has-error');
				$("#certificate_address_postcode").next('.help-block').hide();
      	 	}
	    },
	    error: function(result){
	    	//
	    },
	});
}

function checkDateFormat(dateDtring) {
    var pattern = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
    return pattern.test(dateDtring);
}


$(window).click(function(e) {
    var user_dob = $( "#user_dob" ).val();
	if(typeof user_dob != 'undefined' && user_dob.length == 10){
		validate_user_dob(user_dob);
	}

	var other_names_from = $( "#user_other_names_from" ).val();
	if(typeof other_names_from != 'undefined' && other_names_from.length == 10){
		validate_other_names_from(other_names_from);
	}

	var other_names_to = $( "#user_other_names_to" ).val();
	if(typeof other_names_to != 'undefined' && other_names_to.length == 10){
		validate_other_names_from(other_names_to);
	}

	var current_address_from = $( "#user_current_address_from" ).val();
	if(typeof current_address_from != 'undefined' && current_address_from.length == 10){
		validate_current_address_from(current_address_from);
	}

	var previous_address_from = $( "#user_previous_address_from" ).val();
	if(typeof previous_address_from != 'undefined' && previous_address_from.length == 10){
		validate_previous_address_from(previous_address_from);
	}

	var previous_address_until = $( "#user_previous_address_until" ).val();
	if(typeof previous_address_until != 'undefined' && previous_address_until.length == 10){
		validate_previous_address_until(previous_address_until);
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
	var purpose_of_check = $( "#user_purpose_of_check option:selected" ).val();
	set_purpose_of_check_options(purpose_of_check);

	validate_terms_accepted();
	validate_privacy_policy_accepted();
	validate_consent_basic_check();
	validate_declaration_by_applicant_accepted();

	if (purpose_of_check == 'employment'){
		var employment_sector = $( "#user_employment_sector option:selected" ).val();
		set_employment_sector_options(employment_sector);
		
		var position_applied_for = $( "#user_position_applied_for" ).val();
		validate_position_applied_for(position_applied_for);

		var name_of_employer = $( "#user_name_of_employer" ).val();
		validate_name_of_employer(name_of_employer);
	}
}

function checkErrorBlock(){
	if ($('.submitStep1').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}



//---------------------------------------------------------------------------//
$("#user_purpose_of_check").change(function() {
	var purpose_of_check = $( "#user_purpose_of_check option:selected" ).val();
	set_purpose_of_check_options(purpose_of_check);
});

function set_purpose_of_check_options(purpose_of_check){
	if (purpose_of_check == 'other' || purpose_of_check =='personal interest' || purpose_of_check.length == 0){
		$("#employmentSectorBlock, #declarationEmploymentBlock").hide();
		$("#declarationByApplicantBlock").show();
	} else if (purpose_of_check == 'employment'){
		//$("#declarationByApplicantBlock").hide();
		$("#employmentSectorBlock, #declarationEmploymentBlock").show();
	}

	if(purpose_of_check.length == 0){
		$("#user_purpose_of_check").parent().addClass('has-error');
		$("#user_purpose_of_check").next('.help-block').show();
		$('.submitStep1').attr('type','button');
	} else {
		$("#user_purpose_of_check").parent().removeClass('has-error');
		$("#user_purpose_of_check").next('.help-block').hide();
	}
	checkErrorBlock();
}

//---------------------------------------------------------------------------//
$("#user_employment_sector").change(function() {
	var employment_sector = $( "#user_employment_sector option:selected" ).val();
	set_employment_sector_options(employment_sector);
});

function set_employment_sector_options(employment_sector){
	if(employment_sector.length == 0){
		$("#user_employment_sector").parent().addClass('has-error');
		$("#user_employment_sector").next('.help-block').show();
		$('.submitStep1').attr('type','button');
	} else {
		$("#user_employment_sector").parent().removeClass('has-error');
		$("#user_employment_sector").next('.help-block').hide();
	}
	checkErrorBlock();
}

//---------------------------------------------------------------------------//
$("#user_position_applied_for").focusout(function() {
	var position_applied_for = $( "#user_position_applied_for" ).val();
	validate_position_applied_for(position_applied_for);
});

function validate_position_applied_for(position_applied_for){
	if(position_applied_for.length == 0){
		$("#user_position_applied_for").parent().addClass('has-error');
		$("#user_position_applied_for").next('.help-block').text('Cannot be blank');
		$("#user_position_applied_for").next('.help-block').show();
		$('.submitStep1').attr('type','button');
	}else if(position_applied_for.length > 60){
		$("#user_position_applied_for").parent().addClass('has-error');
		$("#user_position_applied_for").next('.help-block').text('Position cannot be more than 60 characters in length.');
		$("#user_position_applied_for").next('.help-block').show();
		$('.submitStep1').attr('type','button');
	}else if(!onlyLettersNumbersAndSpace(position_applied_for)){
		$("#user_position_applied_for").parent().addClass('has-error');
		$("#user_position_applied_for").next('.help-block').text('Position an only contain letters, numbers and spaces.');
		$("#user_position_applied_for").next('.help-block').show();
		$('.submitStep1').attr('type','button');
	} else {
		$("#user_position_applied_for").parent().removeClass('has-error');
		$("#user_position_applied_for").next('.help-block').hide();
	}
	checkErrorBlock();
}

//---------------------------------------------------------------------------//
$("#user_name_of_employer").focusout(function() {
	var name_of_employer = $( "#user_name_of_employer" ).val();
	validate_name_of_employer(name_of_employer);
});

function validate_name_of_employer(name_of_employer){
	if(name_of_employer.length == 0){
		$("#user_name_of_employer").parent().addClass('has-error');
		$("#user_name_of_employer").next('.help-block').text('Cannot be blank');
		$("#user_name_of_employer").next('.help-block').show();
		$('.submitStep1').attr('type','button');
	}else if(!onlyLettersNumbersAndSpace(name_of_employer)){
		$("#user_name_of_employer").parent().addClass('has-error');
		$("#user_name_of_employer").next('.help-block').text('The name can only contain letters, numbers and spaces.');
		$("#user_name_of_employer").next('.help-block').show();
		$('.submitStep1').attr('type','button');
	}else if(name_of_employer.length > 60){
		$("#user_name_of_employer").parent().addClass('has-error');
		$("#user_name_of_employer").next('.help-block').text('Position cannot be more than 60 characters in length.');
		$("#user_name_of_employer").next('.help-block').show();
		$('.submitStep1').attr('type','button');
	} else {
		$("#user_name_of_employer").parent().removeClass('has-error');
		$("#user_name_of_employer").next('.help-block').hide();
	}
	checkErrorBlock();
}

//---------------------------------------------------------------------------//
$("#user_terms_accepted").click(function() {
	validate_terms_accepted();
});

function validate_terms_accepted(){
	if($("#user_terms_accepted").is(':checked')){
	    $("#user_terms_accepted").parent().removeClass('has-error');
		$("#user_terms_accepted_error_block").hide();
	} else{
	    $("#user_terms_accepted").parent().addClass('has-error');
		$("#user_terms_accepted_error_block").show();
		$('.submitStep1').attr('type','button');
	}
	checkErrorBlock();
}

//---------------------------------------------------------------------------//
$(document).on("keypress keyup blur", "#user_privacy_policy_accepted", function(event) {
	validate_privacy_policy_accepted();
});

function validate_privacy_policy_accepted(){
	var privacy_policy_accepted = $( "#user_privacy_policy_accepted" ).val().trim();
	if (privacy_policy_accepted.length > 0 && privacy_policy_accepted.toLowerCase() == 'i confirm'){
	    $("#user_privacy_policy_accepted").parent().removeClass('has-error');
		$("#user_privacy_policy_accepted_error_block").hide();
	} else{
	    $("#user_privacy_policy_accepted").parent().addClass('has-error');
		$("#user_privacy_policy_accepted_error_block").show();
		$('.submitStep1').attr('type','button');
	}
	checkErrorBlock();
}

//---------------------------------------------------------------------------//
$(document).on("keypress keyup blur", "#user_consent_basic_check", function(event) {
	validate_consent_basic_check();
});

function validate_consent_basic_check(){
	var consent_basic_check = $( "#user_consent_basic_check" ).val().trim();
	if (consent_basic_check.length > 0 && consent_basic_check.toLowerCase() == 'i agree'){
	    $("#user_consent_basic_check").parent().removeClass('has-error');
		$("#user_consent_basic_check_error_block").hide();
	} else{
	    $("#user_consent_basic_check").parent().addClass('has-error');
		$("#user_consent_basic_check_error_block").show();
		$('.submitStep1').attr('type','button');
	}
	checkErrorBlock();
}


//---------------------------------------------------------------------------//
$(document).on("keypress keyup blur", "#user_declaration_by_applicant_accepted", function(event) {
	validate_declaration_by_applicant_accepted();
});

function validate_declaration_by_applicant_accepted(){
	var declaration_by_applicant = $( "#user_declaration_by_applicant_accepted" ).val().trim();
	if (declaration_by_applicant.length > 0 && declaration_by_applicant.toLowerCase() == 'i confirm'){
	    $("#user_declaration_by_applicant_accepted").parent().removeClass('has-error');
		$("#user_declaration_by_applicant_accepted_error_block").hide();
	} else{
	    $("#user_declaration_by_applicant_accepted").parent().addClass('has-error');
		$("#user_declaration_by_applicant_accepted_error_block").show();
		$('.submitStep1').attr('type','button');
	}
	checkErrorBlock();
}

//---------------------------------------------------------------------------//
//STEP 1 END










//STEP 2 START
//---------------------------------------------------------------------------//
$('.submitStep2').on('click', function() {
    validateStep2Form();
});

function validateStep2Form(){
	$('.submitStep2').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;

	var user_email = $( "#user_email" ).val();
	validate_user_email(user_email);

	var contact_number = $( "#user_contact_number" ).val();
	validate_contact_number(contact_number);

	var mobile_number = $( "#user_mobile_number" ).val();
	validate_mobile_number(mobile_number);

	var address_line_1 = $( "#user_address_line_1" ).val();
	validate_address_line_1(address_line_1);
	var address_line_2 = $( "#user_address_line_2" ).val();
	validate_address_line_2(address_line_2);

	var address_town = $( "#user_address_town" ).val();
	validate_address_town(address_town);

	var address_county = $( "#user_address_county" ).val();
	validate_address_county(address_county);

	var address_postcode = $( "#user_address_postcode" ).val();
	validate_address_postcode(address_postcode);

	var current_address_from = $( "#user_current_address_from" ).val();
	validate_current_address_from(current_address_from);

	if ($('.submitStep2').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}


//---------------------------------------------------------------------------//

$("#user_email").focusout(function() {
	var user_email = $( "#user_email" ).val();
	validate_user_email(user_email);
});

function validate_user_email(user_email){
	if  (!isValidEmailAddress(user_email) || user_email.length == 0){
		$("#user_email").parent().addClass('has-error');
		$("#user_email").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else {
		$("#user_email").parent().removeClass('has-error');
		$("#user_email").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//

$("#user_contact_number").focusout(function() {
	var contact_number = $( "#user_contact_number" ).val();
	validate_contact_number(contact_number);
});

function validate_contact_number(contact_number){
	if  (contact_number.length > 0 && contact_number.length <= 16){
		if(!contact_number.match(/^\d+$/)) {
			$("#user_contact_number").parent().parent().addClass('has-error');
			$("#contact_number_error_block").text('Phone number may contain numbers only.');
			$("#contact_number_error_block").show();
			$('.submitStep2').attr('type','button');
		} else {
			validate_contact_number_country_code();
		}
	}else if(contact_number.length > 16){
		$("#user_contact_number").parent().parent().addClass('has-error');
		$("#contact_number_error_block").text('Phone number cannot be longer that 16 digits.');
		$("#contact_number_error_block").show();
		$('.submitStep2').attr('type','button');
	}else {
		$("#user_contact_number").parent().parent().removeClass('has-error');
		$("#contact_number_error_block").hide();
	}

}

$("#user_contact_number_country_code").change(function() {
	validate_contact_number_country_code()
});

function validate_contact_number_country_code(){
	var contact_number = $( "#user_contact_number" ).val();
	var countryTelephoneCode = $("#user_contact_number_country_code").val();
	if (contact_number.length > 0 && contact_number.length <= 16 && countryTelephoneCode.length == 0){
		$("#user_contact_number").parent().parent().addClass('has-error');
		$("#contact_number_error_block").text('You must select a country code.');
		$("#contact_number_error_block").show();
		$('.submitStep2').attr('type','button');
	} else {
		$("#user_contact_number").parent().parent().removeClass('has-error');
		$("#contact_number_error_block").hide();
	}
}

//---------------------------------------------------------------------------//

$("#user_mobile_number").focusout(function() {
	var mobile_number = $( "#user_mobile_number" ).val();
	validate_mobile_number(mobile_number);
});

function validate_mobile_number(mobile_number){
	if  (mobile_number.length > 0 && mobile_number.length <= 16){
		if(!mobile_number.match(/^\d+$/)) {
			$("#user_mobile_number").parent().parent().addClass('has-error');
			$("#mobile_number_error_block").text('Phone number may contain numbers only.');
			$("#mobile_number_error_block").show();
			$('.submitStep2').attr('type','button');
		} else {
			validate_mobile_number_country_code();
			
		}
	}else if(mobile_number.length > 16){
		$("#user_mobile_number").parent().parent().addClass('has-error');
		$("#mobile_number_error_block").text('Phone number cannot be longer that 16 digits.');
		$("#mobile_number_error_block").show();
		$('.submitStep2').attr('type','button');
	}else {
		$("#user_mobile_number").parent().parent().removeClass('has-error');
		$("#mobile_number_error_block").hide();
	}

}

$("#user_mobile_number_country_code").change(function() {
	validate_mobile_number_country_code()
});

function validate_mobile_number_country_code(){
	var mobile_number = $( "#user_mobile_number" ).val();
	var countryTelephoneCode = $("#user_mobile_number_country_code").val();
	if (mobile_number.length > 0 && mobile_number.length <= 16 && countryTelephoneCode.length == 0){
		$("#user_mobile_number").parent().parent().addClass('has-error');
		$("#mobile_number_error_block").text('You must select a country code.');
		$("#mobile_number_error_block").show();
		$('.submitStep2').attr('type','button');
	} else {
		$("#user_mobile_number").parent().parent().removeClass('has-error');
		$("#mobile_number_error_block").hide();
	}
}

//---------------------------------------------------------------------------//
$("#user_address_line_1").focusout(function() {
	var address_line_1 = $( "#user_address_line_1" ).val();
	validate_address_line_1(address_line_1);
});

function validate_address_line_1(address_line_1){
	if  (address_line_1.length == 0){
		$("#user_address_line_1").parent().addClass('has-error');
		$("#user_address_line_1").next('.help-block').text('Address line cannot be empty.');
		$("#user_address_line_1").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else if  (address_line_1.length >60){
		$("#user_address_line_1").parent().addClass('has-error');
		$("#user_address_line_1").next('.help-block').text('Address line cannot be longer than 60 characters.');
		$("#user_address_line_1").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else if  (!validateAddressLine(address_line_1) ){
		$("#user_address_line_1").parent().addClass('has-error');
		$("#user_address_line_1").next('.help-block').text('Address line must contain only letters, spaces, numbers or hyphen.');
		$("#user_address_line_1").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else {
		$("#user_address_line_1").parent().removeClass('has-error');
		$("#user_address_line_1").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#user_address_line_2").focusout(function() {
	var address_line_2 = $( "#user_address_line_2" ).val();
	validate_address_line_2(address_line_2);
});

function validate_address_line_2(address_line_2){
	if  (address_line_2.length > 60){
		$("#user_address_line_2").parent().addClass('has-error');
		$("#user_address_line_2").next('.help-block').text('Address line cannot be longer than 60 characters.');
		$("#user_address_line_2").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else if (address_line_2.length > 0 && !validateAddressLine(address_line_2) ){
		$("#user_address_line_2").parent().addClass('has-error');
		$("#user_address_line_2").next('.help-block').text('Address line must contain only letters, spaces, numbers or hyphen.');
		$("#user_address_line_2").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else {
		$("#user_address_line_2").parent().removeClass('has-error');
		$("#user_address_line_2").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#user_address_town").focusout(function() {
	var address_town = $( "#user_address_town" ).val();
	validate_address_town(address_town);
});

function validate_address_town(address_town){
	if  (address_town.length == 0){
		$("#user_address_town").parent().addClass('has-error');
		$("#user_address_town").next('.help-block').text('Town cannot be empty.');
		$("#user_address_town").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else if  (address_town.length >30){
		$("#user_address_town").parent().addClass('has-error');
		$("#user_address_town").next('.help-block').text('Town cannot be longer than 30 characters.');
		$("#user_address_town").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else if  (!lettersAndNumbers(address_town) ){
		$("#user_address_town").parent().addClass('has-error');
		$("#user_address_town").next('.help-block').text('Town must contain only letters, space, full-stop or hyphen.');
		$("#user_address_town").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else {
		$("#user_address_town").parent().removeClass('has-error');
		$("#user_address_town").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#user_address_county").focusout(function() {
	var address_county = $( "#user_address_county" ).val();
	validate_address_county(address_county);
});

function validate_address_county(address_county){
	if  (address_county.length > 30){
		$("#user_address_county").parent().addClass('has-error');
		$("#user_address_county").next('.help-block').text('County cannot be longer than 30 characters.');
		$("#user_address_county").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else if (address_county.length > 0 && !lettersAndNumbers(address_county) ){
		$("#user_address_county").parent().addClass('has-error');
		$("#user_address_county").next('.help-block').text('County must contain only letters, space, full-stop or hyphen.');
		$("#user_address_county").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else {
		$("#user_address_county").parent().removeClass('has-error');
		$("#user_address_county").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#user_address_postcode").focusout(function() {
	var address_postcode = $( "#user_address_postcode" ).val();
	validate_address_postcode(address_postcode);
	
});

function validate_address_postcode(address_postcode){
	var user_address_country = $( "#user_address_country" ).val();
	if  (address_postcode.length == 0 && user_address_country == 'GBR'){
		$("#user_address_postcode").parent().addClass('has-error');
		$("#user_address_postcode").next('.help-block').text('Postcode cannot be empty.');
		$("#user_address_postcode").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else if (address_postcode.length > 0 && !lettersAndNumbers(address_postcode) ){
		$("#user_address_postcode").parent().addClass('has-error');
		$("#user_address_postcode").next('.help-block').text('County must contain only letters, space, full-stop or hyphen.');
		$("#user_address_postcode").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else if (!validatePostcodeFormat(address_postcode) && user_address_country == 'GBR' ){
		$("#user_address_postcode").parent().addClass('has-error');
		$("#user_address_postcode").next('.help-block').text('This is not a valid postcode format. Make sure all letters are uppercase and you add a space between the 2 postcode parts!');
		$("#user_address_postcode").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	}else{
		$("#user_address_postcode").parent().removeClass('has-error');
		$("#user_address_postcode").next('.help-block').hide();
		if(user_address_country == 'GBR'){
			validate_postcode(address_postcode);
		}
	}

	
}


//---------------------------------------------------------------------------//

$("#user_address_country").change(function() {
	var address_country = $( "#user_address_country" ).val();
	validate_address_country(address_country);
});
function validate_address_country(address_country){
	var address_postcode = $( "#user_address_postcode" ).val();
	if (address_country.length != 3){
		$("#user_address_country").parent().parent().addClass('has-error');
		$("#user_address_country").next('.help-block').show();
		$('.submitStep2').attr('type','button');
	} else {
		$("#user_address_country").parent().parent().removeClass('has-error');
		$("#user_address_country").next('.help-block').hide();
		if(address_country == 'GBR'){
			validate_postcode(address_postcode);
		} else {
			var address_postcode = $( "#user_address_postcode" ).val();
			validate_address_postcode(address_postcode);
		}
	}
}

//---------------------------------------------------------------------------//
$("#user_current_address_from").focusout(function() {
	var current_address_from = $( "#user_current_address_from" ).val();
	validate_current_address_from(current_address_from);
	
});

function validate_current_address_from(current_address_from){
	var user_dob = $( "#validate_dob" ).val();
	var user_dob_raw = user_dob.split('-');
	var user_dob_formatted = new Date(user_dob_raw[0],user_dob_raw[1]-1,user_dob_raw[2]);

	var current_address_raw = current_address_from.split('/');
	var date_limit = new Date('1900','00','01');
	var today = new Date();

	//var current_address_formatted = new Date(current_address_raw[2],current_address_raw[1],current_address_raw[0]);
	var current_address_formatted = new Date(current_address_from.split("/").reverse().join("-"));

	if  (current_address_from.length == 0){
		$("#user_current_address_from").parent().parent().addClass('has-error');
		$("#validate_dob_error_block").text('Please enter a date between now and your date of birth.');
		$("#validate_dob_error_block").show();
		$('.submitStep2').attr('type','button');
	}else if (current_address_from.length != 10 || !checkDateFormat(current_address_from) ){
		$("#user_current_address_from").parent().parent().addClass('has-error');
		$("#validate_dob_error_block").text('Date format is invalid.');
		$("#validate_dob_error_block").show();
		$('.submitStep2').attr('type','button');
	}else{
		$("#user_current_address_from").parent().parent().removeClass('has-error');
		$("#validate_dob_error_block").hide();

		if(current_address_formatted < date_limit){
			$("#user_current_address_from").parent().parent().addClass('has-error');
			$("#validate_dob_error_block").text('Date cannot be before 01 January 1900.');
			$("#validate_dob_error_block").show();
			$('.submitStep2').attr('type','button');
		} else if(current_address_formatted > today){
			$("#user_current_address_from").parent().parent().addClass('has-error');
			$("#validate_dob_error_block").text('Date cannot be in the future.');
			$("#validate_dob_error_block").show();
			$('.submitStep2').attr('type','button');
		} else if(current_address_formatted < user_dob_formatted){
			$("#user_current_address_from").parent().parent().addClass('has-error');
			$("#validate_dob_error_block").text('Please enter a date between now and your date of birth.');
			$("#validate_dob_error_block").show();
			$('.submitStep2').attr('type','button');
		}
	}

	
}

//---------------------------------------------------------------------------//


//STEP 2 END









//STEP 3 START
//---------------------------------------------------------------------------//
$('.submitStep3').on('click', function() {
    validateStep3Form();
});

function validateStep3Form(){
	$('.submitStep3').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;

	// var previous_address_line_1 = $( "#user_previous_address_line_1" ).val();
	// validate_previous_address_line_1(previous_address_line_1);

	// var previous_address_line_2 = $( "#user_previous_address_line_2" ).val();
	// validate_previous_address_line_2(previous_address_line_2);

	// var previous_address_town = $( "#user_previous_address_town" ).val();
	// validate_previous_address_town(previous_address_town);

	// var previous_address_county = $( "#user_previous_address_county" ).val();
	// validate_previous_address_county(previous_address_county);

	// var previous_address_postcode = $( "#user_previous_address_postcode" ).val();
	// validate_previous_address_postcode(previous_address_postcode);

	// var previous_address_country = $( "#user_previous_address_country" ).val();
	// validate_previous_address_country(previous_address_country);

	// var previous_address_from = $( "#user_previous_address_from" ).val();
	// validate_previous_address_from(previous_address_from);
	
	// var previous_address_until = $( "#user_previous_address_until" ).val();
	// validate_previous_address_until(previous_address_until);

	if ($('.submitStep3').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}


//---------------------------------------------------------------------------//
$("#user_previous_address_line_1").focusout(function() {
	var previous_address_line_1 = $( "#user_previous_address_line_1" ).val();
	validate_previous_address_line_1(previous_address_line_1);
});

function validate_previous_address_line_1(previous_address_line_1){
	if  (previous_address_line_1.length == 0){
		$("#user_previous_address_line_1").parent().addClass('has-error');
		$("#user_previous_address_line_1").next('.help-block').text('Address line cannot be empty.');
		$("#user_previous_address_line_1").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else if  (previous_address_line_1.length >60){
		$("#user_previous_address_line_1").parent().addClass('has-error');
		$("#user_previous_address_line_1").next('.help-block').text('Address line cannot be longer than 60 characters.');
		$("#user_previous_address_line_1").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else if  (!validateAddressLine(previous_address_line_1) ){
		$("#user_previous_address_line_1").parent().addClass('has-error');
		$("#user_previous_address_line_1").next('.help-block').text('Address line must contain only letters, numbers, space or hyphen.');
		$("#user_previous_address_line_1").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else {
		$("#user_previous_address_line_1").parent().removeClass('has-error');
		$("#user_previous_address_line_1").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#user_previous_address_line_2").focusout(function() {
	var previous_address_line_2 = $( "#user_previous_address_line_2" ).val();
	validate_previous_address_line_2(previous_address_line_2);
});

function validate_previous_address_line_2(previous_address_line_2){
	if  (previous_address_line_2.length > 60){
		$("#user_previous_address_line_2").parent().addClass('has-error');
		$("#user_previous_address_line_2").next('.help-block').text('Address line cannot be longer than 60 characters.');
		$("#user_previous_address_line_2").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else if (previous_address_line_2.length > 0 && !validateAddressLine(previous_address_line_2) ){
		$("#user_previous_address_line_2").parent().addClass('has-error');
		$("#user_previous_address_line_2").next('.help-block').text('Address line must contain only letters, numbers, space or hyphen.');
		$("#user_previous_address_line_2").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else {
		$("#user_previous_address_line_2").parent().removeClass('has-error');
		$("#user_previous_address_line_2").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#user_previous_address_town").focusout(function() {
	var previous_address_town = $( "#user_previous_address_town" ).val();
	validate_previous_address_town(previous_address_town);
});

function validate_previous_address_town(previous_address_town){
	if  (previous_address_town.length == 0){
		$("#user_previous_address_town").parent().addClass('has-error');
		$("#user_previous_address_town").next('.help-block').text('Town cannot be empty.');
		$("#user_previous_address_town").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else if  (previous_address_town.length >30){
		$("#user_previous_address_town").parent().addClass('has-error');
		$("#user_previous_address_town").next('.help-block').text('Town cannot be longer than 60 characters.');
		$("#user_previous_address_town").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else if  (!lettersAndNumbers(previous_address_town) ){
		$("#user_previous_address_town").parent().addClass('has-error');
		$("#user_previous_address_town").next('.help-block').text('Town must contain only letters, space, full-stop or hyphen.');
		$("#user_previous_address_town").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else {
		$("#user_previous_address_town").parent().removeClass('has-error');
		$("#user_previous_address_town").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#user_previous_address_county").focusout(function() {
	var previous_address_county = $( "#user_previous_address_county" ).val();
	validate_previous_address_county(previous_address_county);
});

function validate_previous_address_county(previous_address_county){
	if  (previous_address_county.length > 30){
		$("#user_previous_address_county").parent().addClass('has-error');
		$("#user_previous_address_county").next('.help-block').text('County cannot be longer than 30 characters.');
		$("#user_previous_address_county").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else if (previous_address_county.length > 0 && !lettersAndNumbers(previous_address_county) ){
		$("#user_previous_address_county").parent().addClass('has-error');
		$("#user_previous_address_county").next('.help-block').text('County must contain only letters, space, full-stop or hyphen.');
		$("#user_previous_address_county").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else {
		$("#user_previous_address_county").parent().removeClass('has-error');
		$("#user_previous_address_county").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#user_previous_address_postcode").focusout(function() {
	var previous_address_postcode = $( "#user_previous_address_postcode" ).val();
	validate_previous_address_postcode(previous_address_postcode);
	
});

function validate_previous_address_postcode(previous_address_postcode){
	var previous_address_country = $( "#user_previous_address_country" ).val();
	if  (previous_address_postcode.length == 0 && previous_address_country == 'GBR'){
		$("#user_previous_address_postcode").parent().addClass('has-error');
		$("#user_previous_address_postcode").next('.help-block').text('Postcode cannot be empty.');
		$("#user_previous_address_postcode").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else if (previous_address_postcode.length > 0 && !lettersAndNumbers(previous_address_postcode) ){
		$("#user_previous_address_postcode").parent().addClass('has-error');
		$("#user_previous_address_postcode").next('.help-block').text('County must contain only letters, space, full-stop or hyphen.');
		$("#user_previous_address_postcode").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else if (!validatePostcodeFormat(previous_address_postcode) && previous_address_country == 'GBR'){		
		$("#user_previous_address_postcode").parent().addClass('has-error');
		$("#user_previous_address_postcode").next('.help-block').text('This is not a valid postcode format. Make sure all letters are uppercase and you add a space between the 2 postcode parts!');
		$("#user_previous_address_postcode").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else{
		$("#user_previous_address_postcode").parent().removeClass('has-error');
		$("#user_previous_address_postcode").next('.help-block').hide();
		if(previous_address_country == 'GBR'){
			validate_postcode_previous_address(previous_address_postcode);
		}
	}

	
}


//---------------------------------------------------------------------------//

$("#user_previous_address_country").change(function() {
	var previous_address_country = $( "#user_previous_address_country" ).val();
	validate_previous_address_country(previous_address_country);
});
function validate_previous_address_country(previous_address_country){
	var previous_address_postcode = $( "#user_previous_address_postcode" ).val();
	if (previous_address_country.length != 3){
		$("#user_previous_address_country").parent().parent().addClass('has-error');
		$("#user_previous_address_country").next('.help-block').show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	} else {
		$("#user_previous_address_country").parent().parent().removeClass('has-error');
		$("#user_previous_address_country").next('.help-block').hide();
		if(previous_address_country == 'GBR'){
			validate_postcode_previous_address(previous_address_postcode);
		} else {
			var previous_address_postcode = $( "#user_previous_address_postcode" ).val();
			validate_previous_address_postcode(previous_address_postcode);
		}
	}
}

//---------------------------------------------------------------------------//
function check_address_overlap_start(){
var previous_address_from = $( "#user_previous_address_from" ).val();
if  (previous_address_from.length == 10){
	var previous_address_from_raw = previous_address_from.split('/');
	var previous_address_from_formatted = new Date(previous_address_from_raw[2],previous_address_from_raw[1]-1,previous_address_from_raw[0]);
} else var previous_address_from_formatted = undefined;


var testResult ={'success':true, 'address':''};

if (previous_address_from_formatted == undefined){
	testResult.success = false;
	testResult.address = 'Please select the start date.';
	return testResult;
} else {
	$('[id^="loggedInterval_start_"]').each(function(index, value) {
		var keyID = $(this).attr('key-id');
		var startDate = $('#loggedInterval_start_'+keyID).val();
		var startDate_raw = startDate.split('-');
		var startDate_formatted = new Date(startDate_raw[0],startDate_raw[1]-1,startDate_raw[2]);

		var endDate = $('#loggedInterval_end_'+keyID).val();
		var endDate_raw = endDate.split('-');
		var endDate_formatted = new Date(endDate_raw[0],endDate_raw[1]-1,endDate_raw[2]);

		var address = $('#loggedInterval_address_'+keyID).val();
		//test if dates are overlapping
		if (startDate_formatted < previous_address_from_formatted && endDate_formatted > previous_address_from_formatted){
			testResult.success = false;
			testResult.address = address;
		}
	});
}
return testResult;
}

//---------------------------------------------------------------------------//
function check_address_overlap_end(){
var previous_address_until = $( "#user_previous_address_until" ).val();
if  (previous_address_until.length == 10){
	var previous_address_until_raw = previous_address_until.split('/');
	var previous_address_until_formatted = new Date(previous_address_until_raw[2],previous_address_until_raw[1]-1,previous_address_until_raw[0]);
} else var previous_address_until_formatted = undefined;


var testResult ={'success':true, 'address':''};

if (previous_address_until_formatted == undefined){
	testResult.success = false;
	testResult.address = 'Please select the end date.';
	return testResult;
} else {
	$('[id^="loggedInterval_start_"]').each(function(index, value) {
		var keyID = $(this).attr('key-id');
		var startDate = $('#loggedInterval_start_'+keyID).val();
		var startDate_raw = startDate.split('-');
		var startDate_formatted = new Date(startDate_raw[0],startDate_raw[1]-1,startDate_raw[2]);

		var endDate = $('#loggedInterval_end_'+keyID).val();
		var endDate_raw = endDate.split('-');
		var endDate_formatted = new Date(endDate_raw[0],endDate_raw[1]-1,endDate_raw[2]);

		var address = $('#loggedInterval_address_'+keyID).val();

		
		//test if dates are overlapping
		if (startDate_formatted < previous_address_until_formatted && endDate_formatted > previous_address_until_formatted){
			testResult.success = false;
			testResult.address = address;
		}
	});
}
return testResult;
}

//---------------------------------------------------------------------------//
function validate_address_interval(){

var previous_address_from = $( "#user_previous_address_from" ).val();
if  (previous_address_from.length == 10){
	var previous_address_from_raw = previous_address_from.split('/');
	var previous_address_from_formatted = new Date(previous_address_from_raw[2],previous_address_from_raw[1]-1,previous_address_from_raw[0]);
} else var previous_address_from_formatted = undefined;

var previous_address_until = $( "#user_previous_address_until" ).val();
if  (previous_address_until.length == 10){
	var previous_address_until_raw = previous_address_until.split('/');
	var previous_address_until_formatted = new Date(previous_address_until_raw[2],previous_address_until_raw[1]-1,previous_address_until_raw[0]);
} else var previous_address_until_formatted = undefined;


var testResult ={'success':true, 'address':''};

if (previous_address_from_formatted == undefined || previous_address_until_formatted == undefined){
	testResult.success = false;
	testResult.address = 'Please select the date interval.';
	return testResult;
} else {
	$('[id^="loggedInterval_start_"]').each(function(index, value) {
		var keyID = $(this).attr('key-id');
		var startDate = $('#loggedInterval_start_'+keyID).val();
		var startDate_raw = startDate.split('-');
		var startDate_formatted = new Date(startDate_raw[0],startDate_raw[1]-1,startDate_raw[2]);

		var endDate = $('#loggedInterval_end_'+keyID).val();
		var endDate_raw = endDate.split('-');
		var endDate_formatted = new Date(endDate_raw[0],endDate_raw[1]-1,endDate_raw[2]);

		var address = $('#loggedInterval_address_'+keyID).val();

		//alert(previous_address_from_formatted +' -- '+ startDate_formatted +' -- '+ endDate_formatted +' -- '+ previous_address_until_formatted);
		//test if dates are overlapping
		if  ((startDate_formatted < previous_address_from_formatted && previous_address_from_formatted < endDate_formatted ) ||
			 (startDate_formatted < previous_address_until_formatted && previous_address_until_formatted < endDate_formatted ) || 
			 (previous_address_from_formatted <= startDate_formatted && endDate_formatted <= previous_address_until_formatted )
		    )
		{
			testResult.success = false;
			testResult.address = address;
		}
	});
}
return testResult;
}

//---------------------------------------------------------------------------//
$("#user_previous_address_from").focusout(function() {
	var previous_address_from = $( "#user_previous_address_from" ).val();
	validate_previous_address_from(previous_address_from);
	validate_address_interval();
});

function validate_previous_address_from(previous_address_from){
	var user_dob = $( "#validate_dob" ).val();
	var user_dob_raw = user_dob.split('-');
	var user_dob_formatted = new Date(user_dob_raw[0],user_dob_raw[1]-1,user_dob_raw[2]);
	var overlapTest = check_address_overlap_start();

	var date_limit = new Date('1900','01','01');
	var today = new Date();

	var previous_address_from = $( "#user_previous_address_from" ).val();
	var previous_address_from_raw = previous_address_from.split('/');
	var previous_address_from_formatted = new Date(previous_address_from_raw[2],previous_address_from_raw[1]-1,previous_address_from_raw[0]);

	//var previous_address_from_formatted = new Date(previous_address_from.split("/").reverse().join("-"));
	if  (previous_address_from.length == 0){
		$("#user_previous_address_from").parent().parent().addClass('has-error');
		$("#previous_address_from_error_block").text('Please enter a date between now and your date of birth.');
		$("#previous_address_from_error_block").show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else if (previous_address_from.length != 10 || !checkDateFormat(previous_address_from) ){
		$("#user_previous_address_from").parent().parent().addClass('has-error');
		$("#previous_address_from_error_block").text('Date format is invalid.');
		$("#previous_address_from_error_block").show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else{
		$("#user_previous_address_from").parent().parent().removeClass('has-error');
		$("#previous_address_from_error_block").hide();

		if(previous_address_from_formatted < date_limit){
			$("#user_previous_address_from").parent().parent().addClass('has-error');
			$("#previous_address_from_error_block").text('Date cannot be before 01 January 1900.');
			$("#previous_address_from_error_block").show();
			$('.submitStep3').attr('type','button');
			$( "#user_save_previous_address" ).attr('validation-flag', false);
		} else if(previous_address_from_formatted > today){
			$("#user_previous_address_from").parent().parent().addClass('has-error');
			$("#previous_address_from_error_block").text('Date cannot be in the future.');
			$("#previous_address_from_error_block").show();
			$('.submitStep3').attr('type','button');
			$( "#user_save_previous_address" ).attr('validation-flag', false);
		} else if(previous_address_from_formatted < user_dob_formatted){
			$("#user_previous_address_from").parent().parent().addClass('has-error');
			$("#previous_address_from_error_block").text('Please enter a date between now and your date of birth.');
			$("#previous_address_from_error_block").show();
			$('.submitStep3').attr('type','button');
			$( "#user_save_previous_address" ).attr('validation-flag', false);
		} else if (!overlapTest.success){
			$("#user_previous_address_from").parent().parent().addClass('has-error');
			$("#previous_address_from_error_block").text('Your date interval is covering another date interval: '+overlapTest.address);
			$("#previous_address_from_error_block").show();
			$('.submitStep3').attr('type','button');
			$( "#user_save_previous_address" ).attr('validation-flag', false);
		}
	}

	
}


//---------------------------------------------------------------------------//
$("#user_previous_address_until").focusout(function() {
	var previous_address_until = $( "#user_previous_address_until" ).val();
	validate_previous_address_until(previous_address_until);
	validate_address_interval();
});

function validate_previous_address_until(previous_address_until){
	var user_dob = $( "#validate_dob" ).val();
	var user_dob_raw = user_dob.split('-');
	var user_dob_formatted = new Date(user_dob_raw[0],user_dob_raw[1]-1,user_dob_raw[2]);

	var previous_address_from = $( "#user_previous_address_from" ).val();
	var previous_address_from_raw = previous_address_from.split('/');
	var previous_address_from_formatted = new Date(previous_address_from_raw[2],previous_address_from_raw[1]-1,previous_address_from_raw[0]);

	var previous_address_until = $( "#user_previous_address_until" ).val();
	var previous_address_until_raw = previous_address_until.split('/');
	var previous_address_until_formatted = new Date(previous_address_until_raw[2],previous_address_until_raw[1]-1,previous_address_until_raw[0]);

	var overlapTest = check_address_overlap_end();

	var date_limit = new Date('1900','01','01');
	var today = new Date();

//var previous_address_until_formatted = new Date(previous_address_until.split("/").reverse().join("-"));
	if  (previous_address_until.length == 0){
		$("#user_previous_address_until").parent().parent().addClass('has-error');
		$("#user_previous_address_until_error_block").text('Please enter a date between now and your date of birth.');
		$("#user_previous_address_until_error_block").show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else if (previous_address_until.length != 10 || !checkDateFormat(previous_address_until) ){
		$("#user_previous_address_until").parent().parent().addClass('has-error');
		$("#user_previous_address_until_error_block").text('Date format is invalid.');
		$("#user_previous_address_until_error_block").show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	}else{
		$("#user_previous_address_until").parent().parent().removeClass('has-error');
		$("#user_previous_address_until_error_block").hide();

		if(previous_address_until_formatted < date_limit){
			$("#user_previous_address_until").parent().parent().addClass('has-error');
			$("#user_previous_address_until_error_block").text('Date cannot be before 01 January 1900.');
			$("#user_previous_address_until_error_block").show();
			$('.submitStep3').attr('type','button');
			$( "#user_save_previous_address" ).attr('validation-flag', false);
		} else if(previous_address_until_formatted < previous_address_from_formatted){
			$("#user_previous_address_until").parent().parent().addClass('has-error');
			$("#user_previous_address_until_error_block").text('Date cannot be before from date.');
			$("#user_previous_address_until_error_block").show();
			$('.submitStep3').attr('type','button');
			$( "#user_save_previous_address" ).attr('validation-flag', false);
		} else if(previous_address_until_formatted > today){
			$("#user_previous_address_until").parent().parent().addClass('has-error');
			$("#user_previous_address_until_error_block").text('Date cannot be in the future.');
			$("#user_previous_address_until_error_block").show();
			$('.submitStep3').attr('type','button');
			$( "#user_save_previous_address" ).attr('validation-flag', false);
		} else if(previous_address_until_formatted < user_dob_formatted){
			$("#user_previous_address_until").parent().parent().addClass('has-error');
			$("#user_previous_address_until_error_block").text('Please enter a date between now and your date of birth.');
			$("#user_previous_address_until_error_block").show();
			$('.submitStep3').attr('type','button');
			$( "#user_save_previous_address" ).attr('validation-flag', false);
		} else if (!overlapTest.success){
			$("#user_previous_address_until").parent().parent().addClass('has-error');
			$("#user_previous_address_until_error_block").text('Your date interval is covering another date interval: '+overlapTest.address);
			$("#user_previous_address_until_error_block").show();
			$('.submitStep3').attr('type','button');
			$( "#user_save_previous_address" ).attr('validation-flag', false);
		}
	}

	
}


$(document).on("click", '#user_save_previous_address', function() {
	$( "#user_save_previous_address" ).attr('validation-flag', true);
	var previous_address_line_1 = $( "#user_previous_address_line_1" ).val();
	validate_previous_address_line_1(previous_address_line_1);

	var previous_address_line_2 = $( "#user_previous_address_line_2" ).val();
	validate_previous_address_line_2(previous_address_line_2);

	var previous_address_town = $( "#user_previous_address_town" ).val();
	validate_previous_address_town(previous_address_town);

	var previous_address_county = $( "#user_previous_address_county" ).val();
	validate_previous_address_county(previous_address_county);

	var previous_address_postcode = $( "#user_previous_address_postcode" ).val();
	validate_previous_address_postcode(previous_address_postcode);

	var previous_address_country = $( "#user_previous_address_country" ).val();
	validate_previous_address_country(previous_address_country);

	var previous_address_from = $( "#user_previous_address_from" ).val();
	validate_previous_address_from(previous_address_from);
	
	var previous_address_until = $( "#user_previous_address_until" ).val();
	validate_previous_address_until(previous_address_until);

	var address_interval = validate_address_interval();
	if (!address_interval.success){
		$("#previous_address_overlap_error_block").parent().addClass('has-error');
		var errorMessage ='Your date interval is covering another date interval: '+address_interval.address;
		errorMessage += '<br />Change the dates and save again!';
		$("#previous_address_overlap_error_block").html(errorMessage);
		$("#previous_address_overlap_error_block").show();
		$('.submitStep3').attr('type','button');
		$( "#user_save_previous_address" ).attr('validation-flag', false);
	} else {
		$("#previous_address_overlap_error_block").parent().removeClass('has-error');
		$("#previous_address_overlap_error_block").hide();
	}

	var checkValidationFlag = $( "#user_save_previous_address" ).attr('validation-flag');
	if(checkValidationFlag == 'true'){
		var DBSApplicationID = "<?php if(isset($DBSApplication->id) && $DBSApplication->id > 0 ) echo $DBSApplication->id; else echo'0'; ?>";
	    save_previous_address(DBSApplicationID);
	}
});
function save_previous_address(DBSApplicationID){
	var previous_address_line_1 = $( "#user_previous_address_line_1" ).val();
	var previous_address_line_2 = $( "#user_previous_address_line_2" ).val();
	var previous_address_town = $( "#user_previous_address_town" ).val();
	var previous_address_county = $( "#user_previous_address_county" ).val();
	var previous_address_postcode = $( "#user_previous_address_postcode" ).val();
	var previous_address_country = $( "#user_previous_address_country option:selected" ).val();
	var previous_address_from = $( "#user_previous_address_from" ).val();
	var previous_address_until = $( "#user_previous_address_until" ).val();

	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/addPreviousAddress", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":DBSApplicationID, "previous_address_line_1":previous_address_line_1, "previous_address_line_2":previous_address_line_2, "previous_address_town":previous_address_town, "previous_address_county":previous_address_county, "previous_address_postcode":previous_address_postcode, "previous_address_country":previous_address_country, "previous_address_from":previous_address_from, "previous_address_until":previous_address_until},
        success: function(result){
        	var updateResult = JSON.parse(result);
        	if(updateResult.status == 1){
        		var addressLine = '<div class="row" id="loggedInterval_block_'+updateResult.pastAddressID+'"><div class="col-md-6">';
        		addressLine += previous_address_line_1 + ' ' + previous_address_line_2 + ' ' + previous_address_town + ' ' + previous_address_county + ' ' + previous_address_postcode + ' ' + previous_address_country;
        		addressLine += '</div>';
        		addressLine += '<div class="col-md-5">';
        		addressLine += previous_address_from + ' - ' + previous_address_until;
        		addressLine += '</div>';
        		addressLine += '<div class="col-md-1" style=" padding-left:0;">';
        		addressLine += '<button id="remove_loggedInterval_'+updateResult.pastAddressID+'" type="button" address-id="'+updateResult.pastAddressID+'" class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i></button>';
        		addressLine += '</div>';
        		addressLine += '<input id="loggedInterval_start_'+updateResult.pastAddressID+'" type="hidden" value="'+previous_address_from.split("/").reverse().join("-")+'" key-id="'+updateResult.pastAddressID+'">';
        		addressLine += '<input id="loggedInterval_end_'+updateResult.pastAddressID+'" type="hidden" value="'+previous_address_until.split("/").reverse().join("-")+'">';
        		addressLine += '<input id="loggedInterval_address_'+updateResult.pastAddressID+'" type="hidden" value="'+previous_address_line_1 + ' ' + previous_address_line_2 + ' ' + previous_address_town + ' ' + previous_address_county + ' ' + previous_address_postcode + ' ' + previous_address_country+'">';
        		addressLine += '<hr />';
        		addressLine += '</div>';
        		$('#previousAddressesListBlock').append(addressLine);
        		window.location.href = "<?php echo env('APP_URL'); ?>" + "applicant/dbsStep4";
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

$(document).on("click", '[id^="remove_loggedInterval_"]', function() {
	var addressID = $(this).attr('address-id');
	var DBSApplicationID = "<?php if(isset($DBSApplication->id) && $DBSApplication->id > 0 ) echo $DBSApplication->id; else echo'0'; ?>";
    remove_loggedInterval_ajax(DBSApplicationID, addressID);
});
function remove_loggedInterval_ajax(DBSApplicationID, addressID){
	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/removePreviousAddress", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":DBSApplicationID, "addressID":addressID},
        success: function(result){
        	var deleteResult = JSON.parse(result);
        	if(deleteResult.status == 1){
        		$("#loggedInterval_block_"+addressID).remove();
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
//STEP 3 END









//STEP 4 START
//---------------------------------------------------------------------------//
$('.submitStep4').on('click', function() {
    validateStep4Form();
});

function validateStep4Form(){
	$('.submitStep4').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;

	var user_title = $( "#user_title" ).val();
	validate_user_title(user_title);

	var user_gender = $( "#user_gender" ).val();
	validate_user_gender(user_gender);

	var user_dob = $( "#user_dob" ).val();
	validate_user_dob(user_dob);

	var birth_town = $( "#user_birth_town" ).val();
	validate_birth_town(birth_town);

	var birth_country = $( "#user_birth_country" ).val();
	validate_birth_country(birth_country);

	var birth_nationality = $( "#user_birth_nationality" ).val();
	validate_birth_nationality(birth_nationality);

	var user_forename = $( "#user_forename" ).val();
	validate_user_forename(user_forename);

	var user_middlename = $( "#user_middlename" ).val();
	validate_user_middlename(user_middlename);

	var present_surname = $( "#user_present_surname" ).val();
	validate_present_surname(present_surname);

	var other_forename = $( "#user_other_forename" ).val();
	validate_other_forename(other_forename);

	var other_middlename = $( "#user_other_middlename" ).val();
	validate_other_middlename(other_middlename);

	var other_surname = $( "#user_other_surname" ).val();
	validate_other_surname(other_surname);

	var other_names_from = $( "#user_other_names_from" ).val();
	validate_other_names_from(other_names_from);

	var other_names_to = $( "#user_other_names_to" ).val();
	validate_other_names_to(other_names_to);

	if ($('.submitStep4').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}


//---------------------------------------------------------------------------//
$("#user_title").focusout(function() {
	var user_title = $( "#user_title" ).val();
	validate_user_title(user_title);
});

function validate_user_title(user_title){
	var user_gender = $( "#user_gender" ).val()
	if (user_title.length > 0 && !lettersAndNumbers(user_title) ){
		$("#user_title").parent().addClass('has-error');
		$("#user_title").next('.help-block').text('Title must contain only letters, space, full-stop or hyphen.');
		$("#user_title").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}if (user_title.length  == 0){
		$("#user_title").parent().addClass('has-error');
		$("#user_title").next('.help-block').text('You must select a user title.');
		$("#user_title").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	} else {
		$("#user_title").parent().removeClass('has-error');
		$("#user_title").next('.help-block').hide();
	}

	if(user_gender == 'female' && user_title != 17){
		$("#user_checkbox_previous_names").prop( "checked", true);
		$("#otherNamesBlock").show();
	}
}

//---------------------------------------------------------------------------//
$("#user_gender").focusout(function() {
	var user_gender = $( "#user_gender" ).val();
	validate_user_gender(user_gender);
});

function validate_user_gender(user_gender){
	var user_title = $( "#user_title" ).val();
	if (user_gender.length == 0){
		$("#user_gender").parent().addClass('has-error');
		$("#user_gender").next('.help-block').text('Please select gender.');
		$("#user_gender").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if (user_gender.length > 0 && user_gender != 'male' && user_gender != 'female'){
		$("#user_gender").parent().addClass('has-error');
		$("#user_gender").next('.help-block').text('Gender must be male or female.');
		$("#user_gender").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	} else {
		$("#user_gender").parent().removeClass('has-error');
		$("#user_gender").next('.help-block').hide();
	}

	if(user_gender == 'female' && user_title != 17){
		$("#user_checkbox_previous_names").prop( "checked", true);
		$("#otherNamesBlock").show();
	}

}
//---------------------------------------------------------------------------//

$("#user_dob").focusout(function() {
	var user_dob = $( "#user_dob" ).val();
	validate_user_dob(user_dob);

});

function validate_user_dob(user_dob){
	var user_dob_raw = user_dob.split('/');
	var user_dob_formatted = new Date(user_dob.split("/").reverse().join("-"));
	var today = new Date();

	if (user_dob.length == 0){
		$("#user_dob").parent().parent().addClass('has-error');
		$("#user_dob_error_block").text('Please enter a date.');
		$("#user_dob_error_block").show();
		$('.submitStep4').attr('type','button');
	}else if (user_dob.length != 10 || !checkDateFormat(user_dob) ){
		$("#user_dob").parent().parent().addClass('has-error');
		$("#user_dob_error_block").text('Date format is invalid.');
		$("#user_dob_error_block").show();
		$('.submitStep4').attr('type','button');
	}else{
		$("#user_dob").parent().parent().removeClass('has-error');
		$("#user_dob_error_block").hide();

		var age = Math.floor((today-user_dob_formatted) / (365.25 * 24 * 60 * 60 * 1000));
		//alert(today+'-'+user_dob_formatted+'-'+age);

		if(user_dob_formatted > today){
			$("#user_dob").parent().parent().addClass('has-error');
			$("#user_dob_error_block").text('Date cannot be in the future.');
			$("#user_dob_error_block").show();
			$('.submitStep4').attr('type','button');
		} else if (age <16){
			$("#user_dob").parent().parent().addClass('has-error');
			$("#user_dob_error_block").text('Must be 16 or older.');
			$("#user_dob_error_block").show();
			$('.submitStep4').attr('type','button');
		}
		 else if (age >110){
			$("#user_dob").parent().parent().addClass('has-error');
			$("#user_dob_error_block").text('Birth date cannot be earlier than 110 years ago.');
			$("#user_dob_error_block").show();
			$('.submitStep4').attr('type','button');
		}
	}

	
}

//---------------------------------------------------------------------------//
$("#user_birth_town").focusout(function() {
	var birth_town = $( "#user_birth_town" ).val();
	validate_birth_town(birth_town);
});

function validate_birth_town(birth_town){
	if  (birth_town.length == 0){
		$("#user_birth_town").parent().addClass('has-error');
		$("#user_birth_town").next('.help-block').text('Town cannot be empty.');
		$("#user_birth_town").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if  (birth_town.length >30){
		$("#user_birth_town").parent().addClass('has-error');
		$("#user_birth_town").next('.help-block').text('Town cannot be longer than 30 characters.');
		$("#user_birth_town").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if  (!lettersAndNumbers(birth_town) ){
		$("#user_birth_town").parent().addClass('has-error');
		$("#user_birth_town").next('.help-block').text('Town must contain only letters, space, full-stop or hyphen.');
		$("#user_birth_town").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else {
		$("#user_birth_town").parent().removeClass('has-error');
		$("#user_birth_town").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#user_birth_country").change(function() {
	var birth_country = $( "#user_birth_country" ).val();
	validate_birth_country(birth_country);
});
function validate_birth_country(birth_country){
	if (birth_country.length != 3){
		$("#user_birth_country").parent().addClass('has-error');
		$("#user_birth_country").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	} else {
		$("#user_birth_country").parent().removeClass('has-error');
		$("#user_birth_country").next('.help-block').hide();
	}
}


//---------------------------------------------------------------------------//
$("#user_birth_nationality").focusout(function() {
	var birth_nationality = $( "#user_birth_nationality" ).val();
	validate_birth_nationality(birth_nationality);
});
function validate_birth_nationality(birth_nationality){
	if  (birth_nationality.length == 0){
		$("#user_birth_nationality").parent().addClass('has-error');
		$("#user_birth_nationality").next('.help-block').text('Birth Nationality cannot be empty.');
		$("#user_birth_nationality").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if (birth_nationality.length > 0 && !lettersAndNumbers(birth_nationality)){
		$("#user_birth_nationality").parent().addClass('has-error');
		$("#user_birth_nationality").next('.help-block').text('Nationality must contain only letters, space, full-stop or hyphen.');
		$("#user_birth_nationality").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	} else {
		$("#user_birth_nationality").parent().removeClass('has-error');
		$("#user_birth_nationality").next('.help-block').hide();
	}
}


//---------------------------------------------------------------------------//
$("#user_forename").focusout(function() {
	var user_forename = $( "#user_forename" ).val();
	validate_user_forename(user_forename);
});
function validate_user_forename(user_forename){
	if (user_forename.length == 0){
		$("#user_forename").parent().addClass('has-error');
		$("#user_forename").next('.help-block').text('Forename cannot be empty.');
		$("#user_forename").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if (user_forename.length > 60){
		$("#user_forename").parent().addClass('has-error');
		$("#user_forename").next('.help-block').text('Forename cannot be more than 60 chars.');
		$("#user_forename").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if (!validateNameStructure(user_forename)){
		$("#user_forename").parent().addClass('has-error');
		$("#user_forename").next('.help-block').text('Forename can only contain letters, space and hyphen.');
		$("#user_forename").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	} else {
		$("#user_forename").parent().removeClass('has-error');
		$("#user_forename").next('.help-block').hide();
	}
}

//---------------------------------------------------------------------------//
$("#user_middlename").focusout(function() {
	var user_middlename = $( "#user_middlename" ).val();
	validate_user_middlename(user_middlename);
});
function validate_user_middlename(user_middlename){
	if (user_middlename.length > 50){
		$("#user_middlename").parent().addClass('has-error');
		$("#user_middlename").next('.help-block').text('Middlename cannot be more than 50 chars.');
		$("#user_middlename").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if (user_middlename.length > 0 && !validateNameStructure(user_middlename)){
		$("#user_middlename").parent().addClass('has-error');
		$("#user_middlename").next('.help-block').text('Middlename can only contain letters, space and hyphen.');
		$("#user_middlename").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	} else {
		$("#user_middlename").parent().removeClass('has-error');
		$("#user_middlename").next('.help-block').hide();
	}
}

//---------------------------------------------------------------------------//
$("#user_present_surname").focusout(function() {
	var present_surname = $( "#user_present_surname" ).val();
	validate_present_surname(present_surname);
});
function validate_present_surname(present_surname){
	if (present_surname.length == 0){
		$("#user_present_surname").parent().addClass('has-error');
		$("#user_present_surname").next('.help-block').text('Surname cannot be empty.');
		$("#user_present_surname").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if (present_surname.length > 50){
		$("#user_present_surname").parent().addClass('has-error');
		$("#user_present_surname").next('.help-block').text('Surname cannot be more than 50 chars.');
		$("#user_present_surname").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if (!validateNameStructure(present_surname)){
		$("#user_present_surname").parent().addClass('has-error');
		$("#user_present_surname").next('.help-block').text('Surname can only contain letters, space and hyphen.');
		$("#user_present_surname").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	} else {
		$("#user_present_surname").parent().removeClass('has-error');
		$("#user_present_surname").next('.help-block').hide();
	}
}



//---------------------------------------------------------------------------//
$("#user_checkbox_previous_names").click(function() {
	if($("#user_checkbox_previous_names").is(':checked')){
	    $("#otherNamesBlock").show();

	    var user_dob = $( "#user_dob" ).val();
		validate_user_dob(user_dob);

	} else{
	    $("#otherNamesBlock").hide();

	    $("#user_other_forename").parent().removeClass('has-error');
		$("#user_other_forename").next('.help-block').hide();
		$("#user_other_forename").val('');

		$("#user_other_middlename").parent().removeClass('has-error');
		$("#user_other_middlename").next('.help-block').hide();
		$("#user_other_middlename").val('');

		$("#user_other_surname").parent().removeClass('has-error');
		$("#user_other_surname").next('.help-block').hide();
		$("#user_other_surname").val('');

		$("#user_other_names_from").parent().parent().removeClass('has-error');
		$("#user_other_names_from_error_block").hide();

		$("#user_other_names_to").parent().parent().removeClass('has-error');
		$("#user_other_names_to_error_block").hide();
	}
});
//---------------------------------------------------------------------------//


//---------------------------------------------------------------------------//
$("#user_other_forename").focusout(function() {
	var other_forename = $( "#user_other_forename" ).val();
	validate_other_forename(other_forename);
});
function validate_other_forename(other_forename){
	var skipOtherNamesValidation = $( "#skipOtherNamesValidation" ).val();
	
	if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_forename.length == 0){
		$("#user_other_forename").parent().addClass('has-error');
		$("#user_other_forename").next('.help-block').text('Forename cannot be empty.');
		$("#user_other_forename").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_forename.length > 60){
		$("#user_other_forename").parent().addClass('has-error');
		$("#user_other_forename").next('.help-block').text('Forename cannot be more than 60 chars.');
		$("#user_other_forename").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && !validateNameStructure(other_forename)){
		$("#user_other_forename").parent().addClass('has-error');
		$("#user_other_forename").next('.help-block').text('Forename can only contain letters, space and hyphen.');
		$("#user_other_forename").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	} else {
		$("#user_other_forename").parent().removeClass('has-error');
		$("#user_other_forename").next('.help-block').hide();
	}
}

//---------------------------------------------------------------------------//
$("#user_other_middlename").focusout(function() {
	var other_middlename = $( "#user_other_middlename" ).val();
	validate_other_middlename(other_middlename);
});
function validate_other_middlename(other_middlename){
	var skipOtherNamesValidation = $( "#skipOtherNamesValidation" ).val();
	if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_middlename.length > 50){
		$("#user_other_middlename").parent().addClass('has-error');
		$("#user_other_middlename").next('.help-block').text('Middlename cannot be more than 50 chars.');
		$("#user_other_middlename").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_middlename.length > 0 && !validateNameStructure(other_middlename)){
		$("#user_other_middlename").parent().addClass('has-error');
		$("#user_other_middlename").next('.help-block').text('Middlename can only contain letters, space and hyphen.');
		$("#user_other_middlename").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	} else {
		$("#user_other_middlename").parent().removeClass('has-error');
		$("#user_other_middlename").next('.help-block').hide();
	}
}

//---------------------------------------------------------------------------//
$("#user_other_surname").focusout(function() {
	var other_surname = $( "#user_other_surname" ).val();
	validate_other_surname(other_surname);
});
function validate_other_surname(other_surname){
	var skipOtherNamesValidation = $( "#skipOtherNamesValidation" ).val();
	if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_surname.length == 0){
		$("#user_other_surname").parent().addClass('has-error');
		$("#user_other_surname").next('.help-block').text('Surname cannot be empty.');
		$("#user_other_surname").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_surname.length > 50){
		$("#user_other_surname").parent().addClass('has-error');
		$("#user_other_surname").next('.help-block').text('Surname cannot be more than 50 chars.');
		$("#user_other_surname").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	}else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && !validateNameStructure(other_surname)){
		$("#user_other_surname").parent().addClass('has-error');
		$("#user_other_surname").next('.help-block').text('Surname can only contain letters, space and hyphen.');
		$("#user_other_surname").next('.help-block').show();
		$('.submitStep4').attr('type','button');
	} else {
		$("#user_other_surname").parent().removeClass('has-error');
		$("#user_other_surname").next('.help-block').hide();
	}
}


//---------------------------------------------------------------------------//

$("#user_other_names_from").focusout(function() {
	var other_names_from = $( "#user_other_names_from" ).val();
	validate_other_names_from(other_names_from);

});

function validate_other_names_from(other_names_from){
	var skipOtherNamesValidation = $( "#skipOtherNamesValidation" ).val();
	var other_names_from_formatted = new Date(other_names_from.split("/").reverse().join("-"));
	var today = new Date();
	
	if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && $("#user_dob").val().length == 0){
		$("#user_other_names_from").parent().parent().addClass('has-error');
		$("#user_other_names_from_error_block").text('You must first select your date of birth.');
		$("#user_other_names_from_error_block").show();
		$('.submitStep4').attr('type','button');
	}else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_names_from.length == 0){
		$("#user_other_names_from").parent().parent().addClass('has-error');
		$("#user_other_names_from_error_block").text('Please enter a date.');
		$("#user_other_names_from_error_block").show();
		$('.submitStep4').attr('type','button');
	}else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && (other_names_from.length != 10 || !checkDateFormat(other_names_from)) ){
		$("#user_other_names_from").parent().parent().addClass('has-error');
		$("#user_other_names_from_error_block").text('Date format is invalid.');
		$("#user_other_names_from_error_block").show();
		$('.submitStep4').attr('type','button');
	}else{
		$("#user_other_names_from").parent().parent().removeClass('has-error');
		$("#user_other_names_from_error_block").hide();

		var user_dob = new Date($("#user_dob").val().split("/").reverse().join("-"));

		if($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_names_from_formatted > today){
			$("#user_other_names_from").parent().parent().addClass('has-error');
			$("#user_other_names_from_error_block").text('Date cannot be in the future.');
			$("#user_other_names_from_error_block").show();
			$('.submitStep4').attr('type','button');
		}
		if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_names_from_formatted < user_dob){
			$("#user_other_names_from").parent().parent().addClass('has-error');
			$("#user_other_names_from_error_block").text('Date cannot be before birthday.');
			$("#user_other_names_from_error_block").show();
			$('.submitStep4').attr('type','button');
		}
		var other_names_to = $( "#user_other_names_to" ).val();
		validate_other_names_to(other_names_to);
	}

	
}

//---------------------------------------------------------------------------//

$("#user_other_names_to").focusout(function() {
	var other_names_to = $( "#user_other_names_to" ).val();
	validate_other_names_to(other_names_to);

});

function validate_other_names_to(other_names_to){
	var skipOtherNamesValidation = $( "#skipOtherNamesValidation" ).val();
	var other_names_to_formatted = new Date(other_names_to.split("/").reverse().join("-"));
	var today = new Date();
	
	if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && $("#user_dob").val().length == 0){
		$("#user_other_names_to").parent().parent().addClass('has-error');
		$("#user_other_names_to_error_block").text('You must first select your date of birth.');
		$("#user_other_names_to_error_block").show();
		$('.submitStep4').attr('type','button');
	}if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && $("#user_other_names_from").val().length == 0){
		$("#user_other_names_to").parent().parent().addClass('has-error');
		$("#user_other_names_to_error_block").text('You must first select a valid from date.');
		$("#user_other_names_to_error_block").show();
		$('.submitStep4').attr('type','button');
	}else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_names_to.length == 0){
		$("#user_other_names_to").parent().parent().addClass('has-error');
		$("#user_other_names_to_error_block").text('Please enter a date.');
		$("#user_other_names_to_error_block").show();
		$('.submitStep4').attr('type','button');
	}else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && (other_names_to.length != 10 || !checkDateFormat(other_names_to)) ){
		$("#user_other_names_to").parent().parent().addClass('has-error');
		$("#user_other_names_to_error_block").text('Date format is invalid.');
		$("#user_other_names_to_error_block").show();
		$('.submitStep4').attr('type','button');
	}else{
		$("#user_other_names_to").parent().parent().removeClass('has-error');
		$("#user_other_names_to_error_block").hide();

		var user_dob = new Date($("#user_dob").val().split("/").reverse().join("-"));
		var other_names_to_formatted = new Date(other_names_to.split("/").reverse().join("-"));
		if($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_names_to_formatted > today){
			$("#user_other_names_to").parent().parent().addClass('has-error');
			$("#user_other_names_to_error_block").text('Date cannot be in the future.');
			$("#user_other_names_to_error_block").show();
			$('.submitStep4').attr('type','button');
		} else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_names_to_formatted < user_dob){
			$("#user_other_names_to").parent().parent().addClass('has-error');
			$("#user_other_names_to_error_block").text('Date cannot be before birthday.');
			$("#user_other_names_to_error_block").show();
			$('.submitStep4').attr('type','button');
		} else if ($("#user_checkbox_previous_names").is(':checked') && skipOtherNamesValidation == 0 && other_names_to_formatted < other_names_from_formatted){
			$("#user_other_names_to").parent().parent().addClass('has-error');
			$("#user_other_names_to_error_block").text('Date cannot be before from date.');
			$("#user_other_names_to_error_block").show();
			$('.submitStep4').attr('type','button');
		}
	}

	
}

$('#user_other_names_add_another').on('click', function() {
	var user_other_forename = $('#user_other_forename').val();
	var user_other_middlename = $('#user_other_middlename').val();
	var user_other_surname = $('#user_other_surname').val();
	var user_other_names_from = $('#user_other_names_from').val();
	var user_other_names_to = $('#user_other_names_to').val();
	var DBSApplicationID = "<?php if(isset($DBSApplication->id) && $DBSApplication->id > 0 ) echo $DBSApplication->id; else echo'0'; ?>";
    add_other_names_ajax(DBSApplicationID, user_other_forename, user_other_middlename, user_other_surname, user_other_names_from, user_other_names_to);
});
function add_other_names_ajax(DBSApplicationID, user_other_forename, user_other_middlename, user_other_surname, user_other_names_from, user_other_names_to){
	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/addOtherNames", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":DBSApplicationID, "user_other_forename":user_other_forename, "user_other_middlename":user_other_middlename, "user_other_surname":user_other_surname, "user_other_names_from":user_other_names_from, "user_other_names_to":user_other_names_to},
        success: function(result){
        	var updateResult = JSON.parse(result);
        	if(updateResult.status == 1){
        		var nameLine = '<div class="row" id="other_names_block_'+updateResult.otherNamesID+'"><div class="col-md-4" >';
        		nameLine += user_other_forename + ' ' + user_other_middlename + ' ' + user_other_surname;
        		nameLine += '</div>';
        		nameLine += '<div class="col-md-6">';
        		nameLine += 'From: '+user_other_names_from + ' To: ' + user_other_names_to;
        		nameLine += '</div>';
        		nameLine += '<div class="col-md-2">';
        		nameLine += '<button id="remove_other_names_'+updateResult.otherNamesID+'" type="button" name-id="'+updateResult.otherNamesID+'" class="btn btn-danger" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i></button>';
        		nameLine += '</div></div>';
        		$('#otherNamesListBlock').append(nameLine);
        		//clean the inputs
        		$('#user_other_forename').val('');
        		$('#user_other_middlename').val('');
        		$('#user_other_surname').val('');
        		$('#user_other_names_from').val('');
        		$('#user_other_names_to').val('');
        		$( "#skipOtherNamesValidation" ).val('1');
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

$(document).on("click", '[id^="remove_other_names_"]', function() {
	var nameID = $(this).attr('name-id');
	var DBSApplicationID = "<?php if(isset($DBSApplication->id) && $DBSApplication->id > 0 ) echo $DBSApplication->id; else echo'0'; ?>";
    remove_name_ajax(DBSApplicationID, nameID);
});
function remove_name_ajax(DBSApplicationID, nameID){
	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/removeName", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":DBSApplicationID, "nameID":nameID},
        success: function(result){
        	var deleteResult = JSON.parse(result);
        	if(deleteResult.status == 1){
        		$("#other_names_block_"+nameID).remove();
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
//STEP 4 END











//STEP 5 START
//---------------------------------------------------------------------------//
$('.submitStep5').on('click', function() {
    validateStep5Form();
});

function validateStep5Form(){
	$('.submitStep5').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;

	var nino = $( "#user_supporting_nino" ).val();
	validate_nino(nino);

	var dln = $( "#user_supporting_dln" ).val();
	validate_dln(dln);

	var passport_no = $( "#user_supporting_passport" ).val();
	validate_passport(passport_no);

	var passport_country = $( "#user_supporting_passport_country" ).val();
	validate_passport_country(passport_country);

	var dbs_profile_id = $( "#user_dbs_profile_id" ).val();
	validate_dbs_profile_id(dbs_profile_id);

	validate_checkbox_consent();

	validate_minimum_one_document();



	if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1'){
		var certificate_address_recipient_name = $( "#certificate_address_recipient_name" ).val();
		validate_certificate_address_recipient_name(certificate_address_recipient_name);

		var certificate_address_line_1 = $( "#certificate_address_line_1" ).val();
		validate_certificate_address_line_1(certificate_address_line_1);

		var certificate_address_line_2 = $( "#certificate_address_line_2" ).val();
		validate_certificate_address_line_2(certificate_address_line_2);

		var certificate_address_town = $( "#certificate_address_town" ).val();
		validate_certificate_address_town(certificate_address_town);

		var certificate_address_county = $( "#certificate_address_county" ).val();
		validate_certificate_address_county(certificate_address_county);

		var certificate_address_postcode = $( "#certificate_address_postcode" ).val();
		validate_certificate_address_postcode(certificate_address_postcode);

		var certificate_address_country = $("#certificate_address_country").val();
		validate_certificate_address_country(certificate_address_country);

		var certificate_address_country = $("#certificate_address_country").val();
		validate_certificate_address_country(certificate_address_country);
	}
	if ($('.submitStep5').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}


//---------------------------------------------------------------------------//
$("#user_supporting_nino").focusout(function() {
	var nino = $( "#user_supporting_nino" ).val();
	validate_nino(nino);
});

function validate_nino(nino){
	$("#temporaryNINOMessage").hide();
	if (nino.length > 9){
		$("#user_supporting_nino").parent().addClass('has-error');
		$("#user_supporting_nino").next('.help-block').text('NINO must not be more than 9 characters.');
		$("#user_supporting_nino").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	} else if (nino.length > 0 && nino.length < 9 ){
		$("#user_supporting_nino").parent().addClass('has-error');
		$("#user_supporting_nino").next('.help-block').text('NINO needs to be 9 characters long.');
		$("#user_supporting_nino").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	} else if (nino.length > 0 && !validateNINO(nino) && !validateTemporaryNINO(nino) ){
		$("#user_supporting_nino").parent().addClass('has-error');
		$("#user_supporting_nino").next('.help-block').text('NINO is invalid. Only uppercase and numbers are allowed');
		$("#user_supporting_nino").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	} else {
		$("#user_supporting_nino").parent().removeClass('has-error');
		$("#user_supporting_nino").next('.help-block').hide();
		$("#minimum_one_document_error_block").hide();

		if (validateTemporaryNINO(nino)){
			$("#temporaryNINOMessage").show();
		} else {
			$("#temporaryNINOMessage").hide();
		}
	}
	
}


//---------------------------------------------------------------------------//
$("#user_supporting_dln").focusout(function() {
	var dln = $( "#user_supporting_dln" ).val();
	validate_dln(dln);
});
function validate_dln(dln){

	var dln_type = $( "#supporting_dln_type" ).val();
	if(dln_type == 1){
		if (dln.length > 18){
			$("#user_supporting_dln_error_block").addClass('has-error');
			$("#user_supporting_dln_error_block").text('Driver License must not be more than 18 characters.');
			$("#user_supporting_dln_error_block").show();
			$('.submitStep5').attr('type','button');
		} else if (dln.length > 0 && !validateDriverLicence(dln) ){
			$("#user_supporting_dln_error_block").addClass('has-error');
			$("#user_supporting_dln_error_block").text('Your Driver License format is invalid.');
			$("#user_supporting_dln_error_block").show();
			$('.submitStep5').attr('type','button');
		} else {

			$("#user_supporting_dln_error_block").removeClass('has-error');
			$("#user_supporting_dln_error_block").hide();
			$("#minimum_one_document_error_block").hide();

			//validate DOB with gender restriction
			var user_dob = $( "#validate_dob" ).val();
			var user_gender = $( "#validate_gender" ).val();
			var user_dob_raw = user_dob.split('-');
			var year_first_number = user_dob_raw[0].slice(2,3);
			var year_second_number = user_dob_raw[0].slice(3,4);
			var month_first_number = user_dob_raw[1].slice(0,1);
			var month_second_number = user_dob_raw[1].slice(1,2);
			if(user_gender == 'female'){
				month_first_number = parseInt(month_first_number)+5;
			}
			month_numbers = month_first_number+month_second_number;
			var correct_dob_format = year_first_number+month_numbers+user_dob_raw[2]+year_second_number;
			var dln_dob = dln.slice(5,11);
			if (dln.length >0 && correct_dob_format != dln_dob){
				$("#user_supporting_dln_error_block").addClass('has-error');
				$("#user_supporting_dln_error_block").text('Your Driver License date of birth is wrong.');
				$("#user_supporting_dln_error_block").show();
				$('.submitStep5').attr('type','button');
			}

			var user_middlename = $( "#validate_middlename" ).val();
			var correct_middlename = '';
			if (user_middlename.length > 0){
				var correct_middlename = user_middlename.slice(0,1).toUpperCase();
			}
			var dln_middlename = dln.slice(12,13).toUpperCase();

			if (dln.length >0 && correct_middlename != dln_middlename && user_middlename.length > 0){
				$("#user_supporting_dln_error_block").addClass('has-error');
				$("#user_supporting_dln_error_block").text('Your Driver License number is missing your middle name.');
				$("#user_supporting_dln_error_block").show();
				$('.submitStep5').attr('type','button');
			}
			
		}
	} else {
		$("#user_supporting_dln_error_block").removeClass('has-error');
		$("#user_supporting_dln_error_block").hide();
		$("#minimum_one_document_error_block").hide();
	}

	
}


//---------------------------------------------------------------------------//
$("#supporting_dln_type").change(function() {
	var dln = $( "#user_supporting_dln" ).val();
	validate_dln(dln);
});

//---------------------------------------------------------------------------//
$("#user_supporting_passport").focusout(function() {
	var passport_no = $( "#user_supporting_passport" ).val();
	validate_passport(passport_no);
});
function validate_passport(passport_no){
	if (passport_no.length > 11){
		$("#user_supporting_passport").parent().addClass('has-error');
		$("#user_supporting_passport").next('.help-block').text('Passport Number must not be more than 11 characters.');
		$("#user_supporting_passport").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	} else if (passport_no.length > 0 && !onlyLettersAndNumbers(passport_no) ){
		$("#user_supporting_passport").parent().addClass('has-error');
		$("#user_supporting_passport").next('.help-block').text('Your Passport Number contains invalid characters.');
		$("#user_supporting_passport").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	} else {
		$("#user_supporting_passport").parent().removeClass('has-error');
		$("#user_supporting_passport").next('.help-block').hide();
		$("#minimum_one_document_error_block").hide();

		var passport_country = $( "#user_supporting_passport_country" ).val();
		validate_passport_country(passport_country);
	}
	
}

//---------------------------------------------------------------------------//
$("#user_supporting_passport_country").change(function() {
	var passport_country = $( "#user_supporting_passport_country" ).val();
	validate_passport_country(passport_country);
});
function validate_passport_country(passport_country){
	var passport_no = $( "#user_supporting_passport" ).val();

	if (passport_country.trim().length != 3 && passport_no.length > 0){// && onlyLettersAndNumbers(passport_no)
		$("#user_supporting_passport_country").parent().parent().addClass('has-error');
		$("#user_supporting_passport_country").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	} else {
		$("#user_supporting_passport_country").parent().parent().removeClass('has-error');
		$("#user_supporting_passport_country").next('.help-block').hide();
		$("#minimum_one_document_error_block").hide();
	}
}

//---------------------------------------------------------------------------//
$("#user_dbs_profile_id").focusout(function() {
	var dbs_profile_id = $( "#user_dbs_profile_id" ).val();
	validate_dbs_profile_id(dbs_profile_id)
});
function validate_dbs_profile_id(dbs_profile_id){
	if (dbs_profile_id.length > 11){
		$("#user_dbs_profile_id").parent().addClass('has-error');
		$("#user_dbs_profile_id").next('.help-block').text('DBS Profile ID must not be more than 11 characters.');
		$("#user_dbs_profile_id").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	} else if (dbs_profile_id.length > 0 && !onlyLettersAndNumbers(dbs_profile_id) ){
		$("#user_dbs_profile_id").parent().addClass('has-error');
		$("#user_dbs_profile_id").next('.help-block').text('Your DBS Profile ID contains invalid characters.');
		$("#user_dbs_profile_id").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	} else {
		$("#user_dbs_profile_id").parent().removeClass('has-error');
		$("#user_dbs_profile_id").next('.help-block').hide();
	}
	
}

//---------------------------------------------------------------------------//
/*
$("#user_checkbox_consent").click(function() {
	validate_checkbox_consent();
});

function validate_checkbox_consent(){
	if($("#user_checkbox_consent").is(':checked')){
	    $("#user_checkbox_consent").parent().removeClass('has-error');
		$("#consent_accepted_error_block").hide();
	} else{
	    $("#user_checkbox_consent").parent().addClass('has-error');
		$("#consent_accepted_error_block").show();
		$('.submitStep5').attr('type','button');
	}
}

*/
//---------------------------------------------------------------------------//

function validate_minimum_one_document(){
	var nino = $( "#user_supporting_nino" ).val();
	var dln = $( "#user_supporting_dln" ).val();
	var passport_no = $( "#user_supporting_passport" ).val();

	if (nino.length == 0 && dln.length == 0 && passport_no.length == 0){
		$("#minimum_one_document_error_block").show();
		$('.submitStep5').attr('type','button');
	} else{
	    $("#minimum_one_document_error_block").hide();
		
	}
}
//---------------------------------------------------------------------------//

 $('#certificate_option_current_address, #certificate_option_differentt_address').click(function() {
	var newAddress = $('input[name=user_paper_certificate_different_address]:checked').val();
	if (newAddress == 1){
		$( "#currentAddressBlock" ).hide();
		$( "#newAddressBlock" ).show();

	}else {
		$( "#currentAddressBlock" ).show();
		$( "#newAddressBlock" ).hide();

		$( "#certificate_address_recipient_name" ).val('');
		validate_certificate_address_recipient_name('');

		$( "#certificate_address_line_1" ).val('');
		validate_certificate_address_line_1('');

		$( "#certificate_address_line_2" ).val('');
		validate_certificate_address_line_2('');

		$( "#certificate_address_town" ).val('');
		validate_certificate_address_town('');

		$( "#certificate_address_county" ).val('');
		validate_certificate_address_county('');

		$( "#certificate_address_postcode" ).val('');
		validate_certificate_address_postcode('');

	}
});

//---------------------------------------------------------------------------//

$("#user_supporting_paper_certificate").change(function() {
	var user_supporting_paper_certificate = $("#user_supporting_paper_certificate option:selected").val();
	if(user_supporting_paper_certificate == 1){
		$( "#userPaperCertificateAddressOptionsBlock" ).show();
	} else {
		$( "#userPaperCertificateAddressOptionsBlock" ).hide();
	}
});
//---------------------------------------------------------------------------//


$("#certificate_address_recipient_name").focusout(function() {
	var certificate_address_recipient_name = $( "#certificate_address_recipient_name" ).val();
	validate_certificate_address_recipient_name(certificate_address_recipient_name);
});

function validate_certificate_address_recipient_name(certificate_address_recipient_name){
	if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_recipient_name.length >60){
		$("#certificate_address_recipient_name").parent().addClass('has-error');
		$("#certificate_address_recipient_name").next('.help-block').text('Recipient Name cannot be longer than 60 characters.');
		$("#certificate_address_recipient_name").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_recipient_name.length >0 && !lettersAndNumbers(certificate_address_recipient_name) ){
		$("#certificate_address_recipient_name").parent().addClass('has-error');
		$("#certificate_address_recipient_name").next('.help-block').text('Recipient Name must contain only letters, space, full-stop or hyphen.');
		$("#certificate_address_recipient_name").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else {
		$("#certificate_address_recipient_name").parent().removeClass('has-error');
		$("#certificate_address_recipient_name").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#certificate_address_line_1").focusout(function() {
	var certificate_address_line_1 = $( "#certificate_address_line_1" ).val();
	validate_certificate_address_line_1(certificate_address_line_1);
});

function validate_certificate_address_line_1(certificate_address_line_1){
	if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_line_1.length == 0){
		$("#certificate_address_line_1").parent().addClass('has-error');
		$("#certificate_address_line_1").next('.help-block').text('Address line cannot be empty.');
		$("#certificate_address_line_1").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_line_1.length >60){
		$("#certificate_address_line_1").parent().addClass('has-error');
		$("#certificate_address_line_1").next('.help-block').text('Address line cannot be longer than 60 characters.');
		$("#certificate_address_line_1").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && !validateAddressLine(certificate_address_line_1) ){
		$("#certificate_address_line_1").parent().addClass('has-error');
		$("#certificate_address_line_1").next('.help-block').text('Address line must contain only letters, space, numbers or hyphen.');
		$("#certificate_address_line_1").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else {
		$("#certificate_address_line_1").parent().removeClass('has-error');
		$("#certificate_address_line_1").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#certificate_address_line_2").focusout(function() {
	var certificate_address_line_2 = $( "#certificate_address_line_2" ).val();
	validate_certificate_address_line_2(certificate_address_line_2);
});

function validate_certificate_address_line_2(certificate_address_line_2){
	if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_line_2.length > 60){
		$("#certificate_address_line_2").parent().addClass('has-error');
		$("#certificate_address_line_2").next('.help-block').text('Address line cannot be longer than 60 characters.');
		$("#certificate_address_line_2").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else if ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_line_2.length > 0 && !validateAddressLine(certificate_address_line_2) ){
		$("#certificate_address_line_2").parent().addClass('has-error');
		$("#certificate_address_line_2").next('.help-block').text('Address line must contain only letters, space, numbers or hyphen.');
		$("#certificate_address_line_2").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else {
		$("#certificate_address_line_2").parent().removeClass('has-error');
		$("#certificate_address_line_2").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#certificate_address_town").focusout(function() {
	var certificate_address_town = $( "#certificate_address_town" ).val();
	validate_certificate_address_town(certificate_address_town);
});

function validate_certificate_address_town(certificate_address_town){
	if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_town.length == 0){
		$("#certificate_address_town").parent().addClass('has-error');
		$("#certificate_address_town").next('.help-block').text('Town cannot be empty.');
		$("#certificate_address_town").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_town.length >30){
		$("#certificate_address_town").parent().addClass('has-error');
		$("#certificate_address_town").next('.help-block').text('Town cannot be longer than 60 characters.');
		$("#certificate_address_town").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else if  (!lettersAndNumbers($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_town) ){
		$("#certificate_address_town").parent().addClass('has-error');
		$("#certificate_address_town").next('.help-block').text('Town must contain only letters, space, full-stop or hyphen.');
		$("#certificate_address_town").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else {
		$("#certificate_address_town").parent().removeClass('has-error');
		$("#certificate_address_town").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#certificate_address_county").focusout(function() {
	var certificate_address_county = $( "#certificate_address_county" ).val();
	validate_certificate_address_county(certificate_address_county);
});

function validate_certificate_address_county(certificate_address_county){
	if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_county.length > 30){
		$("#certificate_address_county").parent().addClass('has-error');
		$("#certificate_address_county").next('.help-block').text('County cannot be longer than 30 characters.');
		$("#certificate_address_county").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else if ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_county.length > 0 && !lettersAndNumbers(certificate_address_county) ){
		$("#certificate_address_county").parent().addClass('has-error');
		$("#certificate_address_county").next('.help-block').text('County must contain only letters, space, full-stop or hyphen.');
		$("#certificate_address_county").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else {
		$("#certificate_address_county").parent().removeClass('has-error');
		$("#certificate_address_county").next('.help-block').hide();
	}

}

//---------------------------------------------------------------------------//
$("#certificate_address_postcode").focusout(function() {
	var certificate_address_postcode = $( "#certificate_address_postcode" ).val();
	validate_certificate_address_postcode(certificate_address_postcode);
	
});

function validate_certificate_address_postcode(certificate_address_postcode){
	var previous_address_country = $( "#certificate_address_postcode" ).val();
	if  ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_postcode.length == 0 && previous_address_country == 'GBR'){
		$("#certificate_address_postcode").parent().addClass('has-error');
		$("#certificate_address_postcode").next('.help-block').text('Postcode cannot be empty.');
		$("#certificate_address_postcode").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else if ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_postcode.length > 0 && !lettersAndNumbers(certificate_address_postcode) ){
		$("#certificate_address_postcode").parent().addClass('has-error');
		$("#certificate_address_postcode").next('.help-block').text('County must contain only letters, space, full-stop or hyphen.');
		$("#certificate_address_postcode").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else if (!validatePostcodeFormat(certificate_address_postcode) && previous_address_country == 'GBR'){
		$("#certificate_address_postcode").parent().addClass('has-error');
		$("#certificate_address_postcode").next('.help-block').text('This is not a valid postcode format. Make sure all letters are uppercase and you add a space between the 2 postcode parts!');
		$("#certificate_address_postcode").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	}else{
		$("#certificate_address_postcode").parent().removeClass('has-error');
		$("#certificate_address_postcode").next('.help-block').hide();
		if(previous_address_country == 'GBR'){
			validate_postcode_new_address(certificate_address_postcode);
		}
	}

	
}


//---------------------------------------------------------------------------//

$("#certificate_address_country").change(function() {
	var certificate_address_country = $("#certificate_address_country").val();
	validate_certificate_address_country(certificate_address_country);
});
function validate_certificate_address_country(certificate_address_country){
	var certificate_address_postcode = $( "#certificate_address_postcode" ).val();
	if ($("#user_supporting_paper_certificate option:selected").val() == 1 && $('input[name=user_paper_certificate_different_address]:checked').val() == '1' && certificate_address_country.length != 3){
		$("#certificate_address_country").parent().parent().addClass('has-error');
		$("#certificate_address_country").next('.help-block').show();
		$('.submitStep5').attr('type','button');
	} else {
		$("#certificate_address_country").parent().parent().removeClass('has-error');
		$("#certificate_address_country").next('.help-block').hide();
		if(certificate_address_country == 'GBR'){
			validate_postcode_new_address(certificate_address_postcode);
		} else {
			validate_postcode_new_address(certificate_address_postcode);
		}
	}
}

$('#upload_document').click(function() {
	var DBSApplicationID = "<?php if(isset($DBSApplication->id) && $DBSApplication->id > 0 ) echo $DBSApplication->id; else echo'0'; ?>";
	var file_data = $("#new_document").prop("files")[0];
	var new_document_category = $( "#new_document_category option:selected" ).val();
	var form_data = new FormData();
    form_data.append("new_document", file_data);
    form_data.append("applicationID", DBSApplicationID);
    form_data.append("new_document_category", new_document_category);
    form_data.append("_token", "{{ csrf_token() }}");

	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/uploadSupportingDocument", 
        method: "POST",
        processData: false,
  		contentType: false,
        data: form_data,
        success: function(result){
        	var uploadResult = JSON.parse(result);
        	var docIcon = '<i class="fa fa-file-o" style="color: #FF00FF; margin-right: 10px;" aria-hidden="true"></i>';
        	var docName = 'Unknown name';
        	if(uploadResult.status == 1){
        		if(uploadResult.docType != undefined && uploadResult.docType == 'pdf'){
  					docIcon ='<i class="fa fa-file-pdf-o" style="color: #FF0000; margin-right: 10px;" aria-hidden="true"></i>';
        		}else if(uploadResult.docType != undefined && uploadResult.docType == 'img'){
  					docIcon ='<i class="fa fa-file-image-o" style="color: #0000FF; margin-right: 10px;" aria-hidden="true"></i>';
        		}
        		if(uploadResult.docName != undefined){
        			docName = uploadResult.docName;
        		}
	        	var docRow = '<tr id="supporting_document_row_'+uploadResult.uploadedDocID+'"><td>'+docIcon+' '+docName+'</td><td><button id="remove_document_'+uploadResult.uploadedDocID+'" doc-id="'+uploadResult.uploadedDocID+'" type="button"  class="btn btn-danger pull-right" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i></button></td></tr>';
	        	$('#supporting_documents_list').append(docRow);
        	
      		} else {
      			alert('Error: Please try again!');
      		}
        },
        error: function(result){
          alert('Error: Please try again!');
        },
	});
	 console.log(file_data);
});

$(document).on("click", '[id^="remove_document_"]', function() {
	var docId = $(this).attr('doc-id');

	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applicant/removeSupportingDocument/"+docId, 
        method: "GET",
        success: function(result){
        	var uploadResult = JSON.parse(result);
        	if(uploadResult.status == 1){
        		$("#supporting_document_row_"+docId).remove();        	
      		} else {
      			alert('Error: Please try again!');
      		}
        },
        error: function(result){
          alert('Error: Please try again!');
        },
	});
	 console.log(file_data);
});
//---------------------------------------------------------------------------//
//STEP 5 END



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