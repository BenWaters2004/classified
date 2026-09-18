<script type="text/javascript">
	$('#passport_date_of_issue, #document_date_of_issue_name').datepicker({
    autoclose: true,
    format: "dd/mm/yyyy",

});
//BPSS VR START
//---------------------------------------------------------------------------//
$(document).on("click", '#save_documentVerified_details', function() {
	var BPSSVR_ApplicationID = "<?php if(isset($userID) && $userID > 0 ) echo $userID; else echo'0'; ?>";
	save_documentVerified_details(BPSSVR_ApplicationID);
});
function save_documentVerified_details(BPSSVR_ApplicationID){
	var document_name = $( "#document_name" ).val();
	var document_date_of_issue_name = $( "#document_date_of_issue_name" ).val();
	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applications/addDocumentVerifiedDetails", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSVR_ApplicationID, "document_name":document_name, "document_date_of_issue_name":document_date_of_issue_name},
        success: function(result){
        	var saveResult = JSON.parse(result);
        	if(saveResult.status == 1){
        		var documentLine = '<div class="row" id="documentVerified_block_'+saveResult.documentID+'"><div class="col-md-6">';
        		documentLine += document_name;
        		documentLine += '</div>';
        		documentLine += '<div class="col-md-3">';
        		documentLine += document_date_of_issue_name;
        		documentLine += '</div>';
        		documentLine += '<div class="col-md-3" style=" padding-left:0;">';
        		documentLine += '<button id="remove_documentVerified_block_'+saveResult.documentID+'" type="button" document-id="'+saveResult.documentID+'" class="btn btn-danger pull-left" style="margin-bottom:5px;"><i class="icon fa fa-remove"></i> Delete</button>';
        		documentLine += '</div>';
        		documentLine += '<hr />';
        		documentLine += '</div>';
        		$('#documentVerifiedListBlock').append(documentLine);
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

$(document).on("click", '[id^="remove_documentVerified_block_"]', function() {
	var documentID = $(this).attr('document-id');
	var BPSSVR_ApplicationID = "<?php if(isset($userID) && $userID > 0 ) echo $userID; else echo'0'; ?>";
    remove_documentVerified_details(BPSSVR_ApplicationID, documentID);
});
function remove_documentVerified_details(BPSSVR_ApplicationID, documentID){
	$.ajax({
		url: "<?php echo env('APP_URL'); ?>" + "applications/removeDocumentVerifiedDetails", 
        method: "POST",
        data: {"_token":"{{ csrf_token() }}", "applicationID":BPSSVR_ApplicationID, "documentID":documentID},
        success: function(result){
        	var deleteResult = JSON.parse(result);
        	if(deleteResult.status == 1){
        		$("#documentVerified_block_"+documentID).remove();
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

$(document).on("click", '#employement_type_employee, #employement_type_contractor', function() {
    update_expity_date();
});
function update_expity_date(){
    var selectedOption = $('input[name=employement_type]:checked').val();
    var expiryDate ='';
    if (selectedOption == 'employee'){
        expiryDate = "<?php echo date('d/m/Y', strtotime(date('Y-m-d', strtotime('+10 years')))); ?>";
    } else if (selectedOption == 'contractor'){
        expiryDate = "<?php echo date('d/m/Y', strtotime(date('Y-m-d', strtotime('+3 years')))); ?>";
    }
    $('#bpssvr_expiry').val(expiryDate);
}



//---------------------------------------------------------------------------//
$('.updateBPSSVR').on('click', function() {
    validateBPSSVRForm();
});

function validateBPSSVRForm(){
	$('.updateBPSSVR').attr('type','submit');
	$('#errorMessageBlock').hide();
	var formValidate = true;


	if ($('.updateBPSSVR').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}

/*---------------------------------------------------------------------------//
$('.emailReference1').on('click', function() {
	var emailAddress = $(this).attr('reference-email');
	if(typeof emailAddress != 'undefined' && emailAddress.length > 0 && isValidEmailAddress(emailAddress)){
		sendReferenceRequestEmail(emailAddress);
	} else {

	}
    
});

function sendReferenceRequestEmail(emailAddress){
	
	if ($('.updateBPSSVR').attr('type') == 'submit'){
		//$('#errorMessageBlock').hide();
	} else {
		//$('#errorMessageBlock').show();
	}
}




//---------------------------------------------------------------------------*/

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}
//BPSS VR END

</script>