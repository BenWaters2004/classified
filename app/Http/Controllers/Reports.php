<?php
namespace App\Http\Controllers;

include_once $_SERVER["DOCUMENT_ROOT"].'/../pdfmerger/PDFMerger.php';


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spipu\Html2Pdf\Html2Pdf;
use Yoti\DocScan\DocScanClient;
use PDFMerger\PDFMerger;
use Illuminate\Support\Facades\Mail;
use setasign\Fpdi\Tcpdf\Fpdi;



class Reports extends Controller
{

	/*
    |--------------------------------------------------------------------------
    | Reports Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling the Reports logic
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

    

    public function applicationsReport()
    {
        $availableOrganisations = \DB::table('organisations')
            ->select('*')
            ->where(['organisations.organisationStatus'=>1])
            ->get();
        return view('reports.applicationsReport', ['availableOrganisations' => $availableOrganisations]);
    }

    public function generateApplicationsReport(Request $request)
    {
        $requestVars = $request->all();
        //echo'<pre>';print_r($requestVars);echo'</pre>';

        if (isset($requestVars['reportDateRangeFrom'])){
            $reportDateRangeFromRaw = explode('/',$requestVars['reportDateRangeFrom']);
            $reportDateRangeFrom = $reportDateRangeFromRaw[2] . '-' . $reportDateRangeFromRaw[1] . '-' . $reportDateRangeFromRaw[0];
        } else $reportDateRangeFrom = null;

        if (isset($requestVars['reportDateRangeTo'])){
            $reportDateRangeToRaw = explode('/',$requestVars['reportDateRangeTo']);
            $reportDateRangeTo = $reportDateRangeToRaw[2] . '-' . $reportDateRangeToRaw[1] . '-' . $reportDateRangeToRaw[0];
        } else $reportDateRangeTo = null;

        $organisationID = (isset($requestVars['organisationID']) && is_numeric($requestVars['organisationID'])) ? $requestVars['organisationID'] : 0;

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
                $applications->where('applications.createdOn', '>=', $reportDateRangeFrom);
            }
            if(!empty($reportDateRangeTo)){
                $applications->where('applications.createdOn', '<=', $reportDateRangeTo);
            }
            if(!empty($organisationID)){
                $applications->where('applications.organisationID', '=', $organisationID);
            }
            $applications = $applications->get();
        if(isset($applications) && count($applications)> 0){
            foreach($applications as $key=>$application){
                $applications[$key]->createdByAdmin = \DB::table('users')->select('users.id', 'users.firstName', 'users.lastName')->where(['users.id'=>$application->createdBy])->first();
            }
        }

        //echo'<pre>';print_r($applications);echo'</pre>';

        return view('reports.applicationsReport', ['availableOrganisations' => $availableOrganisations, 'applications' => $applications, 'reportDateRangeFrom' => $reportDateRangeFrom, 'reportDateRangeTo' => $reportDateRangeTo, 'organisationID' => $organisationID]);
    }

    public function trackerReport()
    {
        $availableOrganisations = \DB::table('organisations')
            ->select('*')
            ->where(['organisations.organisationStatus'=>1])
            ->get();
        return view('reports.trackerReport', ['availableOrganisations' => $availableOrganisations]);
    }

    public function generateTrackerReport(Request $request)
    {
        $requestVars = $request->all();
        //echo'<pre>';print_r($requestVars);echo'</pre>';

        if (isset($requestVars['reportDateRangeFrom'])){
            $reportDateRangeFromRaw = explode('/',$requestVars['reportDateRangeFrom']);
            $reportDateRangeFrom = $reportDateRangeFromRaw[2] . '-' . $reportDateRangeFromRaw[1] . '-' . $reportDateRangeFromRaw[0];
        } else $reportDateRangeFrom = null;

        if (isset($requestVars['reportDateRangeTo'])){
            $reportDateRangeToRaw = explode('/',$requestVars['reportDateRangeTo']);
            $reportDateRangeTo = $reportDateRangeToRaw[2] . '-' . $reportDateRangeToRaw[1] . '-' . $reportDateRangeToRaw[0];
        } else $reportDateRangeTo = null;

        $organisationID = (isset($requestVars['organisationID']) && is_numeric($requestVars['organisationID'])) ? $requestVars['organisationID'] : 0;

        $availableOrganisations = \DB::table('organisations')
            ->select('*')
            ->where(['organisations.organisationStatus'=>1])
            ->get();

        //get search result
        $applications = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->join('organisations', 'organisations.id', '=', 'applications.organisationID')
            ->select('applications.*', 'organisations.organisationName', 'users.email as userEmail', 'users.lastInvitationSent', 'users.dbsApplication', 'users.bpssApplication', 'users.createdBy');
            if(!empty($reportDateRangeFrom)){
                $applications->where('applications.createdOn', '>=', $reportDateRangeFrom);
            }
            if(!empty($reportDateRangeTo)){
                $applications->where('applications.createdOn', '<=', $reportDateRangeTo);
            }
            if(!empty($organisationID)){
                $applications->where('applications.organisationID', '=', $organisationID);
            }
            $applications = $applications->get();
        if(isset($applications) && count($applications)> 0){
            foreach($applications as $key=>$application){
                $applications[$key]->createdByAdmin = \DB::table('users')->select('users.id', 'users.firstName', 'users.lastName')->where(['users.id'=>$application->createdBy])->first();
                if(date("Y-m-d",strtotime($application->lastInvitationSent)) == '1970-01-01'){
                    $applications[$key]->lastInvitationSent = 'N/A';
                } else {
                    $applications[$key]->lastInvitationSent = date("d/m/Y @ H:i:s",strtotime($application->lastInvitationSent));
                }
                //get uploaded documents
                $applicantDocuments = \DB::table('new_applicant_documents')
                    ->select('*')
                    ->where(['userID'=>$application->userID])
                    ->orderBy('id', 'desc')
                    ->get();
                $mkDenialPath = '';
                $secMatrixPath = '';
                if(isset($applicantDocuments) && count($applicantDocuments)>0){
                    foreach ($applicantDocuments as $document) {
                        if($document->file_type == 'mkden' && isset($document->document_path) && strlen($document->document_path) > 0 && strlen($mkDenialPath) == 0){
                            $mkDenialPath = $document->document_path;
                        }
                        if($document->file_type == 'secmx' && isset($document->document_path) && strlen($document->document_path) > 0 && strlen($secMatrixPath) == 0){
                            $secMatrixPath = $document->document_path;
                        }
                    }
                }
                $applications[$key]->mkDenialLink = (strlen($mkDenialPath) > 0) ? $mkDenialPath : '';
                $applications[$key]->secMatrixLink = (strlen($secMatrixPath) > 0) ? $secMatrixPath : '';
            }
        }

        //echo'<pre>';print_r($applications);echo'</pre>';

        return view('reports.trackerReport', ['availableOrganisations' => $availableOrganisations, 'applications' => $applications, 'reportDateRangeFrom' => $reportDateRangeFrom, 'reportDateRangeTo' => $reportDateRangeTo, 'organisationID' => $organisationID]);
    }

    public function generateDbsSubmissionReport(Request $request, $cronTask = false)
    {
        $requestVars = $request->all();
        //echo'<pre>';print_r($requestVars);echo'</pre>';

        if (isset($requestVars['reportDateRangeFrom'])){
            $reportDateRangeFromRaw = explode('/',$requestVars['reportDateRangeFrom']);
            $reportDateRangeFrom = $reportDateRangeFromRaw[2] . '-' . $reportDateRangeFromRaw[1] . '-' . $reportDateRangeFromRaw[0];
        } else $reportDateRangeFrom = null;

        if (isset($requestVars['reportDateRangeTo'])){
            $reportDateRangeToRaw = explode('/',$requestVars['reportDateRangeTo']);
            $reportDateRangeTo = $reportDateRangeToRaw[2] . '-' . $reportDateRangeToRaw[1] . '-' . $reportDateRangeToRaw[0];
        } else $reportDateRangeTo = null;

        $organisationID = (isset($requestVars['organisationID']) && is_numeric($requestVars['organisationID'])) ? $requestVars['organisationID'] : 0;

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
            if(!empty($organisationID)){
                $applications->where('applications.organisationID', '=', $organisationID);
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


        //echo'<pre>';print_r($applications);echo'</pre>';

        return view('admin.dbsReport', ['availableOrganisations' => $availableOrganisations, 'organisationsReport' => $organisationsReport, 'reportDateRangeFrom' => $reportDateRangeFrom, 'reportDateRangeTo' => $reportDateRangeTo, 'organisationID' => $organisationID]);
    }

    protected $docScanClient;

    public function __construct(DocScanClient $docScanClient)
    {
        $this->docScanClient = $docScanClient;
    }

    
    public function reviewFinalReport($userID) {
        
        $userDetails = \DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->leftJoin('applications', 'users.id', '=', 'applications.userID')
            ->leftJoin('application_extra_names', 'applicationID', '=', 'applications.id')
            ->select('users.*', 'users.createdBy', 'organisations.id AS organisations.organisationID', 'organisations.organisationName', 'applications.*', 'application_extra_names.*')
            ->where(["users.id" => $userID])
            ->first();

        $temp = \DB::table('users')
            ->select('users.createdOn')
            ->where('users.id', $userID)
            ->first();
        $createdOn = $temp->createdOn;
        
        
        $BPSSApplication = \DB::table('bpss_applications')
            ->select('bpss_applications.*')
            ->where(['userID' => $userID])
            ->first();
        
        if ($userDetails->bpssApplication > 0 && isset($BPSSApplication->id)) {

            $existingApplicationID = \DB::table('bpss_vr')
                ->join('users', 'users.id', '=', 'bpss_vr.userID')
                ->select('bpss_vr.id', 'bpss_vr.formStatus')
                ->where(['bpss_vr.userID'=>$userID])
                ->first();

            if (!isset($existingApplicationID->id) || empty($existingApplicationID->id)){
                // Create new bpss_vr entry with only userID
                $newVRID = \DB::table('bpss_vr')->insertGetId([
                    'userID' => $userID,
                    'createdOn' => now(),
                    'organisationID'=> $userDetails->organisationID,
                    'createdBy' => 1111,
                ]);

                // Optional: fetch the newly created record if you want to use it after
                $existingApplicationID = \DB::table('bpss_vr')
                    ->where('id', $newVRID)
                    ->first();
            }

            $dualNationality = \DB::table('bpss_application_extra_nationalities')
            ->where('applicationID', $BPSSApplication->id)
            ->get();

        
            $personalRefs = \DB::table('bpss_application_personal_referee')
                ->where('applicationID', $BPSSApplication->id)
                ->limit(2) // Ensures we only get two results
                ->get();
            
            // Assign results to variables (check if both exist)
            $persRef1 = $personalRefs->get(0) ?? null;
            $persRef2 = $personalRefs->get(1) ?? null;

            $employment = \DB::table('bpss_application_employment_history')
            ->where('applicationID', $BPSSApplication->id)
            ->get();
            
            $unemployment = \DB::table('bpss_application_unemployment')
            ->where('applicationID', $BPSSApplication->id)
            ->get();
            

            if ($existingApplicationID && $BPSSApplication) {
                $applicationID = $existingApplicationID->id;

                // Remove any existing references (clean insert)
                \DB::table('bpss_vr_references')->where('verificationRecordID', $applicationID)->delete();

                if ($persRef1) {
                    \DB::table('bpss_vr_references')->insert([
                        'verificationRecordID' => $applicationID,
                        'referee_name' => $persRef1->referee_name ?? null,
                        'referee_email' => $persRef1->referee_email ?? null,
                        'referee_relationship' => $persRef1->relationship ?? null,
                        'referee_address' => $persRef1->referee_address_line . ', ' . $persRef1->referee_address_town . ', ' . $persRef1->referee_address_postcode,
                        'referee_length_of_association' => $persRef1->date_from . ' to ' . $persRef1->date_to,
                    ]);
                }

                if ($persRef2) {
                    \DB::table('bpss_vr_references')->insert([
                        'verificationRecordID' => $applicationID,
                        'referee_name' => $persRef2->referee_name ?? null,
                        'referee_email' => $persRef2->referee_email ?? null,
                        'referee_relationship' => $persRef2->relationship ?? null,
                        'referee_address' => $persRef2->referee_address_line . ', ' . $persRef2->referee_address_town . ', ' . $persRef2->referee_address_postcode,
                        'referee_length_of_association' => $persRef2->date_from . ' to ' . $persRef2->date_to,
                    ]);
                }

                $referenceRecords = \DB::table('bpss_vr_references_log')->where(['verificationRecordID' => $applicationID])->count();
            } else {
                $referenceRecords = null;
                $applicationID = null;
            }
            
        } else {
            
        }
        
        
        $responsibleEmail = \DB::table('settings')
            ->select('settings.settingValue')
            ->where(['settingName' => 'consent_responsible_body_email'])
            ->first();

        if (!($userDetails->useYoti == 0)) {
            $YotiDate = \DB::table('Yoti')
            ->where('userId', $userID)
            ->value('updated_at') ?? now()->toDateString(); // Default to today's date
        }     
        
        $temp= \DB::table('users')
            ->select('users.*')
            ->where(["users.id" => $userID])
            ->first();
        $siteAdmin = \DB::table('users')
            ->select('id', 'email', 'firstName', 'lastName', 'phoneNumber')
            ->where(["id" => $temp->createdBy])
            ->first();

        $orderedChecks = [];

        // Handle Digital Identity Verification
        if ($userDetails->useYoti == 3) {
            $orderedChecks[] = ['name' => 'Digital Identity Verification', 'status' => 'failed', 'updated_at' => 'N/A'];
        } elseif ($userDetails->useYoti == 2) {
            $orderedChecks[] = ['name' => 'Digital Identity Verification', 'status' => 'complete', 'updated_at' => $YotiDate];
        } elseif ($userDetails->useYoti == 1) {
            $orderedChecks[] = ['name' => 'Digital Identity Verification', 'status' => 'incomplete', 'updated_at' => 'N/A'];
        }

        if ($userDetails->useYoti > 1) {
            $sessionId = \DB::table('Yoti')->select('sessionId')->where('userId', '=', $userID)->first();

            // Fetch session results
            $sessionResult = $this->docScanClient->getSession($sessionId->sessionId);
        }
        

        // DBS
        if ($userDetails->dbsApplication) {
            switch ($userDetails->applicationStatus) {
                case -1:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'not started',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 0:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'incomplete',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 1:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'pending review',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 2:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'review fail',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 3:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'pending submission',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 4:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'submitted to DBS',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 5:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'DBS processing',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 6:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'DBS fail',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 8:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => $userDetails->dbsResponse_int023_DisclosureStatus,
                        'updated_at' => $userDetails->dbsResponse_int023_DisclosureIssueDate
                            ? \Carbon\Carbon::parse($userDetails->dbsResponse_int023_DisclosureIssueDate)->format('d M Y')
                            : 'N/A'
                    ];
                    break;
        
                default:
                    break;
            }
        }

        
        //BPSS
        if ($userDetails->bpssApplication > 0) {
            if (isset($BPSSApplication->applicationStatus) && $BPSSApplication->applicationStatus == 1) {
                $orderedChecks[] = ['name' => 'Academic History (5 years)', 'status' => 'complete', 'updated_at' => $BPSSApplication->completedDate
                            ? \Carbon\Carbon::parse($BPSSApplication->completedDate)->format('d M Y')
                            : 'N/A'];
                $orderedChecks[] = ['name' => 'Employment History (5 years)', 'status' => 'complete', 'updated_at' => $BPSSApplication->completedDate
                            ? \Carbon\Carbon::parse($BPSSApplication->completedDate)->format('d M Y')
                            : 'N/A'];
                $orderedChecks[] = ['name' => 'Personal References (5 years)', 'status' => 'complete', 'updated_at' => $BPSSApplication->completedDate
                            ? \Carbon\Carbon::parse($BPSSApplication->completedDate)->format('d M Y')
                            : 'N/A'];
            } else {
                $orderedChecks[] = ['name' => 'Academic History (5 years)', 'status' => 'Incomplete', 'updated_at' => 'N/A'];
                $orderedChecks[] = ['name' => 'Employment History (5 years)', 'status' => 'Incomplete', 'updated_at' => 'N/A'];
                $orderedChecks[] = ['name' => 'Personal References (5 years)', 'status' => 'Incomplete', 'updated_at' => 'N/A'];
                $userDetails->bpssApplication = 0;
            }

            if (isset($BPSSApplication->id)) {
                $userDetails->birth_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$userDetails->birth_country])->first()->name;
            }
        } 

        $temporary = \DB::table('applications')->select('id')->where('userID', $userID)->first();

        $IDdocs = new \stdClass(); 

        if ($temporary) { 
            $IDdocs->supporting_documents = \DB::table('application_supporting_documents')
                ->select('*')
                ->where('applicationID', $temporary->id)
                ->orderBy('document_path', 'desc')
                ->get();
        } else {
            $IDdocs->supporting_documents = collect();
        }

        $userFiles = \DB::table('new_applicant_documents')->select('*')->where(['userID'=>$userID])->orderBy('id', 'ASC')->get();

        $BPSSVR_identity_documents = \DB::table('bpss_vr_identity_documents')
            ->select('bpss_vr_identity_documents.*')
            ->where(['bpss_vr_identity_documents.verificationRecordID'=>$userID])
            ->get();

        $organisationName = $userDetails->organisationName ?? '';
        $isCollins = stripos($organisationName, 'Collins') === 0 || stripos($organisationName, 'Safran') === 0;
        if ($isCollins) {
            $collinsApproval = \DB::table('collins_approvals')->select('*')->where('userID', $userID)->first();
        }
        else {
            $collinsApproval = NULL;
        }

        // Security Matrix lookup
        $securityMatrixRecord = \DB::table('new_applicant_documents')
            ->select('document_path')
            ->where(['userID' => $userID, 'file_type' => 'secmx'])
            ->orderBy('id', 'desc')
            ->first();

        $securityMatrixPath = '';
        if ($securityMatrixRecord && !empty($securityMatrixRecord->document_path)) {
            $orderedChecks[] = [
                'name' => 'Security Clearance Matrix',
                'status' => 'complete',
                'updated_at' => $userDetails->completedDate
                    ? \Carbon\Carbon::parse($userDetails->completedDate)->format('d M Y')
                    : 'N/A'];
        }

        // MK Denial lookup
        $mkDenialRecord = \DB::table('new_applicant_documents')
            ->select('document_path')
            ->where(['userID' => $userID, 'file_type' => 'mkden'])
            ->orderBy('id', 'desc')
            ->first();

        $mkDenialPath = '';
        if ($mkDenialRecord && !empty($mkDenialRecord->document_path)) {
            $orderedChecks[] = [
                'name' => 'MK Screening report',
                'status' => 'complete',
                'updated_at' => $userDetails->completedDate
                    ? \Carbon\Carbon::parse($userDetails->completedDate)->format('d M Y')
                    : 'N/A'];
        }

        if ($userDetails->bpssApplication > 0) {
            return view('reports.finalReportReview', ['userID' => $userID, 'userDetails' => $userDetails, 'orderedChecks' => $orderedChecks, 'siteAdmin' => $siteAdmin, 'BPSSApplication' => $BPSSApplication, 'dualNationality' => $dualNationality, 'createdOn' => $createdOn, 'unemployment' => $unemployment,'employment' => $employment, 'persRef1' => $persRef1, 'persRef2' => $persRef2, 'IDdocs' => $IDdocs, 'userFiles' => $userFiles, 'collinsApproval' => $collinsApproval, 'referenceRecords' => $referenceRecords, 'applicationID' => $applicationID, 'BPSSVR_identity_documents' => $BPSSVR_identity_documents]);
        } else {
            return view('reports.finalReportReview', ['userID' => $userID, 'userDetails' => $userDetails, 'orderedChecks' => $orderedChecks, 'siteAdmin' => $siteAdmin, 'BPSSApplication' => $BPSSApplication, 'createdOn' => $createdOn, 'IDdocs' => $IDdocs, 'userFiles' => $userFiles, 'BPSSVR_identity_documents' => $BPSSVR_identity_documents]);
        }
    }
    


    public function generateFinalReport($id) {

    
        $userDetails = \DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->leftJoin('applications', 'users.id', '=', 'applications.userID')
            ->leftJoin('application_extra_names', 'applicationID', '=', 'applications.id')
            ->select('users.*', 'users.createdBy', 'organisations.id AS organisations.organisationID', 'organisations.organisationName','organisations.permanent_employee_renewal_years', 'organisations.contractor_renewal_years', 'applications.*', 'application_extra_names.*', 'organisations.totalCompleted', 'organisations.logo')
            ->where(["users.id" => $id])
            ->first();

        if ($userDetails->completed != 1) {
            \DB::table('users')
                ->where('id', $id)
                ->update([
                    'completed' => 1,
                    'completedDate' => now()->toDateString(),
                    'completedBy' => auth()->id()
                ]);
            
            $completedAdmin = \DB::table('users')
                ->leftJoin('user_signatures', 'user_signatures.userid', '=', 'users.id')
                ->select('users.*', 'user_signatures.signature_path')
                ->where('users.id', auth()->id())
                ->first();
            
            $newtotal = $userDetails->totalCompleted += 1;
            \DB::table('organisations')
                ->where('id', $userDetails->organisationID)
                ->update([
                    'totalCompleted' => $newtotal
                ]);
            
            
            $userDetails = \DB::table('users')
                ->join('organisations', 'organisations.id', '=', 'users.organisationID')
                ->leftJoin('applications', 'users.id', '=', 'applications.userID')
                ->leftJoin('application_extra_names', 'applicationID', '=', 'applications.id')
                ->select('users.*', 'users.createdBy', 'organisations.id AS organisations.organisationID', 'organisations.organisationName','organisations.permanent_employee_renewal_years', 'organisations.contractor_renewal_years', 'applications.*', 'application_extra_names.*')
                ->where(["users.id" => $id])
                ->first();

            //Email candidate and siteadmin
            $candidateEmail = $userDetails->email;
            $adminEmail = $completedAdmin->email;
            $candidate = $userDetails->firstName . ' ' . $userDetails->lastName;

            $mailDriver = env('MAIL_DRIVER');
            if (isset($mailDriver) && strlen($mailDriver) > 0) {

                // Send to Candidate — no variables needed
                Mail::send([], [], function ($message) use ($candidateEmail) {
                    $message->to($candidateEmail)
                        ->subject('Your Application is Complete')
                        ->setBody(view('email_templates.completion_email')->render(), 'text/html');
                });

                // Send to Site Admin — pass $candidate name
                Mail::send([], [], function ($message) use ($adminEmail, $candidate) {
                    $message->to($adminEmail)
                        ->subject($candidate . ' Completed Their Application')
                        ->setBody(view('email_templates.admin_completion_email', [
                            'candidate' => $candidate
                        ])->render(), 'text/html');
                });

            }
        } else {
            $completedAdmin = \DB::table('users')
                ->leftJoin('user_signatures', 'user_signatures.userid', '=', 'users.id')
                ->select('users.*', 'user_signatures.signature_path')
                ->where('users.id', $userDetails->completedBy)
                ->first();
        }

        
        
        $temp = \DB::table('users')
            ->select('users.createdOn')
            ->where('users.id', $id)
            ->first();
        $createdOn = $temp->createdOn;
        
        
        $BPSSApplication = \DB::table('bpss_applications')
            ->select('bpss_applications.*')
            ->where(['userID' => $id])
            ->first();
        
        if ($userDetails->bpssApplication > 0 && isset($BPSSApplication)) {
            $dualNationality = \DB::table('bpss_application_extra_nationalities')
            ->where('applicationID', $BPSSApplication->id)
            ->get();

        
            $personalRefs = \DB::table('bpss_application_personal_referee')
                ->where('applicationID', $BPSSApplication->id)
                ->limit(2) // Ensures we only get two results
                ->get();
            
            // Assign results to variables (check if both exist)
            $persRef1 = $personalRefs->get(0) ?? null;
            $persRef2 = $personalRefs->get(1) ?? null;

            $employment = \DB::table('bpss_application_employment_history')
            ->where('applicationID', $BPSSApplication->id)
            ->get();
            
            $unemployment = \DB::table('bpss_application_unemployment')
            ->where('applicationID', $BPSSApplication->id)
            ->get();
        } else {
            $dualNationality = NULL;
        }
        
        $responsibleEmail = \DB::table('settings')
            ->select('settings.settingValue')
            ->where(['settingName' => 'consent_responsible_body_email'])
            ->first();

        if (!($userDetails->useYoti == 0)) {
            $YotiDate = \DB::table('Yoti')
            ->where('userId', $id)
            ->value('updated_at') ?? now()->toDateString(); // Default to today's date
        }     
        
        $temp= \DB::table('users')
            ->select('users.*')
            ->where(["users.id" => $id])
            ->first();
        $siteAdmin = \DB::table('users')
            ->select('id', 'email', 'firstName', 'lastName', 'phoneNumber')
            ->where(["id" => $temp->createdBy])
            ->first();

        $orderedChecks = [];

        // Handle Digital Identity Verification
        if ($userDetails->useYoti == 3) {
            $orderedChecks[] = ['name' => 'Digital Identity Verification', 'status' => 'failed', 'updated_at' => 'N/A'];
        } elseif ($userDetails->useYoti == 2) {
            $orderedChecks[] = ['name' => 'Digital Identity Verification', 'status' => 'complete', 'updated_at' => $YotiDate];
        } elseif ($userDetails->useYoti == 1) {
            $orderedChecks[] = ['name' => 'Digital Identity Verification', 'status' => 'incomplete', 'updated_at' => 'N/A'];
        }

        $yotiReportData = [
            'hasSession' => false,
            'state' => 'N/A',
            'sessionId' => null,
            'createdAt' => null,
            'completedAt' => null,
            'documentsData' => [],
            'watchlistChecks' => [],
            'identitySummary' => [],
        ];

        if ((int)$userDetails->useYoti > 0) {
            $yotiReportData = $this->buildYotiDataForReporting((int)$id);
        }
        
        //BPSS
        if ($userDetails->bpssApplication > 0) {
            if (isset($BPSSApplication->applicationStatus) && $BPSSApplication->applicationStatus == 1) {
                $orderedChecks[] = ['name' => 'Personal References (5 years)', 'status' => 'complete', 'updated_at' => $BPSSApplication->completedDate
                            ? \Carbon\Carbon::parse($BPSSApplication->completedDate)->format('d M Y')
                            : 'N/A'];
                $orderedChecks[] = ['name' => 'Employment History (5 years)', 'status' => 'complete', 'updated_at' => $BPSSApplication->completedDate
                            ? \Carbon\Carbon::parse($BPSSApplication->completedDate)->format('d M Y')
                            : 'N/A'];
                $orderedChecks[] = ['name' => 'Academic History (5 years)', 'status' => 'complete', 'updated_at' => $BPSSApplication->completedDate
                            ? \Carbon\Carbon::parse($BPSSApplication->completedDate)->format('d M Y')
                            : 'N/A'];
            } else {
                $orderedChecks[] = ['name' => 'Personal References (5 years)', 'status' => 'Incomplete', 'updated_at' => 'N/A'];
                $orderedChecks[] = ['name' => 'Employment History (5 years)', 'status' => 'Incomplete', 'updated_at' => 'N/A'];
                $orderedChecks[] = ['name' => 'Academic History (5 years)', 'status' => 'Incomplete', 'updated_at' => 'N/A'];
            }

            $userDetails->birth_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$userDetails->birth_country])->first()->name;
        } 

        // DBS
        if ($userDetails->dbsApplication) {
            switch ($userDetails->applicationStatus) {
                case -1:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'not started',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 0:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'incomplete',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 1:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'pending review',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 2:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'review fail',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 3:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'pending submission',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 4:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'submitted to DBS',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 5:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'DBS processing',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 6:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => 'DBS fail',
                        'updated_at' => 'N/A'
                    ];
                    break;
        
                case 8:
                    $orderedChecks[] = [
                        'name' => 'UK Criminal Record (Basic DBS, England & Wales)',
                        'status' => $userDetails->dbsResponse_int023_DisclosureStatus,
                        'updated_at' => $userDetails->dbsResponse_int023_DisclosureIssueDate
                            ? \Carbon\Carbon::parse($userDetails->dbsResponse_int023_DisclosureIssueDate)->format('d M Y')
                            : 'N/A'
                    ];
                    break;
        
                default:
                    break;
            }
        }
        
        if ($userDetails->admin_right_to_work == 1) {
            $orderedChecks[] = ['name' => 'Right to Work (UK)', 'status' => 'complete', 'updated_at' => $userDetails->completedDate
            ? \Carbon\Carbon::parse($userDetails->completedDate)->format('d M Y')
            : 'N/A'];
        }

        $BPSSVR_identity_documents = \DB::table('bpss_vr_identity_documents')
            ->select('bpss_vr_identity_documents.*')
            ->where(['bpss_vr_identity_documents.verificationRecordID'=>$id])
            ->get();


        // Security Matrix lookup
        $securityMatrixRecord = \DB::table('new_applicant_documents')
            ->select('document_path')
            ->where(['userID' => $id, 'file_type' => 'secmx'])
            ->orderBy('id', 'desc')
            ->first();

        $securityMatrixPath = '';
        if ($securityMatrixRecord && !empty($securityMatrixRecord->document_path)) {
            $securityMatrixPath = env('APP_DOCUMENT_ROOT') . '/public/uploads/new_applicant_documents/' . $securityMatrixRecord->document_path;
            $orderedChecks[] = [
                'name' => 'Security Clearance Matrix',
                'status' => 'complete',
                'updated_at' => $userDetails->completedDate
                    ? \Carbon\Carbon::parse($userDetails->completedDate)->format('d M Y')
                    : 'N/A'];
        }

        // MK Denial lookup
        $mkDenialRecord = \DB::table('new_applicant_documents')
            ->select('document_path')
            ->where(['userID' => $id, 'file_type' => 'mkden'])
            ->orderBy('id', 'desc')
            ->first();

        $mkDenialPath = '';
        if ($mkDenialRecord && !empty($mkDenialRecord->document_path)) {
            $mkDenialPath = env('APP_DOCUMENT_ROOT') . '/public/uploads/new_applicant_documents/' . $mkDenialRecord->document_path;
            $orderedChecks[] = [
                'name' => 'MK Screening report',
                'status' => 'complete',
                'updated_at' => $userDetails->completedDate
                    ? \Carbon\Carbon::parse($userDetails->completedDate)->format('d M Y')
                    : 'N/A'];
        }


        \DB::table('adminLogs')->insert([
            'userID' => \Auth::id(),
            'actions' => 'Openned candidate report: '.$id,
            'occurance_at' => now(),
        ]);
        

        set_time_limit(6000);
	    ini_set('memory_limit','-1');
        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(13,13,13,13));
        //$html2pdf->setModeDebug();
        $html2pdf->setTestIsImage(false);
        $html2pdf->pdf->SetAuthor('Get ClassifIeD');
        $html2pdf->pdf->SetTitle('Final-Report_'.$userDetails->forename.'_'.$userDetails->presentSurname.'.pdf');
        $html2pdf->pdf->SetSubject('ClassifIeD Final screening Report');

        $pageNumber = 1;

        // Check if organisation name starts with "Collins" (case-insensitive)
        $organisationName = $userDetails->organisationName ?? '';
        $isCollins = stripos($organisationName, 'Collins') === 0 || stripos($organisationName, 'Safran') === 0;
 
        if ($isCollins) {
            $collinsApproval = \DB::table('collins_approvals')->select('*')->where('userId', '=', $id)->first();

            $html2pdf->writeHTML(view('finalReport_templates.finalReport-cover', ['userDetails' => $userDetails, 'BPSSApplication' => $BPSSApplication, 'pageNumber' => $pageNumber, 'isCollins' => $isCollins, 'collinsApproval' => $collinsApproval]));
        } else {
            $html2pdf->writeHTML(view('finalReport_templates.finalReport-cover', ['userDetails' => $userDetails, 'BPSSApplication' => $BPSSApplication, 'isCollins' => $isCollins, 'pageNumber' => $pageNumber]));
        }
        $pageNumber += 1;

        $html2pdf->writeHTML(view('finalReport_templates.finalReport-checks', ['userDetails' => $userDetails, 'orderedChecks' => $orderedChecks, 'pageNumber' => $pageNumber]));
        $pageNumber += 1;

        $html2pdf->writeHTML(view('finalReport_templates.finalReport-details', ['userDetails' => $userDetails, 'siteAdmin' => $siteAdmin, 'BPSSApplication' => $BPSSApplication, 'dualNationality' => $dualNationality, 'createdOn' => $createdOn, 'pageNumber' => $pageNumber, 'BPSSVR_identity_documents' => $BPSSVR_identity_documents]));
        $pageNumber += 1;

        if ($userDetails->useYoti > 0) {
            $html2pdf->writeHTML(view('finalReport_templates.finalReport-yoti', [
                'userDetails' => $userDetails,
                'orderedChecks' => $orderedChecks,
                'yotiReportData' => $yotiReportData, // new rich data
                'pageNumber' => $pageNumber
            ]));
            $pageNumber += 1;
        }

        if ($userDetails->bpssApplication > 0) {
            if (isset($persRef1) && isset($persRef2)) {
                $html2pdf->writeHTML(view('finalReport_templates.finalReport-personal', ['userDetails' => $userDetails, 'BPSSApplication' => $BPSSApplication, 'persRef1'=> $persRef1, 'persRef2'=> $persRef2, 'pageNumber' => $pageNumber]));
                $pageNumber += 1;
            }

            if (isset($unemployment) && isset($employment)) {
                $html2pdf->writeHTML(view('finalReport_templates.finalReport-employment', ['unemployment' => $unemployment,'employment' => $employment,'BPSSApplication' => $BPSSApplication, 'pageNumber' => $pageNumber]));
                $pageNumber += 1;
            }

            $html2pdf->writeHTML(view('finalReport_templates.finalReport-academic', ['BPSSApplication' => $BPSSApplication, 'pageNumber' => $pageNumber]));
            $pageNumber += 1;
        }

        $html2pdf->writeHTML(view('finalReport_templates.finalReport-dbs', ['userDetails' => $userDetails, 'pageNumber' => $pageNumber, 'BPSSApplication' => $BPSSApplication]));
        $pageNumber += 1;

        if ($userDetails->bpssApplication > 0) {
            $html2pdf->writeHTML(view('finalReport_templates.finalReport-crimDec', ['BPSSApplication' => $BPSSApplication, 'pageNumber' => $pageNumber]));
            $pageNumber += 1;
        }
        

        $html2pdf->writeHTML(view('finalReport_templates.finalReport-decleration', ['userDetails' => $userDetails, 'pageNumber' => $pageNumber, 'responsibleEmail' => $responsibleEmail, 'isCollins' => $isCollins, 'completedAdmin' => $completedAdmin]));
        $pageNumber += 1;
	
        //$html2pdf->output('ClassifIeD_'.$userDetails->forename.'_'.$userDetails->presentSurname.'.pdf');

        // Save the main report
        $finalReportPath = env('APP_DOCUMENT_ROOT') . '/pdf/ClassifIeD_' . $id . '_main.pdf';
        $html2pdf->output($finalReportPath, 'F');

        // Directory to store the images temporarily
        $tempDir = env('APP_DOCUMENT_ROOT') . '/pdf/temp/';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Convert PDFs to Images
        $securityMatrixImages = [];
        $mkDenialImages = [];

        if (!empty($securityMatrixPath) && file_exists($securityMatrixPath)) {
            $securityMatrixImages = $this->convertPdfToImages($securityMatrixPath, $tempDir . 'secmx_images');
        }

        if (!empty($mkDenialPath) && file_exists($mkDenialPath)) {
            $mkDenialImages = $this->convertPdfToImages($mkDenialPath, $tempDir . 'mkden_images');
        }

        // Initialize TCPDI
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Load the main report
        $pageCount = $pdf->setSourceFile($finalReportPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplIdx = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($tplIdx);
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($tplIdx);
        }

        // Add the images to the final PDF
        if (!empty($securityMatrixPath) && file_exists($securityMatrixPath)) {
            $this->addImagesToPdf($pdf, $securityMatrixImages);
        }
        if (!empty($mkDenialPath) && file_exists($mkDenialPath)) {
            $this->addImagesToPdf($pdf, $mkDenialImages);
        }

        // Set PDF metadata (title, author, subject)
        $pdf->SetTitle('Final-Report_' . $userDetails->forename . '_' . $userDetails->presentSurname . '.pdf');
        $pdf->SetAuthor('Get ClassifIeD');
        $pdf->SetSubject('ClassifIeD Final screening Report');

        // Save the final report
        $finalMergedPath = env('APP_DOCUMENT_ROOT') . '/pdf/ClassifIeD_' . $userDetails->forename . '_' . $userDetails->presentSurname . '_final.pdf';
        $pdf->Output($finalMergedPath, 'F');

        // Cleanup temporary image files
        $this->cleanupTempFiles(array_merge($securityMatrixImages, $mkDenialImages));

        // Serve the final file to the browser
        return response()->file($finalMergedPath, [
            'Content-Disposition' => 'inline; filename="Final-Report_' . $userDetails->forename . '_' . $userDetails->presentSurname . '.pdf"'
        ]);
    }

    public function addImagesToPdf($pdf, $imagePaths)
    {
        foreach ($imagePaths as $imagePath) {
            if (!file_exists($imagePath) || filesize($imagePath) < 1024) continue;

            [$imgWidthPx, $imgHeightPx] = getimagesize($imagePath);
            if ($imgWidthPx < 50 || $imgHeightPx < 50) continue;

            // Try reading actual DPI from image
            try {
                $im = new \Imagick($imagePath);
                $dpi = $im->getImageResolution();
                $xDpi = $dpi['x'] ?? 96;
                $yDpi = $dpi['y'] ?? 96;
                $im->destroy();
            } catch (\Throwable $e) {
                $xDpi = $yDpi = 96;
            }

            // Convert pixels to mm using DPI
            $imgWidthMM = $imgWidthPx * 25.4 / $xDpi;
            $imgHeightMM = $imgHeightPx * 25.4 / $yDpi;

            // Always A4 size (portrait or landscape)
            $orientation = ($imgWidthPx > $imgHeightPx) ? 'L' : 'P';
            $pdf->AddPage($orientation);
            $pageWidth = $pdf->getPageWidth();
            $pageHeight = $pdf->getPageHeight();

            // Scale image to fit full width
            $scale = min(1, $pageWidth / $imgWidthMM);
            $finalWidth = $imgWidthMM * $scale;
            $finalHeight = $imgHeightMM * $scale;

            // Center horizontally, top-align vertically
            $x = ($pageWidth - $finalWidth) / 2;
            $y = 0;

            try {
                $pdf->Image($imagePath, $x, $y, $finalWidth, $finalHeight, '', '', '', false, 300, '', false, false, 0, false);
            } catch (\Throwable $e) {
                \Log::warning("Skipped image {$imagePath}: {$e->getMessage()}");
                $pdf->deletePage($pdf->getPage());
            }
        }
    }



    public function convertPdfToImages($pdfPath, $outputDir)
    {
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $imagick = new \Imagick();
        $imagick->setResolution(150, 150);
        $imagick->readImage($pdfPath);

        $images = [];
        $index = 0;

        foreach ($imagick as $page) {
            $page->setImageFormat('png');

            // Check for nearly blank page
            $check = clone $page;
            $check->setImageColorspace(\Imagick::COLORSPACE_GRAY);
            $check->resizeImage(10, 10, \Imagick::FILTER_BOX, 1);
            $pixels = $check->exportImagePixels(0, 0, 10, 10, 'I', \Imagick::PIXEL_CHAR);
            $avg = array_sum($pixels) / count($pixels) / 255;
            $check->destroy();

            if ($avg > 0.98) {
                $index++;
                continue;
            }

            $imagePath = $outputDir . '/page_' . $index . '.png';
            $page->writeImage($imagePath);
            $images[] = $imagePath;
            $index++;
        }

        $imagick->clear();
        $imagick->destroy();

        return $images;
    }

    public function cleanupTempFiles($filePaths)
    {
        foreach ($filePaths as $filePath) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }

    private function buildYotiDataForReporting(int $userId): array
    {
        $sessionRow = \DB::table('Yoti')
            ->select('sessionId', 'updated_at', 'completed_at')
            ->where('userId', $userId)
            ->first();

        if (!$sessionRow || empty($sessionRow->sessionId)) {
            return [
                'hasSession' => false,
                'state' => 'N/A',
                'sessionId' => null,
                'createdAt' => null,
                'completedAt' => null,
                'documentsData' => [],
                'watchlistChecks' => [],
                'identitySummary' => [
                    'frameworkType' => 'N/A',
                    'schemeType' => 'N/A',
                    'schemeObjective' => 'N/A',
                    'requirementsMet' => null,
                    'levelOfAssurance' => 'N/A',
                ],
            ];
        }

        $sessionId = $sessionRow->sessionId;

        try {
            $sessionResult = $this->docScanClient->getSession($sessionId);
        } catch (\Throwable $e) {
            \Log::warning('Yoti getSession failed for report', [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'hasSession' => false,
                'state' => 'ERROR',
                'sessionId' => $sessionId,
                'createdAt' => $sessionRow->updated_at,
                'completedAt' => $sessionRow->completed_at,
                'documentsData' => [],
                'watchlistChecks' => [],
                'identitySummary' => [
                    'frameworkType' => 'N/A',
                    'schemeType' => 'N/A',
                    'schemeObjective' => 'N/A',
                    'requirementsMet' => null,
                    'levelOfAssurance' => 'N/A',
                ],
            ];
        }

        $state = $sessionResult->getState();
        $resources = $sessionResult->getResources();
        $checks = $sessionResult->getChecks() ?: [];
        $identityProfile = $sessionResult->getIdentityProfile();

        // ---- media helper: mediaId => data URI ----
        $mediaCache = [];
        $getDataUri = function ($mediaId) use ($sessionId, &$mediaCache) {
            if (!$mediaId) return null;
            if (array_key_exists($mediaId, $mediaCache)) return $mediaCache[$mediaId];

            try {
                $media = $this->docScanClient->getMediaContent($sessionId, $mediaId);
                if (!$media) return $mediaCache[$mediaId] = null;

                $mime = method_exists($media, 'getMimeType') ? ($media->getMimeType() ?: 'image/jpeg') : 'image/jpeg';

                if (method_exists($media, 'getBase64Content')) {
                    $b64 = $media->getBase64Content();
                    if ($b64) {
                        return $mediaCache[$mediaId] = (str_starts_with($b64, 'data:') ? $b64 : "data:{$mime};base64,{$b64}");
                    }
                }

                if (method_exists($media, 'getContent')) {
                    $raw = $media->getContent();
                    if ($raw) {
                        if (is_string($raw) && str_starts_with($raw, 'data:')) {
                            return $mediaCache[$mediaId] = $raw;
                        }
                        return $mediaCache[$mediaId] = "data:{$mime};base64," . base64_encode($raw);
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('Yoti media fetch failed', [
                    'session_id' => $sessionId,
                    'media_id' => $mediaId,
                    'error' => $e->getMessage(),
                ]);
            }

            return $mediaCache[$mediaId] = null;
        };

        $getCheckResourceIds = function ($check): array {
            $ids = [];

            if (method_exists($check, 'getResourcesUsed')) {
                try {
                    $ru = $check->getResourcesUsed();
                    if (is_array($ru)) {
                        foreach ($ru as $item) {
                            if (is_string($item) && $item !== '') {
                                $ids[] = $item;
                            } elseif (is_object($item) && method_exists($item, 'getId') && $item->getId()) {
                                $ids[] = $item->getId();
                            } elseif (is_array($item) && !empty($item['id'])) {
                                $ids[] = $item['id'];
                            }
                        }
                    }
                } catch (\Throwable $e) {}
            }

            if (empty($ids)) {
                try {
                    $arr = json_decode(json_encode($check), true);
                    if (!empty($arr['resources_used']) && is_array($arr['resources_used'])) {
                        foreach ($arr['resources_used'] as $rid) {
                            if (is_string($rid) && $rid !== '') $ids[] = $rid;
                        }
                    }
                } catch (\Throwable $e) {}
            }

            return array_values(array_unique($ids));
        };

        // classify checks
        $watchlistChecks = [];
        $documentCandidateChecks = [];

        foreach ($checks as $check) {
            $type = strtolower((string)$check->getType());

            if (str_contains($type, 'watchlist')) {
                $watchlistChecks[] = $check;
                continue;
            }
            if (str_contains($type, 'profile') || str_contains($type, 'applicant')) {
                continue;
            }

            $documentCandidateChecks[] = $check;
        }

        // build documents model
        $documentsData = [];
        $documentsById = [];
        $documentOrder = [];

        $idDocuments = $resources ? ($resources->getIdDocuments() ?: []) : [];
        foreach ($idDocuments as $docIndex => $document) {
            $docId = method_exists($document, 'getId') ? $document->getId() : null;
            if (!$docId && method_exists($document, 'getDocumentId')) {
                $docId = $document->getDocumentId();
            }
            if (!$docId) $docId = 'doc_' . $docIndex . '_' . spl_object_id($document);

            $documentOrder[] = $docId;
            $images = [];

            // Only Document Page 1
            if (method_exists($document, 'getPages')) {
                $pages = $document->getPages() ?: [];
                $firstPage = $pages[0] ?? null;

                if ($firstPage && method_exists($firstPage, 'getMedia') && $firstPage->getMedia()) {
                    $uri = $getDataUri($firstPage->getMedia()->getId());
                    if ($uri) {
                        $images[] = [
                            'label' => 'Document Page 1',
                            'src' => $uri
                        ];
                    }
                }
            }

            // Extracted fields
            $fieldsData = [];
            $docFields = method_exists($document, 'getDocumentFields') ? $document->getDocumentFields() : null;
            if ($docFields && method_exists($docFields, 'getMedia') && $docFields->getMedia()) {
                try {
                    $fieldsMedia = $this->docScanClient->getMediaContent($sessionId, $docFields->getMedia()->getId());
                    if ($fieldsMedia && method_exists($fieldsMedia, 'getContent')) {
                        $decoded = json_decode($fieldsMedia->getContent(), true);
                        $fieldsData = is_array($decoded) ? $decoded : [];
                    }
                } catch (\Throwable $e) {}
            }

            $documentsById[$docId] = [
                'documentId' => $docId,
                'type' => method_exists($document, 'getDocumentType') ? $document->getDocumentType() : 'ID Document',
                'issuingCountry' => method_exists($document, 'getIssuingCountry') ? $document->getIssuingCountry() : 'N/A',
                'images' => $images,
                'fieldsData' => $fieldsData,
                'relevantChecks' => [],
            ];
        }

        // map checks to docs
        $validDocumentIds = array_fill_keys(array_keys($documentsById), true);
        foreach ($documentCandidateChecks as $check) {
            $resourceIds = $getCheckResourceIds($check);
            foreach ($resourceIds as $rid) {
                if (isset($validDocumentIds[$rid])) {
                    $documentsById[$rid]['relevantChecks'][] = $check;
                }
            }
        }

        foreach ($documentOrder as $docId) {
            if (isset($documentsById[$docId])) {
                $documentsData[] = $documentsById[$docId];
            }
        }

        // identity summary
        $identitySummary = [
            'frameworkType' => 'N/A',
            'schemeType' => 'N/A',
            'schemeObjective' => 'N/A',
            'requirementsMet' => null,
            'levelOfAssurance' => 'N/A',
        ];

        try {
            if ($identityProfile && method_exists($identityProfile, 'getIdentityProfileReport')) {
                $ipReport = $identityProfile->getIdentityProfileReport();
                $arr = json_decode(json_encode($ipReport), true);

                // --- IMPORTANT: mirror showYotiReport() ---
                // identity profile often contains only a media reference, so fetch real JSON payload
                $mediaId = null;
                if (is_array($arr)) {
                    if (!empty($arr['media']['id'])) {
                        $mediaId = $arr['media']['id'];
                    } elseif (!empty($arr['identity_profile_report']['media']['id'])) {
                        $mediaId = $arr['identity_profile_report']['media']['id'];
                    }
                }

                if ($mediaId) {
                    try {
                        $identityMedia = $this->docScanClient->getMediaContent($sessionId, $mediaId);
                        if ($identityMedia && method_exists($identityMedia, 'getContent')) {
                            $decoded = json_decode($identityMedia->getContent(), true);
                            if (is_array($decoded)) {
                                $arr = $decoded;
                            }
                        }
                    } catch (\Throwable $e) {
                        \Log::warning('Yoti identity profile media fetch failed (report)', [
                            'session_id' => $sessionId,
                            'media_id'   => $mediaId,
                            'error'      => $e->getMessage(),
                        ]);
                    }
                }

                if (is_array($arr)) {
                    $root = $arr;
                    if (isset($arr['identity_profile_report']) && is_array($arr['identity_profile_report'])) {
                        $root = $arr['identity_profile_report'];
                    }

                    $vr = (isset($root['verification_report']) && is_array($root['verification_report']))
                        ? $root['verification_report']
                        : $root;

                    $identitySummary['frameworkType'] =
                        $vr['trust_framework']
                        ?? ($root['trust_framework'] ?? 'N/A');

                    if (!empty($vr['schemes_compliance'][0])) {
                        $scheme = $vr['schemes_compliance'][0];
                        $identitySummary['schemeType'] = $scheme['scheme']['type'] ?? 'N/A';
                        $identitySummary['schemeObjective'] = $scheme['scheme']['objective'] ?? 'N/A';
                        $identitySummary['requirementsMet'] = $scheme['requirements_met'] ?? null;
                    }

                    // --- IMPORTANT: multiple fallback key paths for LOA ---
                    $loa =
                        $vr['assurance_process']['level_of_assurance']
                        ?? $vr['assuranceProcess']['levelOfAssurance']
                        ?? $vr['level_of_assurance']
                        ?? $root['assurance_process']['level_of_assurance']
                        ?? null;

                    if (is_string($loa) && trim($loa) !== '') {
                        $identitySummary['levelOfAssurance'] = strtoupper(trim($loa));
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Yoti identity summary parse failed', [
                'session_id' => $sessionId ?? null,
                'error'      => $e->getMessage(),
            ]);
        }

        return [
            'hasSession' => true,
            'state' => $state,
            'sessionId' => $sessionId,
            'createdAt' => $sessionRow->updated_at,
            'completedAt' => $sessionRow->completed_at,
            'documentsData' => $documentsData,
            'watchlistChecks' => $watchlistChecks,
            'identitySummary' => $identitySummary,
        ];
    }
}