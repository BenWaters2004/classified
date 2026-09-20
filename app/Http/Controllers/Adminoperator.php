<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spipu\Html2Pdf\Html2Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\ClickSendSmsService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;



class Adminoperator extends Controller
{

	/*
    |--------------------------------------------------------------------------
    | Adminoperator Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling the Adminoperator logic
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

    public function newApplicationRequest()
    {
        if (!Auth::check()) return redirect("/login");
        if(!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) return redirect("/login");
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
        $adminProfile->organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', Auth::user()->organisationID)->first()->organisationName;

        $userOrganisations = $this->getUserOrganisations();
        $availableOrganisations = \DB::table('organisations')
            ->select('*')
            ->where(['organisations.organisationStatus'=>1])
            ->whereIn('organisations.id', $userOrganisations)
            ->get();

        return view('adminoperator.newApplicationRequest', ['adminProfile' => $adminProfile, 'availableOrganisations' => $availableOrganisations]);
    }

    public function sendApplicationRequest(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        if(!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) return redirect("/login");
        
        $requestVars = $request->all();
        $validateArray = [
            'forename'          => 'max:50',
            'surname'           => 'max:50',
            'emailAddress'      => 'required|email|max:255|unique:applicants,email',
            'uploads'           => 'array',
            'uploads.*.file'    => 'file|mimes:pdf,jpg,jpeg,png,gif|max:10240', // 10MB
            'uploads.*.type'    => 'nullable|in:misc,secmx,mkden',
        ];
        $messsages = array(
            'emailAddress.unique'=>'email_exists',
        );
        $this->validate($request, $validateArray, $messsages); 


        // Fetch organization details for validation
        $orgId = $requestVars['organisationID'];
        $organisationDetails = \DB::table('organisations')->where('id', $orgId)->first();
        $allowedAppTypes = $organisationDetails->allowed_app_types ? json_decode($organisationDetails->allowed_app_types, true) : [];

        // Validate applicationType
        $appType = $requestVars['applicationType'] ?? '1';
        $requires = [];
        if ($appType == '1') {
            $requires = ['DBS'];
        } elseif ($appType == '2') {
            $requires = ['DBS', 'BPSS'];
        } elseif ($appType == '4') {
            $requires = ['DBS', 'BPSS', 'BPSS_REVAL_ONSITE'];
        } elseif ($appType == '5') {
            $requires = ['DBS', 'BPSS', 'BPSS_REVAL_OFFSITE'];
        } else {
            return back()->withInput()->withErrors(['applicationType' => 'Invalid application type.']);
        }
        if (!empty($requires) && !empty(array_diff($requires, $allowedAppTypes))) {
            return back()->withInput()->withErrors(['applicationType' => 'Selected application type is not allowed for this organization.']);
        }

        // Validate useYoti
        $useYoti = isset($requestVars['useYoti']) && $requestVars['useYoti'] === 'on';
        if ($useYoti && !in_array('YOTI', $allowedAppTypes, true)) {
            return back()->withInput()->withErrors(['useYoti' => 'Yoti is not allowed for this organization.']);
        }

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
            'applicationCode' => (isset($requestVars['aanumber']) && strlen($requestVars['aanumber'])>0) ? strtoupper($requestVars['aanumber']) : null,
            'mobileNumberMain' => (isset($requestVars['phone']) && strlen($requestVars['phone'])>0) ? $requestVars['phone'] : null,
            'userStatus'=> 0,
            'createdOn' => date("Y-m-d H:i:s"),
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

            // Handle dynamic uploads
            $uploads = $request->file('uploads', []);
            foreach ($uploads as $i => $upload) {
                if (!is_array($upload) || !isset($upload['file'])) {
                    continue;
                }

                $doc = $upload['file'];
                if (!$doc->isValid()) {
                    continue;
                }

                $realname  = pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = strtolower($doc->getClientOriginalExtension());

                // Determine document_type (keep parity with your existing logic)
                if ($extension === 'png') {
                    $document_type = 'png';
                } elseif (in_array($extension, ['jpeg','jpg','gif','png'])) {
                    $document_type = 'img';
                } elseif ($extension === 'pdf') {
                    $document_type = 'pdf';
                } else {
                    // skip unsupported
                    continue;
                }

                // File type (misc/secmx/mkden)
                $fileType = strtolower($request->input("uploads.$i.type", 'misc')) ?: 'misc';

                $new_name = $newApplicantID.'_'.date("YmdHis").'_'.$realname.'_'.$i.'.'.$extension;

                \Storage::disk('public_images')->put('new_applicant_documents/'.$new_name, file_get_contents($doc), 'public');

                $docDetails = [
                    'applicantID'   => $newApplicantID,
                    'userID'        => 0,
                    'document_name' => $realname,
                    'document_type' => $document_type,
                    'document_path' => $new_name,
                    'file_type'     => $fileType,
                ];
                \DB::table('new_applicant_documents')->insertGetId($docDetails);
            }
            //send email
            $orgId = $requestVars['organisationID'];
            $emailTemplate = \DB::table('organisation_emails')
                ->where('organisation_id', $orgId)
                ->where('template_key', 'registration')
                ->first();

            $registrationLink = env('APP_URL') . 'register/' . $accessUrlCode;
            $loginUrl = env('APP_URL') . 'login';

            $replacements = [
                '{{ $firstName }}' => $requestVars['forename'],
                '{{ $lastName }}' => $requestVars['surname'],
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
                        'targetEmail' => $requestVars['emailAddress'],
                        'registrationLink' => $registrationLink
                    ], function ($message) use ($emailTo) {
                        $message->to($emailTo);
                        $message->subject('Security Clearance Registration - Get ClassifIeD');
                    });
                }
                $updateApplicationDetails = \DB::table('applicants')->where(['id' => $newApplicantID])->update(['lastEmailSent' => date("Y-m-d H:i:s")]);
            }

            
            //add notification
            $requestingUserDetails = \DB::table('users')->select('firstname', 'lastname')->where('id', '=', Auth::user()->id)->first();
            $requestingUserDetailsFullName = $requestingUserDetails->firstname.' '.$requestingUserDetails->lastname;
            
            $organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', $requestVars['organisationID'])->first()->organisationName;

            $notificationDetails = [
                'title' => 'New clearance invitation sent to '.$requestVars['forename'].' '.$requestVars['surname'],
                'body' => 'A new application invitation was sent to '.$requestVars['forename'].' '.$requestVars['surname'].', email: '.$requestVars['emailAddress'].'<br />Organisation: '.$organisationName.'<br />Raised by: '.$requestingUserDetailsFullName.'<br /><br />Click here to view the request: <a href="'.env('APP_URL').'adminoperator/applicants" target="_blank">View Request</a><br /><br />',
                'category' => 0,
                'relatedAction' => 'New Applicant',

                'emailTemplate' => 'notification_new_clearance_invitation',
                'emailTitle' => 'New clearance invitation sent to user',
                'emailBody' => 'A new application invitation was sent to '.$requestVars['forename'].' '.$requestVars['surname'].', email: '.$requestVars['emailAddress'].'<br />Organisation: '.$organisationName.'<br />Raised by: '.$requestingUserDetailsFullName.'<br /><br />Click here to view the request: <a href="'.env('APP_URL').'adminoperator/applicants" target="_blank">View Request</a><br /><br />',
            ];
            $this->addNotification('superuser', $requestVars['organisationID'], $notificationDetails, true);

            return redirect("/applications/pendingRequests/");
        } else {
            return back()->withInput();
        }
    }


    public function applicants($dbsStatus = null)
    {
        if(!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) return redirect("/login");
        
        $currentUserRoles = $this->getCurrentUserRoles();
        $userOrganisations = $this->getUserOrganisations();
        
        //get users details
        $allusers = \DB::table('users')->where(['userType' => 'applicant']);


        if (array_search('superuser', $currentUserRoles) === false){
            $allusers = $allusers->whereIn('organisationID', $userOrganisations);
        }
        $allusers = $allusers->get();
        foreach($allusers as $key=>$user){
            $allusers[$key]->organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', $user->organisationID)->first()->organisationName;
            $applicationDetails = \DB::table('applications')->select('applications.id', 
                                                                    'applications.userID',
                                                                    'applications.applicationStatus',
                                                                    'applications.forename', 
                                                                    'applications.middlename',
                                                                    'applications.presentSurname',
                                                                    'applications.dbsResponse_int023_DisclosureStatus',
                                                                    'applications.createdOn',
                                                                    'applications.applicationCompleted',
                                                                    'applications.admin_confirm_valid_passport',
                                                                    'applications.admin_confirm_valid_dln',
                                                                    'applications.admin_confirm_valid_nino',
                                                                    'applications.admin_gdpr_checked',
                                                                    'applications.dbs_consent',
                                                                    'applications.dbs_consent_date'
                                                                );
            $applicationDetails = $applicationDetails->where(['applications.userID'=>$user->id]);
            $applicationDetails = $applicationDetails->first();
            if(isset($applicationDetails->id) && !empty($applicationDetails->id)){
                if(!empty($dbsStatus) && is_numeric($dbsStatus)){
                    if($applicationDetails->applicationStatus != intval($dbsStatus)){
                        unset($allusers[$key]);
                        continue;
                    }
                }
            
                $allusers[$key]->DBSApplicationID = $applicationDetails->id;
                $allusers[$key]->userID = $applicationDetails->userID;
                $allusers[$key]->applicationStatus = $applicationDetails->applicationStatus;
                $allusers[$key]->applicationDetails_forename = $applicationDetails->forename;
                $allusers[$key]->applicationDetails_middlename = $applicationDetails->middlename;
                $allusers[$key]->applicationDetails_presentSurname = $applicationDetails->presentSurname;
                $allusers[$key]->applicationDetails_dbsResponse_int023_DisclosureStatus = $applicationDetails->dbsResponse_int023_DisclosureStatus;
                $allusers[$key]->applicationDetails_applicationCreatedOn = $applicationDetails->createdOn;
                $allusers[$key]->applicationCompleted = $applicationDetails->applicationCompleted;
                $allusers[$key]->admin_confirm_valid_passport = $applicationDetails->admin_confirm_valid_passport;
                $allusers[$key]->admin_confirm_valid_dln = $applicationDetails->admin_confirm_valid_dln;
                $allusers[$key]->admin_confirm_valid_nino = $applicationDetails->admin_confirm_valid_nino;
                $allusers[$key]->admin_gdpr_checked = $applicationDetails->admin_gdpr_checked;
                $allusers[$key]->dbs_consent = $applicationDetails->dbs_consent;
                $allusers[$key]->dbs_consent_date = $applicationDetails->dbs_consent_date;

                $allusers[$key]->otherNames = \DB::table('application_extra_names')->select('application_extra_names.id', 'application_extra_names.other_forename', 'application_extra_names.other_middlename', 'application_extra_names.other_surname')->where(['application_extra_names.applicationID'=>$applicationDetails->id])->get();

                $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.id', 'bpss_applications.applicationStatus', 'bpss_applications.completedDate', 'bpss_applications.start_date')->where(['bpss_applications.userID'=>$user->id])->first();
                $allusers[$key]->bpss_applicationID = (isset($BPSSApplication->id) && !empty($BPSSApplication->id)) ? $BPSSApplication->id : 0;
                $allusers[$key]->bpss_applicationStatus = (isset($BPSSApplication->id) && !empty($BPSSApplication->id)) ? $BPSSApplication->applicationStatus : 0;
                $allusers[$key]->bpss_completedDate = (isset($BPSSApplication->id) && !empty($BPSSApplication->id)) ? $BPSSApplication->completedDate : 0;
                $allusers[$key]->bpss_start_date = (isset($BPSSApplication->id) && !empty($BPSSApplication->id)) ? $BPSSApplication->start_date : 0;

                $BPSSVR = \DB::table('bpss_vr')->select('bpss_vr.formStatus', 'bpss_vr.id')->where(['bpss_vr.userID'=>$user->id])->first();
                $allusers[$key]->bpssvr_formStatus = (isset($BPSSVR->id) && !empty($BPSSVR->id)) ? $BPSSVR->formStatus : 0;

                //extra files
                $newApplicantFiles = \DB::table('new_applicant_documents')->select('*')->where(['userID'=>$user->id])->orderBy('id', 'desc')->get();
                $allusers[$key]->file2 = (isset($newApplicantFiles[0]->id) && !empty($newApplicantFiles[0]->id)) ? $newApplicantFiles[0]->document_path : null;
                $allusers[$key]->file1 = (isset($newApplicantFiles[1]->id) && !empty($newApplicantFiles[1]->id)) ? $newApplicantFiles[1]->document_path : null;
            } else {
                $allusers[$key]->applicationStatus = -1;
                unset($allusers[$key]);
                continue;
            }
        }
        //echo'<pre>';print_r($allusers);echo'</pre>';

        //get pending requests
        if(empty($dbsStatus) || !is_numeric($dbsStatus) || $dbsStatus == -1){
            $pendingApplicants = \DB::table('applicants')
                ->join('organisations', 'organisations.id', '=', 'applicants.organisationID')
                ->select('applicants.*', 'organisations.organisationName');
            if (array_search('superuser', $currentUserRoles) === false){
                $pendingApplicants = $pendingApplicants->where(['organisationID' => Auth::user()->organisationID]);
            }
            $pendingApplicants = $pendingApplicants->get();
        } else {
            $pendingApplicants = [];
        }
        //echo'<pre>';print_r($allusers);echo'</pre>';

        return view('adminoperator.listapplicants', ['dbsStatus' => $dbsStatus, 'allusers' => $allusers, 'pendingApplicants' => $pendingApplicants]);
        
    }

    public function editApplicant($userId = null)
    {
        if(!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) return redirect("/login");
        if(empty($userId)) return redirect("/login");

        $currentUserRoles = $this->getCurrentUserRoles();
        $userOrganisations = $this->getUserOrganisations();

        $userDetails = \DB::table('users')->where('id', '=', $userId);
        if (array_search('superuser', $currentUserRoles) === false){
            $userDetails = $userDetails->whereIn('organisationID', $userOrganisations);
        }        
        $userDetails = $userDetails->first();
  
        //extra files
        $userFiles = \DB::table('new_applicant_documents')->select('*')->where(['userID'=>$userId])->orderBy('id', 'ASC')->get();
        return view('adminoperator.editApplicationRequest', ['userDetails' => $userDetails, 'userFiles' => $userFiles]);
        
    }

    public function updateUserFiles(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        if(!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) return redirect("/login");

        $requestVars = $request->all();

        if (!empty($requestVars['userid']) && is_numeric($requestVars['userid'])){
            //save PDF 1
            if ($request->hasFile('newfile')) {
                $doc = $request->file('newfile');
                $realname = pathinfo($request->file('newfile')->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $doc->getClientOriginalExtension();
                $new_name = $requestVars['userid'].'_'.date("YmdHis").'_'.$realname.'_1.'.strtolower($extension);
                $fileType = (isset($requestVars['newfileType']) && strlen($requestVars['newfileType'])>0) ? strtolower($requestVars['newfileType']) : 'misc';

                \Storage::disk('public_images')->put('new_applicant_documents'.'/'.$new_name, file_get_contents($doc), 'public');
                if (strtolower($extension) == 'png') {
                    $document_type = 'png';
                } else if (strtolower($extension) == 'jpeg' || strtolower($extension) == 'jpg' || strtolower($extension) == 'gif' || strtolower($extension) == 'png' ){
                    $document_type = 'img';
                } else if (strtolower($extension) == 'pdf' ){
                    $document_type = 'pdf';
                }

                $docDetails = array(
                    'applicantID'     => 0,
                    'userID'     => $requestVars['userid'],
                    'document_name' => $realname,
                    'document_type' => $document_type,
                    'document_path' => $new_name,
                    'file_type' => $fileType,
                );
                $uploadedDocID = \DB::table('new_applicant_documents')->insertGetId($docDetails);

            }
        return redirect("/adminoperator/editApplicant/".$requestVars['userid']);
        }
    }

    public function addUserFiles(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        if (!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) return redirect("/login");
    
        $request->validate([
            'newfile' => 'required|file|mimes:pdf,jpg,jpeg,png,gif|max:2048',
            'userid' => 'required|integer|exists:users,id'
        ]);
    
        $userId = $request->userid;
    
        if ($request->hasFile('newfile')) {
            $doc = $request->file('newfile');
            $realname = pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($doc->getClientOriginalExtension());
            $new_name = "{$userId}_" . now()->format('YmdHis') . "_{$realname}_1.{$extension}";
            $fileType = $request->newfileType ?? 'misc';
    
            \Storage::disk('public_images')->put('new_applicant_documents/' . $new_name, file_get_contents($doc), 'public');
    
            $document_type = in_array($extension, ['jpeg', 'jpg', 'gif', 'png']) ? 'img' : ($extension === 'pdf' ? 'pdf' : 'misc');
    
            \DB::table('new_applicant_documents')->insert([
                'applicantID'     => 0,
                'userID'          => $userId,
                'document_name'   => $realname,
                'document_type'   => $document_type,
                'document_path'   => $new_name,
                'file_type'       => $fileType,
            ]);
    
            return redirect("/finalReport/review/{$userId}")->with('successMessage', 'File uploaded successfully!');
        }
    
        return back()->with('errorMessage', 'No file uploaded. Please try again.');
    }

    public function saveChecklist(Request $request)
    {
        $requestVars = $request->all();
         if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $userId = $request->input('userID');

        // Validate User Exists
        $user = \DB::table('applications')->where('userID', $userId)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        // Update the user checklist fields
        \DB::table('applications')->where('userID', $userId)->update([
            'admin_x3_ids_uploaded' => $request->has('admin_x3_ids_uploaded') ? 1 : 0,
            'admin_personal_ref1_returned' => $request->has('admin_personal_references_received') ? 1 : 0,
            'admin_personal_ref2_returned' => $request->has('admin_personal_references_received') ? 1 : 0,
            'admin_employment_ref_returned' => $request->has('admin_employment_ref_returned') ? 1 : 0,
            'admin_academic_history_confirmed' => $request->has('admin_academic_history_confirmed') ? 1 : 0,
            'admin_all_evidence_attached' => $request->has('admin_all_evidence_attached') ? 1 : 0,
            'admin_right_to_work' => $request->has('admin_right_to_work') ? 1 : 0,
            'admin_approval_notes' => $request->input('admin_approval_notes'),
            'position_applied_for' => $request->input('contractor_position'),
        ]);

     
         // Fix approval value conversion (from string to integer)
        $approvalValue = null;
        if (isset($requestVars['approval'])) {
            if ($requestVars['approval'] === 'approved') {
                $approvalValue = 1;
            } elseif ($requestVars['approval'] === 'denied') {
                $approvalValue = 0;
            }
        }

        // Update Collins approval section
        $approvalData = [
            'approval' => $approvalValue,
            'adminName' => $requestVars['admin_approval_name'] ?? null,
            'adminTitle' => $requestVars['admin_approval_title'] ?? null,
            'cargo' => $requestVars['cargo_type'] ?? null,
        ];

        $existingApproval = \DB::table('collins_approvals')->where(['userID' => $userId])->first();

        if ($existingApproval) {
            \DB::table('collins_approvals')->where(['userID' => $userId])->update($approvalData);
        } else {
            $approvalData['userID'] = $userId;
            \DB::table('collins_approvals')->insert($approvalData);
        }

        // Update contractor details in a separate table
        if (isset($requestVars['contractor_company']) || isset($requestVars['contractor_position'])) {
            $contractorData = [
                'contractor_name_of_company' => $requestVars['contractor_company'] ?? null,
                'contractor_position' => $requestVars['contractor_position'] ?? null,
                'employement_type' => $request->input('employment_type'),
                'form_type' => $request->input('application_type'),
            ];

            $existingContractor = \DB::table('bpss_applications')->where(['userID' => $userId])->first();

            if ($existingContractor) {
                \DB::table('bpss_applications')->where(['userID' => $userId])->update($contractorData);
            } else {
                $contractorData['userID'] = $userId;
                \DB::table('bpss_applications')->insert($contractorData);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Checklist and approval details updated successfully']);
    }


    public function viewApplicant($applicantID = null)
    {

        return redirect("/adminoperator/applicants");

        if (!Auth::check()) return redirect("/login");
        if((!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) || empty($applicantID))  return redirect("/login");
        $applicantProfile = \DB::table('applicants')
            ->join('organisations', 'organisations.id', '=', 'applicants.organisationID')
            ->select('applicants.*', 'organisations.organisationName')
            ->where(['applicants.id'=>$applicantID]);
        if(!$this->checkAccess('superuser')){
            $applicantProfile->where(['applicants.organisationID'=>Auth::user()->organisationID]);
        }
        $applicantProfile = $applicantProfile->first();
        if (count($applicantProfile) == 0)  return redirect("/login");
        
        $applicantDetails =[];
        if (isset($applicantProfile->userID) && !empty($applicantProfile->userID)){
            $applicantDetails = \DB::table('users')
                ->select('users.*')
                ->where(['users.id'=>$applicantProfile->userID])
                ->first();
        }

        return view('applications.viewApplicant', ['applicantProfile' => $applicantProfile, 'applicantDetails' => $applicantDetails]);
        
    }

    
    public function removeFile(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['documentID']>0){

            //check status
            $documentDetails = \DB::table('new_applicant_documents')->select('document_path')->where(['id'=>intval($requestVars['documentID'])])->first();
            if (isset($documentDetails->document_path) && !empty($documentDetails->document_path)){ 
                $testDelete = \DB::table('new_applicant_documents')->where(['id' => intval($requestVars['documentID'])])->delete();
                unlink(env('APP_DOCUMENT_ROOT').'/public/uploads/new_applicant_documents/'.$documentDetails->document_path);
                if(isset($testDelete) && $testDelete){
                     echo json_encode(['status' => 1]);
                } else echo json_encode(['status' => 0]);
            }
        } else echo json_encode(['status' => 0]);

        
    }


    public function futureDashboard() {
        if(!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) return redirect("/login");

        //get users details
        $currentUserRoles = $this->getCurrentUserRoles();
        $userOrganisations = $this->getUserOrganisations();

        $userID = Auth::user()->id;

        $currentUser = \DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->where('users.id', $userID)
            ->select('users.*', 'organisations.organisationName', 'organisations.totalCompleted')
            ->first();

        //get completed users
        $compUsers = \DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->where('userType', 'applicant')
            ->where('completed', 1)
            ->select('users.*', 'organisations.organisationName');

        if (array_search('superuser', $currentUserRoles) === false){
            $compUsers = $compUsers->whereIn('organisations.id', $userOrganisations);
        }

        $completedUsers  = $compUsers->count();


        // Get incomplete users
        $incompUsers = \DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->where('userType', 'applicant')
            ->where(function ($query) {
                $query->where('completed', '!=', 1)
                    ->orWhereNull('completed'); // Include NULL values
            })
            ->select('users.*', 'organisations.organisationName');

        if (array_search('superuser', $currentUserRoles) === false) {
            $incompUsers = $incompUsers->whereIn('organisations.id', $userOrganisations);
        }

        $incompleteUsers = $incompUsers->count();
        

        //get pending users
        $applicants = \DB::table('applicants')
            ->join('organisations', 'organisations.id', '=', 'applicants.organisationID')
            ->select('applicants.*', 'organisations.organisationName');

        if (array_search('superuser', $currentUserRoles) === false){
            $applicants = $applicants->whereIn('organisations.id', $userOrganisations);
        }

        $pendingAndAwaiting  = $applicants->count();



        // Get completed applications this month
        if (array_search('superuser', $currentUserRoles) === false){
            $completedThisMonth = \DB::table('users')
                ->where('userType', 'applicant')
                ->where('completed', 1)
                ->where('organisationID', $currentUser->organisationID)
                ->whereMonth('completedDate', now()->month)
                ->whereYear('completedDate', now()->year);

            $completedThisMonthCount = $completedThisMonth->count();

            // Get completed applications last month
            $completedLastMonth = \DB::table('users')
                ->where('userType', 'applicant')
                ->where('completed', 1)
                ->where('organisationID', $currentUser->organisationID)
                ->whereMonth('completedDate', now()->subMonth()->month)
                ->whereYear('completedDate', now()->subMonth()->year);

            $completedLastMonthCount = $completedLastMonth->count();
        } else {
            $totalcount = \DB::table('organisations')
                ->select('totalCompleted')
                ->get();

            $currentUser->totalCompleted = $totalcount->sum('totalCompleted');


            $completedThisMonth = \DB::table('users')
                ->where('userType', 'applicant')
                ->where('completed', 1)
                ->whereMonth('completedDate', now()->month)
                ->whereYear('completedDate', now()->year);

            $completedThisMonthCount = $completedThisMonth->count();

            // Get completed applications last month
            $completedLastMonth = \DB::table('users')
                ->where('userType', 'applicant')
                ->where('completed', 1)
                ->whereMonth('completedDate', now()->subMonth()->month)
                ->whereYear('completedDate', now()->subMonth()->year);

            $completedLastMonthCount = $completedLastMonth->count();
        }
        

        // Calculate percentage difference
        if ($completedLastMonthCount > 0) {
            $percentageChange = (($completedThisMonthCount - $completedLastMonthCount) / $completedLastMonthCount) * 100;
        } else {
            $percentageChange = $completedThisMonthCount > 0 ? 100 : 0; // If last month had 0, avoid division by zero
        }

        // Determine the class and arrow direction
        $growthClass = $percentageChange >= 0 ? 'green' : 'red';
        $growthSymbol = $percentageChange >= 0 ? '↑' : '↓';


        $averageDurations = \DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->selectRaw("DATE_FORMAT(completedDate, '%Y-%m') as month, AVG(DATEDIFF(completedDate, createdOn)) as avgDuration")
            ->where('userType', 'applicant')
            ->where('completed', 1)
            ->whereNotNull('createdOn')
            ->whereNotNull('completedDate')
            ->whereBetween('createdOn', [
                now()->subYear()->startOfDay(),
                now()->endOfDay()
            ])
            ->groupBy('month')
            ->orderByRaw("STR_TO_DATE(CONCAT(month, '-01'), '%Y-%m-%d') ASC");

        // Apply organisation filter if not a superuser
        if (array_search('superuser', $currentUserRoles) === false) {
            $averageDurations = $averageDurations->whereIn('organisations.id', $userOrganisations);
        }

        $averageDurations = $averageDurations->get();

        // Map for chart formatting
        $averageDurations = $averageDurations->map(function ($item) {
            return [
                'month' => $item->month,
                'avgDuration' => round($item->avgDuration, 2),
            ];
        });
        

        // -------------------------------------------------------------
        // Candidates due to be purged at the next Monday 09:00 run
        // -------------------------------------------------------------

        $now = Carbon::now();

        // Work out the next scheduled purge.
        // If it is Monday and still before/equal to 09:00, today's 09:00
        // run is considered the next purge. Otherwise use next Monday.
        $todayPurgeTime = $now->copy()->setTime(9, 0, 0);

        if ($now->isMonday() && $now->lte($todayPurgeTime)) {
            $nextPurgeAt = $todayPurgeTime;
        } else {
            $nextPurgeAt = $now->copy()
                ->next(Carbon::MONDAY)
                ->setTime(9, 0, 0);
        }

        $purgeCandidatesQuery = \DB::table('users')
            ->join(
                'organisations',
                'organisations.id',
                '=',
                'users.organisationID'
            )
            ->where('users.completed', 1)
            ->whereNotNull('users.completedDate')

            /*
            * A candidate will be eligible for the next purge when:
            *
            * completedDate + organisation retention period
            *     <= next Monday at 09:00
            *
            * GREATEST(..., 2) protects the minimum 2-year retention
            * period even if an invalid value somehow exists in the DB.
            */
            ->whereRaw(
                'TIMESTAMPADD(
                    YEAR,
                    GREATEST(
                        COALESCE(organisations.data_retention_years, 2),
                        2
                    ),
                    users.completedDate
                ) <= ?',
                [$nextPurgeAt->format('Y-m-d H:i:s')]
            )
            ->select([
                'users.id',
                'users.firstName',
                'users.lastName',
                'users.email',
                'users.completedDate',
                'users.organisationID',
                'organisations.organisationName',
                'organisations.data_retention_years',

                \DB::raw(
                    'TIMESTAMPADD(
                        YEAR,
                        GREATEST(
                            COALESCE(organisations.data_retention_years, 2),
                            2
                        ),
                        users.completedDate
                    ) AS retentionExpiresAt'
                ),
            ]);

        /*
        * Keep exactly the same organisation-access behaviour as the
        * rest of the dashboard.
        */
        if (!in_array('superuser', $currentUserRoles)) {
            $purgeCandidatesQuery->whereIn(
                'organisations.id',
                $userOrganisations
            );
        }

        $purgeCandidates = $purgeCandidatesQuery
            ->orderBy('retentionExpiresAt', 'asc')
            ->get()
            ->map(function ($candidate) use ($nextPurgeAt) {
                $candidate->retentionYears = max(
                    2,
                    (int) ($candidate->data_retention_years ?? 2)
                );

                $candidate->completedDateFormatted = Carbon::parse(
                    $candidate->completedDate
                )->format('d/m/Y');

                $candidate->retentionExpiresFormatted = Carbon::parse(
                    $candidate->retentionExpiresAt
                )->format('d/m/Y');

                $candidate->purgeDateFormatted =
                    $nextPurgeAt->format('d/m/Y \a\t H:i');

                return $candidate;
            });


        // Pass data to the view
        return view('adminoperator.futuredashboard', [
            'pendingAndAwaiting' => $pendingAndAwaiting, 
            'completedUsers' => $completedUsers, 
            'incompleteUsers' => $incompleteUsers, 
            'currentUser' => $currentUser, 
            'completedThisMonthCount' => $completedThisMonthCount, 
            'completedLastMonthCount' => $completedLastMonthCount,
            'percentageChange' => round(abs($percentageChange), 2),
            'growthClass' => $growthClass,
            'growthSymbol' => $growthSymbol,
            'averageDurations' => json_encode($averageDurations),
            'purgeCandidates' => $purgeCandidates,
            'nextPurgeAt'     => $nextPurgeAt
        ]);
    }


    public function completedApplications()
    {   
        // Only administrators
        if(!$this->checkAccess('siteuser')) return redirect("/login");

        return view('adminoperator.completedApplications');
    }

    public function completedApplicationsData()
    {
        if (!$this->checkAccess('siteuser')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $currentUserRoles = $this->getCurrentUserRoles();
        $userOrganisations = $this->getUserOrganisations();

        $query = \DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->leftJoin('applications', 'users.id', '=', 'applications.userID')
            ->where('userType', 'applicant')
            ->where('completed', 1)
            ->select([
                'users.id',
                'users.email',
                'users.firstName',
                'users.lastName',
                'organisations.organisationName',
                'applications.applicationStatus',
                'applications.id as applicationID',
                'users.completedDate',
                'users.dbsApplication'
            ]);

        if (array_search('superuser', $currentUserRoles) === false) {
            $query->whereIn('organisations.id', $userOrganisations);
        }

        // Legacy DataTables uses sEcho, not draw
        $sEcho = request('sEcho', 1);

        $start = request('iDisplayStart', 0);
        $length = request('iDisplayLength', 50);
        $searchValue = request('sSearch');

        // Column mapping for ordering
        $columns = ['email', 'firstName', 'organisationName', 'applicationStatus', null, 'completedDate', null];
        $orderColumnIndex = request('iSortCol_0', 5);
        $orderDirection = request('sSortDir_0', 'desc');
        $orderColumn = $columns[$orderColumnIndex] ?? 'completedDate';

        // Search
        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('users.email', 'like', "%{$searchValue}%")
                ->orWhere('users.firstName', 'like', "%{$searchValue}%")
                ->orWhere('users.lastName', 'like', "%{$searchValue}%")
                ->orWhere('organisations.organisationName', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = $query->count();

        // Order + limit
        if ($orderColumn) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        $records = $query->offset($start)->limit($length)->get();

        // Format time
        foreach ($records as $applicant) {
            if (!empty($applicant->completedDate)) {
                $completedDate = Carbon::parse($applicant->completedDate);
                $now = Carbon::now();
                $diffInYears = $completedDate->diffInYears($now);
                $diffInMonths = $completedDate->diffInMonths($now) % 12;
                $diffInDays = $completedDate->diffInDays($now) % 30;
                $elapsedTime = [];
                if ($diffInYears > 0) $elapsedTime[] = "$diffInYears year".($diffInYears > 1 ? "s" : "");
                if ($diffInMonths > 0) $elapsedTime[] = "$diffInMonths month".($diffInMonths > 1 ? "s" : "");
                if ($diffInDays > 0 || empty($elapsedTime)) $elapsedTime[] = "$diffInDays day".($diffInDays > 1 ? "s" : "");
                $applicant->timeSinceCompletion = implode(", ", $elapsedTime);
            } else {
                $applicant->timeSinceCompletion = "N/A";
            }
        }

        // LEGACY FORMAT (this is what your old DataTables expects)
        return response()->json([
            "sEcho"                => (string) $sEcho,
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData"               => $records
        ]);
    }

    public function inprogressApplications()
    {   
        // Only administrators
        if(!$this->checkAccess('siteuser')) return redirect("/login");

        // Get search result
        $currentUserRoles = $this->getCurrentUserRoles();
        $userOrganisations = $this->getUserOrganisations();
        
        $applicants = \DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->leftJoin('bpss_applications', 'users.id', '=', 'bpss_applications.userID')
            ->leftJoin('applications', 'users.id', '=', 'applications.userID')
            ->where('userType', 'applicant')
            ->where(function ($query) {
                $query->where('completed', '!=', 1)
                    ->orWhereNull('completed'); // Include NULL values
            })
            ->select('users.*', 'organisations.organisationName', 'applications.contact_number_country_code', 'applications.contact_number', 'applications.mobile_number_country_code', 'applications.mobile_number', 'applications.applicationStatus', 'applications.dbsResponse_int023_DisclosureStatus', 'applications.id as applicationID', 'applications.admin_confirm_valid_nino', 'applications.admin_confirm_valid_dln', 'applications.admin_confirm_valid_passport', 'applications.percentCompleted', 'bpss_applications.applicationStatus as BPSSapplicationStatus');

        if (array_search('superuser', $currentUserRoles) === false) {
            $applicants = $applicants->whereIn('organisations.id', $userOrganisations);
        }

        $applicants = $applicants->get();
            
        return view('adminoperator.inprogressApplications', ['applicants' => $applicants]);
    }


    public function allApplications()
    {
        // Only administrators
        if (!$this->checkAccess('siteuser')) return redirect("/login");
        return view('adminoperator.allApplications');   // ← no data needed anymore
    }

    public function allApplicationsData()
    {
        if (!$this->checkAccess('siteuser')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $currentUserRoles = $this->getCurrentUserRoles();
        $userOrganisations = $this->getUserOrganisations();

        $searchValue = request('sSearch', '');

        // === 1. Incomplete Applications (searchStatus = 2) ===
        $incomplete = \DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->leftJoin('bpss_applications', 'users.id', '=', 'bpss_applications.userID')
            ->leftJoin('applications', 'users.id', '=', 'applications.userID')
            ->where('userType', 'applicant')
            ->where(function ($query) {
                $query->where('completed', '!=', 1)
                    ->orWhereNull('completed');
            })
            ->select(
                'users.*',
                'organisations.organisationName',
                'applications.contact_number_country_code',
                'applications.contact_number',
                'applications.mobile_number_country_code',
                'applications.mobile_number',
                'applications.applicationStatus',
                'applications.dbsResponse_int023_DisclosureStatus',
                'applications.id as applicationID',
                'applications.admin_confirm_valid_nino',
                'applications.admin_confirm_valid_dln',
                'applications.admin_confirm_valid_passport',
                'applications.percentCompleted',
                'bpss_applications.applicationStatus as BPSSapplicationStatus',
                'applications.createdOn'
            );

        if (array_search('superuser', $currentUserRoles) === false) {
            $incomplete->whereIn('organisations.id', $userOrganisations);
        }

        if ($searchValue) {
            $incomplete->where(function ($q) use ($searchValue) {
                $q->where('users.email', 'like', "%{$searchValue}%")
                ->orWhere('users.firstName', 'like', "%{$searchValue}%")
                ->orWhere('users.lastName', 'like', "%{$searchValue}%")
                ->orWhere('organisations.organisationName', 'like', "%{$searchValue}%");
            });
        }

        $incompleteRecords = $incomplete->get();

        // === 2. Completed Applications (searchStatus = 3) ===
        $completed = \DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->leftJoin('bpss_applications', 'users.id', '=', 'bpss_applications.userID')
            ->leftJoin('applications', 'users.id', '=', 'applications.userID')
            ->where('userType', 'applicant')
            ->where('completed', 1)
            ->select('users.*', 'organisations.organisationName', 'applications.contact_number_country_code', 'applications.contact_number', 'applications.mobile_number_country_code', 'applications.mobile_number', 'applications.applicationStatus', 'applications.dbsResponse_int023_DisclosureStatus', 'applications.id as applicationID', 'applications.admin_confirm_valid_nino', 'applications.admin_confirm_valid_dln', 'applications.admin_confirm_valid_passport', 'applications.createdOn');

        if (array_search('superuser', $currentUserRoles) === false) {
            $completed->whereIn('organisations.id', $userOrganisations);
        }

        if ($searchValue) {
            $completed->where(function ($q) use ($searchValue) {
                $q->where('users.email', 'like', "%{$searchValue}%")
                ->orWhere('users.firstName', 'like', "%{$searchValue}%")
                ->orWhere('users.lastName', 'like', "%{$searchValue}%")
                ->orWhere('organisations.organisationName', 'like', "%{$searchValue}%");
            });
        }

        $completedRecords = $completed->get();

        // === 3. Pending/Unregistered (searchStatus = 1) ===
        $pending = \DB::table('applicants')
            ->join('organisations', 'organisations.id', '=', 'applicants.organisationID')
            ->select('applicants.*', 'organisations.organisationName');

        if (array_search('superuser', $currentUserRoles) === false) {
            $pending->whereIn('organisations.id', $userOrganisations);
        }

        if ($searchValue) {
            $pending->where(function ($q) use ($searchValue) {
                $q->where('applicants.email', 'like', "%{$searchValue}%")
                ->orWhere('applicants.forename', 'like', "%{$searchValue}%")
                ->orWhere('applicants.surname', 'like', "%{$searchValue}%")
                ->orWhere('organisations.organisationName', 'like', "%{$searchValue}%");
            });
        }

        $pendingRecords = $pending->get();

        // === Normalise fields (fixes 'firstName' error) ===
        $incompleteRecords->transform(function ($applicant) {
            $applicant->searchStatus = 2;
            return $applicant;
        });

        $completedRecords->transform(function ($applicant) {
            $applicant->searchStatus = 3;
            return $applicant;
        });

        $pendingRecords->transform(function ($applicant) {
            $applicant->searchStatus = 1;
            $applicant->firstName = $applicant->forename ?? '';
            $applicant->lastName  = $applicant->surname ?? '';
            return $applicant;
        });

        // Merge + alphabetical sort by name
        $records = $incompleteRecords->merge($completedRecords)->merge($pendingRecords)
            ->sortBy(function ($item) {
                return strtolower(trim($item->firstName . ' ' . $item->lastName));
            })->values();

        // Time since completion for completed records
        foreach ($records as $applicant) {
            if ($applicant->searchStatus == 3 && !empty($applicant->completedDate)) {
                $completedDate = Carbon::parse($applicant->completedDate);
                $now = Carbon::now();
                $diffInYears  = $completedDate->diffInYears($now);
                $diffInMonths = $completedDate->diffInMonths($now) % 12;
                $diffInDays   = $completedDate->diffInDays($now) % 30;

                $elapsed = [];
                if ($diffInYears > 0)   $elapsed[] = "$diffInYears year" . ($diffInYears > 1 ? "s" : "");
                if ($diffInMonths > 0)  $elapsed[] = "$diffInMonths month" . ($diffInMonths > 1 ? "s" : "");
                if ($diffInDays > 0 || empty($elapsed)) $elapsed[] = "$diffInDays day" . ($diffInDays > 1 ? "s" : "");

                $applicant->timeSinceCompletion = implode(", ", $elapsed);
            } else {
                $applicant->timeSinceCompletion = '';
            }
        }

        // Pagination
        $start  = (int) request('iDisplayStart', 0);
        $length = (int) request('iDisplayLength', 50);

        $paginatedRecords = $records->slice($start, $length)->values()->all();

        $sEcho = request('sEcho', 1);

        return response()->json([
            "sEcho"                => (string) $sEcho,
            "iTotalRecords"        => $records->count(),
            "iTotalDisplayRecords" => $records->count(),
            "aaData"               => $paginatedRecords
        ]);
    }

    public function candidateMessagingIndex(Request $request)
    {
        if (!Auth::check() || !$this->checkAccess('superuser')) {
            return redirect('/login');
        }

        $organisations = \DB::table('organisations')
            ->where('organisationStatus', 1)
            ->orderBy('organisationName')
            ->get(['id', 'organisationName']);

        // Filters
        $filterUserType = $request->get('userType', 'candidate'); // candidate|siteuser|superuser|all
        $filterOrgId = $request->get('organisation_id');
        $filterStatus = $request->get('application_status'); // pending_registration|in_progress|complete
        $search = trim((string) $request->get('search', ''));

        // Ordering
        $allowedSortBy = ['name', 'email', 'phone', 'organisation', 'status'];
        $sortBy = in_array($request->get('sort_by'), $allowedSortBy) ? $request->get('sort_by') : 'name';
        $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        // Pagination
        $perPage = 25;
        $page = max((int) $request->get('page', 1), 1);

        $rows = collect();

        /**
         * 1) Pending Registration (from applicants table only)
         */
        if (in_array($filterUserType, ['candidate', 'all'])) {
            $pendingApplicantsQuery = \DB::table('applicants')
                ->join('organisations', 'organisations.id', '=', 'applicants.organisationID')
                ->select(
                    \DB::raw("'applicant_pending' as source_type"),
                    \DB::raw("applicants.id as source_id"),
                    'applicants.forename',
                    'applicants.surname',
                    'applicants.email',
                    \DB::raw("applicants.mobileNumberMain as phone"),
                    'organisations.id as organisation_id',
                    'organisations.organisationName',
                    \DB::raw("'candidate' as user_type_filter"),
                    \DB::raw("'pending_registration' as application_status_filter")
                );

            if (!empty($filterOrgId)) {
                $pendingApplicantsQuery->where('organisations.id', $filterOrgId);
            }

            // Only pending_registration lives in applicants
            if (!empty($filterStatus) && $filterStatus !== 'pending_registration') {
                $pendingApplicantsQuery->whereRaw('1=0');
            }

            $rows = $rows->merge($pendingApplicantsQuery->get());
        }

        /**
         * 2) Users table - applicants (InProgress / Complete)
         * complete = users.userType='applicant' AND completed = 1
         * in_progress = users.userType='applicant' AND completed != 1 (or null)
         */
        if (in_array($filterUserType, ['candidate', 'all'])) {
            $userApplicantsQuery = \DB::table('users')
                ->join('organisations', 'organisations.id', '=', 'users.organisationID')
                ->leftJoin('applications', 'applications.userID', '=', 'users.id')
                ->where('users.userType', 'applicant')
                ->select(
                    \DB::raw("'user' as source_type"),
                    \DB::raw("users.id as source_id"),
                    \DB::raw("users.firstName as forename"),
                    \DB::raw("users.lastName as surname"),
                    'users.email',
                    \DB::raw("
                        COALESCE(
                            NULLIF(
                                CONCAT(
                                    CASE
                                        WHEN COALESCE(applications.mobile_number_country_code, '') <> ''
                                            THEN CONCAT('+', applications.mobile_number_country_code, ' ')
                                        ELSE ''
                                    END,
                                    COALESCE(applications.mobile_number, '')
                                ),
                            ''),
                            NULLIF(
                                CONCAT(
                                    CASE
                                        WHEN COALESCE(applications.contact_number_country_code, '') <> ''
                                            THEN CONCAT('+', applications.contact_number_country_code, ' ')
                                        ELSE ''
                                    END,
                                    COALESCE(applications.contact_number, '')
                                ),
                            ''),
                            users.phoneNumber
                        ) as phone
                    "),
                    'organisations.id as organisation_id',
                    'organisations.organisationName',
                    \DB::raw("'candidate' as user_type_filter"),
                    \DB::raw("
                        CASE
                            WHEN users.completed = 1 THEN 'complete'
                            ELSE 'in_progress'
                        END as application_status_filter
                    ")
                );

            if (!empty($filterOrgId)) {
                $userApplicantsQuery->where('organisations.id', $filterOrgId);
            }

            if ($filterStatus === 'complete') {
                $userApplicantsQuery->where('users.completed', 1);
            } elseif ($filterStatus === 'in_progress') {
                $userApplicantsQuery->where(function ($q) {
                    $q->where('users.completed', '!=', 1)
                    ->orWhereNull('users.completed');
                });
            } elseif ($filterStatus === 'pending_registration') {
                // pending_registration is in applicants table only
                $userApplicantsQuery->whereRaw('1=0');
            }

            $rows = $rows->merge($userApplicantsQuery->get());
        }

        /**
         * 3) Admin users (siteuser/superuser) from users + user_roles
         * No application status for admins
         * Phone from users.phoneNumber
         */
        if (in_array($filterUserType, ['siteuser', 'superuser', 'all'])) {
            $adminQuery = \DB::table('users')
                ->join('organisations', 'organisations.id', '=', 'users.organisationID')
                ->join('user_roles', 'user_roles.userID', '=', 'users.id')
                ->where('users.userType', 'admin')
                ->select(
                    \DB::raw("'user' as source_type"),
                    \DB::raw("users.id as source_id"),
                    \DB::raw("users.firstName as forename"),
                    \DB::raw("users.lastName as surname"),
                    'users.email',
                    \DB::raw("users.phoneNumber as phone"),
                    'organisations.id as organisation_id',
                    'organisations.organisationName',
                    \DB::raw("
                        CASE
                            WHEN user_roles.role = 'superuser' THEN 'superuser'
                            ELSE 'siteuser'
                        END as user_type_filter
                    "),
                    \DB::raw("NULL as application_status_filter")
                );

            if ($filterUserType === 'siteuser') {
                $adminQuery->where('user_roles.role', 'siteuser');
            } elseif ($filterUserType === 'superuser') {
                $adminQuery->where('user_roles.role', 'superuser');
            }

            if (!empty($filterOrgId)) {
                $adminQuery->where('organisations.id', $filterOrgId);
            }

            // application_status ignored for admin user types
            $rows = $rows->merge($adminQuery->get());
        }

        /**
         * Search
         */
        if ($search !== '') {
            $needle = mb_strtolower($search);

            $rows = $rows->filter(function ($r) use ($needle) {
                $name = mb_strtolower(trim(($r->forename ?? '') . ' ' . ($r->surname ?? '')));
                $email = mb_strtolower((string)($r->email ?? ''));
                $phone = mb_strtolower((string)($r->phone ?? ''));
                $org = mb_strtolower((string)($r->organisationName ?? ''));
                $status = mb_strtolower((string)($r->application_status_filter ?? ''));

                return str_contains($name, $needle)
                    || str_contains($email, $needle)
                    || str_contains($phone, $needle)
                    || str_contains($org, $needle)
                    || str_contains($status, $needle);
            })->values();
        }

        /**
         * Sorting
         */
        $sortValue = function ($r) use ($sortBy) {
            switch ($sortBy) {
                case 'email':
                    return mb_strtolower((string)($r->email ?? ''));
                case 'phone':
                    return mb_strtolower((string)($r->phone ?? ''));
                case 'organisation':
                    return mb_strtolower((string)($r->organisationName ?? ''));
                case 'status':
                    return mb_strtolower((string)($r->application_status_filter ?? ''));
                case 'name':
                default:
                    return mb_strtolower(trim(($r->forename ?? '') . ' ' . ($r->surname ?? '')));
            }
        };

        $rows = $sortDir === 'desc'
            ? $rows->sortByDesc($sortValue)->values()
            : $rows->sortBy($sortValue)->values();

        /**
         * Manual pagination
         */
        $total = $rows->count();
        $itemsForCurrentPage = $rows->slice(($page - 1) * $perPage, $perPage)->values();

        $candidates = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('adminoperator.candidateMessaging.messagingSelect', [
            'organisations' => $organisations,
            'candidates' => $candidates,
            'filters' => [
                'userType' => $filterUserType,
                'organisation_id' => $filterOrgId,
                'application_status' => $filterStatus,
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
        ]);
    }

    public function candidateMessagingCompose(Request $request)
    {
        if (!Auth::check() || !$this->checkAccess('superuser')) {
            return redirect('/login');
        }

        $validated = $request->validate([
            'selected_users' => 'required|array|min:1',
            'selected_users.*' => 'string', // IMPORTANT: keys like "user:123"
        ]);

        $split = $this->splitRecipientKeys($validated['selected_users']);
        $userIds = $split['userIds'];
        $applicantIds = $split['applicantIds'];

        $recipients = collect();

        // USERS recipients
        if (!empty($userIds)) {
            $userRecipients = \DB::table('users')
                ->leftJoin('applications', 'applications.userID', '=', 'users.id')
                ->whereIn('users.id', $userIds)
                ->select(
                    \DB::raw("'user' as source_type"),
                    \DB::raw("users.id as source_id"),
                    \DB::raw("users.firstName as forename"),
                    \DB::raw("users.lastName as surname"),
                    'users.email',
                    \DB::raw("
                        COALESCE(
                            NULLIF(
                                CONCAT(
                                    CASE
                                        WHEN COALESCE(applications.mobile_number_country_code, '') <> ''
                                            THEN CONCAT('+', applications.mobile_number_country_code, ' ')
                                        ELSE ''
                                    END,
                                    COALESCE(applications.mobile_number, '')
                                ),
                            ''),
                            NULLIF(
                                CONCAT(
                                    CASE
                                        WHEN COALESCE(applications.contact_number_country_code, '') <> ''
                                            THEN CONCAT('+', applications.contact_number_country_code, ' ')
                                        ELSE ''
                                    END,
                                    COALESCE(applications.contact_number, '')
                                ),
                            ''),
                            users.phoneNumber
                        ) as phone
                    ")
                )
                ->get();

            $recipients = $recipients->merge($userRecipients);
        }

        // APPLICANTS recipients (pending registration)
        if (!empty($applicantIds)) {
            $applicantRecipients = \DB::table('applicants')
                ->whereIn('applicants.id', $applicantIds)
                ->select(
                    \DB::raw("'applicant_pending' as source_type"),
                    \DB::raw("applicants.id as source_id"),
                    'applicants.forename',
                    'applicants.surname',
                    'applicants.email',
                    \DB::raw("applicants.mobileNumberMain as phone")
                )
                ->get();

            $recipients = $recipients->merge($applicantRecipients);
        }

        // If parsing produced no valid IDs
        if ($recipients->count() === 0) {
            return redirect()->route('candidateMessaging.index')
                ->with('error', 'No valid recipients were selected.');
        }

        return view('adminoperator.candidateMessaging.messagingCompose', [
            'recipients' => $recipients,
            'selectedUsers' => $validated['selected_users'], // pass through original keys
        ]);
    }

    public function candidateMessagingSend(Request $request, ClickSendSmsService $smsService)
    {
        if (!Auth::check() || !$this->checkAccess('superuser')) {
            return redirect('/login');
        }

        $validated = $request->validate([
            'selected_users' => 'required|array|min:1',
            'selected_users.*' => 'string',
            'channel' => 'required|in:email,sms',
            'subject' => 'required_if:channel,email|nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'shorten_urls' => 'nullable|boolean',
        ]);

        $split = $this->splitRecipientKeys($validated['selected_users']);
        $userIds = $split['userIds'];
        $applicantIds = $split['applicantIds'];

        $recipients = collect();

        if (!empty($userIds)) {
            $users = \DB::table('users')
                ->leftJoin('applications', 'applications.userID', '=', 'users.id')
                ->whereIn('users.id', $userIds)
                ->select(
                    \DB::raw("'user' as source_type"),
                    \DB::raw("users.id as source_id"),
                    \DB::raw("users.firstName as forename"),
                    \DB::raw("users.lastName as surname"),
                    'users.email',
                    \DB::raw("
                        COALESCE(
                            NULLIF(
                                CONCAT(
                                    CASE
                                        WHEN COALESCE(applications.mobile_number_country_code, '') <> ''
                                            THEN CONCAT('+', applications.mobile_number_country_code, ' ')
                                        ELSE ''
                                    END,
                                    COALESCE(applications.mobile_number, '')
                                ),
                            ''),
                            NULLIF(
                                CONCAT(
                                    CASE
                                        WHEN COALESCE(applications.contact_number_country_code, '') <> ''
                                            THEN CONCAT('+', applications.contact_number_country_code, ' ')
                                        ELSE ''
                                    END,
                                    COALESCE(applications.contact_number, '')
                                ),
                            ''),
                            users.phoneNumber
                        ) as phone
                    ")
                )
                ->get();

            $recipients = $recipients->merge($users);
        }

        if (!empty($applicantIds)) {
            $apps = \DB::table('applicants')
                ->whereIn('applicants.id', $applicantIds)
                ->select(
                    \DB::raw("'applicant_pending' as source_type"),
                    \DB::raw("applicants.id as source_id"),
                    'applicants.forename',
                    'applicants.surname',
                    'applicants.email',
                    \DB::raw("applicants.mobileNumberMain as phone")
                )
                ->get();

            $recipients = $recipients->merge($apps);
        }

        if ($recipients->count() === 0) {
            return redirect()->route('candidateMessaging.index')
                ->with('error', 'No valid recipients were found.');
        }

        $sent = 0;
        $failed = 0;
        $errors = [];

        if ($validated['channel'] === 'email') {
            $mailDriver = env('MAIL_DRIVER');
            if (!isset($mailDriver) || strlen($mailDriver) === 0) {
                return back()->with('error', 'Mail driver is not configured.');
            }

            foreach ($recipients as $r) {
                if (empty($r->email)) {
                    $failed++;
                    $errors[] = "{$r->source_type}:{$r->source_id} has no email.";
                    continue;
                }

                try {
                    Mail::send([], [], function ($message) use ($r, $validated) {
                        $message->to($r->email)
                            ->subject($validated['subject'])
                            ->setBody(
                                view('email_templates.massEmailLayout', [
                                    'subject' => $validated['subject'],
                                    'message' => $validated['message'],
                                ])->render(),
                                'text/html'
                            );
                    });
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    $errors[] = "Email failed for {$r->email}: " . $e->getMessage();
                }
            }
        } else {
            $shortenUrls = (bool) ($validated['shorten_urls'] ?? true);

            // ✅ Safety: ClickSend errors if shorten_urls=true but there is no URL
            if ($shortenUrls && !$this->textHasUrl($validated['message'])) {
                $shortenUrls = false;
            }

            foreach ($recipients as $r) {
                $to = preg_replace('/\s+/', '', (string)($r->phone ?? ''));

                if (empty($to)) {
                    $failed++;
                    $errors[] = "{$r->source_type}:{$r->source_id} has no phone number.";
                    continue;
                }

                try {
                    $smsService->send($to, $validated['message'], $shortenUrls);
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    $errors[] = "SMS failed for {$to}: " . $e->getMessage();
                }
            }
        }

        \DB::table('adminLogs')->insert([
            'userID' => Auth::id(),
            'actions' => sprintf(
                'Sent %s message to %d selected recipients. Success: %d, Failed: %d',
                $validated['channel'],
                count($validated['selected_users']),
                $sent,
                $failed
            ),
            'occurance_at' => now(),
        ]);

        $msg = ucfirst($validated['channel']) . " send complete. Sent: {$sent}, Failed: {$failed}.";
        if ($failed > 0) {
            return back()->with('warning', $msg)->with('errors_list', $errors);
        }

        return redirect()->route('candidateMessaging.index')->with('success', $msg);
    }

    private function textHasUrl(string $text): bool
    {
        return preg_match('/(https?:\/\/|www\.)\S+/i', $text) === 1;
    }

    private function splitRecipientKeys(array $keys): array
    {
        $userIds = [];
        $applicantIds = [];

        foreach ($keys as $key) {
            $key = (string) $key;
            if (str_starts_with($key, 'user:')) {
                $id = (int) substr($key, 5);
                if ($id > 0) $userIds[] = $id;
            } elseif (str_starts_with($key, 'applicant_pending:')) {
                $id = (int) substr($key, strlen('applicant_pending:'));
                if ($id > 0) $applicantIds[] = $id;
            }
        }

        return [
            'userIds' => array_values(array_unique($userIds)),
            'applicantIds' => array_values(array_unique($applicantIds)),
        ];
    }

    public function returnVR(Request $request)
    {
        if(!$this->checkAccess('siteuser')) return redirect("/login");
        
        $request->validate([
            'userID' => 'required|integer|exists:users,id',
        ]);

        \DB::table('users')->where('id', $request->userID)->update(['completed' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'VR returned successfully.'
        ]);
    }

    public function editPendingApplicant($id)
    {
        if (!$this->checkAccess('siteuser')) {
            return redirect('/login');
        }

        $applicant = \DB::table('applicants')
            ->where('id', $id)
            ->first();

        if (!$applicant) {
            abort(404);
        }

        return view('admin.editPendingApplicant', [
            'applicant' => $applicant
        ]);
    }


    public function updatePendingApplicant(Request $request, $id)
    {
        if (!$this->checkAccess('siteuser')) {
            return redirect('/login');
        }

        $applicant = \DB::table('applicants')
            ->where('id', $id)
            ->first();

        if (!$applicant) {
            abort(404);
        }

        $request->validate([
            'forename' => 'required|string|max:100',
            'surname' => 'required|string|max:100',

            'email' => [
                'required',
                'email',
                \Illuminate\Validation\Rule::unique('applicants', 'email')
                    ->ignore($id),
            ],

            'applicationCode' => 'nullable|string|max:100',
            'mobileNumber' => 'nullable|string|max:30',

            'dbsApplication' => 'nullable|in:1',
            'bpssApplication' => 'nullable|in:1',
            'REVALonsite' => 'nullable|in:1',
            'REVALoffsite' => 'nullable|in:1',
            'useYoti' => 'nullable|in:1',
        ]);

        \DB::table('applicants')
            ->where('id', $id)
            ->update([
                'forename' => $request->input('forename'),
                'surname' => $request->input('surname'),
                'email' => $request->input('email'),

                'applicationCode' => $request->input('applicationCode'),

                // Your existing pending page uses mobileNumberMain.
                'mobileNumberMain' => $request->input('mobileNumber'),

                // Unchecked checkboxes do not get submitted,
                // so explicitly convert them to 0/1.
                'dbsApplication' => $request->has('dbsApplication') ? 1 : 0,
                'bpssApplication' => $request->has('bpssApplication') ? 1 : 0,
                'REVALonsite' => $request->has('REVALonsite') ? 1 : 0,
                'REVALoffsite' => $request->has('REVALoffsite') ? 1 : 0,
                'useYoti' => $request->has('useYoti') ? 1 : 0,
            ]);

        return redirect('/applications/pendingRequests')
            ->with('success', 'Applicant details updated successfully.');
    }
}