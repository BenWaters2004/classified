<?php
namespace App\Http\Controllers;

include_once $_SERVER["DOCUMENT_ROOT"].'/../pdfmerger/PDFMerger.php';

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spipu\Html2Pdf\Html2Pdf;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\User;
use PDFMerger\PDFMerger;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Applications extends Controller
{

	/*
    |--------------------------------------------------------------------------
    | Applications Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling the Applications logic
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

     /**
     * Check application and submit application to DBS
     *
     * 
     */

    public function updateGDPRStatus(Request $request)
    { 
        if (!Auth::check() || !$this->checkAccess('siteuser')) return redirect("/login");
        $requestVars = $request->all();
        
        if(!isset($requestVars['applicationID']) ||empty($requestVars['applicationID'])) return redirect("/login");

        $DBSApplication = \DB::table('applications')
            ->select('applications.*')
            ->where(['applications.id'=>$requestVars['applicationID']])
            ->first();
        if(!isset($DBSApplication->organisationID) || empty($DBSApplication->organisationID)) return redirect("/login")->with('errorMessage', 'This application ID is invalid!');
        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($DBSApplication->organisationID, $userOrganisations))
            return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant') {return redirect("/login");}

        //save new data
        $gdprStatus = array(
            'admin_gdpr_checked'      => (isset($requestVars['admin_gdpr_checked']) && $requestVars['admin_gdpr_checked'] == 'true') ? 1 : 0,
        );

        $updateApplicationDetails = \DB::table('applications')->where(['id' => $DBSApplication->id])->update($gdprStatus);

        echo $updateApplicationDetails;
        
    }


    /**
     * Send application Request
     *
     * 
     */

    public function newApplicationRequest()
    {
        if (!Auth::check()) return redirect("/login");
        if(!$this->checkAccess('siteuser')) return redirect("/login");
        $adminProfile = \DB::table('users')
            ->join('organisations', 'users.organisationID', '=', 'organisations.id')
            ->select('users.*','organisations.organisationName')
            ->where(['users.id'=>Auth::user()->id])
            ->first();

        $userRolesRaw = \DB::table('user_roles')->select('role')->where('userID', '=', $adminProfile->id)->get();
        $userRolesRaw = $userRolesRaw->toArray();
        $userRoles = array();
        foreach($userRolesRaw as $userRole){
            if (!in_array($userRole->role, $userRoles)) $userRoles[] = $userRole->role;
        }
        $adminProfile->userRoles = $userRoles;
        $adminProfile->organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', Auth::user()->organisationID)->first()->organisationName;

        $userOrganisations = $this->getUserOrganisations();
        $availableOrganisations = \DB::table('organisations')
            ->select('*')
            ->where(['organisations.organisationStatus'=>1])
            ->whereIn('organisations.id', $userOrganisations)
            ->get();

        return view('applications.newApplicationRequest', ['adminProfile' => $adminProfile, 'availableOrganisations' => $availableOrganisations]);
        
    }

    public function pendingRequests()
    {   
        //only administrators
        if(!$this->checkAccess('siteuser')) return redirect("/login");
        //get search result
        $currentUserRoles = $this->getCurrentUserRoles();
        $userOrganisations = $this->getUserOrganisations();
        $applicants = \DB::table('applicants')
            ->join('organisations', 'organisations.id', '=', 'applicants.organisationID')
            ->select('applicants.*', 'organisations.organisationName');
        if (array_search('superuser', $currentUserRoles) === false){
            $applicants = $applicants->whereIn('organisations.id', $userOrganisations);
        }
        $applicants = $applicants->get();

        return view('applicant.pendingRequests', ['applicants' => $applicants]);
        
    }

    /**
     * Delete Applicant
     * 
     * @param   \Illuminate\Http\Reques
     * @return json
     */

   public function removeRequest(Request $request)
    {
        $requestVars = $request->all();

        //test access
        $organisationID = \DB::table('applicants')->select('applicants.organisationID')->where(['applicants.id'=>$requestVars['applicantID']])->first()->organisationID;
        if(!isset($organisationID) || empty($organisationID)) return redirect("/login")->with('errorMessage', 'This organisation ID is invalid!');
        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($organisationID, $userOrganisations)) return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant') {return redirect("/login");}


        $testDelete = \DB::table('applicants')->where('id', '=', $requestVars['applicantID'])->delete();
        if ($testDelete>0){
            echo 1;
        } else echo 0;


        
    }


    /**
     * Resend invite to the applicant
     * 
     * @param   \Illuminate\Http\Reques
     * @return json
     */

   public function resendRequest(Request $request)
    {
        $requestVars = $request->all();

        //test access
        $applicantDetails =  \DB::table('applicants')->select('applicants.*')->where(['applicants.id'=>$requestVars['applicantID']])->first();
        $organisationID = \DB::table('applicants')->select('applicants.organisationID')->where(['applicants.id'=>$requestVars['applicantID']])->first()->organisationID;
        if(!isset($organisationID) || empty($organisationID)) return redirect("/login")->with('errorMessage', 'This application ID is invalid!');
        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($organisationID, $userOrganisations)) return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant') {return redirect("/login");}
            $emailTo = $applicantDetails->email;

        //send email
        $emailTemplate = \DB::table('organisation_emails')
            ->where('organisation_id', $organisationID)
            ->where('template_key', 'registration')
            ->first();

        $registrationLink = env('APP_URL') . 'register/' . $applicantDetails->accessUrlCode;
        $loginUrl = env('APP_URL') . 'login';

        $replacements = [
            '{{ $firstName }}' => $applicantDetails->forename,
            '{{ $lastName }}' => $applicantDetails->surname,
            '{{ $registrationLink }}' => '<a href="' . $registrationLink . '">' . $registrationLink . '</a>',
            '{{ $loginLink }}' => '<a href="' . $loginUrl . '">' . $loginUrl . '</a>',
        ];

        $mailDriver = env('MAIL_DRIVER');
        if(isset($mailDriver) && strlen($mailDriver)>0){
            if (isset($emailTemplate) && $emailTemplate->use_default == 0 && !empty($emailTemplate->custom_content)) {
                $customBody = $emailTemplate->custom_content;
                foreach ($replacements as $placeholder => $actual) {
                    $customBody = str_replace($placeholder, $actual, $customBody);
                }

                Mail::send([], [], function ($message) use ($emailTo, $customBody) {
                    $message->to($emailTo)
                            ->subject('Security Clearance Registration - Get ClassifIeD')
                            ->setBody($customBody, 'text/html');
                });
            } else {
                Mail::send('email_templates.dbs_registration_request', [
                    'targetEmail' => $emailTo,
                    'registrationLink' =>env('APP_URL').'register/'.$applicantDetails->accessUrlCode
                ], function ($message) use ($emailTo) {
                    $message->to($emailTo);
                    $message->subject('Security Clearance Registration - Get ClassifIeD');
                });
            }
        }

        //update when was last sent
        $updateBPSSVRDetails = \DB::table('applicants')->where(['id' => $applicantDetails->id])->update(['lastEmailSent' => date("Y-m-d H:i:s")]);
        echo 1;
        
    }


    /**
     * Save request details, send email and mobile text
     *
     * 
     */
    public function sendApplicationRequest(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        if(!$this->checkAccess('siteuser')) return redirect("/login");
        
        $requestVars = $request->all();
        $validateArray = [
            'forename'          => 'max:50',
            'surname'           => 'max:50',
            'emailAddress'      => 'unique:applicants,email',
            'emailAddress'      => 'required|email|max:255',
            
        ];
        $messsages = array(
            'emailAddress.unique'=>'email_exists',
        );
        $this->validate($request, $validateArray, $messsages); 

        //echo'<pre>';print_r($requestVars);echo'</pre>';

        //generate application code (to be texted to end user)
        $applicationCode = $this->generateRandomKey(10); 
        $accessUrlCode = $this->generateRandomKey(36);
        $emailTo = $requestVars['emailAddress'];
        $applicantDetails = array(
            'email'     => $requestVars['emailAddress'],
            'accessUrlCode' => $accessUrlCode,
            'forename' => $requestVars['forename'],
            'surname' => $requestVars['surname'],
            'organisationID' => $requestVars['organisationID'],
            'lastEmailSent'=> date("Y-m-d H:i:s"),
            'userStatus'=> 0,
            'createdOn' => date("Y-m-d"),
            'createdBy' => Auth::user()->id,
        );
        //set application type
        if (!isset($requestVars['applicationType']) || empty($requestVars['applicationType']) || $requestVars['applicationType'] == 1){
            $applicantDetails['dbsApplication'] = 1;
            $applicantDetails['bpssApplication'] = 0;
            $applicantDetails['REVALonsite'] = 0;
            $applicantDetails['REVALoffsite'] = 0;
        } else if(isset($requestVars['applicationType']) && $requestVars['applicationType'] == 2){
            $applicantDetails['dbsApplication'] = 1;
            $applicantDetails['bpssApplication'] = 1;
            $applicantDetails['REVALonsite'] = 0;
            $applicantDetails['REVALoffsite'] = 0;
        } else if(isset($requestVars['applicationType']) && $requestVars['applicationType'] == 4){
            $applicantDetails['dbsApplication'] = 1;
            $applicantDetails['bpssApplication'] = 1;
            $applicantDetails['REVALonsite'] = 1;
            $applicantDetails['REVALoffsite'] = 0;
        } else if(isset($requestVars['applicationType']) && $requestVars['applicationType'] == 5){
            $applicantDetails['dbsApplication'] = 1;
            $applicantDetails['bpssApplication'] = 1;
            $applicantDetails['REVALonsite'] = 0;
            $applicantDetails['REVALoffsite'] = 1;
        }

        //Use yoti checkbox
        $useYoti = isset($requestVars['useYoti']) && $requestVars['useYoti'] === 'on';
        $applicantDetails['useYoti'] = $useYoti ? 1 : 0;

       
        $newApplicantID = \DB::table('applicants')->insertGetId($applicantDetails);
        if (!empty($newApplicantID) && is_numeric($newApplicantID)){
            //send email
            Mail::send('email_templates.dbs_registration_request', ['targetEmail' => $requestVars['emailAddress'], 'registrationLink' =>env('APP_URL').'register/'.$accessUrlCode], function ($message) use ($emailTo) {
                $message->to($emailTo);
                $message->subject('DBS Registration');
            });


            return redirect("/applications/viewApplicant/".$newApplicantID);
        } else {
            return back()->withInput();
        }
    }

    /**
     * BPSS VR Review Application
     *
     * 
     */

    public function reviewBPSSVRDetails($applicantID, $option = null){

        if (!Auth::check() || !$this->checkAccess('siteuser')) return redirect("/login");

        if(!isset($applicantID) ||empty($applicantID)) return redirect("/login");

       $applicantDetails = \DB::table('users')
            ->select('users.dbsApplication', 'users.bpssApplication', 'users.organisationID')
            ->where(['users.id'=>$applicantID])
            ->first();

        //check if there is an application for our user, if not create one
        $existingApplicationID = \DB::table('bpss_vr')
            ->join('users', 'users.id', '=', 'bpss_vr.userID')
            ->select('bpss_vr.id', 'bpss_vr.formStatus')
            ->where(['bpss_vr.userID'=>$applicantID])
            ->first();
        if (!isset($existingApplicationID->id) || empty($existingApplicationID->id)){
            $applicationDetails = array(
                'userID'     => $applicantID,
                'organisationID' => $applicantDetails->organisationID,
                'createdOn' => date("Y-m-d H:i:s"),
                'createdBy' => Auth::user()->id,
            );
           
            $applicationID = \DB::table('bpss_vr')->insertGetId($applicationDetails);
        } else $applicationID = $existingApplicationID->id;

        //check if the AA No is available
        $checkAA = \DB::table('bpss_vr')
                ->select('id','approved_access_no')
                ->where(['id'=>$applicationID])
                ->first();
        if(isset($checkAA->id) && empty($checkAA->approved_access_no)){
            //check if there is an AA number in the user table
            $checkAAUser = \DB::table('users')->select('id','applicationCode')->where(['id'=>$applicantID])->first();
            if(isset($checkAAUser->applicationCode) && strlen($checkAAUser->applicationCode)>0){
                $updateAANumber = ['approved_access_no' => $checkAAUser->applicationCode];
                $updateAANumberResult = \DB::table('bpss_vr')->where(['id' => $applicationID])->update($updateAANumber);
            }
        }


        //force download PDF
        if(!empty($option) && $option =='downloadPDF'){
            $this->downloadBPSSVRPDF($applicationID);
        }

        

        $DBSApplication = \DB::table('applications')
            ->select('applications.*')
            ->where(['applications.userID'=>$applicantID])
            ->first();

        if(!isset($DBSApplication->organisationID) || empty($DBSApplication->organisationID)) return redirect("/login")->with('errorMessage', 'This application ID is invalid!');
        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($DBSApplication->organisationID, $userOrganisations)) return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant') {return redirect("/login");}

        $DBSApplication->userTitle = \DB::table('user_titles')->select('userTitle')->where(['id'=>$DBSApplication->title])->first()->userTitle;
        $DBSApplication->birth_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->birth_country])->first()->name;
        $DBSApplication->address_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->address_country])->first()->name;

        $DBSApplication->supporting_passport_country_fullName = '';
        if (!empty($DBSApplication->supporting_passport_country)) {
            $supporting_passport_country_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$DBSApplication->supporting_passport_country])->first();
            if (isset($supporting_passport_country_raw) && !empty($supporting_passport_country_raw->id)){
                $DBSApplication->supporting_passport_country_fullName = $supporting_passport_country_raw->name;
            } else {
                
            }
        }
        
        $DBSApplication->otherNames = \DB::table('application_extra_names')->select('*')->where(['applicationID'=>$DBSApplication->id])->get();
        
        //supporting documents
        $DBSApplication->supporting_documents = \DB::table('application_supporting_documents')->select('*')->where(['applicationID' => $DBSApplication->id])->orderBy('document_path', 'desc')->get();
        
        if (isset($applicantDetails->bpssApplication) && !empty($applicantDetails->bpssApplication)){
            $BPSSApplication = \DB::table('bpss_applications')
                ->join('organisations', 'bpss_applications.organisationID', '=', 'organisations.id')
                ->select('bpss_applications.*','organisations.organisationName')
                ->where(['bpss_applications.userID'=>$applicantID])
                ->first();
        
            $BPSSVR = \DB::table('bpss_vr')
                ->select('bpss_vr.*')
                ->where(['bpss_vr.userID'=>$applicantID])
                ->first();
            $BPSSVR_identity_documents = \DB::table('bpss_vr_identity_documents')
                ->select('bpss_vr_identity_documents.*')
                ->where(['bpss_vr_identity_documents.verificationRecordID'=>$applicationID])
                ->get();

            if (isset($BPSSApplication->dual_citizenship) && !empty($BPSSApplication->dual_citizenship)) {
                $BPSSApplication->dualNationalities = \DB::table('bpss_application_extra_nationalities')->select('*')->where(['applicationID'=>$BPSSApplication->id])->get();
                if(count($BPSSApplication->dualNationalities) > 0) {
                    foreach($BPSSApplication->dualNationalities as $key => $extraNationality){
                        $extraNationality_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$extraNationality->country])->first();
                        if (isset($extraNationality_raw) && !empty($extraNationality_raw->id)){
                            $BPSSApplication->dualNationalities[$key]->dual_citizenship_country_fullName = $extraNationality_raw->name;
                        } else {
                            $BPSSApplication->dualNationalities[$key]->dual_citizenship_country_fullName = '';
                        }
                    }
                }
            } else {
                $BPSSApplication->dualNationalities = new \stdClass();
            }
            //get BPSSVR references
            $BPSSVR_reference1 = \DB::table('bpss_vr_references')
                ->select('bpss_vr_references.*')
                ->where(['bpss_vr_references.verificationRecordID'=>$BPSSVR->id])
                ->skip(0)->take(1)->first();
            $BPSSVR_reference2 = \DB::table('bpss_vr_references')
                ->select('bpss_vr_references.*')
                ->where(['bpss_vr_references.verificationRecordID'=>$BPSSVR->id])
                ->skip(1)->take(1)->first();
            $BPSSVR_reference3 = \DB::table('bpss_vr_references')
                ->select('bpss_vr_references.*')
                ->where(['bpss_vr_references.verificationRecordID'=>$BPSSVR->id])
                ->skip(2)->take(1)->first();


            //get references from BPSS
            $reference1 = \DB::table('bpss_application_personal_referee')
                ->select('bpss_application_personal_referee.*')
                ->where(['bpss_application_personal_referee.applicationID'=>$BPSSApplication->id])
                ->skip(0)
                ->take(1)
                ->first();
            if(isset($reference1->date_from) && date('Y-m-d',strtotime($reference1->date_from) != '1970-01-01') && isset($reference1->date_to) && date('Y-m-d',strtotime($reference1->date_to))){
                    $reference1->referee_length_of_association = $this->time_diff_string($reference1->date_from, $reference1->date_to, true);
            } else {$reference1->referee_length_of_association = '';}
            
            $reference2 = \DB::table('bpss_application_personal_referee')
                ->select('bpss_application_personal_referee.*')
                ->where(['bpss_application_personal_referee.applicationID'=>$BPSSApplication->id])
                ->skip(1)
                ->take(1)
                ->first();
            if(isset($reference2->date_from) && date('Y-m-d',strtotime($reference2->date_from) != '1970-01-01') && isset($reference2->date_to) && date('Y-m-d',strtotime($reference2->date_to))){
                    $reference2->referee_length_of_association = $this->time_diff_string($reference2->date_from, $reference2->date_to, true);
            } 

            //get completed by and signature
            $BPSSVR->adminCompletedFullName = '';
            if(isset($BPSSVR->admin_approved_by) && !empty($BPSSVR->admin_approved_by)){
                $adminCompletedFullName = \DB::table('users')
                    ->select('id', 'firstName', 'lastName')
                    ->where(['id'=>$BPSSVR->admin_approved_by])
                    ->first();
                if(isset($adminCompletedFullName->id) && !empty($adminCompletedFullName->id)){
                    $BPSSVR->adminCompletedFullName = $adminCompletedFullName->firstName.' '.$adminCompletedFullName->lastName;
                }

                //getSignature
                $signature = \DB::table('user_signatures')->select('id', 'signature_path')->where(['userid'=>$BPSSVR->admin_approved_by])->first();
                if(isset($signature->id) && !empty($signature->id)){
                    $BPSSVR->adminSignaturePath = $signature->signature_path;
                }
            }
            
        } else {
            $BPSSApplication = new \stdClass();
            $BPSSApplication->dualNationalities = new \stdClass();
            $BPSSVR = new \stdClass();
            $BPSSVR->identity_documents = new \stdClass();
        }

        $referenceRecords = \DB::table('bpss_vr_references_log')->where(['verificationRecordID' => $BPSSVR->id])->count();

/*
        
        */
        //echo'<pre>';print_r($referenceRecords);echo'</pre>';
        return view('applications.reviewBPSSVRDetails', ['applicationID' => $applicationID, 'DBSApplication' => $DBSApplication,'BPSSApplication' => $BPSSApplication, 'BPSSVR' => $BPSSVR, 'BPSSVR_identity_documents' => $BPSSVR_identity_documents, 'reference1' => $reference1, 'reference2' => $reference2, 'BPSSVR_reference1' => $BPSSVR_reference1, 'BPSSVR_reference2' => $BPSSVR_reference2, 'BPSSVR_reference3' => $BPSSVR_reference3, 'referenceRecords' => $referenceRecords, 'currentTime' => time()]);
        
    }

    public function loadReferenceLog($verificationRecordID){
        if (!Auth::check()) return redirect("/login");
        if (!isset($verificationRecordID) ||empty($verificationRecordID)) return redirect("/");
        $verificationRecordID = intval($verificationRecordID);
        $DBSVRApplication = \DB::table('bpss_vr')
            ->select('bpss_vr.userID')
            ->where(['bpss_vr.id'=>$verificationRecordID])
            ->first();

        if(isset($DBSVRApplication->userID) && !empty($DBSVRApplication->userID)){
            $userOrg = \DB::table('users')
            ->select('users.organisationID')
            ->where(['users.id'=>$DBSVRApplication->userID])
            ->first();
        }

        if(!isset($userOrg->organisationID) || empty($userOrg->organisationID)) return redirect("/login")->with('errorMessage', 'This application ID is invalid!');
        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($userOrg->organisationID, $userOrganisations)){
            return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        }
        else if(Auth::user()->userType == 'applicant') {return redirect("/login");}

        $referenceRecords = [];
        $referenceRecordsResults = \DB::table('bpss_vr_references_log')
                    ->leftJoin('bpss_vr_references', 'bpss_vr_references_log.referenceID', '=', 'bpss_vr_references.id')
                    ->leftJoin('bpss_vr_references_forms', 'bpss_vr_references_log.id', '=', 'bpss_vr_references_forms.reference_logID')
                    ->select('bpss_vr_references_log.id',
                            'bpss_vr_references_log.referenceStatus AS log_referenceStatus',
                            'bpss_vr_references_log.referenceType AS log_referenceType',
                            'bpss_vr_references_log.createdOn AS log_createdOn',
                            'bpss_vr_references_log.createdBy AS log_createdById',
                            'bpss_vr_references_log.valid AS log_valid',

                            'bpss_vr_references.verificationRecordID',
                            'bpss_vr_references.referee_name',
                            'bpss_vr_references.referee_email',
                            'bpss_vr_references.referee_relationship', 
                            'bpss_vr_references.referee_address', 
                            'bpss_vr_references.referee_length_of_association', 

                            'bpss_vr_references_forms.id AS reference_form_id',
                            'bpss_vr_references_forms.reference_nameOfCandidate',
                            'bpss_vr_references_forms.reference_dobCandidate',
                            'bpss_vr_references_forms.reference_relationYesNo',
                            'bpss_vr_references_forms.reference_relationCandidate',
                            'bpss_vr_references_forms.reference_preriodKnownFrom',
                            'bpss_vr_references_forms.reference_periodKnownTo',
                            'bpss_vr_references_forms.reference_natureOfAq',
                            'bpss_vr_references_forms.reference_subjectHonest',
                            'bpss_vr_references_forms.reference_factorsConcerning',
                            'bpss_vr_references_forms.referenceDetails_fullName',
                            'bpss_vr_references_forms.referenceDetails_contactAddress',
                            'bpss_vr_references_forms.referenceDetails_contactTelephome',
                            'bpss_vr_references_forms.referenceDetails_contactEmail',
                            'bpss_vr_references_forms.createdOn AS referenceDetails_createdOn',
                            'bpss_vr_references_forms.reference_logID AS referenceDetails_logID',
                        )
                    ->where(['bpss_vr_references_log.verificationRecordID' => $verificationRecordID])
                    ->orderBy('log_createdOn', 'desc')
                    ->get();

        //echo'<pre>';print_r($referenceRecordsResults);echo'</pre>';
        foreach ($referenceRecordsResults as $key => $logDetails) {
            $logDetails->requestedBy = '';
            if(isset($logDetails->log_createdById) && !empty($logDetails->log_createdById)){
                $requestedByRaw = \DB::table('users')
                    ->select('id', 'firstName', 'lastName')
                    ->where(['id'=>intval($logDetails->log_createdById)])
                    ->first();
                if(isset($requestedByRaw->id) && !empty($requestedByRaw->id)){
                    $logDetails->requestedBy = $requestedByRaw->firstName.' '.$requestedByRaw->lastName;
                }
            }
            $referenceRecords[] = $logDetails;            
        }

        return view('applications.referenceLog', ['verificationRecordID' => $verificationRecordID, 'referenceRecords' => $referenceRecords]);

    }

    public function addDocumentVerifiedDetails(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            $documentDate = null;
            if(date('Y-m-d', strtotime(str_replace('/', '-', $requestVars['document_date_of_issue_name']))) != '1970-01-01'){
                $documentDateRaw = explode('/',$requestVars['document_date_of_issue_name']);
                $documentDate = $documentDateRaw[2] . '-' . $documentDateRaw[1] . '-' . $documentDateRaw[0];
            }

            $documentDetails = array(
                'verificationRecordID'     => $requestVars['applicationID'],
                'document_name' => $requestVars['document_name'],
                'document_date_of_issue_name' => $documentDate,
            );
           
            $documentID = \DB::table('bpss_vr_identity_documents')->insertGetId($documentDetails);
            if(isset($documentID) && $documentID>0){
                 echo json_encode(['status' => 1, 'documentID' => $documentID]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }

    public function removeDocumentVerifiedDetails(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            $testDelete = \DB::table('bpss_vr_identity_documents')->where(['id' => $requestVars['documentID'], 'verificationRecordID' => $requestVars['applicationID']])->delete();

            if(isset($testDelete) && $testDelete){
                 echo json_encode(['status' => 1]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }


    /**
     * Update BPSS VR information collected during the review step
     * 
     * @param  \Illuminate\Http\Request
     * @return redirect
     */

   public function updateBPSSVRDetails(Request $request)
    {   
        if (!Auth::check() || !$this->checkAccess('siteuser')) return redirect("/login");
        $requestVars = $request->all();
        
        if(!isset($requestVars['formSubmitted']) || empty($requestVars['formSubmitted']) || !isset($requestVars['userID']) || empty($requestVars['userID'])) return redirect("/users/consolidatedSearch");
        
        

        //get applicant details
        $applicantDetails = \DB::table('users')
        ->select('users.*')
        ->where(['users.id'=>$requestVars['userID']])
        ->first();
        if(!isset($applicantDetails->id) || empty($applicantDetails->id)) return redirect("/users/consolidatedSearch");
        
        //check if there is an application for our user, if not create one
        $existingApplicationID = \DB::table('bpss_vr')
            ->join('users', 'users.id', '=', 'bpss_vr.userID')
            ->select('bpss_vr.id', 'bpss_vr.formStatus')
            ->where(['bpss_vr.userID'=>$applicantDetails->id])
            ->first();
        if (!isset($existingApplicationID->id) || empty($existingApplicationID->id)){
            //check status
            if (isset($existingApplicationID->applicationStatus) && $existingApplicationID->applicationStatus != 0){ return redirect('/'); }
            $applicationDetails = array(
                'userID'     => $requestVars['userID'],
                'organisationID' => $applicantDetails->organisationID,
                'createdOn' => date("Y-m-d H:i:s"),
                'createdBy' => Auth::user()->id,
            );
           
            $applicationID = \DB::table('bpss_vr')->insertGetId($applicationDetails);
        } else $applicationID = $existingApplicationID->id;

        //get DBS data
        $DBSApplication = \DB::table('applications')
            ->select('applications.*')
            ->where(['applications.userID'=>$requestVars['userID']])
            ->first();

        $DBSApplication->userTitle = \DB::table('user_titles')->select('userTitle')->where(['id'=>$DBSApplication->title])->first()->userTitle;
        $DBSApplication->otherNames = \DB::table('application_extra_names')->select('*')->where(['applicationID'=>$DBSApplication->id])->get();

        //get BPSS data
        $BPSSApplication = \DB::table('bpss_applications')
                ->join('organisations', 'bpss_applications.organisationID', '=', 'organisations.id')
                ->select('bpss_applications.*','organisations.organisationName')
                ->where(['bpss_applications.userID'=>$requestVars['userID']])
                ->first();
        $dualNationalities = '';
        if (isset($BPSSApplication->dual_citizenship) && !empty($BPSSApplication->dual_citizenship)) {
            $BPSSApplication->dualNationalities = \DB::table('bpss_application_extra_nationalities')->select('*')->where(['applicationID'=>$BPSSApplication->id])->get();
            if(isset($BPSSApplication->dualNationalities) && count($BPSSApplication->dualNationalities) > 0){
                foreach ($BPSSApplication->dualNationalities as $key => $extraNationality){
                    $extraNationality_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$extraNationality->country])->first();
                    if (isset($extraNationality_raw) && !empty($extraNationality_raw->id)){
                        if($key>0){$dualNationalities .=', ';}
                        $dualNationalities .=$extraNationality_raw->name;
                    }
                }
            }
        }


        if (isset($BPSSApplication->dual_citizenship) && !empty($BPSSApplication->dual_citizenship)) {
            $BPSSApplication->dualNationalities = \DB::table('bpss_application_extra_nationalities')->select('*')->where(['applicationID'=>$BPSSApplication->id])->get();
            if(count($BPSSApplication->dualNationalities) > 0) {
                foreach($BPSSApplication->dualNationalities as $key => $extraNationality){
                    $extraNationality_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$extraNationality->country])->first();
                    if (isset($extraNationality_raw) && !empty($extraNationality_raw->id)){
                        $BPSSApplication->dualNationalities[$key]->dual_citizenship_country_fullName = $extraNationality_raw->name;
                    } else {
                        $BPSSApplication->dualNationalities[$key]->dual_citizenship_country_fullName = '';
                    }
                }
            }
        } else {
            $BPSSApplication->dualNationalities = new \stdClass();
        }


        //get form details, validate and update the existing entry
        $expityDate = date('Y-m-d');
        if(isset($requestVars['employement_type']) && $requestVars['employement_type']=='employee'){
            $expityDate = date('Y-m-d', strtotime('+10 years'));
        } elseif(isset($requestVars['employement_type']) && $requestVars['employement_type']=='contractor'){
            $expityDate = date('Y-m-d', strtotime('+3 years'));
        }
        $disclosure_comments = null;
        if (isset($DBSApplication->dbsResponse_int023_DisclosureStatus) && trim($DBSApplication->dbsResponse_int023_DisclosureStatus) =='Certificate contains no information'){
            $disclosure_comments = 'no_convictions_for_diclosure';
        }elseif (isset($DBSApplication->dbsResponse_int023_DisclosureStatus) && trim($DBSApplication->dbsResponse_int023_DisclosureStatus) =='Please wait to view applicant certificate'){
            $disclosure_comments = 'disclosure_comments_see_notes';
        }
        $admin_certify_post = Auth::user()->position;
        if(strlen(Auth::user()->position) == 0){
            $admin_certify_post = (isset($requestVars['admin_certify_post'])) ? $requestVars['admin_certify_post'] : null;
        }
        $admin_certify_telephone_number = Auth::user()->phoneNumber;
        if(strlen(Auth::user()->phoneNumber) == 0){
            $admin_certify_telephone_number = (isset($requestVars['admin_certify_telephone_number'])) ? $requestVars['admin_certify_telephone_number'] : null;
        }

        if (isset($requestVars['passport_date_of_issue']) && date("Y-m-d", strtotime(str_replace('/', '-', $requestVars['passport_date_of_issue']))) != '1970-01-01'){
            $passportDateRaw = explode('/',$requestVars['passport_date_of_issue']);
            $passportDate = $passportDateRaw[2] . '-' . $passportDateRaw[1] . '-' . $passportDateRaw[0];
        } else $passportDate = null;

        $supporting_passport_country_fullName = '';
        if (!empty($DBSApplication->supporting_passport_country)) {
            $supporting_passport_country_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$DBSApplication->supporting_passport_country])->first();
            if (isset($supporting_passport_country_raw) && !empty($supporting_passport_country_raw->id)){
                $supporting_passport_country_fullName = $supporting_passport_country_raw->name;
            }
        }

        $known_cargo = null;
        if (isset($requestVars['known_cargo']) && $requestVars['known_cargo'] == 'yes') {$known_cargo = 'yes';}
        if (isset($requestVars['known_cargo']) && $requestVars['known_cargo'] == 'no') {$known_cargo = 'no';}
//echo'<pre>';print_r($requestVars);echo'</pre>';exit;         
        
        $bpssVRFormData = array(
            'completedDate'                     => date("Y-m-d H:i:s"),
            'userID'                            => $applicantDetails->id,
            'approved_access_no'                => isset($requestVars['approved_access_no']) ? $requestVars['approved_access_no'] : null,
            'admin_approved_by'                 => Auth::user()->id,
            'expiry_date'                       => $expityDate,
            'present_surname'                   => $DBSApplication->presentSurname,
            'present_forenames'                 => (strlen($DBSApplication->middlename)>0) ? $DBSApplication->forename.' '.$DBSApplication->middlename : $DBSApplication->forename,
            //use others name from current DBS table (or future independent BPSS table)
            'address_line_1'                    => $DBSApplication->address_line_1,
            'address_line_2'                    => $DBSApplication->address_line_2,
            'address_town'                      => $DBSApplication->address_town,
            'address_county'                    => $DBSApplication->address_county,
            'address_postcode'                  => $DBSApplication->address_postcode,
            'address_country'                   => $DBSApplication->address_country,
            'contact_number'                    => '+'.$DBSApplication->contact_number_country_code.' '.$DBSApplication->contact_number,
            'dob'                               => $DBSApplication->dob,
            'birth_town'                        => $DBSApplication->birth_town,
            'birth_country'                     => $DBSApplication->birth_country,
            'present_nationality'               => $BPSSApplication->present_nationality,
            'dual_nationalities'                => $dualNationalities,
            'former_nationality_details'        => $BPSSApplication->former_nationality_details,
            'naturalisation_certificate_number' => $BPSSApplication->naturalisation_certificate_number,
            'naturalisation_certificate_date'   => $BPSSApplication->naturalisation_certificate_date,
            'dual_citizenship'                  => $BPSSApplication->dual_citizenship,
            'lawfully_resident_in_uk'           => $BPSSApplication->lawfully_resident_in_uk,
            'continued_residence_restrictions'  => $BPSSApplication->continued_residence_restrictions,
            'continued_residence_restrictions_details'  => $BPSSApplication->continued_residence_restrictions_details,
            'subject_to_immigration_control'    => $BPSSApplication->subject_to_immigration_control,
            'subject_to_immigration_control_details'    => $BPSSApplication->subject_to_immigration_control_details,
            'freedom_to_take_employment'        => $BPSSApplication->freedom_to_take_employment,
            'freedom_to_take_employment_details'=> $BPSSApplication->freedom_to_take_employment_details,
            'ho_port_reference_number'          => $BPSSApplication->ho_port_reference_number,
            'passport_number'                   => $DBSApplication->supporting_passport,
            'passport_date_of_issue'            => $passportDate,
            'passport_country'                  => $supporting_passport_country_fullName,
            'verification_of_national_status_received'  => isset($requestVars['verification_of_national_status_received']) ? $requestVars['verification_of_national_status_received'] : 0,
            'last_years_employemnt_confirmed'   => isset($requestVars['last_years_employemnt_confirmed']) ? $requestVars['last_years_employemnt_confirmed'] : 0,
            'academic_qualifications_received'  => isset($requestVars['academic_qualifications_received']) ? $requestVars['academic_qualifications_received'] : 0,
            'document_received_support_employment_history'  => isset($requestVars['document_received_support_employment_history']) ? $requestVars['document_received_support_employment_history'] : 0,
            'denied_party_screening'            => isset($requestVars['denied_party_screening']) ? $requestVars['denied_party_screening'] : 0,
            'items_of_interest'                 => isset($requestVars['items_of_interest']) ? $requestVars['items_of_interest'] : 0,
            'security_matrix'                   => isset($requestVars['security_matrix']) ? $requestVars['security_matrix'] : 0,
            'clearance_type'                    => isset($requestVars['clearance_type']) ? $requestVars['clearance_type'] : null,
            'admin_certify_userid'              => Auth::user()->id,
            'admin_certify_name'                => Auth::user()->title.' '.Auth::user()->firstName.' '.Auth::user()->lastName,
            'admin_certify_post'                => $admin_certify_post,
            'admin_certify_date'                => date("Y-m-d H:i:s"),
            'admin_certify_telephone_number'    => $admin_certify_telephone_number,
            'type_of_disclosure'                => $DBSApplication->dbsResponse_int023_DisclosureType,
            'disclosure_certificate_date'       => $DBSApplication->dbsResponse_int023_DisclosureIssueDate,
            'disclosure_certificate_reference_no'   => $DBSApplication->dbsResponse_int022_DBSApplicationFormReference,
            'disclosure_comments'               => $disclosure_comments,
            'status'                            => isset($requestVars['status']) ? $requestVars['status'] : null,
            'admin_approval_name'               => isset($requestVars['admin_approval_name']) ? $requestVars['admin_approval_name'] : null,
            'admin_approval_title'              => isset($requestVars['admin_approval_title']) ? $requestVars['admin_approval_title'] : null,
            'admin_approval_date'               => date("Y-m-d"),
            'admin_approval_notes'              => isset($requestVars['admin_approval_notes']) ? $requestVars['admin_approval_notes'] : null,
            'employement_type'                  => isset($requestVars['employement_type']) ? $requestVars['employement_type'] : null,
            'application_type'                  => isset($requestVars['application_type']) ? $requestVars['application_type'] : null,
            'organisationID'                    => $BPSSApplication->organisationID,
            'utas_site'                         => $BPSSApplication->organisationName,
            'known_cargo'                       => $known_cargo,
            'contractor_company'                => isset($requestVars['contractor_company']) ? $requestVars['contractor_company'] : $BPSSApplication->contractor_name_of_company,
            'contractor_position'               => isset($requestVars['contractor_position']) ? $requestVars['contractor_position'] : $BPSSApplication->contractor_position,
        );

        $updateBPSSVRDetails = \DB::table('bpss_vr')->where(['id' => $applicationID])->update($bpssVRFormData);
        if($updateBPSSVRDetails>0){
            
            //save image
            if ($request->hasFile('new_photograph')) {
                $image = $request->file('new_photograph');
                $realname = pathinfo($request->file('new_photograph')->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $image->getClientOriginalExtension();
                $new_name = $applicationID.'.'.strtolower($extension);
                //create new resized photo
                $img = \Image::make($image->getRealPath());
                $img->resize(120, 120, function ($constraint) {
                    $constraint->aspectRatio();                 
                });

                $img->stream();
                \Storage::disk('public_images')->put('bpssvr_photos'.'/'.$new_name, $img, 'public');
                $bpssVRImageData = array('photo_path'   => $new_name);
                $updateBPSSVRDetails = \DB::table('bpss_vr')->where(['id' => $applicationID])->update($bpssVRImageData);
            }
        }
//echo'<pre>';print_r($requestVars);echo'</pre>';exit;
        //update or save references
        //delete references to save the new ones 
        $testDeleteReferences = \DB::table('bpss_vr_references')->where('verificationRecordID', '=', $applicationID)->delete();
        $reference1Data = array(
            'verificationRecordID' => $applicationID,
            'referee_name' => isset($requestVars['reference1_referee_name']) ? $requestVars['reference1_referee_name'] : null,
            'referee_email' => isset($requestVars['reference1_referee_email']) ? $requestVars['reference1_referee_email'] : null,
            'referee_relationship' => isset($requestVars['reference1_referee_relationship']) ? $requestVars['reference1_referee_relationship'] : null,
            'referee_address' => isset($requestVars['reference1_referee_address']) ? $requestVars['reference1_referee_address'] : null,
            'referee_length_of_association' => isset($requestVars['reference1_referee_length_of_association']) ? $requestVars['reference1_referee_length_of_association'] : null,
        );
        //insert new line
        $newReference1ID = \DB::table('bpss_vr_references')->insertGetId($reference1Data);

        $reference2Data = array(
            'verificationRecordID' => $applicationID,
            'referee_name' => isset($requestVars['reference2_referee_name']) ? $requestVars['reference2_referee_name'] : null,
            'referee_email' => isset($requestVars['reference2_referee_email']) ? $requestVars['reference2_referee_email'] : null,
            'referee_relationship' => isset($requestVars['reference2_referee_relationship']) ? $requestVars['reference2_referee_relationship'] : null,
            'referee_address' => isset($requestVars['reference2_referee_address']) ? $requestVars['reference2_referee_address'] : null,
            'referee_length_of_association' => isset($requestVars['reference2_referee_length_of_association']) ? $requestVars['reference2_referee_length_of_association'] : null,
        );
        //insert new line
        $newReference2ID = \DB::table('bpss_vr_references')->insertGetId($reference2Data);

        $reference3Data = array(
            'verificationRecordID' => $applicationID,
            'referee_name' => isset($requestVars['reference3_referee_name']) ? $requestVars['reference3_referee_name'] : null,
            'referee_email' => isset($requestVars['reference3_referee_email']) ? $requestVars['reference3_referee_email'] : null,
            'referee_relationship' => isset($requestVars['reference3_referee_relationship']) ? $requestVars['reference3_referee_relationship'] : null,
            'referee_address' => isset($requestVars['reference3_referee_address']) ? $requestVars['reference3_referee_address'] : null,
            'referee_length_of_association' => isset($requestVars['reference3_referee_length_of_association']) ? $requestVars['reference3_referee_length_of_association'] : null,
        );
        //insert new line
        $newReference3ID = \DB::table('bpss_vr_references')->insertGetId($reference3Data);


        return redirect("applications/reviewBPSSVRDetails/".$applicantDetails->id."");

    }

    /**
     * Generate BPSS VR PDF
     * 
     *  
     */

public function downloadBPSSVRPDF($applicationID){
    if (!Auth::check()) return redirect("/login");

    if(!isset($applicationID) ||empty($applicationID)) return redirect("/users/consolidatedSearch");
    
    $BPSSVR = \DB::table('bpss_vr')->select('bpss_vr.*')->where(['bpss_vr.id'=>$applicationID])->first();
    if (!isset($BPSSVR->id) || empty($BPSSVR->id)) return redirect("/users/consolidatedSearch");
    $BPSSVR->address_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$BPSSVR->address_country])->first()->name;
    $BPSSVR->birth_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$BPSSVR->birth_country])->first()->name;
    
    $BPSSVR->identity_documents = \DB::table('bpss_vr_identity_documents')->select('bpss_vr_identity_documents.*')->where(['bpss_vr_identity_documents.verificationRecordID'=>$applicationID])->get();
    //get DBSS applicationm ID in order to get the other names
    $DBSApplication = \DB::table('applications')->select('applications.*')->where(['applications.userID'=>$BPSSVR->userID])->first();
    $BPSSVR->dbsResponse_int023_DisclosureNumber = $DBSApplication->dbsResponse_int023_DisclosureNumber;
    // $BPSSVR->supporting_passport = $DBSApplication->supporting_passport;
    // $BPSSVR->supporting_passport_date_of_issue = '';
    // $BPSSVR->supporting_passport_country_fullName = '';
    // if (!empty($DBSApplication->supporting_passport_country)) {
    //     $supporting_passport_country_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$DBSApplication->supporting_passport_country])->first();
    //     if (isset($supporting_passport_country_raw) && !empty($supporting_passport_country_raw->id)){
    //         $BPSSVR->supporting_passport_country_fullName = $supporting_passport_country_raw->name;
    //     }
    // }

    $otherNames = \DB::table('application_extra_names')->select('*')->where(['applicationID'=>$DBSApplication->id])->get();
    $reference1 = \DB::table('bpss_vr_references')
        ->select('bpss_vr_references.*')
        ->where(['bpss_vr_references.verificationRecordID'=>$BPSSVR->id])
        ->skip(0)->take(1)->first();
    $reference2 = \DB::table('bpss_vr_references')
        ->select('bpss_vr_references.*')
        ->where(['bpss_vr_references.verificationRecordID'=>$BPSSVR->id])
        ->skip(1)->take(1)->first();
    $reference3 = \DB::table('bpss_vr_references')
        ->select('bpss_vr_references.*')
        ->where(['bpss_vr_references.verificationRecordID'=>$BPSSVR->id])
        ->skip(2)->take(1)->first();

    if(isset($BPSSVR->admin_approved_by) && !empty($BPSSVR->admin_approved_by)){
        $signature = \DB::table('user_signatures')->select('id', 'signature_path')->where(['userid'=>$BPSSVR->admin_approved_by])->first();
        if(isset($signature->id) && !empty($signature->id)){
            $BPSSVR->adminSignaturePath = $signature->signature_path;
        }
    }

    //get BPSS data
    $BPSSApplication = \DB::table('bpss_applications')
            ->join('organisations', 'bpss_applications.organisationID', '=', 'organisations.id')
            ->select('bpss_applications.*','organisations.organisationName')
            ->where(['bpss_applications.userID'=>$BPSSVR->userID])
            ->first();
    $securitymatrixPath = \DB::table('new_applicant_documents')
            ->select('document_path')
            ->where(['userID'=>$BPSSVR->userID, 'file_type'=>'secmx'])
            ->orderBy('id', 'desc')
            ->first();

    if(isset($securitymatrixPath->document_path) && strlen($securitymatrixPath->document_path)>0) {
        $securitymatrixImagePath = env('APP_DOCUMENT_ROOT').'/public/uploads/new_applicant_documents/'.$securitymatrixPath->document_path; 

        $securitymatrixExtension = substr($securitymatrixPath->document_path, strrpos($securitymatrixPath->document_path, '.') + 1);
        if(strtolower($securitymatrixExtension) != 'pdf') {
            $securitymatrixImagePath = env('APP_DOCUMENT_ROOT').'/public/uploads/new_applicant_documents/secmxnopdf.pdf';
        }     
    } else {
       $securitymatrixImagePath = '';
    }


    $mkdenialPath = \DB::table('new_applicant_documents')
            ->select('document_path')
            ->where(['userID'=>$BPSSVR->userID, 'file_type'=>'mkden'])
            ->orderBy('id', 'desc')
            ->first();
    if(isset($mkdenialPath->document_path) && strlen($mkdenialPath->document_path)>0) {
        $mkdenialImagePath = env('APP_DOCUMENT_ROOT').'/public/uploads/new_applicant_documents/'.$mkdenialPath->document_path; 

        $mkdenialExtension = substr($mkdenialPath->document_path, strrpos($mkdenialPath->document_path, '.') + 1);
        if(strtolower($mkdenialExtension) != 'pdf') {
            $mkdenialImagePath = env('APP_DOCUMENT_ROOT').'/public/uploads/new_applicant_documents/mkdennopdf.pdf';
        } 
        
    } else {
       $mkdenialImagePath = '';
    }


    set_time_limit(6000);

    $currentTime = time();
    $bpssVrPdfName = 'BPSS_VR_'.$BPSSVR->present_forenames.'_'.$BPSSVR->present_surname.'.pdf';
    $bpssVrFileNamePart1 = 'BPSS_VR_'.$currentTime.'_1_'.$BPSSVR->id.'.pdf';
    $bpssVrFileNamePart2 = 'BPSS_VR_'.$currentTime.'_2_'.$BPSSVR->id.'.pdf';
    $bpssVrFileNamePart3 = 'BPSS_VR_'.$currentTime.'_3_'.$BPSSVR->id.'.pdf';
    $bpssVrFileNameSecurityMatrix = 'BPSS_VR_sec_'.$BPSSVR->id.'_'.$currentTime.'.pdf';
    $bpssVrFileNameMkDenial = 'BPSS_VR_mkd_'.$BPSSVR->id.'_'.$currentTime.'.pdf';

    //part1
    $html2pdf_p1 = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(8,8,8,8));
    //$html2pdf_p1->setModeDebug();
    $html2pdf_p1->pdf->SetTitle($bpssVrPdfName);
    $html2pdf_p1->writeHTML(view('bpssvr_templates.bpssvr-page1', ['BPSSVR' => $BPSSVR, 'otherNames' => $otherNames]));
    $html2pdf_p1->writeHTML(view('bpssvr_templates.bpssvr-page2', ['BPSSVR' => $BPSSVR, 'reference1' => $reference1, 'reference2' => $reference2, 'reference3' => $reference3, 'currentTime' => time()]));
    $html2pdf_p1->writeHTML(view('bpssvr_templates.bpssvr-page3', ['BPSSVR' => $BPSSVR, 'otherNames' => $otherNames]));

    $bpssVrFileContentPart1 = $html2pdf_p1->output('bpssvr_pdf'.'/'.$bpssVrFileNamePart1, 'S');
    $pdfPart1 = fopen(env('APP_DOCUMENT_ROOT').'/pdf/'.$bpssVrFileNamePart1, "w");
    fwrite($pdfPart1, $bpssVrFileContentPart1);
    fclose($pdfPart1);

    //part2
    $html2pdf_p2 = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(8,8,8,8));
    $html2pdf_p2->writeHTML(view('bpssvr_templates.bpssvr-dbsdeclaration', ['BPSSApplication' => $BPSSApplication]));
    $bpssVrFileContentPart2 = $html2pdf_p2->output('bpssvr_pdf'.'/'.$bpssVrFileNamePart2, 'S');
    $pdfPart2 = fopen(env('APP_DOCUMENT_ROOT').'/pdf/'.$bpssVrFileNamePart2, "w");
    fwrite($pdfPart2, $bpssVrFileContentPart2);
    fclose($pdfPart2);

    //part3
    $html2pdf_p3 = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(8,8,8,8));
    $html2pdf_p3->writeHTML(view('bpssvr_templates.bpssvr-page2a', ['BPSSApplication' => $BPSSApplication]));
    $html2pdf_p3->writeHTML(view('bpssvr_templates.bpssvr-page2b', ['BPSSApplication' => $BPSSApplication]));
    $bpssVrFileContentPart3 = $html2pdf_p3->output('bpssvr_pdf'.'/'.$bpssVrFileNamePart3, 'S');
    $pdfPart3 = fopen(env('APP_DOCUMENT_ROOT').'/pdf/'.$bpssVrFileNamePart3, "w");
    fwrite($pdfPart3, $bpssVrFileContentPart3);
    fclose($pdfPart3);
    $pdf = new PDFMerger;

    $pdf->addPDF(env('APP_DOCUMENT_ROOT').'/pdf/'.$bpssVrFileNamePart1);
    if(strlen($securitymatrixImagePath)>0 && strlen(file_get_contents($securitymatrixImagePath))>0){
        $pdf->addPDF($securitymatrixImagePath);
    }
    $pdf->addPDF(env('APP_DOCUMENT_ROOT').'/pdf/'.$bpssVrFileNamePart2);
    if(strlen($mkdenialImagePath)>0 && strlen(file_get_contents($mkdenialImagePath))>0){
        $pdf->addPDF($mkdenialImagePath);
    }
    $pdf->addPDF(env('APP_DOCUMENT_ROOT').'/pdf/'.$bpssVrFileNamePart3);

    //remove pdf parts
    $pdf->merge('download',$bpssVrPdfName);
    unlink(env('APP_DOCUMENT_ROOT').'/pdf/'.$bpssVrFileNamePart1);
    unlink(env('APP_DOCUMENT_ROOT').'/pdf/'.$bpssVrFileNamePart2);
    unlink(env('APP_DOCUMENT_ROOT').'/pdf/'.$bpssVrFileNamePart3);
    


}



    /**
     * Administrator view of the user details
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

   public function viewApplicant($applicantID = null)
    {
        if (!Auth::check()) return redirect("/login");
        if(!$this->checkAccess('siteuser') || empty($applicantID))  return redirect("/login");
        $applicantProfile = \DB::table('applicants')
            ->join('organisations', 'organisations.id', '=', 'applicants.organisationID')
            ->select('applicants.*', 'organisations.organisationName')
            ->where(['applicants.id'=>$applicantID]);
        if(!$this->checkAccess('superuser')){
            $applicantProfile->where(['applicants.organisationID'=>Auth::user()->organisationID]);
        }
        $applicantProfile = $applicantProfile->first();
        if (!isset($applicantProfile->id))  return redirect("/login");
        
        $applicantDetails =[];
        if (isset($applicantProfile->userID) && !empty($applicantProfile->userID)){
            $applicantDetails = \DB::table('users')
                ->select('users.*')
                ->where(['users.id'=>$applicantProfile->userID])
                ->first();
        }

        return view('applications.viewApplicant', ['applicantProfile' => $applicantProfile, 'applicantDetails' => $applicantDetails]);
        
    }


    public function searchApplications()
    {   
        if (!Auth::check() || !$this->checkAccess('siteuser')) return redirect("/login");

        //get search result
        $applications = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->join('organisations', 'organisations.id', '=', 'applications.organisationID')
            ->select('applications.*', 'organisations.organisationName', 'users.email as userEmail', 'users.dbsApplication', 'users.bpssApplication')
            ->get();
        if(isset($applications) && count($applications)> 0){
            foreach($applications as $key=>$application){
                $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.id', 'bpss_applications.applicationStatus')->where(['bpss_applications.userID'=>$application->userID])->first();
                $applications[$key]->BPSSApplicationID = (isset($BPSSApplication->id) && !empty($BPSSApplication->id)) ? $BPSSApplication->id : 0;
                $applications[$key]->BPSSApplicationStatus = (isset($BPSSApplication->id) && !empty($BPSSApplication->id)) ? $BPSSApplication->applicationStatus : 0;

                $applications[$key]->otherNames = \DB::table('application_extra_names')->select('application_extra_names.id', 'application_extra_names.other_forename', 'application_extra_names.other_middlename', 'application_extra_names.other_surname')->where(['application_extra_names.applicationID'=>$application->id])->get();
            }
        }
        //echo'<pre>';print_r($applications);echo'</pre>';
        return view('applications.searchApplications', ['applications' => $applications]);
        
    }


    /**
     * DBS Review Application
     *
     * 
     */

    public function viewApplicationDetails($applicationID){

        if (!Auth::check() || !$this->checkAccess('siteuser')) return redirect("/login");

        if(!isset($applicationID) ||empty($applicationID)) return redirect("/login");
    
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*', 'users.dbsApplication', 'users.bpssApplication')
            ->where(['applications.id'=>$applicationID])
            ->first();

        if(!isset($DBSApplication->organisationID) || empty($DBSApplication->organisationID)) return redirect("/login")->with('errorMessage', 'This application ID is invalid!');
        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($DBSApplication->organisationID, $userOrganisations))
            return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant') {return redirect("/login");}

        $DBSApplication->userTitle = \DB::table('user_titles')->select('userTitle')->where(['id'=>$DBSApplication->title])->first()->userTitle;
        $DBSApplication->employment_sector_name = \DB::table('employment_sectors')->select('employment_sector_name')->where(['id'=>$DBSApplication->employment_sector])->first()->employment_sector_name;
        $DBSApplication->birth_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->birth_country])->first()->name;
        $DBSApplication->address_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->address_country])->first()->name;

        if (isset($DBSApplication->certificate_address_country) && strlen($DBSApplication->certificate_address_country) == 3){
            $DBSApplication->certificate_address_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->certificate_address_country])->first()->name;
        } else {
            $DBSApplication->certificate_address_country_fullName = '';
        }

        $DBSApplication->otherNames = \DB::table('application_extra_names')->select('*')->where(['applicationID'=>$DBSApplication->id])->get();
        $DBSApplication->previousAddresses = \DB::table('application_previous_addresses')->select('*')->where(['applicationID'=>$DBSApplication->id])->orderBy('previous_address_from', 'asc')->get();

        if (!empty($DBSApplication->previousAddresses)) {
            foreach($DBSApplication->previousAddresses as $key => $previousAddress){
                $previousAddress_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$previousAddress->previous_address_country])->first();

                if (isset($previousAddress_raw) && !empty($previousAddress_raw->id)){
                    $DBSApplication->previousAddresses[$key]->previous_address_country_fullName = $previousAddress_raw->name;
                } else {
                    $DBSApplication->previousAddresses[$key]->previous_address_country_fullName = '';
                }
            }
        }

        $DBSApplication->supporting_passport_country_fullName = '';
        if (!empty($DBSApplication->supporting_passport_country)) {
            $supporting_passport_country_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$DBSApplication->supporting_passport_country])->first();
            if (isset($supporting_passport_country_raw) && !empty($supporting_passport_country_raw->id)){
                $DBSApplication->supporting_passport_country_fullName = $supporting_passport_country_raw->name;
            } else {
                
            }
        }

        if (isset($DBSApplication->bpssApplication) && !empty($DBSApplication->bpssApplication)){
            $BPSSApplication = \DB::table('bpss_applications')
                ->join('users', 'users.id', '=', 'bpss_applications.userID')
                ->select('bpss_applications.*')
                ->where(['bpss_applications.userID'=>$DBSApplication->userID])
                ->first();
        } else $BPSSApplication = new \stdClass();

        $loggedUserOrganisationDetails = \DB::table('organisations')->select('*')->where(['id'=>$DBSApplication->organisationID])->first();
        $roName = \DB::table('settings')->select('settingName', 'settingValue')->where(['settingName'=>'responsible_organisation_name'])->first()->settingValue;

        $DBSApplication->supporting_documents = \DB::table('application_supporting_documents')->select('*')->where(['applicationID' => $applicationID])->orderBy('document_path', 'desc')->get();

        return view('applications.adminReviewDBSApplication', ['DBSApplication' => $DBSApplication,'BPSSApplication' => $BPSSApplication, 'loggedUserOrganisationDetails' => $loggedUserOrganisationDetails, 'roName' => $roName]);
        
    }


    /**
     * Check application and submit application to DBS
     *
     * 
     */

    public function submitToDBS(Request $request)
    { 
        if (!Auth::check() || !$this->checkAccess('siteuser')) return redirect("/login");
        $requestVars = $request->all();
        
        if(!isset($requestVars['applicationID']) ||empty($requestVars['applicationID'])) return redirect("/login");

        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*')
            ->where(['applications.id'=>$requestVars['applicationID']])
            ->first();
        if(!isset($DBSApplication->organisationID) || empty($DBSApplication->organisationID)) return redirect("/login")->with('errorMessage', 'This application ID is invalid!');
        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($DBSApplication->organisationID, $userOrganisations)) return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant') {return redirect("/login");}

        //check status
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 1){ return redirect('/login')->with('errorMessage', 'Application has not been completed by the applicant.'); }

        //save new data
        $saveSubmissionDetails = array(
            //'applicationStatus'             => 3,
            'admin_confirm_valid_nino'      => isset($requestVars['admin_confirm_valid_nino']) ? $requestVars['admin_confirm_valid_nino'] : 0,
            'admin_confirm_valid_dln'       => isset($requestVars['admin_confirm_valid_dln']) ? $requestVars['admin_confirm_valid_dln'] : 0,
            'admin_confirm_valid_passport'  => isset($requestVars['admin_confirm_valid_passport']) ? $requestVars['admin_confirm_valid_passport'] : 0,
            'admin_further_evidence_details'=> $requestVars['admin_further_evidence_details'],
            'admin_submitted_by'            => Auth::user()->id,
            'admin_submission_date'         => date("Y-m-d H:i:s"),
        );

        $updateApplicationDetails = \DB::table('applications')->where(['id' => $DBSApplication->id])->update($saveSubmissionDetails);

        //check details have saved
        $DBSApplicationCheck = \DB::table('applications')->select('applications.*')->where(['applications.id'=>$DBSApplication->id])->first();
        
        if(isset($DBSApplicationCheck->applicationStatus) && $DBSApplicationCheck->applicationStatus == 1){
            $this->int022SubmitDisclosureApplication($DBSApplicationCheck->id);
        }
        
    }


    /**
     * Check application and submit application to DBS
     *
     * 
     */

    public function int022SubmitDisclosureApplication($applicationID)
    { 
        if (!Auth::check() || !$this->checkAccess('siteuser') || !isset($applicationID) ||empty($applicationID)) return redirect("/login");
        $DBSApplication = \DB::table('applications')->select('applications.*')->where(['applications.id'=>$applicationID])->first();

        if(!isset($DBSApplication->organisationID) || empty($DBSApplication->organisationID) || !isset($DBSApplication->id) || empty($DBSApplication->id)) return redirect("/login")->with('errorMessage', 'This application ID is invalid!');
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && $DBSApplication->organisationID != Auth::user()->organisationID) return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant') {return redirect("/login");}
        if(!isset($DBSApplication->applicationStatus) || empty($DBSApplication->applicationStatus) || $DBSApplication->applicationStatus != 1) return redirect("applications/viewApplicationDetails/".$applicationID)->with('errorMessage', 'You must review the application first');


        $DBSApplication->userTitle = \DB::table('user_titles')->select('userTitle')->where(['id'=>$DBSApplication->title])->first()->userTitle;
        $DBSApplication->employment_sector_name = \DB::table('employment_sectors')->select('employment_sector_name')->where(['id'=>$DBSApplication->employment_sector])->first()->employment_sector_name;
        $DBSApplication->birth_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->birth_country])->first()->name;
        $DBSApplication->address_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->address_country])->first()->name;

        if (isset($DBSApplication->certificate_address_country) && strlen($DBSApplication->certificate_address_country) == 3){
            $DBSApplication->certificate_address_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->certificate_address_country])->first()->name;
        } else {
            $DBSApplication->certificate_address_country_fullName = '';
        }

        $DBSApplication->otherNames = \DB::table('application_extra_names')->select('*')->where(['applicationID'=>$DBSApplication->id])->get();
        $DBSApplication->previousAddresses = \DB::table('application_previous_addresses')->select('*')->where(['applicationID'=>$DBSApplication->id])->orderBy('previous_address_from', 'asc')->get();

        $loggedUserOrganisationDetails = \DB::table('organisations')->select('*')->where(['id'=>$DBSApplication->organisationID])->first();
        $roName = \DB::table('settings')->select('settingName', 'settingValue')->where(['settingName'=>'responsible_organisation_name'])->first()->settingValue;

        ######################################################### GENERATE XML ###############################################
        $MessageID = 10000000 + $DBSApplication->id;
        //populate Template with variables
        $XMLRequest = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><DisclosureApplication></DisclosureApplication>');
        $XMLRequest->addAttribute('xmlns', 'http://disclosure.service.gov.uk/edisclosure');
        $XMLChild_MessageHeader = $XMLRequest->addChild('MessageHeader');
        $XMLChild_MessageHeader->addChild('MessageID', $MessageID);
        $XMLChild_MessageHeader->addChild('RegisteredOrganizationNumber', env("DBS_RO_NUMBER"));
        $XMLChild_MessageHeader->addChild('Timestamp', date("Y-m-d\TH:i:s"));

        $BasicApplication = $XMLRequest->addChild('BasicApplication');
        
        $ApplicantDetails = $BasicApplication->addChild('ApplicantDetails');
        $ApplicantDetails->addChild('Title', $DBSApplication->userTitle);
        $ApplicantDetails->addChild('Forename', strtoupper($DBSApplication->forename));
        if(strlen($DBSApplication->middlename)>0){
            $ApplicantDetails_Middlenames = $ApplicantDetails->addChild('Middlenames');
            $ApplicantDetails_Middlenames->addChild('Middlename', strtoupper($DBSApplication->middlename));
        }
        $ApplicantDetails->addChild('PresentSurname', strtoupper($DBSApplication->presentSurname));
        $ApplicantDetails_CurrentAddress = $ApplicantDetails->addChild('CurrentAddress');
            $ApplicantDetails_CurrentAddress_Address = $ApplicantDetails_CurrentAddress->addChild('Address');
                $ApplicantDetails_CurrentAddress_Address->addChild('AddressLine1', strtoupper($DBSApplication->address_line_1));
                if(strlen($DBSApplication->address_line_2)>0){
                    $ApplicantDetails_CurrentAddress_Address->addChild('AddressLine2', strtoupper($DBSApplication->address_line_2));
                }
                $ApplicantDetails_CurrentAddress_Address->addChild('AddressTown', strtoupper($DBSApplication->address_town));
                if(strlen($DBSApplication->address_county)>0){
                    $ApplicantDetails_CurrentAddress_Address->addChild('AddressCounty', strtoupper($DBSApplication->address_county));
                }
                $ApplicantDetails_CurrentAddress_Address->addChild('Postcode', $DBSApplication->address_postcode);
                $ApplicantDetails_CurrentAddress_Address->addChild('CountryCode', $DBSApplication->address_country);
            $ApplicantDetails_CurrentAddress->addChild('ResidentFromDate', $DBSApplication->current_address_from);


        if(isset($DBSApplication->previousAddresses) && count($DBSApplication->previousAddresses) > 0){
            $ApplicantDetails->addChild('Last5yearsPreviousAddressAvailable', 'y');
            foreach ($DBSApplication->previousAddresses as $key => $previousAddress){
                $ApplicantDetails_PreviousAddress = $ApplicantDetails->addChild('PreviousAddress');
                    $ApplicantDetails_PreviousAddress_Address = $ApplicantDetails_PreviousAddress->addChild('Address');
                        $ApplicantDetails_PreviousAddress_Address->addChild('AddressLine1', strtoupper($previousAddress->previous_address_line_1));
                        if(strlen($previousAddress->previous_address_line_2)>0){
                            $ApplicantDetails_PreviousAddress_Address->addChild('AddressLine2', strtoupper($previousAddress->previous_address_line_2));
                        }
                        $ApplicantDetails_PreviousAddress_Address->addChild('AddressTown', strtoupper($previousAddress->previous_address_town));
                        if(strlen($previousAddress->previous_address_county)>0){
                            $ApplicantDetails_PreviousAddress_Address->addChild('AddressCounty', strtoupper($previousAddress->previous_address_county));
                        }
                        $ApplicantDetails_PreviousAddress_Address->addChild('Postcode', $previousAddress->previous_address_postcode);
                        $ApplicantDetails_PreviousAddress_Address->addChild('CountryCode', $previousAddress->previous_address_country);
                    $ApplicantDetails_PreviousAddress_ResidentDates = $ApplicantDetails_PreviousAddress->addChild('ResidentDates');
                        $ApplicantDetails_PreviousAddress_ResidentDates->addChild('ResidentFromDate', $previousAddress->previous_address_from);
                        $ApplicantDetails_PreviousAddress_ResidentDates->addChild('ResidentToDate', $previousAddress->previous_address_to);
            }
        } else {
            $ApplicantDetails->addChild('Last5yearsPreviousAddressAvailable', 'n');
        }

        $ApplicantDetails->addChild('DateOfBirth', $DBSApplication->dob);
        $ApplicantDetails->addChild('Gender', $DBSApplication->gender);
        if(strlen($DBSApplication->supporting_nino)>0){
            $ApplicantDetails->addChild('NINumberAvailable', 'y');
            $ApplicantDetails->addChild('NINumber', strtoupper($DBSApplication->supporting_nino));
        } else {
            $ApplicantDetails->addChild('NINumberAvailable', 'n');
        }

        $ApplicantDetails_AdditionalApplicantDetails = $ApplicantDetails->addChild('AdditionalApplicantDetails');
            $dbs_profile_id_available = ($DBSApplication->user_dbs_profile_id_available) ? 'y':'n';
            $ApplicantDetails_AdditionalApplicantDetails->addChild('DBSProfileNumberAvailable', $dbs_profile_id_available);
            if ($DBSApplication->user_dbs_profile_id_available){
                $ApplicantDetails_AdditionalApplicantDetails->addChild('DBSProfileId', $DBSApplication->user_dbs_profile_id);
            }
            $ApplicantDetails_AdditionalApplicantDetails->addChild('EmailAddress', $DBSApplication->application_email);

            $ApplicantDetails_OtherNames = $ApplicantDetails_AdditionalApplicantDetails->addChild('OtherNames');
                $otherNames = (isset($DBSApplication->otherNames) && count($DBSApplication->otherNames) > 0) ? 'y':'n';
                $ApplicantDetails_OtherNames->addChild('OtherNamesAvailable', $otherNames);
                if(isset($DBSApplication->otherNames) && count($DBSApplication->otherNames) > 0){
                    foreach ($DBSApplication->otherNames as $otherName){
                        $ApplicantDetails_OtherNames_OtherName = $ApplicantDetails_OtherNames->addChild('OtherName');
                            $ApplicantDetails_OtherNames_OtherName->addChild('OtherForname', strtoupper($otherName->other_forename));
                            if(strlen($otherName->other_middlename)>0){
                                $OtherNames_OtherName_Middlenames = $ApplicantDetails_OtherNames_OtherName->addChild('OtherMiddlenames');
                                $OtherNames_OtherName_Middlenames->addChild('Middlename', strtoupper($otherName->other_middlename));
                            }
                            $ApplicantDetails_OtherNames_OtherName->addChild('OtherSurname', strtoupper($otherName->other_surname));
                            $ApplicantDetails_OtherNames_OtherName->addChild('UsedFrom', $otherName->dateFrom);
                            if(strlen($otherName->dateTo)>0 && date("Y-m-d",strtotime($otherName->dateTo)) != '1970-01-01'){
                                $ApplicantDetails_OtherNames_OtherName->addChild('UsedTo', $otherName->dateTo);
                            }
                    }
                }

            $ApplicantDetails_AdditionalApplicantDetails->addChild('BirthTown', strtoupper($DBSApplication->birth_town));
            $ApplicantDetails_AdditionalApplicantDetails->addChild('BirthCountry', $DBSApplication->birth_country);
            if(isset($DBSApplication->mobile_number) && strlen($DBSApplication->mobile_number) > 0){
                $ApplicantDetails_AdditionalApplicantDetails->addChild('MobileNumber', '+'.$DBSApplication->mobile_number_country_code.$DBSApplication->mobile_number);
            }
            if(isset($DBSApplication->contact_number) && strlen($DBSApplication->contact_number) > 0){
                $ApplicantDetails_AdditionalApplicantDetails->addChild('ContactNumber', '+'.$DBSApplication->contact_number_country_code.$DBSApplication->contact_number);
            }

            $ApplicantDetails_ReceivePaperCertificate = $ApplicantDetails_AdditionalApplicantDetails->addChild('ReceivePaperCertificate');
                $paperCertificate = ($DBSApplication->supporting_paper_certificate) ? 'y':'n';
                $ApplicantDetails_ReceivePaperCertificate->addChild('ReceivePaperCertificate', $paperCertificate);

                $paperCertificateAtCurrentAddress = (!$DBSApplication->user_paper_certificate_different_address) ? 'y':'n';
                $ApplicantDetails_ReceivePaperCertificate->addChild('ReceivePaperCertificateAtCurrentAddress', $paperCertificateAtCurrentAddress);
                if ($DBSApplication->user_paper_certificate_different_address){
                    $ApplicantDetails_ReceivePaperCertificate->addChild('RecipientName', strtoupper($DBSApplication->certificate_address_recipient_name));
                    $ApplicantDetails_ReceivePaperCertificate->addChild('RecipientDepartment', '-');

                    $ApplicantDetails_RecipientAddress = $ApplicantDetails_ReceivePaperCertificate->addChild('RecipientAddress');
                        $ApplicantDetails_RecipientAddress->addChild('AddressLine1', strtoupper($DBSApplication->certificate_address_line_1));
                        if(strlen($DBSApplication->certificate_address_line_2)>0){
                            $ApplicantDetails_RecipientAddress->addChild('AddressLine2', strtoupper($DBSApplication->certificate_address_line_2));
                        }
                        $ApplicantDetails_RecipientAddress->addChild('AddressTown', strtoupper($DBSApplication->certificate_address_town));
                        if(strlen($DBSApplication->certificate_address_county)>0){
                            $ApplicantDetails_RecipientAddress->addChild('AddressCounty', strtoupper($DBSApplication->certificate_address_county));
                        }
                        $ApplicantDetails_RecipientAddress->addChild('Postcode', $DBSApplication->certificate_address_postcode);
                        $ApplicantDetails_RecipientAddress->addChild('CountryCode', $DBSApplication->certificate_address_country);

                }

            $DeclarationByApplicant = ($DBSApplication->terms_accepted) ? 'y':'n';
            $ApplicantDetails_AdditionalApplicantDetails->addChild('DeclarationByApplicant', $DeclarationByApplicant);
            $ApplicantDetails_AdditionalApplicantDetails->addChild('UnspentConvictionsWithROA', 'n');
            if(env("APP_ENV") == 'production'){
                if($DBSApplication->dbs_consent){
                    $ApplicantDetails_AdditionalApplicantDetails->addChild('IsConsentProvidedtoRO', 'y');
                    $ApplicantDetails_AdditionalApplicantDetails->addChild('Consented3rdPartyEmailAddress', $loggedUserOrganisationDetails->organisationEmail);
                }
            }
            $ApplicantDetails_AdditionalApplicantDetails->addChild('PurposeOfCheck', ucfirst($DBSApplication->purpose_of_check));
            if($DBSApplication->purpose_of_check =='employment'){
                $ApplicantDetails_AdditionalApplicantDetails->addChild('EmploymentSector', strtoupper($DBSApplication->employment_sector_name));
                $ApplicantDetails_AdditionalApplicantDetails->addChild('PositionAppliedFor', strtoupper($DBSApplication->position_applied_for));
                $ApplicantDetails_AdditionalApplicantDetails->addChild('EmployerName', strtoupper($DBSApplication->name_of_employer));
            } else if($DBSApplication->purpose_of_check =='other'){
                $ApplicantDetails_AdditionalApplicantDetails->addChild('Other', ' ');
            }

        $ApplicantDetails_ApplicantIdentityDetails = $ApplicantDetails->addChild('ApplicantIdentityDetails');
            $ApplicantDetails_ApplicantIdentityDetails->addChild('IdentityVerified', 'y');
            $ApplicantDetails_ApplicantIdentityDetails->addChild('EvidenceCheckedBy', strtoupper(Auth::user()->title.' '.Auth::user()->firstName.' '.Auth::user()->lastName));

            $ApplicantDetails_PassportDetails = $ApplicantDetails_ApplicantIdentityDetails->addChild('PassportDetails');
                $ValidPassportAvailable = (strlen($DBSApplication->supporting_passport)>0) ? 'y':'n';
                $ApplicantDetails_PassportDetails->addChild('ValidPassportAvailable', $ValidPassportAvailable);
                if (strlen($DBSApplication->supporting_passport)>0){
                    $ApplicantDetails_PassportDetails->addChild('PassportNumber', $DBSApplication->supporting_passport);
                    $ApplicantDetails_PassportDetails->addChild('PassportCountryOfIssue', $DBSApplication->supporting_passport_country);
                }

            $ApplicantDetails_DriverLicenceDetails = $ApplicantDetails_ApplicantIdentityDetails->addChild('DriverLicenceDetails');
                $UKDrivingLicenceAvailable = (strlen($DBSApplication->supporting_dln)>0 && ($DBSApplication->supporting_dln_type == 1 || $DBSApplication->supporting_dln_type == 2)) ? 'y':'n';
                $ApplicantDetails_DriverLicenceDetails->addChild('UKDrivingLicenceAvailable', $UKDrivingLicenceAvailable);
                if (strlen($DBSApplication->supporting_dln)>0){
                    $ApplicantDetails_DriverLicenceDetails->addChild('DriverLicenceNumber', $DBSApplication->supporting_dln);
                }

        $ApplicantDetails_UpdateServiceSubscription = $ApplicantDetails->addChild('UpdateServiceSubscription');
            $ApplicantDetails_UpdateServiceSubscription->addChild('JoinUpdateService', 'n');
        $BasicApplication->addChild('RegOrgApplicationReference', 'REF_'.$DBSApplication->id);
        
//echo $XMLRequest->asXML(); exit;
        //test validation
        $doc = new \DOMDocument();
        $doc->loadXML($XMLRequest->asXML()); // load xml
        libxml_use_internal_errors(true);
        $is_valid_xml = @$doc->schemaValidate(\Storage::disk('public')->path('DBSXMLTemplates/INT022_SubmitDisclosureApplication_request.xsd')); // path to xsd file

        if (!$is_valid_xml){
            
            $errors = libxml_get_errors();
            //echo '<b>Invalid XML:</b> validation failed<br>';
            $errorsArray = [];
            $errorsArray['message'] = 'This application details are failing the validation!';
            foreach(libxml_get_errors() AS $key => $error){
                $errorsArray[$key] = $error->message;
            }
            //echo'<pre>';print_r($errorsArray);echo'</pre>';exit;
            return redirect("applications/viewApplicationDetails/".$applicationID)->with('errorMessage', $errorsArray)->send();
        }else{
            //echo '<b>Valid XML:</b> validation passed<br>';
        }  

        //echo $XMLRequest->asXML(); exit;
//submit XML
if(env("APP_ENV") == 'production'){
    $response = $this->sendXMLToDBS(env("DBS_SUBMIT_URL"), $XMLRequest->asXML());
} else {
    $response='
    <?xml version="1.0" encoding="UTF-8"?>
    <ApplicationAcceptanceResponse xmlns="http://disclosure.service.gov.uk/edisclosure">
        <MessageHeader>
        <MessageID>10000001</MessageID>
        <RegisteredOrganizationNumber>1234567890</RegisteredOrganizationNumber>
        <Timestamp>'.date("Y-m-d H:is").'</Timestamp>
        </MessageHeader>
        <ApplicationStatusDetails>
            <RegOrgApplicationReference>demoref_'.$applicationID.'</RegOrgApplicationReference>
            <ApplicationStatus>ok</ApplicationStatus>
            <DBSApplicationFormReference>E0000000000'.$applicationID.'</DBSApplicationFormReference> 
        </ApplicationStatusDetails>
    </ApplicationAcceptanceResponse>';
}
//echo'<pre>';print_r($response);echo'</pre>';exit;
        if (strlen($response) > 1){

            $testResponse = simplexml_load_string($response);

            if (!$testResponse) {

            }

            $XMLResponse = new \SimpleXMLElement($response);

            $XMLResponse_messageID = isset($XMLResponse->MessageHeader->MessageID) ? $XMLResponse->MessageHeader->MessageID : null;
            $XMLResponse_ApplicationStatus = isset($XMLResponse->ApplicationStatusDetails->ApplicationStatus) ? $XMLResponse->ApplicationStatusDetails->ApplicationStatus : null;
            $XMLResponse_RegOrgApplicationReference = isset($XMLResponse->ApplicationStatusDetails->RegOrgApplicationReference) ? $XMLResponse->ApplicationStatusDetails->RegOrgApplicationReference : null;
            $XMLResponse_DBSApplicationFormReference = isset($XMLResponse->ApplicationStatusDetails->DBSApplicationFormReference) ? $XMLResponse->ApplicationStatusDetails->DBSApplicationFormReference : null;

            $XMLResponse_OriginalDBSApplicationFormReference = isset($XMLResponse->ApplicationStatusDetails->ErrorDetails[0]->OriginalDBSApplicationFormReference) ? $XMLResponse->ApplicationStatusDetails->ErrorDetails[0]->OriginalDBSApplicationFormReference : null;
            $XMLResponse_ErrorCode = isset($XMLResponse->ApplicationStatusDetails->ErrorDetails[0]->ErrorCode) ? $XMLResponse->ApplicationStatusDetails->ErrorDetails[0]->ErrorCode : null;
            $XMLResponse_ErrorReason = isset($XMLResponse->ApplicationStatusDetails->ErrorDetails[0]->ErrorReason) ? $XMLResponse->ApplicationStatusDetails->ErrorDetails[0]->ErrorReason : null;

            $saveXMLResponse = [];
            if(strtolower($XMLResponse_ApplicationStatus) == 'ok'){
                $saveXMLResponse['applicationStatus'] = 4;
                $saveXMLResponse = array(
                
                    'dbsResponse_int022_MessageID'                  => $XMLResponse_messageID,
                    'dbsResponse_int022_RegOrgApplicationReference' => $XMLResponse_RegOrgApplicationReference,
                    'dbsResponse_int022_ApplicationStatus'          => $XMLResponse_ApplicationStatus,
                    'dbsResponse_int022_DBSApplicationFormReference'=> $XMLResponse_DBSApplicationFormReference,
                    'applicationStatus'                             => 4,
                    'dbsResponse_int022_xml'                        => $response,
                    'dbsResponse_int022_submission_date'            => date("Y-m-d H:i:s"),
                );
        
            } else {

                $saveXMLResponse = array(
                    'dbsResponse_int022_ErrorCode'                  => $XMLResponse_ErrorCode,
                    'dbsResponse_int022_ErrorReason'                => $XMLResponse_ErrorReason,
                    'dbsResponse_int022_xml'                        => $response,
                );

                $errorsArray = [];
                $errorsArray['message'] = 'The application has been rejected by DBS because of the error(s) listed bellow. Please make the necessary changes and then re-submit!';
                $count = 0;
                $xmlErrors= $XMLResponse->ApplicationStatusDetails->ErrorDetails;
                foreach($XMLResponse->ApplicationStatusDetails->ErrorDetails AS $error){
                    $errorsArray[$count] = $error->ErrorReason.'';
                    if (isset($error->OriginalDBSApplicationFormReference)){
                        $errorsArray[$count] = $error->ErrorReason.' ('.$error->OriginalDBSApplicationFormReference.')';
                    }
                    $count++;
                }

                return redirect("applications/viewApplicationDetails/".$applicationID)->with('errorMessage', $errorsArray)->send();
            }
            $updateApplicationDetails = \DB::table('applications')->where(['id' => $DBSApplication->id])->update($saveXMLResponse);

        } else {
            return redirect("applications/viewApplicationDetails/".$applicationID)->with('errorMessage', 'Application was rejected by DBS with no error message returned. Please contact the website administrator for further details!')->send();
        }

        return redirect("applications/viewApplicationDetails/".$applicationID)->with('successMessage', 'This application has been successfully sent to DBS!')->send();
    }



    /**
     * Check application status
     *
     * 
     */

    public function int023GetApplicationResult($applicationID)
    {
        if (!Auth::check() || !$this->checkAccess('siteuser') || !isset($applicationID) ||empty($applicationID)) return redirect("/login");
        
        $DBSApplication = \DB::table('applications')->select('applications.*')->where(['applications.id'=>$applicationID])->first();

        $XMLRequest = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><eResultRequest></eResultRequest>');
        $XMLRequest->addAttribute('xmlns', 'http://disclosure.service.gov.uk/edisclosure');

        $XMLChild_MessageHeader = $XMLRequest->addChild('MessageHeader');
        $XMLChild_MessageHeader->addChild('MessageID', $DBSApplication->dbsResponse_int022_MessageID); //$DBSApplication->dbsResponse_int022_MessageID

        $XMLChild_MessageHeader->addChild('RegisteredOrganizationNumber', env("DBS_RO_NUMBER"));
        $XMLChild_MessageHeader->addChild('Timestamp', date("Y-m-d\TH:i:s"));

        $XMLChild_eResultRequestBasedOnRefNumber = $XMLRequest->addChild('eResultRequestBasedOnRefNumber');
        $XMLChild_eResultRequestBatch = $XMLChild_eResultRequestBasedOnRefNumber->addChild('eResultRequestBatch');
        $XMLChild_eResultRequestBatch->addChild('DBSApplicationFormReference', $DBSApplication->dbsResponse_int022_DBSApplicationFormReference);


        //echo $XMLRequest->asXML();

        //test validation
        $doc = new \DOMDocument();
        $doc->loadXML($XMLRequest->asXML()); // load xml
        libxml_use_internal_errors(true);
        $is_valid_xml = @$doc->schemaValidate(\Storage::disk('public')->path('DBSXMLTemplates/INT023_GetApplicationeResult_request.xsd')); // path to xsd file

        if (!$is_valid_xml){
            
            $errors = libxml_get_errors();
            //echo '<b>Invalid XML:</b> validation failed<br>';
            $errorsArray = [];
            $errorsArray['message'] = 'This application details are failing the validation!';
            foreach(libxml_get_errors() AS $key => $error){
                $errorsArray[$key] = $error->message;
            }
            //echo'<pre>';print_r($errorsArray);echo'</pre>';exit;
            return redirect("applications/viewApplicationDetails/".$applicationID)->with('errorMessage', $errorsArray)->send();
        }else{
            //echo '<b>Valid XML:</b> validation passed<br>';
        }

        //send request
        $response = $this->sendXMLToDBS(env("DBS_RESULT_URL"), $XMLRequest->asXML());
//echo'<pre>';print_r($response); echo'</pre>';
        if (strlen($response) > 1){

            $testResponse = simplexml_load_string($response);

            if (!$testResponse) {

            }

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
            }

            $updateApplicationDetails = \DB::table('applications')->where(['id' => $DBSApplication->id])->update($saveXMLResponse);

            }
        return redirect("applications/viewApplicationDetails/".$applicationID);
    }

     /**
     * Check application status
     *
     * 
     */

    public function int023GetApplicationResultForm()
    {
               return view('applications.checkApplication');
    }

    public function int023GetApplicationResultFormProcess(Request $request)
    {

        if (!Auth::check() || !$this->checkAccess('siteuser')) return redirect("/login");
        $requestVars = $request->all();

        $XMLRequest = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><eResultRequest></eResultRequest>');
        $XMLRequest->addAttribute('xmlns', 'http://disclosure.service.gov.uk/edisclosure');

        $XMLChild_MessageHeader = $XMLRequest->addChild('MessageHeader');
        $XMLChild_MessageHeader->addChild('MessageID', 10000000);

        $XMLChild_MessageHeader->addChild('RegisteredOrganizationNumber', env("DBS_RO_NUMBER"));
        $XMLChild_MessageHeader->addChild('Timestamp', date("Y-m-d\TH:i:s"));

        $XMLChild_eResultRequestBasedOnRefNumber = $XMLRequest->addChild('eResultRequestBasedOnRefNumber');
        $XMLChild_eResultRequestBatch = $XMLChild_eResultRequestBasedOnRefNumber->addChild('eResultRequestBatch');
        $XMLChild_eResultRequestBatch->addChild('DBSApplicationFormReference', $requestVars['refnumber']);

        //echo $XMLRequest->asXML();exit;

        //test validation
        $doc = new \DOMDocument();
        $doc->loadXML($XMLRequest->asXML()); // load xml
        libxml_use_internal_errors(true);
        $is_valid_xml = @$doc->schemaValidate(\Storage::disk('public')->path('DBSXMLTemplates/INT023_GetApplicationeResult_request.xsd')); // path to xsd file

        if (!$is_valid_xml){
            
            $errors = libxml_get_errors();
            //echo '<b>Invalid XML:</b> validation failed<br>';
            $errorsArray = [];
            $errorsArray['message'] = 'This application details are failing the validation!';
            foreach(libxml_get_errors() AS $key => $error){
                $errorsArray[$key] = $error->message;
            }
            //echo'<pre>';print_r($errorsArray);echo'</pre>';exit;
            //return redirect("applications/viewApplicationDetails/".$applicationID)->with('errorMessage', $errorsArray)->send();
            return view('applications.checkApplicationResult', ['errorMessage' => $errorsArray]);
        }else{
            //echo '<b>Valid XML:</b> validation passed<br>';
        }

        //send request
        $response = $this->sendXMLToDBS(env("DBS_RESULT_URL"), $XMLRequest->asXML());
//echo'<pre>';print_r($response); echo'</pre>';
        if (strlen($response) > 1){

            $testResponse = simplexml_load_string($response);

            if (!$testResponse) {

            }

            $XMLResponse = new \SimpleXMLElement($response);

            $XMLResponse_messageID = isset($XMLResponse->MessageHeader[0]->MessageID) ? $XMLResponse->MessageHeader[0]->MessageID : null;
            $XMLResponse_DisclosureStatus = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureStatus) ? $XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureStatus : null;
            $XMLResponse_DisclosureType = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureType) ? $XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureType : null;
            $XMLResponse_DisclosureNumber = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureNumber) ? $XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureNumber : null;
            $XMLResponse_DisclosureIssueDate = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureIssueDate) ? date("Y-m-d H:i:s", strtotime($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->eResultAvailabilityDetails->eResultDetails->eResultInfo->DisclosureIssueDate)) : null;
            $XMLResponse_ErrorCode = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->ErrorDetails->ErrorCode) ? $XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->ErrorDetails->ErrorCode : null;
            $XMLResponse_ErrorReason = isset($XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->ErrorDetails->ErrorReason) ? $XMLResponse->eResultRetrieval[0]->eResultRetrievalBasedOnRefNumber->eResultRetrievalDetails->ErrorDetails->ErrorReason : null;
            //save XML data
            $dbsResponse = array(
                'dbsResponse_int023_MessageID'          => $XMLResponse_messageID,
                'dbsResponse_int023_DisclosureStatus'   => $XMLResponse_DisclosureStatus,
                'dbsResponse_int023_DisclosureType'     => $XMLResponse_DisclosureType,
                'dbsResponse_int023_DisclosureNumber'   => $XMLResponse_DisclosureNumber,
                'dbsResponse_int023_DisclosureIssueDate'=> $XMLResponse_DisclosureIssueDate,
                'dbsResponse_int023_ErrorCode'          => $XMLResponse_ErrorCode,
                'dbsResponse_int023_ErrorReason'        => $XMLResponse_ErrorReason,
                'dbsResponse_int023_xml'                => $response,
            );


            } else {
                $dbsResponse = [];
            }
        return view("applications/checkApplicationResult", ['dbsResponse' => $dbsResponse]);
    }


    /**
     * Check application status
     *
     * 
     */

    public function int025CheckApplicationsStatus($applicationID)
    {
        if (!Auth::check() || !$this->checkAccess('siteuser') || !isset($applicationID) ||empty($applicationID)) return redirect("/login");
        
        $DBSApplication = \DB::table('applications')->select('applications.*')->where(['applications.id'=>$applicationID])->first();

        $XMLRequest = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><ApplicationStatusCheckRequestBatch></ApplicationStatusCheckRequestBatch>');
        $XMLRequest->addAttribute('xmlns', 'http://disclosure.service.gov.uk/app_stat_track');
        $XMLRequest->addChild('OrganizationID', env("DBS_RO_NUMBER"));

        $ApplicationStatusCheckRequests = $XMLRequest->addChild('ApplicationStatusCheckRequests');
        $ApplicationDetails = $ApplicationStatusCheckRequests->addChild('ApplicationDetails');
        $ApplicationDetails->addChild('ApplicationReferenceNumber', $DBSApplication->dbsResponse_int022_DBSApplicationFormReference);// 'E0471308121');
        $ApplicationDetails->addChild('ApplicantCurrentSurname', strtoupper($DBSApplication->presentSurname));
        $ApplicationDetails->addChild('ApplicantDateOfBirth', $DBSApplication->dob);


        //echo $XMLRequest->asXML();

        //test validation
        $doc = new \DOMDocument();
        $doc->loadXML($XMLRequest->asXML()); // load xml
        libxml_use_internal_errors(true);
        $is_valid_xml = @$doc->schemaValidate(\Storage::disk('public')->path('DBSXMLTemplates/INT025_CheckApplicationsStatus_request.xsd')); // path to xsd file

        if (!$is_valid_xml){
            
            $errors = libxml_get_errors();
            //echo '<b>Invalid XML:</b> validation failed<br>';
            $errorsArray = [];
            $errorsArray['message'] = 'This application details are failing the validation!';
            foreach(libxml_get_errors() AS $key => $error){
                $errorsArray[$key] = $error->message;
            }
            //echo'<pre>';print_r($errorsArray);echo'</pre>';exit;
            return redirect("applications/viewApplicationDetails/".$applicationID)->with('errorMessage', $errorsArray)->send();
        }else{
            //echo '<b>Valid XML:</b> validation passed<br>';
        }  

        //send request
        $response = $this->sendXMLToDBS(env("DBS_STATUS_URL"), $XMLRequest->asXML());

        if (strlen($response) > 1){

            $testResponse = simplexml_load_string($response);

            if (!$testResponse) {

            }

            $XMLResponse = new \SimpleXMLElement($response);
//echo'<pre>';print_r($response); echo'</pre>'; exit;
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
//echo'<pre>';print_r($saveXMLResponse); echo'</pre>'; exit;
            if (in_array(strtolower($XMLResponse_ApplicationStatus), ['assemble certificate', 'certificate issued / despatched'])){//, 'no record found for details provided'
                $saveXMLResponse['applicationStatus'] = 5;
            }

            $updateApplicationDetails = \DB::table('applications')->where(['id' => $DBSApplication->id])->update($saveXMLResponse);

            }

        return redirect("applications/viewApplicationDetails/".$applicationID);
    }

    /**
     * Check application status
     *
     * 
     */

    public function sendXMLToDBS($url, $XMLRequest)
    {
        if (!Auth::check() || !$this->checkAccess('siteuser')) return redirect("/login");

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
     * update application checklist
     * 
     * @param   \Illuminate\Http\Request
     * @return json
     */

     public function updateChecklist(Request $request)
     {
        $requestVars = $request->all();

        if(!$this->checkAccess('superuser')) echo 0;
        $userID = (isset($requestVars['userID'])) ? intval($requestVars['userID']) : 0;
        $itemName = (isset($requestVars['itemName'])) ? $requestVars['itemName'] : '';
        $checklistValue = (isset($requestVars['checklistValue'])) ? intval($requestVars['checklistValue']) : 0;

        if(!empty($userID) && !empty($itemName) && in_array($checklistValue, [0,1])){
            $achecklistStatus = array(
            $itemName      => $checklistValue,
            $itemName.'_date'      => date("Y-m-d H:i:s",time()),
        );

        $updateApplicationDetails = \DB::table('applications')->where(['userID' => $userID])->update($achecklistStatus);
            echo 1;
        }
        else echo 0;

    }
     


    public function downloadReference($referenceID){
    
    if (!Auth::check()) return redirect("/login");
        if (!isset($referenceID) ||empty($referenceID)) return redirect("/login");
        $referenceID = intval($referenceID);

        $referenceDetails = \DB::table('bpss_vr_references_forms')
            ->select('*')
            ->where(['id'=>$referenceID])
            ->first();

        if(!isset($referenceDetails->id) || empty($referenceDetails->id)) return redirect("/login")->with('errorMessage', 'This Reference ID is invalid!');

        $DBSVRApplication = \DB::table('bpss_vr')
            ->select('bpss_vr.userID')
            ->where(['bpss_vr.id'=>$referenceDetails->verificationRecordID])
            ->first();

        if(isset($DBSVRApplication->userID) && !empty($DBSVRApplication->userID)){
            $userDetails = \DB::table('users')
            ->select('users.*')
            ->where(['users.id'=>$DBSVRApplication->userID])
            ->first();
        }

        if(!isset($userDetails->organisationID) || empty($userDetails->organisationID)) return redirect("/login")->with('errorMessage', 'This application ID is invalid!');
        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($userDetails->organisationID, $userOrganisations)){
            return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        }
        else if(Auth::user()->userType == 'applicant') {return redirect("/login");}

   
        set_time_limit(6000);

        $currentTime = time();
        $referencePdfName = 'Reference_for_'.$userDetails->firstName.' '.$userDetails->lastName.'_from_'.$referenceDetails->referenceDetails_fullName.'.pdf';
       

        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(8,8,8,8));
        //$html2pdf->setModeDebug();
        $html2pdf->pdf->SetTitle('Reference for '.$userDetails->firstName.' '.$userDetails->lastName);
        $html2pdf->writeHTML(view('reference_templates.reference-page1', ['referenceDetails' => $referenceDetails, 'userDetails'=>$userDetails]));
        $html2pdf->output($referencePdfName);

    }

    public function markApplicationForReview(Request $request)
    {
        $requestVars = $request->all();

        if(!$this->checkAccess('superuser')) echo 0;
        $userID = (isset($requestVars['userID'])) ? intval($requestVars['userID']) : 0;
        $applicationId = (isset($requestVars['applicationId'])) ? $requestVars['applicationId'] : 0;

        if(!empty($userID) && !empty($applicationId)){
            $applicationDetails = array(
                'formStatus'           => 1,
                'adminCompletedID'      => Auth::user()->id,
                'adminSignaturePath'    => '',
            );

            $updateApplicationDetails = \DB::table('bpss_vr')->where(['userID' => $userID, 'id' => $applicationId])->update($applicationDetails);

            $loggedUserID = Auth::user()->id;
            $loggedUserOrganisationID = Auth::user()->organisationID;

            $applicantDetails = \DB::table('users')
            ->select('users.*')
            ->where(['users.id'=>$userID])
            ->first();
            $notificationDetails = [
                'title' => 'New BPSS VR ready for review - '.$applicantDetails->firstName.' '.$applicantDetails->lastName,
                'body' => 'A new BPSS VR application is ready for review for '.$applicantDetails->firstName.' '.$applicantDetails->lastName.'<br /><br />Click here to view the BPSS VR: <a href="'.env('APP_URL').'applications/reviewBPSSVRDetails/'.$userID.'" target="_blank">BPSS VR Application details</a><br /><br />',
                'category' => 0,
                'relatedUserId' => $loggedUserID,
                'relatedAction' => 'BPSS VR ready for review',

                'emailTemplate' => 'notification_dbs_submission',
                'emailTitle' => 'New BPSS VR ready for review',
                'emailBody' => ' new BPSS VR application is ready for review for '.$applicantDetails->firstName.' '.$applicantDetails->lastName.'<br /><br />Click here to view the BPSS VR: <a href="'.env('APP_URL').'applications/reviewBPSSVRDetails/'.$userID.'" target="_blank">BPSS VR Application details</a><br /><br />',
            ];
            $this->addNotification('sitedesignatedemail', $loggedUserOrganisationID, $notificationDetails, true);
            echo 1;
        }
        else echo 0;
    }
    
    public function markApplicationCompleteSite(Request $request)
    {
        $requestVars = $request->all();

        if(!$this->checkAccess('superuser')) echo 0;
        $userID = (isset($requestVars['userID'])) ? intval($requestVars['userID']) : 0;
        $applicationId = (isset($requestVars['applicationId'])) ? $requestVars['applicationId'] : 0;

        if(!empty($userID) && !empty($applicationId)){
            $applicationDetails = array(
                'candidateStatus'    => 1,
            );

            $updateApplicationDetails = \DB::table('bpss_vr')->where(['userID' => $userID, 'id' => $applicationId])->update($applicationDetails);
            echo 1;
        }
        else echo 0;
    }

    public function resetVrCompletion(Request $request)
    {
        $requestVars = $request->all();

        if(!$this->checkAccess('superuser')) echo 0;
        $userID = (isset($requestVars['userID'])) ? intval($requestVars['userID']) : 0;
        $applicationId = (isset($requestVars['applicationId'])) ? $requestVars['applicationId'] : 0;

        if(!empty($userID) && !empty($applicationId)){
            $applicationDetails = array(
                'formStatus'        => 0,
                'candidateStatus'    => 0,
            );

            $updateApplicationDetails = \DB::table('bpss_vr')->where(['userID' => $userID, 'id' => $applicationId])->update($applicationDetails);
            echo 1;
        }
        else echo 0;
    }


    public function previewApplicantsFromFile(Request $request)
    {
        if (!$request->hasFile('bulkFile')) {
            return response()->json(['error' => 'No file uploaded.']);
        }

        try {
            $file = $request->file('bulkFile');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (count($rows) < 2) {
                return response()->json(['error' => 'The file is empty or only contains headers.']);
            }

            $rawHeaders = $rows[0];
            $cleanHeaders = array_map(function ($h) {
                return strtolower(trim(preg_replace('/[^a-z0-9 ]/i', '', $h)));
            }, $rows[0]);

            $requiredFields = [
                'Email' => ['email', 'email address', 'emailaddress', 'e-mail'],
                'Forename' => ['forename', 'first name', 'firstname', 'fname'],
                'Surname' => ['surname', 'last name', 'lastname', 'sname', 'lname'],
                'Approved Access Number' => ['aanumber', 'access number', 'applicant reference number', 'approved access number', 'reference', 'hr number', 'hrnumber', 'hr reference number', 'applicant tracking number', 'candidate id', 'application id', 'tracking id', 'reference number', 'applicant id', 'ref', 'paye ref'],
                'Phone Number' => ['phone', 'phone number', 'contact number', 'mobile', 'telephone', 'phonenumber', 'telephonenumber', 'telephone number', 'mobile number']
            ];

            $mappings = [];
            $confidence = [];

            foreach ($requiredFields as $key => $aliases) {
                $bestMatch = null;
                $bestScore = 0;

                foreach ($cleanHeaders as $i => $cleaned) {
                    foreach ($aliases as $alias) {
                        similar_text($alias, $cleaned, $score);
                        if ($score > $bestScore) {
                            $bestScore = $score;
                            $bestMatch = $rawHeaders[$i];
                        }
                    }
                }

                if ($bestScore >= 80) {
                    $mappings[$key] = $bestMatch;
                    $confidence[$key] = ['score' => round($bestScore), 'type' => 'high'];
                } elseif ($bestScore >= 60) {
                    $mappings[$key] = $bestMatch;
                    $confidence[$key] = ['score' => round($bestScore), 'type' => 'medium'];
                } else {
                    $mappings[$key] = null;
                    $confidence[$key] = ['score' => round($bestScore), 'type' => 'low'];
                }
            }

            $total = count($rows) - 1;

            return response()->json([
                'total' => $total,
                'mappings' => $mappings,
                'confidence' => $confidence,
                'headers' => $rawHeaders,
                'applicants' => array_slice($rows, 1)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process file.']);
        }
    }


    public function sendBulkApplicationRequest(Request $request)
    {
        if (!Auth::check()) return response()->json(['error' => 'Unauthorized'], 401);

        if (!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $submittedApplicants = $request->input('applicants');
        $orgID = $request->input('organisationID');
        $appType = $request->input('applicationType');
        $useYoti = $request->input('useYoti') ? 1 : 0;
        $results = ['inserted' => [], 'duplicates' => [], 'skipped' => []];

        foreach ($submittedApplicants as $applicant) {
            $email = $applicant['email'] ?? null;
            $aanumber = trim($applicant['aanumber'] ?? '');

            // Normalize phone: remove tel: prefix + common separators
            $phoneRaw = trim($applicant['phone'] ?? '');
            $phone = preg_replace('/^tel:\s*/i', '', $phoneRaw);
            $phone = preg_replace('/[\s\-\(\)\.]/', '', $phone);

            // Normalize/split names if needed
            $forename = trim($applicant['forename'] ?? '');
            $surname  = trim($applicant['surname'] ?? '');

            if (($forename === '' || $surname === '') && !empty($applicant['name'])) {
                $fullName = trim(preg_replace('/\s+/', ' ', $applicant['name']));
                if ($fullName !== '') {
                    $parts = explode(' ', $fullName, 2);
                    if ($forename === '') $forename = $parts[0] ?? '';
                    if ($surname === '')  $surname = $parts[1] ?? '';
                }
            }

            $normalisedEmail = strtolower(trim($email));

            $isDuplicate = \DB::table('applicants')
                ->whereRaw('LOWER(TRIM(email)) = ?', [$normalisedEmail])
                ->exists() ||

                \DB::table('users')
                ->whereRaw('LOWER(TRIM(email)) = ?', [$normalisedEmail])
                ->exists();

            if (!$email || $isDuplicate) {
                $results['duplicates'][] = $applicant;
                continue;
            }

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $results['skipped'][] = $applicant;
                continue;
            }

            if (!preg_match('/^[0-9]{6,20}$/', $phone) && strlen($phone) > 0) {
                $results['skipped'][] = $applicant;
                continue;
            }

            if (empty($aanumber)) {
                $results['skipped'][] = $applicant;
                continue;
            }


            $applicationCode = $this->generateRandomKey(10);
            $accessUrlCode = $this->generateRandomKey(36);

            $details = [
                'email' => $email,
                'accessUrlCode' => $accessUrlCode,
                'forename' => $forename,
                'surname' => $surname,
                'organisationID' => $orgID,
                'applicationCode' => strtoupper($applicant['aanumber'] ?? ''),
                'mobileNumberMain' => $phone ?: null,
                'userStatus' => 0,
                'createdOn' => now(),
                'createdBy' => Auth::user()->id,
                'useYoti' => $useYoti,
            ];

            // Application Type Handling
            $details['dbsApplication'] = $details['bpssApplication'] = 0;
            $details['REVALonsite'] = $details['REVALoffsite'] = 0;

            switch ($appType) {
                case 2: $details['dbsApplication'] = $details['bpssApplication'] = 1; break;
                case 4: $details['dbsApplication'] = $details['bpssApplication'] = $details['REVALonsite'] = 1; break;
                case 5: $details['dbsApplication'] = $details['bpssApplication'] = $details['REVALoffsite'] = 1; break;
                default: $details['dbsApplication'] = 1; break;
            }

            // Insert applicant
            $newApplicantID = \DB::table('applicants')->insertGetId($details);
            if (!$newApplicantID) {
                $results['skipped'][] = $applicant;
                continue;
            }

            // Send email
            $emailTemplate = \DB::table('organisation_emails')
                ->where('organisation_id', $orgID)
                ->where('template_key', 'registration')
                ->first();

            $registrationLink = env('APP_URL') . 'register/' . $accessUrlCode;
            $loginUrl = env('APP_URL') . 'login';

            $replacements = [
                '{{ $firstName }}' => $details['forename'],
                '{{ $lastName }}' => $details['surname'],
                '{{ $registrationLink }}' => '<a href="' . $registrationLink . '">' . $registrationLink . '</a>',
                '{{ $loginLink }}' => '<a href="' . $loginUrl . '">' . $loginUrl . '</a>',
            ];

            $mailDriver = env('MAIL_DRIVER');
            if (isset($mailDriver) && strlen($mailDriver) > 0) {
                if (!empty($emailTemplate) && $emailTemplate->use_default == 0 && !empty($emailTemplate->custom_content)) {
                    $customBody = $emailTemplate->custom_content;
                    foreach ($replacements as $placeholder => $actual) {
                        $customBody = str_replace($placeholder, $actual, $customBody);
                    }

                    Mail::send([], [], function ($message) use ($email, $customBody) {
                        $message->to($email)
                            ->subject('Security Clearance Registration - Get ClassifIeD')
                            ->setBody($customBody, 'text/html');
                    });
                } else {
                    Mail::send('email_templates.dbs_registration_request', [
                        'targetEmail' => $email,
                        'registrationLink' => $registrationLink
                    ], function ($message) use ($email) {
                        $message->to($email);
                        $message->subject('Security Clearance Registration - Get ClassifIeD');
                    });
                }

                \DB::table('applicants')->where('id', $newApplicantID)->update(['lastEmailSent' => now()]);
            }

            // Notify
            $requester = Auth::user();
            $orgName = \DB::table('organisations')->where('id', $orgID)->value('organisationName');

            $notification = [
                'title' => "New clearance invitation sent to {$details['forename']} {$details['surname']}",
                'body' => "A new application invitation was sent to {$details['forename']} {$details['surname']}<br />Email: {$email}<br />Organisation: {$orgName}<br />Raised by: {$requester->firstname} {$requester->lastname}<br />",
                'category' => 0,
                'relatedAction' => 'New Applicant',
                'emailTemplate' => 'notification_new_clearance_invitation',
                'emailTitle' => 'New clearance invitation sent to user',
                'emailBody' => "A new application invitation was sent to {$details['forename']} {$details['surname']}<br />Email: {$email}<br />Organisation: {$orgName}<br />Raised by: {$requester->firstname} {$requester->lastname}<br />",
            ];

            $this->addNotification('superuser', $orgID, $notification, true);
            $results['inserted'][] = $applicant;
        }

        return response()->json($results);
    }


    public function sendRegistrationSms(Request $request)
    {
        $request->validate([
            'applicantID' => 'required|integer'
        ]);

        $applicant = \App\Models\Applicant::find($request->applicantID);

        if (!$applicant) {
            return response()->json(['success' => false, 'message' => 'Applicant not found.'], 404);
        }

        if (empty($applicant->mobileNumberMain)) {
            return response()->json(['success' => false, 'message' => 'Applicant has no mobile number.'], 422);
        }

        // Build registration link
        $registrationLink = env('APP_URL') . 'register/' . $applicant->accessUrlCode;

        // Keep SMS concise
        $message = "Complete your registration here: {$registrationLink}";

        // ClickSend credentials from .env
        $username = env('CLICKSEND_USERNAME');
        $apiKey   = env('CLICKSEND_API_KEY');
        $from     = env('CLICKSEND_FROM', 'ClassifIeD'); // optional sender ID (country dependent)

        if (!$username || !$apiKey) {
            return response()->json(['success' => false, 'message' => 'SMS service is not configured.'], 500);
        }

        // Normalize number to E.164 where possible (basic cleanup)
        $to = preg_replace('/\s+/', '', $applicant->mobileNumberMain);

        $payload = [
            'messages' => [[
                'source' => 'php',
                'from'   => $from,
                'body'   => $message,
                'to'     => $to
            ]]
        ];

        try {
            $response = Http::withBasicAuth($username, $apiKey)
                ->acceptJson()
                ->post('https://rest.clicksend.com/v3/sms/send', $payload);

            if (!$response->successful()) {
                Log::error('ClickSend SMS send failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'applicant_id' => $applicant->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'SMS provider rejected the request.'
                ], 500);
            }

            $json = $response->json();

            // Optional: inspect result code(s) more deeply
            // $json['data']['messages'][0]['status'] etc.

            // Save audit timestamp if you want:
            // $applicant->lastSmsSent = now();
            // $applicant->save();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('ClickSend SMS exception', [
                'error' => $e->getMessage(),
                'applicant_id' => $applicant->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending SMS.'
            ], 500);
        }
    }
}