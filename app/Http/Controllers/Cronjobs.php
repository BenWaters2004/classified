<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spipu\Html2Pdf\Html2Pdf;
use Illuminate\Support\Facades\Mail;

class Cronjobs extends Controller
{

    var $countStatusChange;
	/*
    |--------------------------------------------------------------------------
    | Cronjobs Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling the Cronjobs logic
    |
    */

    /**
     * index fallback
     *
     * 
     */

    public function index()
    {
        return redirect("/login");
        
    }

    public function sendXMLToDBS($url, $XMLRequest)
    {

        //setting the curl parameters.
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        curl_setopt($ch, CURLOPT_SSLCERT,  \Storage::disk('public')->path('certificates/crt-crt.pem'));
        curl_setopt($ch, CURLOPT_SSLCERTTYPE,"PEM");
        curl_setopt($ch, CURLOPT_SSLKEY, \Storage::disk('public')->path('certificates/crt-key.pem'));

        curl_setopt($ch, CURLOPT_URL,$url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_HEADER, 0);//0
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $XMLRequest);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); 

        $response = '';
        if (curl_errno($ch)) 
        {
            // moving to display page to display curl errors
               //echo curl_errno($ch) ;
               //echo curl_error($ch);
        } 
        else 
        {
            //getting response from server
            $response = curl_exec($ch);
            //echo'<pre>';print_r($response); echo'</pre>';
            curl_close($ch);
        }
        return $response;

    }

    /**
     * Check DBS status
     * $checkType == 1 for INT25, $checkType = 2 for INT23
     * 
     */

    public function checkDBS($checkType = null)
    {   
        $this->countStatusChange = 0;
        if (empty($checkType) || $checkType == 1){
            //get applications

            $submittedApplications = \DB::table('applications')->select('applications.*')->where(['applications.applicationStatus'=>4])->get();
            if(count($submittedApplications)>0){
                foreach($submittedApplications as $application){

                    $XMLRequest = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><ApplicationStatusCheckRequestBatch></ApplicationStatusCheckRequestBatch>');
                    $XMLRequest->addAttribute('xmlns', 'http://disclosure.service.gov.uk/app_stat_track');
                    $XMLRequest->addChild('OrganizationID', env("DBS_RO_NUMBER"));

                    $ApplicationStatusCheckRequests = $XMLRequest->addChild('ApplicationStatusCheckRequests');
                    $ApplicationDetails = $ApplicationStatusCheckRequests->addChild('ApplicationDetails');
                    $ApplicationDetails->addChild('ApplicationReferenceNumber', $application->dbsResponse_int022_DBSApplicationFormReference);
                    $ApplicationDetails->addChild('ApplicantCurrentSurname', strtoupper($application->presentSurname));
                    $ApplicationDetails->addChild('ApplicantDateOfBirth', $application->dob);


                    //echo $XMLRequest->asXML();

                    //test validation
                    $doc = new \DOMDocument();
                    $doc->loadXML($XMLRequest->asXML()); // load xml
                    libxml_use_internal_errors(true);
                    $is_valid_xml = @$doc->schemaValidate(\Storage::disk('public')->path('DBSXMLTemplates/INT025_CheckApplicationsStatus_request.xsd')); // path to xsd file

                    if ($is_valid_xml){
                        //send request
                        $response = $this->sendXMLToDBS(env("DBS_STATUS_URL"), $XMLRequest->asXML());

                        if (strlen($response) > 1){
                            $testResponse = simplexml_load_string($response);
                            if (!$testResponse) {
                                //
                            } else {
                                $XMLResponse = new \SimpleXMLElement($response);
    //echo'<pre>';print_r($XMLResponse); echo'</pre>'; exit;
                                $XMLResponse_ApplicationStatus = isset($XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->ApplicationStatus) ? $XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->ApplicationStatus.'' : null;
                                $XMLResponse_SubmittedForSponsorshipDate = isset($XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->SubmittedForSponsorshipDate) ? $XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->SubmittedForSponsorshipDate.'' : null;
                                $XMLResponse_ReceivedDate = isset($XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->ReceivedDate) ? $XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->ReceivedDate.'' : null;
                                $XMLResponse_PoliceNationalComputerSearchDate    = isset($XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->ApplicationStatus) ? $XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->PoliceNationalComputerSearchDate.''     : null;
                                $XMLResponse_AssembleCertificateDate = isset($XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->AssembleCertificateDate) ? $XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->AssembleCertificateDate.'' : null;
                                $XMLResponse_CertificateDespatchedDate = isset($XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->CertificateDespatchedDate) ? $XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->CertificateDespatchedDate.'' : null;
                                $XMLResponse_LocalPoliceSearchDate = isset($XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->LocalPoliceSearchDate) ? $XMLResponse->ApplicationStatusCheckResponse->ApplicationStatusDetails->LocalPoliceSearchDate.'' : null;
                                //save XML data
                                $saveXMLResponse = array(
                                    'dbsResponse_int025_ApplicationStatus'              => $XMLResponse_ApplicationStatus,
                                    'dbsResponse_int025_xml'                            => $response,
                                );
                                if(strlen($XMLResponse_SubmittedForSponsorshipDate) > 1){
                                    $saveXMLResponse['dbsResponse_int025_SubmittedForSponsorshipDate'] = $XMLResponse_SubmittedForSponsorshipDate;
                                    $saveXMLResponse['dbsResponse_int025_ReceivedDate'] = $XMLResponse_ReceivedDate;
                                    $saveXMLResponse['dbsResponse_int025_PoliceNationalComputerSearchDate'] = $XMLResponse_PoliceNationalComputerSearchDate;
                                    $saveXMLResponse['dbsResponse_int025_AssembleCertificateDate'] = $XMLResponse_AssembleCertificateDate;
                                    $saveXMLResponse['dbsResponse_int025_CertificateDespatchedDate'] = $XMLResponse_CertificateDespatchedDate;
                                    $saveXMLResponse['dbsResponse_int025_LocalPoliceSearchDate'] = $XMLResponse_LocalPoliceSearchDate;
                                }

                                if (in_array(strtolower($XMLResponse_ApplicationStatus), ['assemble certificate', 'certificate issued / despatched'])){//, 'no record found for details provided'
                                    $saveXMLResponse['applicationStatus'] = 5;
                                }

                                $updateApplicationDetails = \DB::table('applications')->where(['id' => $application->id])->update($saveXMLResponse);
                            }

                        }
                    }
                }
            }
        }

        if (empty($checkType) || $checkType == 2){
            $submittedApplications = \DB::table('applications')->select('applications.*')->where(['applications.applicationStatus'=>5])->get();
            if(count($submittedApplications)>0){
                foreach($submittedApplications as $application){
                    $XMLRequest = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><eResultRequest></eResultRequest>');
                    $XMLRequest->addAttribute('xmlns', 'http://disclosure.service.gov.uk/edisclosure');

                    $XMLChild_MessageHeader = $XMLRequest->addChild('MessageHeader');
                    $XMLChild_MessageHeader->addChild('MessageID', $application->dbsResponse_int022_MessageID);

                    $XMLChild_MessageHeader->addChild('RegisteredOrganizationNumber', env("DBS_RO_NUMBER"));
                    $XMLChild_MessageHeader->addChild('Timestamp', date("Y-m-d\TH:i:s"));

                    $XMLChild_eResultRequestBasedOnRefNumber = $XMLRequest->addChild('eResultRequestBasedOnRefNumber');
                    $XMLChild_eResultRequestBatch = $XMLChild_eResultRequestBasedOnRefNumber->addChild('eResultRequestBatch');
                    $XMLChild_eResultRequestBatch->addChild('DBSApplicationFormReference', $application->dbsResponse_int022_DBSApplicationFormReference);


                    //echo $XMLRequest->asXML();

                    //test validation
                    $doc = new \DOMDocument();
                    $doc->loadXML($XMLRequest->asXML()); // load xml
                    libxml_use_internal_errors(true);
                    $is_valid_xml = @$doc->schemaValidate(\Storage::disk('public')->path('DBSXMLTemplates/INT023_GetApplicationeResult_request.xsd'));

                    if ($is_valid_xml){
                        $response = $this->sendXMLToDBS(env("DBS_RESULT_URL"), $XMLRequest->asXML());
                //echo'<pre>';print_r($response); echo'</pre>';
                        if (strlen($response) > 1){
                            $testResponse = simplexml_load_string($response);

                            if (!$testResponse) {

                            } else {

                                $XMLResponse = new \SimpleXMLElement($response);

                                $XMLResponse_messageID = isset($XMLResponse->MessageHeader[0]->MessageID) ? $XMLResponse->MessageHeader[0]->MessageID : null;
                                $XMLResponse_DisclosureStatus = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureStatus) ? $XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureStatus : null;
                                $XMLResponse_DisclosureType = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureType) ? $XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureType : null;
                                $XMLResponse_DisclosureNumber = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureNumber) ? $XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureNumber : null;
                                $XMLResponse_DisclosureIssueDate = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureIssueDate) ? date("Y-m-d H:i:s", strtotime($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureIssueDate)) : null;
                                $XMLResponse_ErrorCode = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->ErrorDetails->ErrorCode) ? $XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->ErrorDetails->ErrorCode : null;
                                $XMLResponse_ErrorReason = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->ErrorDetails->ErrorReason) ? $XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->ErrorDetails->ErrorReason : null;
                                //save XML data
                                $saveXMLResponse = array(
                                    'dbsResponse_int023_MessageID'          => $XMLResponse_messageID,
                                    'dbsResponse_int023_DisclosureStatus'   => $XMLResponse_DisclosureStatus,
                                    'dbsResponse_int023_DisclosureType'     => $XMLResponse_DisclosureType,
                                    'dbsResponse_int023_DisclosureNumber'   => $XMLResponse_DisclosureNumber,
                                    'dbsResponse_int023_DisclosureIssueDate'=> $XMLResponse_DisclosureIssueDate,
                                    'dbsResponse_int023_ErrorCode'          => $XMLResponse_ErrorCode,
                                    'dbsResponse_int023_ErrorReason'        => $XMLResponse_ErrorReason,
                                    'dbsResponse_int023_xml'                => $response,
                                );

                                if (strlen($XMLResponse_DisclosureStatus) > 1){
                                    $saveXMLResponse['applicationStatus'] = 8;
                                    $this->countStatusChange++;
                                }

                                $updateApplicationDetails = \DB::table('applications')->where(['id' => $application->id])->update($saveXMLResponse);

                                //add notification
                                $requestingUserDetails = \DB::table('users')->select('*')->where('id', '=', $application->id)->first();
                                
                                $organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', $application->organisationID)->first()->organisationName;

                                $notificationDetails = [
                                    'title' => 'DBS Clearance - '.$application->forename.' '.$applicant->presentSurname.,
                                    'body' => 'A new user has accepted the invitation and registered for a DBS application. The user is '.$application->forename.' '.$applicant->presentSurname.', email: '.$applicant->application_email.'.<br />Organisation: '.$organisationName.'<br /><br />Click here to view the details: <a href="'.env('APP_URL').'applications/viewApplicationDetails/'.$application->id.'" target="_blank">DBS application details</a><br /><br />',
                                    'category' => 0,
                                    'relatedUserId' => $application->id,
                                    'relatedAction' => 'DBS Cleared',

                                    'emailTemplate' => 'notification_dbs_clearance',
                                    'emailTitle' => 'DBS Clearance',
                                    'emailBody' => 'A new user has accepted the invitation and registered for a DBS application. The user is '.$application->forename.' '.$applicant->presentSurname.', email: '.$applicant->application_email.'.<br />Organisation: '.$organisationName.'<br /><br />Click here to view the details: <a href="'.env('APP_URL').'applications/viewApplicationDetails/'.$application->id.'" target="_blank">DBS application details</a><br /><br />',
                                ];
                                $this->addNotification('siteuser', $application->organisationID, $notificationDetails, true);
                            }
                        }
                    }

                }
            }
        }

        //sent test email to check work
/*
        $emailTo = 'Sarah.Veacock@utas.utc.com';
        Mail::send('email_templates.dbs_cronjob_report', ['resetLinkExpire' => date('Y-m-d H:i:s'), 'countStatusChange' =>$this->countStatusChange], function ($message) use ($emailTo) {
               $message->to($emailTo)->cc('Michael.Dieroff@bluescreenit.co.uk');
               $message->subject(env("APP_COMPANY_NAME").' Cron Job Report');
        });
*/
    }


    public function generateDbsSubmissionReportCronTask()
    {   
        $reportDateRangeFrom = date('Y-m-d', strtotime('first day of previous month'));
        $reportDateRangeTo = date('Y-m-d', strtotime('last day of previous month'));
        
        $availableOrganisations = \DB::table('organisations')
            ->select('*')
            ->where(['organisations.organisationStatus'=>1])
            ->get();

        //get search result
        $applications = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->join('organisations', 'organisations.id', '=', 'applications.organisationID')
            ->select('applications.*', 'organisations.organisationName', 'users.email as userEmail', 'users.dbsApplication', 'users.bpssApplication', 'users.createdBy');
            if(!empty($reportDateRangeFrom)){
                $applications->where('applications.admin_submission_date', '>=', $reportDateRangeFrom);
            }
            if(!empty($reportDateRangeTo)){
                $applications->where('applications.admin_submission_date', '<=', $reportDateRangeTo);
            }

            $applications = $applications->get();
        if(isset($applications) && count($applications)> 0){
            foreach($applications as $key=>$application){
                $applications[$key]->createdByAdmin = \DB::table('users')->select('users.id', 'users.firstName', 'users.lastName')->where(['users.id'=>$application->createdBy])->first();
            }
        }

        $organisationsReport = [];
        foreach($applications as $key=>$application){
            if(isset($organisationsReport[$application->organisationID])){
                $organisationsReport[$application->organisationID]['numberOfApplications']++;
                $organisationsReport[$application->organisationID]['totalCost'] += 23;//£23 fix cost per applicant
            } else {
                $organisationsReport[$application->organisationID]['organisationName'] = $application->organisationName;
                $organisationsReport[$application->organisationID]['numberOfApplications'] = 1;
                $organisationsReport[$application->organisationID]['totalCost'] = 23;
            }

        }

        $appSettings = \DB::table('settings')
            ->select('settingValue AS consent_responsible_body_email')
            ->where(['settingName'=>'consent_responsible_body_email'])
            ->first();

        $dateRangeFrom = (date("Y-m-d",strtotime($reportDateRangeFrom)) == '1970-01-01') ? '' : 'from '.date("d/m/Y",strtotime($reportDateRangeFrom)).' ';
        $dateRangeTo = (date("Y-m-d",strtotime($reportDateRangeTo)) == '1970-01-01') ? '' : 'to '.date("d/m/Y",strtotime($reportDateRangeTo));
        if(strlen($dateRangeFrom)>0 || strlen($dateRangeTo)>0) {
            $dateRangeFrom = 'Date Range: '.$dateRangeFrom;
        } else {
            $dateRangeFrom = 'Date Range: All';
        }

        $emailTo = $appSettings->consent_responsible_body_email;
        Mail::send('email_templates.cronjob_dbs_submission_report', ['organisationsReport' => $organisationsReport, 'dateRangeFrom' => $dateRangeFrom, 'dateRangeTo' => $dateRangeTo], function ($message) use ($emailTo) {
               $message->to($emailTo);//->cc('Michael.Dieroff@bluescreenit.co.uk');
               $message->subject(env("APP_COMPANY_NAME").' DBS Submission Report');
        });

    }

    public function sendEmailsFromQueue()
    {   
        ini_set('max_execution_time', '300');
        $count = 0;
        //get search result
        $emailsArray = \DB::table('email_queue')
            ->select('email_queue.*')
            ->where(['processedStatus' => 0]);
            $emailsArray = $emailsArray->get();
            $emailsVariables = (isset($email->emailVariables) && !empty($email->emailVariables) && is_array($email->emailVariables)) ? $email->emailVariables : [];
            
        if(isset($emailsArray) && count($emailsArray)> 0){
            foreach($emailsArray as $key=>$email){
                $emailTo = $email->emailTo;
                $emailTitle = $email->emailTitle;
                $emailsVariables['emailData'] = $email->emailBody;
                Mail::send(['html'=>'email_templates.customemail'], $emailsVariables, function ($message) use ($emailTo, $emailTitle) {
                       $message->to($emailTo);
                       $message->subject($emailTitle);
                });
                //update status
                $saveEmailSubmissionStatus = array(
                    'processedStatus'      =>   1,
                    'processedOn'         => date("Y-m-d H:i:s"),
                );

                $updateApplicationDetails = \DB::table('email_queue')->where(['id' => $email->id])->update($saveEmailSubmissionStatus);
                $count++;
            }
        }
        echo $count;
    }
    
}
