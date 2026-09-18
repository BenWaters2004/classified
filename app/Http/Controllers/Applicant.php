<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spipu\Html2Pdf\Html2Pdf;
use Illuminate\Support\Facades\Mail;
use Yoti\DocScan\DocScanClient;
use Yoti\DocScan\Session\Create\SdkConfigBuilder;
use Yoti\DocScan\Session\Create\SessionSpecificationBuilder;
use Yoti\DocScan\Session\Create\Resources\ResourceCreationContainer;
use Illuminate\Support\Facades\Storage;

class Applicant extends Controller
{


	/*
    |--------------------------------------------------------------------------
    | Applicant Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling the Applicant logic
    |
    */

    /**
     * index fallback
     *
     * 
     */

    public function index()
    {
        return redirect("/applicant");
        
    }

    /**
     * Default load
     *
     * 
     */

    public function dashboard()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        
        $DBSApplication = \DB::table('applications')
//            ->join('user_titles', 'user_titles.id', '=', 'applications.title')
//            ->select('applications.*', 'user_titles.userTitle', 'users.dbsApplication', 'users.bpssApplication')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*', 'users.dbsApplication', 'users.bpssApplication')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
//echo'<pre>';print_r($DBSApplication);echo'</pre>';
        if (isset($DBSApplication) && !empty($DBSApplication->userID)){
            $BPSSApplication = \DB::table('bpss_applications')
                ->join('users', 'users.id', '=', 'bpss_applications.userID')
                ->select('bpss_applications.*')
                ->where(['bpss_applications.userID'=>$DBSApplication->userID])
                ->first();
        } else $BPSSApplication = new \stdClass();
//echo'<pre>';print_r($DBSApplication);echo'</pre>';

        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($DBSApplication->organisationID, $userOrganisations))return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant' && isset($DBSApplication->userID) && $DBSApplication->userID != Auth::user()->id) return redirect("/login")->with('errorMessage', 'The application link is invalid!');

        return view('applicant.newDashboard', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]);

        return $this->newDashboard();
    }

    public function dbsReset()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;

        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->join('user_titles', 'user_titles.id', '=', 'applications.title')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
        if (isset($DBSApplication) && !empty($DBSApplication->userID)){
            $updateApplicationDetails = \DB::table('applications')->where(['id' => $DBSApplication->id])->update(['applicationStatus' => 0]);
            return $this->dbsStep1();;
        } else {
            return redirect("/login");
        }
    }

    public function faq()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;

       return view('applicant.faq');
    }

    public function editProfile()
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;

        $userDetails = \DB::table('users')
                ->join('organisations', 'organisations.id', '=', 'users.organisationID')
                ->select('users.*', 'organisations.id AS organisations.organisationID', 'organisations.organisationName')
                ->where(["users.id" => $loggedUserID])
                ->first();

        $DBSApplication = \DB::table('applications')->select('applications.id', 'applications.title', 'applications.forename', 'applications.presentSurname')->where(['applications.userID'=>$userDetails->id])->first();
            if(isset($DBSApplication->id) && !empty($DBSApplication->id)){
                $userDetails->DBSApplicationID = $DBSApplication->id;
                $userDetails->applicationForename = $DBSApplication->forename;
                $userDetails->applicationPresentSurname = $DBSApplication->presentSurname;
                $userDetails->applicationTitle = \DB::table('user_titles')->select('userTitle')->where(['id'=>$DBSApplication->title])->first()->userTitle;
            } else {
                $userDetails->DBSApplicationID = 0;
                $userDetails->forename = '';
                $userDetails->presentSurname = '';
                $userDetails->userApplicationTitle = '';
            }


        return view('applicant.editProfile', ['userDetails' => $userDetails]);

        
    }

    public function updateUserDetails(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        $requestVars = $request->all();
        $validateArray = [
            'userTitle'             => 'max:50',
            'firstName'             => 'required|max:50',
            'lastName'              => 'required|max:50',
            'userEmail'             => 'required|email|max:255',
            'userEmail'             => 'unique:users,email,'.$requestVars['userID'],
            'password'              => 'confirmed',
        ];

        $messsages = array(
            'userEmail.unique'=>'email_exists',
        );
        $this->validate($request, $validateArray, $messsages); 

        $userDetails = array(
            'email'      => $requestVars['userEmail'],
            'title'      => $requestVars['userTitle'],
            'firstName'  => $requestVars['firstName'],
            'lastName'   => $requestVars['lastName'],
            'position'   => (isset($requestVars['position'])) ? $requestVars['position'] : '',
            'phoneNumber'=> (isset($requestVars['phoneNumber'])) ? $requestVars['phoneNumber'] : '',
        );


        if (!empty($requestVars['password']) && strlen($requestVars['password']) > 0){
            $userDetails['password'] = bcrypt($requestVars['password']);
        }

          
        $updateUserDetails = \DB::table('users')->where('id', $loggedUserID)->update($userDetails);
        if ($updateUserDetails){
            //
        } else {
            //
        }
        return $this->dashboard();
    }


    /**
     * Modify application if not yet submitted to DBS
     *
     * 
     */

    public function modifyDBSApplication($applicationID){
        if (!Auth::check()) return redirect("/login");

        if(!isset($applicationID) ||empty($applicationID)) return redirect("/login");
        
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->join('user_titles', 'user_titles.id', '=', 'applications.title')
            ->select('applications.*', 'user_titles.userTitle', 'users.dbsApplication', 'users.bpssApplication', 'users.email as loginEmail')
            ->where(['applications.id'=>$applicationID])
            ->first();
//echo'<pre>';print_r($DBSApplication);echo'</pre>';
        if($DBSApplication->applicationStatus == 1){
            $userOrganisations = $this->getUserOrganisations();
            if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($DBSApplication->organisationID, $userOrganisations)) return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
            else if(Auth::user()->userType == 'applicant' && $DBSApplication->userID != Auth::user()->id) return redirect("/login")->with('errorMessage', 'The application link is invalid!');

            $updateApplicationDetails = \DB::table('applications')->where(['id' => $DBSApplication->id])->update(['applicationStatus' => 0]);
            
            //send email to notify user
            /*
            $emailTo = $DBSApplication->application_email;
            $loginEmail = $DBSApplication->loginEmail;
            Mail::send('email_templates.dbs_application_review_fail', ['loginLink' =>env('APP_URL')], function ($message) use ($emailTo, $loginEmail) {
                $message->to($emailTo)->cc($loginEmail);
                $message->subject('DBS Application');
            });
            */
        }
        if(Auth::user()->userType == 'admin'){
            return redirect("/applications/viewApplicationDetails/".$DBSApplication->id);
        } else {
            return redirect("/login");
        }
        
    }

    /**
     * DBS Application Status
     *
     * 
     */

    public function viewApplicationStatus($applicationID){
        if (!Auth::check()) return redirect("/login");

        if(!isset($applicationID) ||empty($applicationID)) return redirect("/login");
        
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->join('user_titles', 'user_titles.id', '=', 'applications.title')
            ->select('applications.*', 'user_titles.userTitle', 'users.dbsApplication', 'users.bpssApplication')
            ->where(['applications.id'=>$applicationID])
            ->first();
        if (isset($DBSApplication) && !empty($DBSApplication->userID)){
            $BPSSApplication = \DB::table('bpss_applications')
                ->join('users', 'users.id', '=', 'bpss_applications.userID')
                ->select('bpss_applications.*')
                ->where(['bpss_applications.userID'=>$DBSApplication->userID])
                ->first();
        } else $BPSSApplication = new \stdClass();
//echo'<pre>';print_r($DBSApplication);echo'</pre>';

        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($DBSApplication->organisationID, $userOrganisations))return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant' && $DBSApplication->userID != Auth::user()->id) return redirect("/login")->with('errorMessage', 'The application link is invalid!');

        return view('applicant.viewApplicationStatus', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]);
        
    }

    /**
     * BPSS Review Application
     *
     * 
     */

    public function reviewBPSSApplication($applicationID){
        if (!Auth::check()) return redirect("/login");

        if(!isset($applicationID) || empty($applicationID)) return redirect("/login");
        $whereConditions = (Auth::user()->userType == 'admin') ? ['bpss_applications.id'=>$applicationID] : ['bpss_applications.userID' => Auth::user()->id, 'bpss_applications.id'=>$applicationID];

        $BPSSApplication = \DB::table('bpss_applications')
            ->join('users', 'users.id', '=', 'bpss_applications.userID')
            ->select('bpss_applications.*')
            ->where($whereConditions)
            ->first();

        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($DBSApplication->organisationID, $userOrganisations)) return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant' && $BPSSApplication->userID != Auth::user()->id) return redirect("/login")->with('errorMessage', 'The application link is invalid!');

        if (isset($BPSSApplication->id) && !empty($BPSSApplication->id)){

        }
//echo'<pre>';print_r($roName);echo'</pre>';
        return view('applicant.reviewBPSSApplication', ['BPSSApplication' => $BPSSApplication]);
        
    }

    /**
     * BPSS application step1
     *
     * 
     */

    public function bpssStep1()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;

        $DBSApplication = \DB::table('applications')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
            
        if (!isset($DBSApplication->id) || empty($DBSApplication->id) || $DBSApplication->applicationStatus == 0){ return redirect('/login'); }
            
        $BPSSApplication = \DB::table('bpss_applications')
            ->join('users', 'users.id', '=', 'bpss_applications.userID')
            ->select('bpss_applications.applicationStatus', 'bpss_applications.id', 'bpss_applications.form_type', 'bpss_applications.employement_type', 'bpss_applications.subcontractor_company_name', 'bpss_applications.start_date', 'bpss_applications.dbs_application_id', 'bpss_applications.mother_maiden_last_name', 'bpss_applications.known_as_name')
            ->where(['bpss_applications.userID'=>$loggedUserID])
            ->first();
//echo'<pre>';print_r($BPSSApplication);echo'</pre>';
        return view('applicant.bpssStep1', ['BPSSApplication' => $BPSSApplication, 'DBSApplication' => $DBSApplication]);
        
    }

    /**
     * Save Data from Step 1
     *
     * 
     */

    public function savebpssStep1(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        $requestVars = $request->all();
        //echo'<pre>';print_r($requestVars);echo'</pre>';
        //check if there is an application for our user, if not create one
        $existingBPSSApplicationID = \DB::table('bpss_applications')
            ->join('users', 'users.id', '=', 'bpss_applications.userID')
            ->select('bpss_applications.id', 'bpss_applications.applicationStatus')
            ->where(['bpss_applications.userID'=>$loggedUserID])
            ->first();
        if (!isset($existingBPSSApplicationID->id) || empty($existingBPSSApplicationID->id)){
            
            $applicationDetails = array(
                'userID'     => $loggedUserID,
                'organisationID' => $loggedUserOrganisationID,
                'createdOn' => date("Y-m-d"),
                'createdBy' => Auth::user()->id,
            );
           
            $applicationID = \DB::table('bpss_applications')->insertGetId($applicationDetails);
        } else $applicationID = $existingBPSSApplicationID->id;

        //check status
        if (isset($existingBPSSApplicationID->applicationStatus) && $existingBPSSApplicationID->applicationStatus != 0){ return redirect('/login'); }

        //save new data
        if (isset($requestVars['start_date']) && date("Y-m-d", strtotime(str_replace('/', '-', $requestVars['start_date']))) != '1970-01-01'){
            $startDateRaw = explode('/',$requestVars['start_date']);
            $startDate = $startDateRaw[2] . '-' . $startDateRaw[1] . '-' . $startDateRaw[0];
        } else $startDate = null;

        $step1Data = array(
            'form_type'             => isset($requestVars['form_type']) ? $requestVars['form_type'] : null,
            'employement_type'      => isset($requestVars['employement_type']) ? $requestVars['employement_type'] : null,
            'subcontractor_company_name'    => isset($requestVars['subcontractor_company_name']) ? $requestVars['subcontractor_company_name'] : null,
            'start_date'            => $startDate,
            'mother_maiden_last_name'  => isset($requestVars['mother_maiden_last_name']) ? $requestVars['mother_maiden_last_name'] : null,
            'known_as_name'  => isset($requestVars['known_as_name']) ? $requestVars['known_as_name'] : null,
        );

        $updateBPSSApplicationDetails = \DB::table('bpss_applications')->where(['id' => $applicationID])->update($step1Data);
        //if contractor then show contractor details form if not then go to address
        if(isset($requestVars['employement_type']) && $requestVars['employement_type'] == 'contractor'){
            return $this->bpssStep1Extra();
        } else {
            return $this->bpssStep2();
        }
        
    }

    /**
     * BPSS application Step1Extra
     *
     * 
     */

    public function bpssStep1Extra()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
            
        if (!isset($DBSApplication->id) || empty($DBSApplication->id) || $DBSApplication->applicationStatus == 0){ return redirect('/login'); }

        $BPSSApplication = \DB::table('bpss_applications')
            ->join('users', 'users.id', '=', 'bpss_applications.userID')
            ->select('bpss_applications.*')
            ->where(['bpss_applications.userID'=>$loggedUserID])
            ->first();

        return view('applicant.bpssStep1Extra', ['BPSSApplication' => $BPSSApplication, 'DBSApplication' => $DBSApplication]);
        
    }

    /**
     * Save Data from Step1Extra
     *
     * 
     */

    public function savebpssStep1Extra(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        $requestVars = $request->all();
        //echo'<pre>';print_r($requestVars);echo'</pre>';exit;
        
        //get app id
        $applicationID = \DB::table('bpss_applications')->select('bpss_applications.id')->where(['bpss_applications.userID'=>$loggedUserID])->first()->id;


        //save new data
        if(isset($requestVars['se_accountant_known_from']) && strlen($requestVars['se_accountant_known_from']) == 10){
            $accountantFromRaw = explode('/',$requestVars['se_accountant_known_from']);
            $accountantFrom = $accountantFromRaw[2] . '-' . $accountantFromRaw[1] . '-' . $accountantFromRaw[0];
        } else $accountantFrom = null;
        if(isset($requestVars['se_accountant_known_to']) && strlen($requestVars['se_accountant_known_to']) == 10){
            $accountantToRaw = explode('/',$requestVars['se_accountant_known_to']);
            $accountantTo = $accountantToRaw[2] . '-' . $accountantToRaw[1] . '-' . $accountantToRaw[0];
        } else $accountantTo = null;


        $step1ExtraData = array(
            'contractor_name_of_company'                => isset($requestVars['contractor_name_of_company']) ? $requestVars['contractor_name_of_company'] : null,
            'contractor_address_of_company'             => isset($requestVars['contractor_address_of_company']) ? $requestVars['contractor_address_of_company'] : null,
            'contractor_number_of_years_with_company'   => isset($requestVars['contractor_number_of_years_with_company']) ? $requestVars['contractor_number_of_years_with_company'] : null,
            'contractor_name_of_agency'                 => isset($requestVars['contractor_name_of_agency']) ? $requestVars['contractor_name_of_agency'] : null,
            'contractor_address_of_agency'              => isset($requestVars['contractor_address_of_agency']) ? $requestVars['contractor_address_of_agency'] : null,
            'contractor_agency_contact'                 => isset($requestVars['contractor_agency_contact']) ? $requestVars['contractor_agency_contact'] : null,
            'contractor_utas_contact'                   => isset($requestVars['contractor_utas_contact']) ? $requestVars['contractor_utas_contact'] : null,
            'selfemployment'                            => isset($requestVars['selfemployment']) ? $requestVars['selfemployment'] : 0,
            'selfemployment_documentation_option'       => isset($requestVars['selfemployment_documentation_option']) ? $requestVars['selfemployment_documentation_option'] : null,
            'se_accountant_known_from'                  => $accountantFrom,
            'se_accountant_known_to'                    => $accountantTo,
            'se_accountant_name'                        => isset($requestVars['se_accountant_name']) ? $requestVars['se_accountant_name'] : null,
            'se_accountant_address'                     => isset($requestVars['se_accountant_address']) ? $requestVars['se_accountant_address'] : null,
            'se_accountant_town'                        => isset($requestVars['se_accountant_town']) ? $requestVars['se_accountant_town'] : null,
            'se_accountant_postcode'                    => isset($requestVars['se_accountant_postcode']) ? preg_replace('/\s+/', ' ',$requestVars['se_accountant_postcode']) : null,
            'se_accountant_email'                       => isset($requestVars['se_accountant_email']) ? $requestVars['se_accountant_email'] : null,
            'se_accountant_contact_number'              => isset($requestVars['se_accountant_contact_number']) ? $requestVars['se_accountant_contact_number'] : null,
        );

        $updateBPSSApplicationDetails = \DB::table('bpss_applications')->where(['id' => $applicationID])->update($step1ExtraData);


        return $this->bpssStep2();
        
    }

    /**
     * BPSS application step2
     *
     * 
     */

    public function bpssStep2()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
            
        if (!isset($DBSApplication->id) || empty($DBSApplication->id) || $DBSApplication->applicationStatus == 0){ return redirect('/login'); }
        $DBSApplication->supporting_passport_country_fullName = '';
        if (!empty($DBSApplication->supporting_passport_country)) {
            $supporting_passport_country_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$DBSApplication->supporting_passport_country])->first();
            if (isset($supporting_passport_country_raw) && !empty($supporting_passport_country_raw->id)){
                $DBSApplication->supporting_passport_country_fullName = $supporting_passport_country_raw->name;
            } else {
                
            }
        }

        $BPSSApplication = \DB::table('bpss_applications')
            ->join('users', 'users.id', '=', 'bpss_applications.userID')
            ->select('bpss_applications.*')
            ->where(['bpss_applications.userID'=>$loggedUserID])
            ->first();
        $savedPassports = \DB::table('bpss_application_passports')
            ->join('countries', 'countries.iso3', '=', 'bpss_application_passports.country_of_issue')
            ->select('bpss_application_passports.*', 'countries.nicename AS country_of_issue_name')
            ->where(['bpss_application_passports.applicationID'=>$BPSSApplication->id])
            ->get();
        $dualCitizenships = \DB::table('bpss_application_extra_nationalities')
            ->join('countries', 'countries.iso3', '=', 'bpss_application_extra_nationalities.country')
            ->select('bpss_application_extra_nationalities.*', 'countries.nicename AS country_of_issue_name')
            ->where(['bpss_application_extra_nationalities.applicationID'=>$BPSSApplication->id])
            ->get();
        $countDualCitizenships = (isset($dualCitizenships) && count($dualCitizenships) > 0) ? count($dualCitizenships) : 0;

        $countries = \DB::table('countries')
            ->select('*')
            ->orderByRaw('id=225 DESC')
            ->orderBy('nicename', 'asc')
            ->get();

//echo'<pre>';print_r($savedPassports);echo'</pre>';
        return view('applicant.bpssStep2', ['BPSSApplication' => $BPSSApplication, 'DBSApplication' => $DBSApplication, 'savedPassports' => $savedPassports, 'dualCitizenships' => $dualCitizenships, 'countDualCitizenships' => $countDualCitizenships, 'countries' => $countries]);
        
    }

    public function addPassportDetails(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.applicationStatus', 'bpss_applications.id')->where(['bpss_applications.id'=>$requestVars['applicationID']])->first();
            if (isset($BPSSApplication->id) && !empty($BPSSApplication->id) && $BPSSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }
            $passportDetails = array(
                'applicationID'     => $requestVars['applicationID'],
                'passport_number' => $requestVars['passport_number'],
                'country_of_issue' => $requestVars['country_of_issue'],
            );
           
            $passportID = \DB::table('bpss_application_passports')->insertGetId($passportDetails);
            if(isset($passportID) && $passportID>0){
                 echo json_encode(['status' => 1, 'passportID' => $passportID]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }

    public function removePassportDetails(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.applicationStatus', 'bpss_applications.id')->where(['bpss_applications.id'=>$requestVars['applicationID']])->first();
            if (isset($BPSSApplication->id) && !empty($BPSSApplication->id) && $BPSSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }

            $testDelete = \DB::table('bpss_application_passports')->where(['id' => $requestVars['passportID'], 'applicationID' => $requestVars['applicationID']])->delete();

            if(isset($testDelete) && $testDelete){
                 echo json_encode(['status' => 1]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }

    public function addCitizenshipCountry(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.applicationStatus', 'bpss_applications.id')->where(['bpss_applications.id'=>$requestVars['applicationID']])->first();
            if (isset($BPSSApplication->id) && !empty($BPSSApplication->id) && $BPSSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }
            $countryDetails = array(
                'applicationID'     => $requestVars['applicationID'],
                'country' => $requestVars['country_of_nationality'],
            );
           
            $countryID = \DB::table('bpss_application_extra_nationalities')->insertGetId($countryDetails);
            if(isset($countryID) && $countryID>0){
                 echo json_encode(['status' => 1, 'countryID' => $countryID]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }

    public function removeCitizenshipCountry(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.applicationStatus', 'bpss_applications.id')->where(['bpss_applications.id'=>$requestVars['applicationID']])->first();
            if (isset($BPSSApplication->id) && !empty($BPSSApplication->id) && $BPSSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }

            $testDelete = \DB::table('bpss_application_extra_nationalities')->where(['id' => $requestVars['citizenshipCountry'], 'applicationID' => $requestVars['applicationID']])->delete();

            if(isset($testDelete) && $testDelete){
                 echo json_encode(['status' => 1]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }

    /**
     * Save Data from Step 2
     *
     * 
     */

    public function savebpssStep2(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        $requestVars = $request->all();
        //echo'<pre>';print_r($requestVars);echo'</pre>';exit;
        
        //get app id
        $applicationID = \DB::table('bpss_applications')->select('bpss_applications.id')->where(['bpss_applications.userID'=>$loggedUserID])->first()->id;


        //save new data
        if (isset($requestVars['naturalisation_certificate_date']) && date("Y-m-d", strtotime(str_replace('/', '-', $requestVars['naturalisation_certificate_date']))) != '1970-01-01'){
            $naturalisationDateRaw = explode('/',$requestVars['naturalisation_certificate_date']);
            $naturalisationDate = $naturalisationDateRaw[2] . '-' . $naturalisationDateRaw[1] . '-' . $naturalisationDateRaw[0];
        } else $naturalisationDate = null;

        $step2Data = array(
            'present_nationality'               => isset($requestVars['present_nationality']) ? $requestVars['present_nationality'] : null,
            'national_identity_card'            => isset($requestVars['national_identity_card']) ? $requestVars['national_identity_card'] : 0,
            'national_identity_card_number'     => isset($requestVars['national_identity_card_number']) ? $requestVars['national_identity_card_number'] : null,
            'passport'                          => isset($requestVars['passport']) ? $requestVars['passport'] : 0,
            'dual_citizenship'                  => isset($requestVars['dual_citizenship']) ? $requestVars['dual_citizenship'] : 0,
            'dual_citizenship_details'          => isset($requestVars['dual_citizenship_details']) ? $requestVars['dual_citizenship_details'] : null,
            'former_nationality'                => isset($requestVars['former_nationality']) ? $requestVars['former_nationality'] : 0,
            'former_nationality_details'        => isset($requestVars['former_nationality_details']) ? $requestVars['former_nationality_details'] : null,
            'naturalisation_certificate_number' => isset($requestVars['naturalisation_certificate_number']) ? $requestVars['naturalisation_certificate_number'] : null,
            'naturalisation_certificate_date'   => $naturalisationDate,            
            'subject_to_immigration_control'    => isset($requestVars['subject_to_immigration_control']) ? $requestVars['subject_to_immigration_control'] : 0,
            'subject_to_immigration_control_details' => isset($requestVars['subject_to_immigration_control_details']) ? $requestVars['subject_to_immigration_control_details'] : null,
            'lawfully_resident_in_uk'           => isset($requestVars['lawfully_resident_in_uk']) ? $requestVars['lawfully_resident_in_uk'] : 0,
            'continued_residence_restrictions'  => isset($requestVars['continued_residence_restrictions']) ? $requestVars['continued_residence_restrictions'] : 0,
            'continued_residence_restrictions_details' => isset($requestVars['continued_residence_restrictions_details']) ? $requestVars['continued_residence_restrictions_details'] : null,
            'freedom_to_take_employment'        => isset($requestVars['freedom_to_take_employment']) ? $requestVars['freedom_to_take_employment'] : 0,
            'freedom_to_take_employment_details'=> isset($requestVars['freedom_to_take_employment_details']) ? $requestVars['freedom_to_take_employment_details'] : null,
            'ho_port_reference_number'          => isset($requestVars['ho_port_reference_number']) ? $requestVars['ho_port_reference_number'] : null,
            
        );

        $updateBPSSApplicationDetails = \DB::table('bpss_applications')->where(['id' => $applicationID])->update($step2Data);
            return $this->bpssStep3();
        
    }

    /**
     * BPSS application step3
     *
     * 
     */

    public function bpssStep3()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
            
        if (!isset($DBSApplication->id) || empty($DBSApplication->id) || $DBSApplication->applicationStatus == 0){ return redirect('/login'); }

        $BPSSApplication = \DB::table('bpss_applications')
            ->join('users', 'users.id', '=', 'bpss_applications.userID')
            ->select('bpss_applications.*')
            ->where(['bpss_applications.userID'=>$loggedUserID])
            ->first();

        $employmentHistory = \DB::table('bpss_application_employment_history')
            ->select('bpss_application_employment_history.*')
            ->where(['bpss_application_employment_history.applicationID'=>$BPSSApplication->id])
            ->get();

        $unemploymentRecords = \DB::table('bpss_application_unemployment')
            ->select('bpss_application_unemployment.*')
            ->where(['bpss_application_unemployment.applicationID'=>$BPSSApplication->id])
            ->get();

        $reference1 = \DB::table('bpss_application_personal_referee')
            ->select('bpss_application_personal_referee.*')
            ->where(['bpss_application_personal_referee.applicationID'=>$BPSSApplication->id])
            ->skip(0)
            ->take(1)
            ->first();
        $reference2 = \DB::table('bpss_application_personal_referee')
            ->select('bpss_application_personal_referee.*')
            ->where(['bpss_application_personal_referee.applicationID'=>$BPSSApplication->id])
            ->skip(1)
            ->take(1)
            ->first();
        $check_employment_set = \DB::table('bpss_application_employment_history')->select('*')->where(['applicationID'=>$BPSSApplication->id])->get();
            if(isset($check_employment_set) && count($check_employment_set)>0){
                $employment_set = 1;
            } else {
                $employment_set = 0;
            }


//echo'<pre>';print_r($savedPassports);echo'</pre>';
        return view('applicant.bpssStep3', ['BPSSApplication' => $BPSSApplication, 'DBSApplication' => $DBSApplication, 'unemploymentRecords' => $unemploymentRecords, 'employmentHistory' => $employmentHistory, 'reference1' => $reference1, 'reference2' => $reference2, 'check_employment_set' => $employment_set]);
        
    }

    public function addEmploymentDetails(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.applicationStatus', 'bpss_applications.id')->where(['bpss_applications.id'=>$requestVars['applicationID']])->first();
            if (isset($BPSSApplication->id) && !empty($BPSSApplication->id) && $BPSSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }
            

            $dateFromRaw = explode('/',$requestVars['employment_date_from']);
            $dateFrom = $dateFromRaw[2] . '-' . $dateFromRaw[1] . '-' . $dateFromRaw[0];
            if(isset($requestVars['employment_date_to']) && strlen($requestVars['employment_date_to']) == 10){
                $dateToRaw = explode('/',$requestVars['employment_date_to']);
                $dateTo = $dateToRaw[2] . '-' . $dateToRaw[1] . '-' . $dateToRaw[0];
            } else $dateTo = null;

            $allow_contact = (isset($requestVars['referee_allow_contact']) && $requestVars['referee_allow_contact']) ? 0:1;
            $employmentDetails = array(
                'applicationID'     => $requestVars['applicationID'],
                'date_from'     => $dateFrom,
                'date_to'     => $dateTo,
                'company_name' => $requestVars['company_name'],
                'email_address' => $requestVars['email_address'],
                'company_address_line' => $requestVars['company_address_line'],
                'company_address_town' => $requestVars['company_address_town'],
                'company_address_postcode' => preg_replace('/\s+/', ' ',$requestVars['company_address_postcode']),
                'referee_allow_contact' => $allow_contact,
                'p60_enclosed' => $requestVars['p60_enclosed'],
            );
           
            $employmentID = \DB::table('bpss_application_employment_history')->insertGetId($employmentDetails);
            if(isset($employmentID) && $employmentID>0){
                 echo json_encode(['status' => 1, 'employmentID' => $employmentID]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }

public function removeEmploymentDetails(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.applicationStatus', 'bpss_applications.id')->where(['bpss_applications.id'=>$requestVars['applicationID']])->first();
            if (isset($BPSSApplication->id) && !empty($BPSSApplication->id) && $BPSSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }

            $testDelete = \DB::table('bpss_application_employment_history')->where(['id' => $requestVars['employmentID'], 'applicationID' => $requestVars['applicationID']])->delete();

            if(isset($testDelete) && $testDelete){
                 echo json_encode(['status' => 1]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }


    public function addUnemploymentDetails(Request $request){
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.applicationStatus', 'bpss_applications.id')->where(['bpss_applications.id'=>$requestVars['applicationID']])->first();
            if (isset($BPSSApplication->id) && !empty($BPSSApplication->id) && $BPSSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }
            

            $dateFromRaw = explode('/',$requestVars['unemployment_date_from']);
            $dateFrom = $dateFromRaw[2] . '-' . $dateFromRaw[1] . '-' . $dateFromRaw[0];
            if(isset($requestVars['unemployment_date_to']) && strlen($requestVars['unemployment_date_to']) == 10){
                $dateToRaw = explode('/',$requestVars['unemployment_date_to']);
                $dateTo = $dateToRaw[2] . '-' . $dateToRaw[1] . '-' . $dateToRaw[0];
            } else $dateTo = null;

            $unemploymentDetails = array(
                'applicationID'     => $requestVars['applicationID'],
                'date_from'     => $dateFrom,
                'date_to'     => $dateTo,
            );
           
            $unemploymentID = \DB::table('bpss_application_unemployment')->insertGetId($unemploymentDetails);
            if(isset($unemploymentID) && $unemploymentID>0){
                 echo json_encode(['status' => 1, 'unemploymentID' => $unemploymentID]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }

    public function removeUnemploymentDetails(Request $request){
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.applicationStatus', 'bpss_applications.id')->where(['bpss_applications.id'=>$requestVars['applicationID']])->first();
            if (isset($BPSSApplication->id) && !empty($BPSSApplication->id) && $BPSSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }
            
            $testDelete = \DB::table('bpss_application_unemployment')->where(['id' => $requestVars['unemploymentID'], 'applicationID' => $requestVars['applicationID']])->delete();

            if(isset($testDelete) && $testDelete){
                 echo json_encode(['status' => 1]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }


    /**
     * Save Data from Step 3
     *
     * 
     */

    public function savebpssStep3(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        $requestVars = $request->all();
        //echo'<pre>';print_r($requestVars);echo'</pre>';
        
        //get app id
        $applicationID = \DB::table('bpss_applications')->select('bpss_applications.id')->where(['bpss_applications.userID'=>$loggedUserID])->first()->id;


        //save new data
        if (isset($requestVars['school_date_from'])){
            $school_date_fromRaw = explode('/',$requestVars['school_date_from']);
            $school_date_from = $school_date_fromRaw[2] . '-' . $school_date_fromRaw[1] . '-' . $school_date_fromRaw[0];
        } else $school_date_from = null;
        if (isset($requestVars['school_date_to'])){
            $school_date_toRaw = explode('/',$requestVars['school_date_from']);
            $school_date_to = $school_date_toRaw[2] . '-' . $school_date_toRaw[1] . '-' . $school_date_toRaw[0];
        } else $school_date_to = null;

        if (isset($requestVars['college_date_from'])){
            $college_date_fromRaw = explode('/',$requestVars['college_date_from']);
            $college_date_from = $college_date_fromRaw[2] . '-' . $college_date_fromRaw[1] . '-' . $college_date_fromRaw[0];
        } else $college_date_from = null;
        if (isset($requestVars['college_date_to'])){
            $college_date_toRaw = explode('/',$requestVars['college_date_to']);
            $college_date_to = $college_date_toRaw[2] . '-' . $college_date_toRaw[1] . '-' . $college_date_toRaw[0];
        } else $college_date_to = null;

        if (isset($requestVars['university_date_from'])){
            $university_date_fromRaw = explode('/',$requestVars['university_date_from']);
            $university_date_from = $university_date_fromRaw[2] . '-' . $university_date_fromRaw[1] . '-' . $university_date_fromRaw[0];
        } else $university_date_from = null;
        if (isset($requestVars['university_date_to'])){
            $university_date_toRaw = explode('/',$requestVars['university_date_to']);
            $university_date_to = $university_date_toRaw[2] . '-' . $university_date_toRaw[1] . '-' . $university_date_toRaw[0];
        } else $university_date_to = null;

        if (isset($requestVars['reference1_date_from'])){
            $reference1_date_fromRaw = explode('/',$requestVars['reference1_date_from']);
            $reference1_date_from = $reference1_date_fromRaw[2] . '-' . $reference1_date_fromRaw[1] . '-' . $reference1_date_fromRaw[0];
        } else $reference1_date_from = null;
        if (isset($requestVars['reference1_date_to'])){
            $reference1_date_toRaw = explode('/',$requestVars['reference1_date_to']);
            $reference1_date_to = $reference1_date_toRaw[2] . '-' . $reference1_date_toRaw[1] . '-' . $reference1_date_toRaw[0];
        } else $reference1_date_to = null;

        if (isset($requestVars['reference2_date_from'])){
            $reference2_date_fromRaw = explode('/',$requestVars['reference2_date_from']);
            $reference2_date_from = $reference2_date_fromRaw[2] . '-' . $reference2_date_fromRaw[1] . '-' . $reference2_date_fromRaw[0];
        } else $reference2_date_from = null;
        if (isset($requestVars['reference2_date_to'])){
            $reference2_date_toRaw = explode('/',$requestVars['reference2_date_to']);
            $reference2_date_to = $reference2_date_toRaw[2] . '-' . $reference2_date_toRaw[1] . '-' . $reference2_date_toRaw[0];
        } else $reference2_date_to = null;


        $step3Data = array(
            'school_data'                       => isset($requestVars['school_data']) ? $requestVars['school_data'] : 0,
            'school_name'                       => isset($requestVars['school_name']) ? $requestVars['school_name'] : null,
            'school_date_from'                  => $school_date_from, 
            'school_date_to'                    => $school_date_to, 
            'school_contact_name'               => isset($requestVars['school_contact_name']) ? $requestVars['school_contact_name'] : null,
            'school_contact_address'            => isset($requestVars['school_contact_address']) ? $requestVars['school_contact_address'] : null,

            'college_data'                      => isset($requestVars['college_data']) ? $requestVars['college_data'] : 0,
            'college_name'                      => isset($requestVars['college_name']) ? $requestVars['college_name'] : null,
            'college_date_from'                 => $college_date_from, 
            'college_date_to'                   => $college_date_to, 
            'college_contact_name'              => isset($requestVars['college_contact_name']) ? $requestVars['college_contact_name'] : null,
            'college_contact_address'           => isset($requestVars['college_contact_address']) ? $requestVars['college_contact_address'] : null,

            'university_data'                   => isset($requestVars['university_data']) ? $requestVars['university_data'] : 0,
            'university_name'                   => isset($requestVars['university_name']) ? $requestVars['university_name'] : null,
            'university_date_from'              => $university_date_from, 
            'university_date_to'                => $university_date_to, 
            'university_contact_email'          => isset($requestVars['university_contact_email']) ? $requestVars['university_contact_email'] : null,
            'university_contact_number'         => isset($requestVars['university_contact_number']) ? $requestVars['university_contact_number'] : null, 

            'unemployment'                      => isset($requestVars['unemployment']) ? $requestVars['unemployment'] : 0,           
        );

        $updateBPSSApplicationDetails = \DB::table('bpss_applications')->where(['id' => $applicationID])->update($step3Data);
//echo'<pre>';print_r($step3Data);echo'</pre>';exit;
        //save references

        $reference1Data = array(
            'applicationID'                     => $applicationID,
            'referee_name'                      => isset($requestVars['reference1_referee_name']) ? $requestVars['reference1_referee_name'] : null,
            'date_from'                         => $reference1_date_from, 
            'date_to'                           => $reference1_date_to, 
            'referee_address_line'              => isset($requestVars['reference1_referee_address_line']) ? $requestVars['reference1_referee_address_line'] : null,
            'referee_address_town'              => isset($requestVars['reference1_referee_address_town']) ? $requestVars['reference1_referee_address_town'] : null,
            'referee_address_postcode'          => isset($requestVars['reference1_referee_address_postcode']) ? preg_replace('/\s+/', ' ',$requestVars['reference1_referee_address_postcode']) : null,
            'referee_email'                     => isset($requestVars['reference1_referee_email']) ? $requestVars['reference1_referee_email'] : null,
            'referee_contact_number'            => isset($requestVars['reference1_referee_contact_number']) ? $requestVars['reference1_referee_contact_number'] : null,
            'relationship'                      => isset($requestVars['reference1_relationship']) ? $requestVars['reference1_relationship'] : null,
        );
        if (empty($requestVars['reference1_id'])){
            $referenceID = \DB::table('bpss_application_personal_referee')->insertGetId($reference1Data);
        } else {
            $updateReferenceDetails = \DB::table('bpss_application_personal_referee')->where(['id' => $requestVars['reference1_id']])->update($reference1Data);
        }


        $reference2Data = array(
            'applicationID'                     => $applicationID,
            'referee_name'                      => isset($requestVars['reference2_referee_name']) ? $requestVars['reference2_referee_name'] : null,
            'date_from'                         => $reference2_date_from, 
            'date_to'                           => $reference2_date_to, 
            'referee_address_line'              => isset($requestVars['reference2_referee_address_line']) ? $requestVars['reference2_referee_address_line'] : null,
            'referee_address_town'              => isset($requestVars['reference2_referee_address_town']) ? $requestVars['reference2_referee_address_town'] : null,
            'referee_address_postcode'          => isset($requestVars['reference2_referee_address_postcode']) ? preg_replace('/\s+/', ' ',$requestVars['reference2_referee_address_postcode']) : null,
            'referee_email'                     => isset($requestVars['reference2_referee_email']) ? $requestVars['reference2_referee_email'] : null,
            'referee_contact_number'            => isset($requestVars['reference2_referee_contact_number']) ? $requestVars['reference2_referee_contact_number'] : null,
            'relationship'                      => isset($requestVars['reference2_relationship']) ? $requestVars['reference2_relationship'] : null,
        );
        if (empty($requestVars['reference2_id'])){
            $referenceID = \DB::table('bpss_application_personal_referee')->insertGetId($reference2Data);
        } else {
            $updateReferenceDetails = \DB::table('bpss_application_personal_referee')->where(['id' => $requestVars['reference2_id']])->update($reference2Data);
        }

        return $this->bpssStep4();
        
    }

    /**
     * BPSS application step4
     *
     * 
     */

    public function bpssStep4()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
            
        if (!isset($DBSApplication->id) || empty($DBSApplication->id) || $DBSApplication->applicationStatus == 0){ return redirect('/login'); }

        $BPSSApplication = \DB::table('bpss_applications')
            ->join('users', 'users.id', '=', 'bpss_applications.userID')
            ->select('bpss_applications.*')
            ->where(['bpss_applications.userID'=>$loggedUserID])
            ->first();

        $appSettings = \DB::table('settings')
            ->select('settingValue AS consent_responsible_body_email', 'comments AS consent_responsible_body_email_label')
            ->where(['settingName'=>'consent_responsible_body_email'])
            ->first();

//echo'<pre>';print_r($savedPassports);echo'</pre>';
        return view('applicant.bpssStep4', ['BPSSApplication' => $BPSSApplication, 'DBSApplication' => $DBSApplication, 'appSettings' => $appSettings]);
        
    }


    /**
     * Save Data from Step 4
     *
     * 
     */

    public function savebpssStep4(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        $requestVars = $request->all();
        //echo'<pre>';print_r($requestVars);echo'</pre>';exit;
        
        //get app id
        $applicationID = \DB::table('bpss_applications')->select('bpss_applications.id')->where(['bpss_applications.userID'=>$loggedUserID])->first()->id;


        //save new data
        if (isset($requestVars['date_issued'])){
            $date_issuedRaw = explode('/',$requestVars['date_issued']);
            $date_issued = $date_issuedRaw[2] . '-' . $date_issuedRaw[1] . '-' . $date_issuedRaw[0];
        } else $date_issued = null;

        $appSettings = \DB::table('settings')
            ->select('settingValue AS consent_responsible_body_email')
            ->where(['settingName'=>'consent_responsible_body_email'])
            ->first();

        $step4Data = array(
            'consent_responsible_body_submitting_dbs_application'   => isset($requestVars['consent_responsible_body_submitting_dbs_application']) ? $requestVars['consent_responsible_body_submitting_dbs_application'] : 0,
            'electronic_notification_state'                         => isset($requestVars['electronic_notification_state']) ? $requestVars['electronic_notification_state'] : 0,
            'read_and_understood_dbs_Statement'                     => isset($requestVars['read_and_understood_dbs_Statement']) ? $requestVars['read_and_understood_dbs_Statement'] : 0,
            'is_consent_provided'                                   => isset($requestVars['is_consent_provided']) ? $requestVars['is_consent_provided'] : 0,
            'consent_responsible_body_email'                        => $appSettings->consent_responsible_body_email,
            'purpose_of_check'                                      => 'BasicPAD', 
            'clearance_level_sc'                                    => isset($requestVars['clearance_level_sc']) ? $requestVars['clearance_level_sc'] : 0,
            'clearance_level_dv'                                    => isset($requestVars['clearance_level_dv']) ? $requestVars['clearance_level_dv'] : 0,
            'clearance_level_details'                               => isset($requestVars['clearance_level_details']) ? $requestVars['clearance_level_details'] : null,
            'date_issued'                                           => $date_issued,           
        );

        $updateBPSSApplicationDetails = \DB::table('bpss_applications')->where(['id' => $applicationID])->update($step4Data);


        return $this->bpssStep5();
        
    }

    
    /**
     * BPSS application step5
     *
     * 
     */

    public function bpssStep5()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
            
        if (!isset($DBSApplication->id) || empty($DBSApplication->id) || $DBSApplication->applicationStatus == 0){ return redirect('/login'); }

        $BPSSApplication = \DB::table('bpss_applications')
            ->join('users', 'users.id', '=', 'bpss_applications.userID')
            ->select('bpss_applications.*')
            ->where(['bpss_applications.userID'=>$loggedUserID])
            ->first();


//echo'<pre>';print_r($savedPassports);echo'</pre>';
        return view('applicant.bpssStep5', ['BPSSApplication' => $BPSSApplication, 'DBSApplication' => $DBSApplication]);
        
    }

    /**
     * Save Data from Step 5
     *
     * 
     */

    public function savebpssStep5(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        $requestVars = $request->all();
        //echo'<pre>';print_r($requestVars);echo'</pre>';exit;
        
        //get app id
        $applicationID = \DB::table('bpss_applications')->select('bpss_applications.id')->where(['bpss_applications.userID'=>$loggedUserID])->first()->id;


        //save new data
        if (isset($requestVars['date_issued'])){
            $date_issuedRaw = explode('/',$requestVars['date_issued']);
            $date_issued = $date_issuedRaw[2] . '-' . $date_issuedRaw[1] . '-' . $date_issuedRaw[0];
        } else $date_issued = null;


        $step5Data = array(
            'convicted_by_court'            => isset($requestVars['convicted_by_court']) ? $requestVars['convicted_by_court'] : 0,
            'convicted_by_court_martial'    => isset($requestVars['convicted_by_court_martial']) ? $requestVars['convicted_by_court_martial'] : 0,
            'background_reliability'        => isset($requestVars['background_reliability']) ? $requestVars['background_reliability'] : 0,
            'convictions_details'           => isset($requestVars['convictions_details']) ? $requestVars['convictions_details'] : null,
        );

        $updateBPSSApplicationDetails = \DB::table('bpss_applications')->where(['id' => $applicationID])->update($step5Data);


        return $this->bpssStep6();
        
    }


    /**
     * BPSS application step6
     *
     * 
     */

    public function bpssStep6()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
            
        if (!isset($DBSApplication->id) || empty($DBSApplication->id) || $DBSApplication->applicationStatus == 0){ return redirect('/login'); }

        $BPSSApplication = \DB::table('bpss_applications')
            ->join('users', 'users.id', '=', 'bpss_applications.userID')
            ->select('bpss_applications.*')
            ->where(['bpss_applications.userID'=>$loggedUserID])
            ->first();


//echo'<pre>';print_r($savedPassports);echo'</pre>';
        return view('applicant.bpssStep6', ['BPSSApplication' => $BPSSApplication, 'DBSApplication' => $DBSApplication]);
        
    }

    /**
     * Save Data from Step 6
     *
     * 
     */

    public function savebpssStep6(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        $requestVars = $request->all();
        //echo'<pre>';print_r($requestVars);echo'</pre>';exit;
        
        //get app id
        $applicationID = \DB::table('bpss_applications')->select('bpss_applications.id')->where(['bpss_applications.userID'=>$loggedUserID])->first()->id;


        //save new data
        if (isset($requestVars['date_issued'])){
            $date_issuedRaw = explode('/',$requestVars['date_issued']);
            $date_issued = $date_issuedRaw[2] . '-' . $date_issuedRaw[1] . '-' . $date_issuedRaw[0];
        } else $date_issued = null;


        $step6Data = array(
            'additional_information'    => isset($requestVars['additional_information']) ? $requestVars['additional_information'] : null,
            'applicationStatus'         => 1,
            'completedDate'             => date("Y-m-d"),
        );

        //add notification
        $requestingUserDetails = \DB::table('users')->select('*')->where('id', '=', $loggedUserID)->first();
        
        $organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', $loggedUserOrganisationID)->first()->organisationName;

        $notificationDetails = [
            'title' => 'New BPSS Submission - '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName,
            'body' => 'A new BPSS application was submitted by '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName.', email: '.$requestingUserDetails->email.' and it is awaiting review<br />Organisation: '.$organisationName.'<br /><br />Click here to view the request: <a href="'.env('APP_URL').'applicant/generateBPSSPDF/'.$applicationID.'" target="_blank">BPSS Application details</a><br /><br />',
            'category' => 0,
            'relatedUserId' => $loggedUserID,
            'relatedAction' => 'New DBS submission',

            'emailTemplate' => 'notification_bpss_submission',
            'emailTitle' => 'New BPSS Submission',
            'emailBody' => 'A new BPSS application was submitted by '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName.', email: '.$requestingUserDetails->email.' and it is awaiting review<br />Organisation: '.$organisationName.'<br /><br />Click here to view the request: <a href="'.env('APP_URL').'applicant/generateBPSSPDF/'.$applicationID.'" target="_blank">BPSS Application details</a><br /><br />',
        ];
        $this->addNotification('superuser', $loggedUserOrganisationID, $notificationDetails, true);

        $updateBPSSApplicationDetails = \DB::table('bpss_applications')->where(['id' => $applicationID])->update($step6Data);


        return $this->submitBPSSApplication();
        
    }
    /**
     * BPSS application submit
     *
     * 
     */

    public function submitBPSSApplication()
    {   
        if (!Auth::check()) return redirect("/login");

        return view('applicant.bpssApplicationSubmitted');
    }

    /**
     * Modify BPSS application 
     *
     * 
     */

    public function modifyBPSSApplication($applicationID){
        if (!Auth::check()) return redirect("/login");

        if(!isset($applicationID) ||empty($applicationID)) return redirect("/login");
        
        $BPSSApplication = \DB::table('bpss_applications')
            ->join('users', 'users.id', '=', 'bpss_applications.userID')
            ->select('bpss_applications.*')
            ->where(['bpss_applications.id'=>$applicationID])
            ->first();

        if($BPSSApplication->applicationStatus == 1){
            $userOrganisations = $this->getUserOrganisations();
            if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($BPSSApplication->organisationID, $userOrganisations)) return redirect("/")->with('errorMessage', 'This application is not related to your office!');
            else if(Auth::user()->userType == 'applicant' && $BPSSApplication->userID != Auth::user()->id) return redirect("/login")->with('errorMessage', 'The application link is invalid!');

            $updateApplicationDetails = \DB::table('bpss_applications')->where(['id' => $BPSSApplication->id])->update(['applicationStatus' => 0]);
        }
        if(Auth::user()->userType == 'admin'){
            return redirect("/users/consolidatedSearch");
        } else {
            return $this->bpssStep1();
        }
    }

/**
 * Generate BPSS PDF
 *
 * 
 */

public function downloadBPSSPDF($applicationID){
    if (!Auth::check()) return redirect("/login");

    if(!isset($applicationID) ||empty($applicationID)) return redirect("/login");
    

    $BPSSApplication = \DB::table('bpss_applications')
        ->join('users', 'users.id', '=', 'bpss_applications.userID')
        ->select('bpss_applications.*')
        ->where(['bpss_applications.id'=>$applicationID])
        ->first();

//echo'<pre>';print_r($DBSApplication);echo'</pre>';
    if($BPSSApplication->applicationStatus == 1){
        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($BPSSApplication->organisationID, $userOrganisations)) return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant' && $BPSSApplication->userID != Auth::user()->id) return redirect("/login")->with('errorMessage', 'The application link is invalid!');

        $DBSApplication = \DB::table('applications')
            ->join('organisations', 'organisations.id', '=', 'applications.organisationID')
            ->select('applications.*', 'organisations.organisationName')
            ->where(['applications.userID'=>$BPSSApplication->userID])
            ->first();
        $DBSApplication->userTitle = \DB::table('user_titles')->select('userTitle')->where(['id'=>$DBSApplication->title])->first()->userTitle;
        $DBSApplication->employment_sector_name = \DB::table('employment_sectors')->select('employment_sector_name')->where(['id'=>$DBSApplication->employment_sector])->first()->employment_sector_name;
        $DBSApplication->birth_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->birth_country])->first()->name;
        $DBSApplication->address_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->address_country])->first()->name;
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
        if (!empty($DBSApplication->supporting_passport_country)) {
            $supporting_passport_country_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$DBSApplication->supporting_passport_country])->first();
            if (isset($supporting_passport_country_raw) && !empty($supporting_passport_country_raw->id)){
                $DBSApplication->supporting_passport_country_fullName = $supporting_passport_country_raw->name;
            } else {
                
            }
        }

        $BPSSApplication->passports = \DB::table('bpss_application_passports')->select('*')->where(['applicationID'=>$BPSSApplication->id])->orderBy('id', 'asc')->get();
        if (!empty($BPSSApplication->passports)) {
            foreach($BPSSApplication->passports as $key => $passport){
                $passportCountry_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$passport->country_of_issue])->first();

                if (isset($passportCountry_raw) && !empty($passportCountry_raw->id)){
                    $BPSSApplication->passports[$key]->passport_country_fullName = $passportCountry_raw->name;
                } else {
                    $BPSSApplication->passports[$key]->passport_country_fullName = '';
                }
            }
        }

        $BPSSApplication->employment_history = \DB::table('bpss_application_employment_history')->select('*')->where(['applicationID'=>$BPSSApplication->id])->orderBy('date_from', 'desc')->get();
        $BPSSApplication->unemployment_history = \DB::table('bpss_application_unemployment')->select('*')->where(['applicationID'=>$BPSSApplication->id])->orderBy('date_from', 'desc')->get();
        $reference1 = \DB::table('bpss_application_personal_referee')
            ->select('bpss_application_personal_referee.*')
            ->where(['bpss_application_personal_referee.applicationID'=>$BPSSApplication->id])
            ->skip(0)
            ->take(1)
            ->first();
        $reference2 = \DB::table('bpss_application_personal_referee')
            ->select('bpss_application_personal_referee.*')
            ->where(['bpss_application_personal_referee.applicationID'=>$BPSSApplication->id])
            ->skip(1)
            ->take(1)
            ->first();

//echo'<pre>';print_r($DBSApplication->previousAddresses);echo'</pre>';exit;
        set_time_limit(6000);

        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(8,8,8,8));
        //$html2pdf->setModeDebug();
        $html2pdf->pdf->SetTitle('BPSS_'.$DBSApplication->forename.'_'.$DBSApplication->presentSurname.'.pdf');
        $html2pdf->writeHTML(view('bpss_templates.bpss-page1', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page2', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page3', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page4', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page5', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page6', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page7', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication, 'reference1' => $reference1, 'reference2' => $reference2]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page8', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page9', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page10', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page11', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page12', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page13', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss-page14', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->output('BPSS_'.$DBSApplication->forename.'_'.$DBSApplication->presentSurname.'.pdf');
    }
}

/**
 * Generate BPSS PDF
 * Form version September 2018
 * Only to be used with BPSS applications completed after 30 October 2018
 */

public function generateBPSSPDF($applicationID){
    if (!Auth::check()) return redirect("/login");

    if(!isset($applicationID) ||empty($applicationID)) return redirect("/login");
    

    $BPSSApplication = \DB::table('bpss_applications')
        ->join('users', 'users.id', '=', 'bpss_applications.userID')
        ->select('bpss_applications.*')
        ->where(['bpss_applications.id'=>$applicationID])
        ->first();

//echo'<pre>';print_r($BPSSApplication);echo'</pre>';
    if($BPSSApplication->applicationStatus == 1){
        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($BPSSApplication->organisationID, $userOrganisations)) return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant' && $BPSSApplication->userID != Auth::user()->id) return redirect("/login")->with('errorMessage', 'The application link is invalid!');

        $DBSApplication = \DB::table('applications')
            ->join('organisations', 'organisations.id', '=', 'applications.organisationID')
            ->select('applications.*', 'organisations.organisationName')
            ->where(['applications.userID'=>$BPSSApplication->userID])
            ->first();
        $DBSApplication->userTitle = \DB::table('user_titles')->select('userTitle')->where(['id'=>$DBSApplication->title])->first()->userTitle;
        $DBSApplication->employment_sector_name = \DB::table('employment_sectors')->select('employment_sector_name')->where(['id'=>$DBSApplication->employment_sector])->first()->employment_sector_name;
        $DBSApplication->birth_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->birth_country])->first()->name;
        $DBSApplication->address_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->address_country])->first()->name;
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
        if (!empty($DBSApplication->supporting_passport_country)) {
            $supporting_passport_country_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$DBSApplication->supporting_passport_country])->first();
            if (isset($supporting_passport_country_raw) && !empty($supporting_passport_country_raw->id)){
                $DBSApplication->supporting_passport_country_fullName = $supporting_passport_country_raw->name;
            } else {
                
            }
        }

        $BPSSApplication->passports = \DB::table('bpss_application_passports')->select('*')->where(['applicationID'=>$BPSSApplication->id])->orderBy('id', 'asc')->get();
        if (!empty($BPSSApplication->passports)) {
            foreach($BPSSApplication->passports as $key => $passport){
                $passportCountry_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$passport->country_of_issue])->first();

                if (isset($passportCountry_raw) && !empty($passportCountry_raw->id)){
                    $BPSSApplication->passports[$key]->passport_country_fullName = $passportCountry_raw->name;
                } else {
                    $BPSSApplication->passports[$key]->passport_country_fullName = '';
                }
            }
        }

        $BPSSApplication->dualNationalities = \DB::table('bpss_application_extra_nationalities')->select('*')->where(['applicationID'=>$BPSSApplication->id])->orderBy('id', 'asc')->get();
        if (!empty($BPSSApplication->dualNationalities)) {
            foreach($BPSSApplication->dualNationalities as $key => $extraNationality){
                $extraNationality_raw = \DB::table('countries')->select('id', 'name')->where(['iso3'=>$extraNationality->country])->first();

                if (isset($extraNationality_raw) && !empty($extraNationality_raw->id)){
                    $BPSSApplication->dualNationalities[$key]->extraNationality_country_fullName = $extraNationality_raw->name;
                } else {
                    $BPSSApplication->dualNationalities[$key]->extraNationality_country_fullName = '';
                }
            }
        }

        $BPSSApplication->employment_history = \DB::table('bpss_application_employment_history')->select('*')->where(['applicationID'=>$BPSSApplication->id])->orderBy('date_from', 'desc')->get();
        $BPSSApplication->unemployment_history = \DB::table('bpss_application_unemployment')->select('*')->where(['applicationID'=>$BPSSApplication->id])->orderBy('date_from', 'desc')->get();
        $reference1 = \DB::table('bpss_application_personal_referee')
            ->select('bpss_application_personal_referee.*')
            ->where(['bpss_application_personal_referee.applicationID'=>$BPSSApplication->id])
            ->skip(0)
            ->take(1)
            ->first();
        $reference2 = \DB::table('bpss_application_personal_referee')
            ->select('bpss_application_personal_referee.*')
            ->where(['bpss_application_personal_referee.applicationID'=>$BPSSApplication->id])
            ->skip(1)
            ->take(1)
            ->first();

//echo'<pre>';print_r($DBSApplication->previousAddresses);echo'</pre>';exit;
        set_time_limit(6000);
	    ini_set('memory_limit','-1');
        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', array(8,8,8,8));
        //$html2pdf->setModeDebug();
        $html2pdf->setTestIsImage(false);
        $html2pdf->pdf->SetTitle('BPSS_'.$DBSApplication->forename.'_'.$DBSApplication->presentSurname.'.pdf');
        $html2pdf->writeHTML(view('bpss_templates.bpss2-page1', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        
        $html2pdf->writeHTML(view('bpss_templates.bpss2-page2', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        if(isset($BPSSApplication->selfemployment_documentation_option) && $BPSSApplication->selfemployment_documentation_option =='accountant'){
            $html2pdf->writeHTML(view('bpss_templates.bpss2-extrapage-accountant', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        }
        $html2pdf->writeHTML(view('bpss_templates.bpss2-page3', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss2-page4', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss2-page5', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss2-page6', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss2-page7', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss2-page8', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
        $html2pdf->writeHTML(view('bpss_templates.bpss2-page9', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication, 'reference1' => $reference1, 'reference2' => $reference2]));
        $html2pdf->writeHTML(view('bpss_templates.bpss2-page10', ['DBSApplication' => $DBSApplication, 'BPSSApplication' => $BPSSApplication]));
	
        $html2pdf->output('BPSS_'.$DBSApplication->forename.'_'.$DBSApplication->presentSurname.'.pdf');
    }
}
       



    /**
     * DBS Review Application
     *
     * 
     */

    public function reviewDBSApplication($applicationID){
        if (!Auth::check()) return redirect("/login");

        if(!isset($applicationID) ||empty($applicationID)) return redirect("/login");
        
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*')
            ->where(['applications.id'=>$applicationID])
            ->first();

        $userOrganisations = $this->getUserOrganisations();
        if(Auth::user()->userType == 'admin' && $this->checkAccess('siteuser') && !$this->checkAccess('superuser') && !in_array($DBSApplication->organisationID, $userOrganisations)) return redirect("/login")->with('errorMessage', 'This application is not related to your office!');
        else if(Auth::user()->userType == 'applicant' && $DBSApplication->userID != Auth::user()->id) return redirect("/login")->with('errorMessage', 'The application link is invalid!');

        if (isset($DBSApplication->id) && !empty($DBSApplication->id)){
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

            $loggedUserOrganisationDetails = \DB::table('organisations')->select('*')->where(['id'=>$DBSApplication->organisationID])->first();
            $roName = \DB::table('settings')->select('settingName', 'settingValue')->where(['settingName'=>'responsible_organisation_name'])->first()->settingValue;

        }
//echo'<pre>';print_r($roName);echo'</pre>';
        return view('applicant.reviewDBSApplication', ['DBSApplication' => $DBSApplication, 'loggedUserOrganisationDetails' => $loggedUserOrganisationDetails, 'roName' => $roName]);
        
    }

    /**
     * DBS application step1
     *
     * 
     */

    public function dbsStep1()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.applicationStatus', 'applications.id', 'applications.terms_accepted', 'applications.terms_accepted_date', 'applications.privacy_policy', 'applications.consent_basic_check', 'applications.declaration_by_applicant', 'applications.purpose_of_check', 'applications.employment_sector', 'applications.position_applied_for', 'applications.name_of_employer')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ return redirect('/login'); }

        $employmentSectors = \DB::table('employment_sectors')
            ->select('*')
            ->orderBy('id', 'asc')
            ->get();
        return view('applicant.dbsStep1', ['DBSApplication' => $DBSApplication, 'employmentSectors' => $employmentSectors]);
        
    }


    /**
     * Save Data from Step 1
     *
     * 
     */

    public function saveStep1Data(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        $requestVars = $request->all();
        $validateArray = [
            'terms_accepted'        => 'accepted',
            'purpose_of_check'      => 'required',
            'employment_sector'     => 'required_if:purpose_of_check,employment|numeric',
            'position_applied_for'  => 'required_if:purpose_of_check,employment|max:60',
            'name_of_employer'      => 'required_if:purpose_of_check,employment|max:60',
            
        ];
        //$this->validate($request, $validateArray); 



        //check if there is an application for our user, if not create one
        $existingApplicationID = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.id', 'applications.applicationStatus')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
        if (!isset($existingApplicationID->id) || empty($existingApplicationID->id)){
            //check status
            if (isset($existingApplicationID->applicationStatus) && $existingApplicationID->applicationStatus != 0){ return redirect('/login'); }
            $applicationDetails = array(
                'userID'     => $loggedUserID,
                'organisationID' => $loggedUserOrganisationID,
                'createdOn' => date("Y-m-d H:i:s"),
                'createdBy' => Auth::user()->id,
            );
           
            $applicationID = \DB::table('applications')->insertGetId($applicationDetails);
        } else $applicationID = $existingApplicationID->id;


        //save new data
        $step1Data = array(
            'percentCompleted'      => 10,
            'privacy_policy'        => 1,
            'privacy_policy_date'   => date("Y-m-d H:i:s"),
            'consent_basic_check'        => 1,
            'consent_basic_check_date'   => date("Y-m-d H:i:s"),
            'declaration_by_applicant' => 1,
            'declaration_by_applicant_date' => date("Y-m-d H:i:s"),
            'terms_accepted'        => 1,
            'terms_accepted_date'   => date("Y-m-d H:i:s"),
            'purpose_of_check'      => $requestVars['user_purpose_of_check'],
            'employment_sector'      => isset($requestVars['user_employment_sector']) ? $requestVars['user_employment_sector'] : 0,
            'position_applied_for'  => isset($requestVars['user_position_applied_for']) ? $requestVars['user_position_applied_for'] : null,
            'name_of_employer'      => isset($requestVars['user_name_of_employer']) ? $requestVars['user_name_of_employer'] : null,
        );

        $updateApplicationDetails = \DB::table('applications')->where(['id' => $applicationID])->update($step1Data);
            //if ($updateApplicationDetails){
                return $this->dbsStep2();
            // } else {
            //     return $this->dbsStep1();
            // }
        
    }

    /**
     * DBS application step2
     *
     * 
     */

    public function dbsStep2()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
        //check status
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ return redirect('/login'); }

        $userTitles = \DB::table('user_titles')
            ->select('*')
            ->orderBy('id', 'asc')
            ->get();

        $countries = \DB::table('countries')
            ->select('*')
            ->orderByRaw('id=225 DESC')
            ->orderBy('nicename', 'asc')
            ->get();
        $otherNames = \DB::table('application_extra_names')
            ->select('*')
            ->where(['applicationID'=>$DBSApplication->id])
            ->orderBy('dateFrom', 'asc')
            ->get();
        return view('applicant.dbsStep2', ['DBSApplication' => $DBSApplication, 'userTitles' => $userTitles, 'countries' => $countries, 'otherNames' => $otherNames]);
        
    }

    /**
     * Save Data from Step 2
     *
     * 
     */

    public function saveStep2Data(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        //check status
        $DBSApplication = \DB::table('applications')->select('applications.applicationStatus', 'applications.id')->where(['applications.userID'=>$loggedUserID])->first();
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ return redirect('/login'); }

        $requestVars = $request->all();
        $validateArray = [
            'terms_accepted'        => 'accepted',
            
        ];
        //$this->validate($request, $validateArray); 



        //get app id
        $applicationID = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first()->id;
        

        $dobRaw = explode('/',$requestVars['user_dob']);
        $dob = $dobRaw[2] . '-' . $dobRaw[1] . '-' . $dobRaw[0];
        //save new data
        $step1Data = array(
            'percentCompleted'      => 30,
            'title'    => isset($requestVars['user_title']) ? $requestVars['user_title'] : 0,
            'gender'   => $requestVars['user_gender'],
            'dob'      => $dob,
            'birth_town'      => $requestVars['user_birth_town'],
            'birth_country'  => $requestVars['user_birth_country'],
            'birth_nationality'      => isset($requestVars['user_birth_nationality']) ? $requestVars['user_birth_nationality'] : null,
            'previous_names'      => isset($requestVars['user_checkbox_previous_names']) ? $requestVars['user_checkbox_previous_names'] : 0,
            'forename'      => $requestVars['user_forename'],
            'middlename'      => isset($requestVars['user_middlename']) ? $requestVars['user_middlename'] : null,
            'presentSurname'      => $requestVars['user_present_surname'],
        );

        $updateApplicationDetails = \DB::table('applications')->where(['id' => $applicationID])->update($step1Data);
            //if ($updateApplicationDetails){

                return $this->dbsStep3();
            // } else {
            //     return $this->dbsStep2();
            // }
        
    }

    public function addOtherNames(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $DBSApplication = \DB::table('applications')->select('applications.applicationStatus', 'applications.id')->where(['applications.id'=>$requestVars['applicationID']])->first();
            if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }
            if(strlen($requestVars['user_other_names_from']) < 10 || strlen($requestVars['user_other_names_to']) < 10){
                echo json_encode(['status' => 0]); exit;
            }
            $dateFromRaw = explode('/',$requestVars['user_other_names_from']);
            $dateFrom = $dateFromRaw[2] . '-' . $dateFromRaw[1] . '-' . $dateFromRaw[0];

            $dateToRaw = explode('/',$requestVars['user_other_names_to']);
            $dateTo = $dateToRaw[2] . '-' . $dateToRaw[1] . '-' . $dateToRaw[0];


            $otherNames = array(
                'applicationID'     => $requestVars['applicationID'],
                'other_forename' => $requestVars['user_other_forename'],
                'other_middlename' => $requestVars['user_other_middlename'],
                'other_surname' => $requestVars['user_other_surname'],
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'createdOn' => date("Y-m-d"),
                'createdBy' => Auth::user()->id,
            );
           
            $otherNamesID = \DB::table('application_extra_names')->insertGetId($otherNames);
            if(isset($otherNamesID) && $otherNamesID>0){
                 echo json_encode(['status' => 1, 'otherNamesID' => $otherNamesID]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }

    public function removeName(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $DBSApplication = \DB::table('applications')->select('applications.applicationStatus', 'applications.id')->where(['applications.id'=>$requestVars['applicationID']])->first();
            if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }

            $testDelete = \DB::table('application_extra_names')->where(['id' => $requestVars['nameID'], 'applicationID' => $requestVars['applicationID']])->delete();

            if(isset($testDelete) && $testDelete){
                 echo json_encode(['status' => 1]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }

    /**
     * DBS application step3
     *
     * 
     */

    public function dbsStep3()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*', 'users.email AS userEmail')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
        //check status
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ return redirect('/login'); }

        if (!isset($DBSApplication->application_email) || empty($DBSApplication->application_email)) $DBSApplication->application_email = $DBSApplication->userEmail;
        $countryTelephoneCodesRaw = \DB::table('countries')
            ->select('phonecode')
            ->orderBy('phonecode', 'asc')
            ->get();
        $countryTelephoneCodes = array();
        foreach($countryTelephoneCodesRaw as $countryCode){
            if (!in_array($countryCode->phonecode, $countryTelephoneCodes)) $countryTelephoneCodes[] = $countryCode->phonecode;
        }

        $countries = \DB::table('countries')
            ->select('*')
            ->orderByRaw('id=225 DESC')
            ->orderBy('nicename', 'asc')
            ->get();

        return view('applicant.dbsStep3', ['DBSApplication' => $DBSApplication, 'countryTelephoneCodes' => $countryTelephoneCodes, 'countries' => $countries]);
        
    }

    /**
     * Save Data from Step 3
     *
     * 
     */

    public function saveStep3Data(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        //check status
        $DBSApplication = \DB::table('applications')->select('applications.applicationStatus', 'applications.id')->where(['applications.userID'=>$loggedUserID])->first();
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ return redirect('/login'); }

        $requestVars = $request->all();
        $validateArray = [
            'application_email'        => 'required|email|max:255',
            
        ];
        //$this->validate($request, $validateArray); 



        //get app id
        $applicationID = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first()->id;

        $currentAddressRaw = explode('/',$requestVars['user_current_address_from']);
        $currentAddressFrom = $currentAddressRaw[2] . '-' . $currentAddressRaw[1] . '-' . $currentAddressRaw[0];
        //save new data
        $step3ata = array(
            'percentCompleted'              => 50,
            'application_email'             => $requestVars['user_email'],
            'contact_number_country_code'   => $requestVars['user_contact_number_country_code'],
            'contact_number'                => $requestVars['user_contact_number'],
            'mobile_number_country_code'    => $requestVars['user_mobile_number_country_code'],
            'mobile_number'                 => $requestVars['user_mobile_number'],
            'address_line_1'                => $requestVars['user_address_line_1'],
            'address_line_2'                => $requestVars['user_address_line_2'],
            'address_town'                  => $requestVars['user_address_town'],
            'address_county'                => $requestVars['user_address_county'],
            'address_postcode'              => preg_replace('/\s+/', ' ',$requestVars['user_address_postcode']),
            'address_country'               => $requestVars['user_address_country'],
            'current_address_from'          => $currentAddressFrom,
        );

        $updateApplicationDetails = \DB::table('applications')->where(['id' => $applicationID])->update($step3ata);
//if ($updateApplicationDetails){
                //if(strtotime($currentAddressFrom) < strtotime('-5 years')) {
                if (time() < strtotime('+5 years', strtotime($currentAddressFrom))) {
                    return $this->dbsStep4();
                } else {
                    return $this->dbsStep5();
                }
                
            // } else {
            //     return $this->dbsStep3();
            // }
        
    }

    /**
     * DBS application step4
     *
     * 
     */

    public function dbsStep4()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();
        //check status
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ return redirect('/login'); }


        
        $countries = \DB::table('countries')
            ->select('*')
            ->orderByRaw('id=225 DESC')
            ->orderBy('nicename', 'asc')
            ->get();
        //calculate address intervals
        $loggedIntervals = [];
        $loggedIntervals[0]['addressID'] = 0;
        $loggedIntervals[0]['start'] = $DBSApplication->current_address_from;
        $loggedIntervals[0]['end'] = date("Y-m-d");
        $loggedIntervals[0]['address'] = $DBSApplication->address_line_1.' '.$DBSApplication->address_line_2.' '.$DBSApplication->address_town.' '.$DBSApplication->address_county.' '.$DBSApplication->address_postcode.' '.$DBSApplication->address_country;

        $previousAddresses = \DB::table('application_previous_addresses')->select('*')->where(['applicationID'=>$DBSApplication->id])->orderBy('previous_address_from', 'desc')->get();
//echo'<pre>';print_r($previousAddresses);echo'</pre>';

        if(isset($previousAddresses) && count($previousAddresses)>0){
            $count = 1;
            foreach($previousAddresses as $previousAddress){
                $loggedIntervals[$count]['addressID'] = $previousAddress->id;
                $loggedIntervals[$count]['start'] = $previousAddress->previous_address_from;
                $loggedIntervals[$count]['end'] = $previousAddress->previous_address_to;
                $loggedIntervals[$count]['address'] = $previousAddress->previous_address_line_1.' '.$previousAddress->previous_address_line_2.' '.$previousAddress->previous_address_town.' '.$previousAddress->previous_address_county.' '.$previousAddress->previous_address_postcode.' '.$previousAddress->previous_address_country;
                $count++;
            }
        }

        return view('applicant.dbsStep4', ['DBSApplication' => $DBSApplication, 'countries' => $countries, 'loggedIntervals' => $loggedIntervals]);
        
    }

    /**
     * DBS check for 5 years of address history
     *
     * 
     */

    public function checkFor5YearsOfAddressHistory()
    { 

        //if 5 years of more than redirect to step 5
        //start with current address start date
  

        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();

        $loggedIntervals = [];
        $loggedIntervals[0]['addressID'] = 0;
        $loggedIntervals[0]['start'] = $DBSApplication->current_address_from;
        $loggedIntervals[0]['end'] = date("Y-m-d");

        $previousAddresses = \DB::table('application_previous_addresses')->select('*')->where(['applicationID'=>$DBSApplication->id])->orderBy('previous_address_from', 'desc')->get();


        if(isset($previousAddresses) && count($previousAddresses)>0){
            $count = 1;
            foreach($previousAddresses as $previousAddress){
                $loggedIntervals[$count]['addressID'] = $previousAddress->id;
                $loggedIntervals[$count]['start'] = $previousAddress->previous_address_from;
                $loggedIntervals[$count]['end'] = $previousAddress->previous_address_to;
                $count++;
            }
        }


        $today = date("Y-m-d");
        $fiveYearsLimit = date("Y-m-d",strtotime('- 5 years'));
        $fiveYearsLimit = date("Y-m-d",strtotime($fiveYearsLimit.' + 1 day'));


        //test for gaps and 5 years address
        $testAddress= true;
        $lastIntervalDate = date("Y-m-d",strtotime('- 1 day'));
        foreach ($loggedIntervals as $key => $loggedInterval){
            if($loggedInterval['end'] >= $lastIntervalDate && $loggedInterval['start'] < $lastIntervalDate){
                $lastIntervalDate = date("Y-m-d",strtotime($loggedInterval['start'].' - 1 day'));
            } else {
                $testAddress= false;
                //echo 'fail '.$loggedInterval['start'].' - '.$loggedInterval['end'].' -- '.$lastIntervalDate.'<br />';
            }
        }
        if ($lastIntervalDate<=$fiveYearsLimit){
            //
        } else {
            $testAddress = false;
        }

        return $testAddress;

    }

        public function addPreviousAddress(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $DBSApplication = \DB::table('applications')->select('applications.applicationStatus', 'applications.id')->where(['applications.id'=>$requestVars['applicationID']])->first();
            if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }

            $dateFromRaw = explode('/',$requestVars['previous_address_from']);
            $dateFrom = $dateFromRaw[2] . '-' . $dateFromRaw[1] . '-' . $dateFromRaw[0];

            $dateToRaw = explode('/',$requestVars['previous_address_until']);
            $dateTo = $dateToRaw[2] . '-' . $dateToRaw[1] . '-' . $dateToRaw[0];

            //check for overlapping
            //To be added a second layer of verification

            $otherNames = array(
                'applicationID'     => $requestVars['applicationID'],
                'previous_address_line_1' => $requestVars['previous_address_line_1'],
                'previous_address_line_2' => $requestVars['previous_address_line_2'],
                'previous_address_town' => $requestVars['previous_address_town'],
                'previous_address_county' => $requestVars['previous_address_county'],
                'previous_address_postcode' => preg_replace('/\s+/', ' ',$requestVars['previous_address_postcode']),
                'previous_address_country' => $requestVars['previous_address_country'],
                'previous_address_from' => $dateFrom,
                'previous_address_to' => $dateTo,
            );
           
            $pastAddressID = \DB::table('application_previous_addresses')->insertGetId($otherNames);
            if(isset($pastAddressID) && $pastAddressID>0){
                 echo json_encode(['status' => 1, 'pastAddressID' => $pastAddressID]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

    }

    public function removePreviousAddress(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){

            //check status
            $DBSApplication = \DB::table('applications')->select('applications.applicationStatus', 'applications.id')->where(['applications.id'=>$requestVars['applicationID']])->first();
            if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }

            $testDelete = \DB::table('application_previous_addresses')->where(['id' => $requestVars['addressID'], 'applicationID' => $requestVars['applicationID']])->delete();

            if(isset($testDelete) && $testDelete){
                 echo json_encode(['status' => 1]);
            } else echo json_encode(['status' => 0]);
        } else echo json_encode(['status' => 0]);

        
    }

    public function saveStep4Data(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        //check status
        $DBSApplication = \DB::table('applications')->select('applications.applicationStatus', 'applications.id')->where(['applications.userID'=>$loggedUserID])->first();
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ return redirect('login/'); }

        return $this->dbsStep5();

     }

    /**
     * DBS application step5
     *
     * 
     */

    public function dbsStep5()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();

        //check status
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ return redirect('/login'); }

        //check for 5 years of history
        $test5Years = $this->checkFor5YearsOfAddressHistory();

        if(!$test5Years) return redirect("/applicant/dbsStep4")->with('errorMessage', 'You have a gap in your address history! Please review the dates and make sure you have covered 5 years of address history');;

        $countries = \DB::table('countries')
            ->select('*')
            ->orderByRaw('id=225 DESC')
            ->orderBy('nicename', 'asc')
            ->get();

        $DBSApplication->address_country_fullName = \DB::table('countries')->select('name')->where(['iso3'=>$DBSApplication->address_country])->first()->name;

        //supporting documents
        $DBSApplication->supporting_documents = \DB::table('application_supporting_documents')->select('*')->where(['applicationID' => $DBSApplication->id])->orderBy('document_path', 'desc')->get();

        return view('applicant.dbsStep5', ['DBSApplication' => $DBSApplication, 'countries' => $countries]);
        
    }


    public function saveStep5Data(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $loggedUserOrganisationID = Auth::user()->organisationID;

        //check status
        $DBSApplication = \DB::table('applications')->select('applications.applicationStatus', 'applications.id')->where(['applications.userID'=>$loggedUserID])->first();
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ return redirect('/login'); }

        $requestVars = $request->all();
        $validateArray = [
            'dbs_consent'        => 'accepted',
            
        ];
        //$this->validate($request, $validateArray); 
        $datePattern = '/^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/';

        $dlnIssueDate = null;
        $passportIssueDate = null;
        if(isset($requestVars['user_supporting_dln_issue_date']) && strlen($requestVars['user_supporting_dln_issue_date']) == 10 && preg_match($datePattern, $requestVars['user_supporting_dln_issue_date'])){
            $dlnIssueDateRaw = explode('/',$requestVars['user_supporting_dln_issue_date']);
            $dlnIssueDate = $dlnIssueDateRaw[2] . '-' . $dlnIssueDateRaw[1] . '-' . $dlnIssueDateRaw[0];
        }
        if(isset($requestVars['user_supporting_passport_date']) && strlen($requestVars['user_supporting_passport_date']) == 10 && preg_match($datePattern, $requestVars['user_supporting_passport_date'])){
            $passportIssueDateRaw = explode('/',$requestVars['user_supporting_passport_date']);
            $passportIssueDate = $passportIssueDateRaw[2] . '-' . $passportIssueDateRaw[1] . '-' . $passportIssueDateRaw[0];
        }
        

        //get app id
        $applicationID = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first()->id;
        //save new data
        $step5ata = array(
            'percentCompleted'              => 100,
            'applicationStatus'             => 1,
            'applicationCompleted'          => date("Y-m-d H:i:s"),
            'supporting_nino'               => $requestVars['user_supporting_nino'],
            'supporting_dln_type'           => $requestVars['supporting_dln_type'],
            'supporting_dln'                => strtoupper($requestVars['user_supporting_dln']),
            'supporting_dln_issue_date'     => $dlnIssueDate,
            'supporting_passport'           => $requestVars['user_supporting_passport'],
            'supporting_passport_country'   => $requestVars['user_supporting_passport_country'],
            'supporting_passport_date'      => $passportIssueDate,
            'supporting_paper_certificate'  => (isset($requestVars['user_supporting_paper_certificate']) && $requestVars['user_supporting_paper_certificate']) ? 1:0,
            'dbs_consent'                   => (isset($requestVars['user_dbs_consent']) && $requestVars['user_dbs_consent']) ? 1:0,
            'dbs_consent_date'              => date("Y-m-d H:i:s"),
        );
        if (isset($requestVars['user_supporting_paper_certificate']) && $requestVars['user_supporting_paper_certificate']){
            $step5ata['user_paper_certificate_different_address'] = (isset($requestVars['user_paper_certificate_different_address']) && $requestVars['user_paper_certificate_different_address']) ? 1:0;
        }
        if (isset($requestVars['user_paper_certificate_different_address']) && $requestVars['user_paper_certificate_different_address']){
            $step5ata['certificate_address_recipient_name'] = $requestVars['certificate_address_recipient_name'];
            $step5ata['certificate_address_line_1']         = $requestVars['certificate_address_line_1'];
            $step5ata['certificate_address_line_2']         = $requestVars['certificate_address_line_2'];
            $step5ata['certificate_address_town']           = $requestVars['certificate_address_town'];
            $step5ata['certificate_address_county']         = $requestVars['certificate_address_county'];
            $step5ata['certificate_address_postcode']       = preg_replace('/\s+/', ' ',$requestVars['certificate_address_postcode']);
            $step5ata['certificate_address_country']        = $requestVars['certificate_address_country'];
        } else {
            $step5ata['certificate_address_recipient_name'] = null;
            $step5ata['certificate_address_line_1']         = null;
            $step5ata['certificate_address_line_2']         = null;
            $step5ata['certificate_address_town']           = null;
            $step5ata['certificate_address_county']         = null;
            $step5ata['certificate_address_postcode']       = null;
            $step5ata['certificate_address_country']        = null;
        }

        if (isset($requestVars['user_dbs_profile_id_available']) && $requestVars['user_dbs_profile_id_available']){
            $step5ata['user_dbs_profile_id_available'] = (isset($requestVars['user_dbs_profile_id_available']) && $requestVars['user_dbs_profile_id_available']) ? 1:0;
            $step5ata['user_dbs_profile_id'] = $requestVars['user_dbs_profile_id'];
        }

        $updateApplicationDetails = \DB::table('applications')->where(['id' => $applicationID])->update($step5ata);

        //add notification
            $requestingUserDetails = \DB::table('users')->select('*')->where('id', '=', $loggedUserID)->first();
            
            $organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', $loggedUserOrganisationID)->first()->organisationName;

            $notificationDetails = [
                'title' => 'New DBS Submission - '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName,
                'body' => 'A new DBS application was submitted by '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName.', email: '.$requestingUserDetails->email.' and it is awaiting review<br />Organisation: '.$organisationName.'<br /><br />Click here to view the request: <a href="'.env('APP_URL').'applications/viewApplicationDetails/'.$applicationID.'" target="_blank">DBS Application details</a><br /><br />',
                'category' => 0,
                'relatedUserId' => $loggedUserID,
                'relatedAction' => 'New DBS submission',

                'emailTemplate' => 'notification_dbs_submission',
                'emailTitle' => 'New DBS Submission',
                'emailBody' => 'A new DBS application was submitted by '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName.', email: '.$requestingUserDetails->email.' and it is awaiting review<br />Organisation: '.$organisationName.'<br /><br />Click here to view the request: <a href="'.env('APP_URL').'applications/viewApplicationDetails/'.$applicationID.'" target="_blank">DBS Application details</a><br /><br />',
            ];
            $this->addNotification('superuser', $loggedUserOrganisationID, $notificationDetails, true);
            $this->addNotification('siteuser', $loggedUserOrganisationID, $notificationDetails, true);
        return $this->submitApplication();
        
    }
    /**
     * DBS application submit
     *
     * 
     */

    public function submitApplication()
    {   
        if (!Auth::check()) return redirect("/login");

        $bpssRequired = \DB::table('users')->select('users.dbsApplication', 'users.bpssApplication', 'users.useYoti')->where(['users.id'=>Auth::user()->id])->first();

        return view('applicant.applicationSubmitted', ['bpssRequired' => $bpssRequired]);
        
    }




    /**
     * logout user.
     *
     * @return Response
     */
    public function logout()
    {   
        if (Auth::check()){ 
            Auth::logout();
            return redirect('/applicant');
        } else {
            return view('applicant.login');
        }
        
    }

    /**
     * display the forgot password form.
     *
     * @return Response
     */
    public function forgotPassword()
    {   
        

        if (Auth::check()){ 
            return redirect('/login');
        } else {
            return view('applicant.forgotPassword');
        }
        
    }
    
    

    public function uploadSupportingDocument(Request $request)
    {
        if (!Auth::check()) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['applicationID']>0){
            $applicationID = $requestVars['applicationID'];
            $document_category = $requestVars['new_document_category'];
            //check status
            $DBSApplication = \DB::table('applications')->select('applications.applicationStatus', 'applications.id')->where(['applications.id'=>$requestVars['applicationID']])->first();
            if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ echo json_encode(['status' => 0]); }
 
            //save document
            if ($request->hasFile('new_document')) {
                $doc = $request->file('new_document');
                $realname = pathinfo($request->file('new_document')->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $doc->getClientOriginalExtension();
                $new_name = $applicationID.'_'.date("YmdHis").'_'.$realname.'.'.strtolower($extension);

                \Storage::disk('public_images')->put('supporting_documents'.'/'.$new_name, file_get_contents($doc), 'public');
                if (strtolower($extension) == 'png') {
                    $document_type = 'png';
                } else if (strtolower($extension) == 'jpeg' || strtolower($extension) == 'jpg' || strtolower($extension) == 'gif' || strtolower($extension) == 'png' ){
                    $document_type = 'img';
                } else if (strtolower($extension) == 'pdf' ){
                    $document_type = 'pdf';
                }

                $docDetails = array(
                    'applicationID'     => $requestVars['applicationID'],
                    'document_name' => $realname,
                    'document_type' => $document_type,
                    'document_category' => $document_category,
                    'document_path' => $new_name,
                );
               
                $uploadedDocID = \DB::table('application_supporting_documents')->insertGetId($docDetails);
                if(isset($uploadedDocID) && $uploadedDocID>0){
                     echo json_encode(['status' => 1, 'uploadedDocID' => $uploadedDocID, 'docType' => $document_type, 'docName' => $realname]);
                } else echo json_encode(['status' => 0]);
            }
        } else echo json_encode(['status' => 0]);        
    }

    public function removeSupportingDocument($docId = 0)
    {
        if (!Auth::check()) return redirect("/login");

        $loggedUserID = Auth::user()->id;
        $DBSApplication = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->select('applications.*')
            ->where(['applications.userID'=>$loggedUserID])
            ->first();

        //check status
        if (isset($DBSApplication->id) && !empty($DBSApplication->id) && $DBSApplication->applicationStatus != 0){ return redirect('/login'); }

        //get doc details
        $docDetails = \DB::table('application_supporting_documents')
            ->select('*')
            ->where(['id'=>$docId])
            ->first();
            if(!isset($docDetails->id) || $docDetails->applicationID != $DBSApplication->id) { return redirect('/login'); }

        //remove the file from DB
        \Storage::disk('public_images')->delete('supporting_documents/'.$docDetails->document_path);

        //remove from DB
        $testDelete = \DB::table('application_supporting_documents')->where(['id' => $docDetails->id, 'applicationID' => $DBSApplication->id])->delete();

        if(isset($testDelete) && $testDelete){
             echo json_encode(['status' => 1]);
        } 
        else echo json_encode(['status' => 0]);      
    }

    public function sendReferenceEmail(Request $request)
    {
        $requestVars = $request->all();

        $referenceEmail = $requestVars['referenceEmail'];
        $applicationId = $requestVars['applicationId'];

        $bpssDetails = \DB::table('bpss_applications')
            ->select('*')
            ->where(['id'=>$applicationId])
            ->orderBy('id', 'desc')
            ->first();

        if(isset($bpssDetails->id) && !empty($bpssDetails->id)){
            $userDetails = \DB::table('users')
            ->select('*')
            ->where(['id'=>$bpssDetails->userID])
            ->first();

            $bpssVRDetails = \DB::table('bpss_vr')
            ->select('*')
            ->where(['userid'=>$bpssDetails->userID])
            ->orderBy('id', 'desc')
            ->first();

            if(isset($bpssVRDetails->id) && !empty($bpssVRDetails->id)){
                //get referenve details
                $refDetails = \DB::table('bpss_vr_references')
                ->select('*')
                ->where(['verificationRecordID'=>$bpssVRDetails->id, 'referee_email' => $referenceEmail])
                ->orderBy('id', 'desc')
                ->first();

                if(isset($refDetails->id) && !empty($refDetails->id)){
                    //check if there's a reference email already sent and mark as not active the old email. Generate new code
                    $activeReferenceReq = \DB::table('bpss_vr_references_log')
                        ->select('*')
                        ->where(['verificationRecordID' => $bpssVRDetails->id, 'referenceID' => $refDetails->id, 'referenceStatus' => 1])
                        ->get();
                    foreach ($activeReferenceReq as $key => $refLine) {
                        \DB::table('bpss_vr_references_log')->where(['id' => $refLine->id])->update(['referenceStatus' => 3]);
                    }

                    //insert new reference request record
                    $randomCode =  $this->generateRandomKey(36);
                    \DB::table('bpss_vr_references_log')->insert([
                        'verificationRecordID' => $bpssVRDetails->id, 
                        'referenceID' => $refDetails->id,
                        'uniqueCode' => $randomCode,
                        'referenceStatus' => 1,
                        'referenceType' => null,
                        'createdOn' => date("Y-m-d H:i:s"),
                        'createdBy' => Auth::user()->id,
                        'valid' => 0, // 0- no expiration
                    ]);

                    //send email
                    $emailDetails = new \stdClass();
                    $emailDetails->firstname = $userDetails->firstName;
                    $emailDetails->lastname = $userDetails->lastName;
                    $emailDetails->referenceLink = env('APP_URL').'reference/'.$randomCode;

                    $hour = date("G"); 
                    $minute = date("i"); 
                    $second = date("s");

                    if ( (int)$hour <= 11 ) { 
                        $greet = "morning"; 
                    } else if ( (int)$hour >= 11 && (int)$hour <= 15 ) { 
                        $greet = "afternoon"; 
                    } else if ( (int)$hour >= 16) { 
                        $greet = "evening"; 
                    } else { 
                        $greet = "day"; 
                    }
                    $emailDetails->timeofday = $greet;
                    $emailTo = $refDetails->referee_email;
                    Mail::send('email_templates.reference_initial_email', [
                        'targetEmail' => $emailTo,
                        'details' =>$emailDetails
                        ], 
                        function ($message) use ($emailTo, $userDetails) {
                            $message->to($emailTo);
                            $message->subject('Personal Reference Request For '. $userDetails->firstName.' '.$userDetails->lastName);
                        }
                    );

                    return 1;
                    exit;
                } else { return null; }
            } else { return null; }
        } else { return null; }
       
        return null;

    }

    public function submitReference($referenceCode = null)
    {   
        if(isset($referenceCode) && !empty($referenceCode)){
            $refLogDetails = \DB::table('bpss_vr_references_log')
                ->select('*')
                ->where(['uniqueCode'=>$referenceCode, 'referenceStatus' => 1])
                ->orderBy('id', 'desc')
                ->first();
            if(isset($refLogDetails->id) & !empty($refLogDetails->id)){
                //get user details
                $userDetails = \DB::table('bpss_vr')
                ->join('users', 'users.id', '=', 'bpss_vr.userID')
                ->select('users.*')
                ->where(['bpss_vr.id'=>$refLogDetails->verificationRecordID])
                ->first();

                return view('applicant.submitReference', ['referenceCode' => $referenceCode, 'userDetails' => $userDetails]);
            } else {
                return view('applicant.submitReferenceExpired');
            }
        } else {
            return view('applicant.submitReferenceExpired');
        }
        
    }


    public function submitReferenceDetails(Request $request)
    {   
        $requestVars = $request->all();
        $referenceCode = $requestVars['referenceCode'];
        $candidate_relate = (isset($requestVars['candidate_relate']) && strtolower(trim($requestVars['candidate_relate'])) == 'yes') ? 1 : 0;
        $candidate_relatedetails = $requestVars['candidate_relatedetails'];
        $candidate_period_known_from = $requestVars['candidate_period_known_from'];
        $candidate_period_known_to = $requestVars['candidate_period_known_to'];
        $candidate_natureofaq = $requestVars['candidate_natureofaq'];
        $candidate_subjecthonest = $requestVars['candidate_subjecthonest'];
        $candidate_factorsconcerning = $requestVars['candidate_factorsconcerning'];
        $reference_fullname = $requestVars['reference_fullname'];
        $reference_contactaddress = $requestVars['reference_contactaddress'];
        $reference_contacttelephone = $requestVars['reference_contacttelephone'];
        $reference_email = $requestVars['reference_email'];

//echo'<pre>';print_r($requestVars);echo'</pre>';
        if(isset($referenceCode) && strlen($referenceCode)>1){
            $refLogDetails = \DB::table('bpss_vr_references_log')
                    ->select('*')
                    ->where(['uniqueCode'=>$referenceCode, 'referenceStatus' => 1])
                    ->orderBy('id', 'desc')
                    ->first();

            $bpssVRDetails = \DB::table('bpss_vr')
            ->select('*')
            ->where(['id'=>$refLogDetails->verificationRecordID])
            ->orderBy('id', 'desc')
            ->first();

            $userDetails = \DB::table('users')
            ->select('*')
            ->where(['id'=>$bpssVRDetails->userID])
            ->first();

            if (isset($requestVars['candidate_dob']) && date("Y-m-d", strtotime(str_replace('/', '-', $requestVars['candidate_dob']))) != '1970-01-01'){
                $candidateDobRaw = explode('/',$requestVars['candidate_dob']);
                $candidateDob = $candidateDobRaw[2] . '-' . $candidateDobRaw[1] . '-' . $candidateDobRaw[0];
            } else $candidateDob = null;

            if (isset($requestVars['candidate_period_known_from']) && date("Y-m-d", strtotime(str_replace('/', '-', $requestVars['candidate_period_known_from']))) != '1970-01-01'){
                $preriodKnownFromRaw = explode('/',$requestVars['candidate_period_known_from']);
                $preriodKnownFrom = $preriodKnownFromRaw[2] . '-' . $preriodKnownFromRaw[1] . '-' . $preriodKnownFromRaw[0];
            } else $preriodKnownFrom = null;

            if (isset($requestVars['candidate_period_known_to']) && date("Y-m-d", strtotime(str_replace('/', '-', $requestVars['candidate_period_known_to']))) != '1970-01-01'){
                $preriodKnownToRaw = explode('/',$requestVars['candidate_period_known_to']);
                $preriodKnownTo = $preriodKnownToRaw[2] . '-' . $preriodKnownToRaw[1] . '-' . $preriodKnownToRaw[0];
            } else $preriodKnownTo = null;

            $refId = \DB::table('bpss_vr_references_forms')->insert([
                            'verificationRecordID' => $refLogDetails->verificationRecordID, 
                            'referenceID' => $refLogDetails->referenceID,
                            'reference_nameOfCandidate' => $userDetails->firstName.' '.$userDetails->lastName,
                            'reference_dobCandidate' => $candidateDob,
                            'reference_relationYesNo' => $candidate_relate,
                            'reference_relationCandidate' => $candidate_relatedetails,
                            'reference_preriodKnownFrom' => $preriodKnownFrom,
                            'reference_periodKnownTo' => $preriodKnownTo,
                            'reference_natureOfAq' => $candidate_natureofaq,
                            'reference_subjectHonest' => $candidate_subjecthonest,
                            'reference_factorsConcerning' => $candidate_factorsconcerning,
                            'referenceDetails_fullName' => $reference_fullname,
                            'referenceDetails_contactAddress' => $reference_contactaddress,
                            'referenceDetails_contactTelephome' => $reference_contacttelephone,
                            'referenceDetails_contactEmail' => $reference_email,
                            'reference_logID' => $refLogDetails->id,
                            'createdOn' => date("Y-m-d H:i:s")
                        ]);
            if(isset($refId) && !empty($refId)){
                $updateRefLog = \DB::table('bpss_vr_references_log')->where(['id' => $refLogDetails->id])->update(['referenceStatus' => 2]);

                //add notification                            
                $notificationDetails = [
                    'title' => 'A personal reference has been submitted for - '.$userDetails->firstName.' '.$userDetails->lastName,
                    'body' => 'A personal reference has successfuly been submited for '.$userDetails->firstName.' '.$userDetails->lastName.', email: '.$userDetails->email,
                    'category' => 0,
                    'relatedUserId' => $userDetails->id,
                    'relatedAction' => 'New Personal Reference submission',
                    'emailTemplate' => 'notification_new_user_signup',
                    'emailTitle' => 'New Personal Reference submission',
                    'emailBody' => 'A personal reference has successfuly been submited for '.$userDetails->firstName.' '.$userDetails->lastName.', email: '.$userDetails->email,
                ];
                $this->addNotification('superuser', $userDetails->organisationID, $notificationDetails, true);

                return view('applicant.submitReferenceComplete');
            } else {
                return view('applicant.submitReferenceCompleteError');
            }
        } else {
            return view('applicant.submitReferenceCompleteError');
        }
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'applicantID' => 'required|integer|exists:applicants,id',
            'newEmail' => 'required|email|unique:applicants,email'
        ]);

        $applicantID = $request->input('applicantID');
        $newEmail = $request->input('newEmail');

        \DB::table('applicants')
            ->where('id', $applicantID)
            ->update(['email' => $newEmail]);

        return response()->json(['success' => true]);
    }

    protected $docScanClient;

    public function __construct(DocScanClient $docScanClient)
    {
        $this->docScanClient = $docScanClient;
    }


    public function yotiPage()
    {
        try {
            $loggedUserID = Auth::id();

            $subject = (object) [
                'subject_id' => 'classified_user_' . $loggedUserID,
            ];

            $identityProfileRequirements = (object) [
                'trust_framework' => 'UK_TFIDA',
                'scheme' => [
                    'type' => 'DBS_RTW',
                    'objective' => 'BASIC',
                ],
            ];

            $user = \DB::table('applications')
                ->where('userID', $loggedUserID)
                ->first();

            if (!$user) {
                return response()->json([
                    'error' => 'No application record found for this user.'
                ], 422);
            }

            // -----------------------------
            // SDK config + base session spec
            // -----------------------------
            $sdkConfig = (new SdkConfigBuilder())
                ->withAllowHandoff(true)
                ->withLocale('en-GB')
                ->withPresetIssuingCountry('GBR')
                ->withSuccessUrl(rtrim(env('APP_URL'), '/') . '/success')
                ->withErrorUrl(rtrim(env('APP_URL'), '/') . '/error')
                ->withPrimaryColour('#2c3c64')
                ->build();

            $baseBuilder = (new SessionSpecificationBuilder())
                ->withClientSessionTokenTtl(900)
                ->withResourcesTtl(7678400)
                ->withUserTrackingId('ClassifIeD:' . $loggedUserID)
                ->withSubject($subject)
                ->withIdentityProfileRequirements($identityProfileRequirements)
                ->withSdkConfig($sdkConfig);

            $sessionSpec = $baseBuilder->build();
            $session = $this->docScanClient->createSession($sessionSpec);

            $sessionId = $session->getSessionId();
            $sessionToken = $session->getClientSessionToken();

            $iframeUrl = "https://api.yoti.com/idverify/v1/web/index.html?sessionID={$sessionId}&sessionToken={$sessionToken}";

            \DB::table('Yoti')->updateOrInsert(
                ['userId' => $loggedUserID],
                ['sessionId' => $sessionId, 'updated_at' => now()]
            );

            return view('applicant.yotiSession', compact('iframeUrl'));
        } catch (\Throwable $e) {
            \Log::error('Error creating Yoti DBS Basic session', [
                'message' => $e->getMessage(),
                'class'   => get_class($e),
                'code'    => $e->getCode(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to create YOTI session, please contact screening@thinkbitgroup.co.uk'
            ], 500);
        }
    }

    public function candidateIdvIframe()
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $loggedUserID = Auth::id();

            // Optional: enforce "useYoti" / form checks here if you want
            // (recommended to prevent people hitting the endpoint directly)
            $userDetails = \DB::table('users')->where('id', $loggedUserID)->first();
            if (!$userDetails || (int)($userDetails->useYoti ?? 0) !== 1) {
                return response()->json(['error' => 'Yoti not enabled for this user'], 403);
            }

            $subject = (object) [
                'subject_id' => 'classified_user_' . $loggedUserID,
            ];

            $identityProfileRequirements = (object) [
                'trust_framework' => 'UK_TFIDA',
                'scheme' => [
                    'type' => 'DBS',
                    'objective' => 'BASIC',
                ],
            ];

            $application = \DB::table('applications')
                ->where('userID', $loggedUserID)
                ->first();

            if (!$application) {
                return response()->json(['error' => 'No application record found for this user.'], 422);
            }

            $sdkConfig = (new SdkConfigBuilder())
                ->withAllowHandoff(true)
                ->withLocale('en-GB')
                ->withPresetIssuingCountry('GBR')
                ->withSuccessUrl(rtrim(env('APP_URL'), '/') . '/success')
                ->withErrorUrl(rtrim(env('APP_URL'), '/') . '/error')
                ->withPrimaryColour('#2c3c64')
                ->build();

            $sessionSpec = (new SessionSpecificationBuilder())
                ->withClientSessionTokenTtl(900)
                ->withResourcesTtl(7678400)
                ->withUserTrackingId('ClassifIeD:' . $loggedUserID)
                ->withSubject($subject)
                ->withIdentityProfileRequirements($identityProfileRequirements)
                ->withSdkConfig($sdkConfig)
                ->build();

            $session = $this->docScanClient->createSession($sessionSpec);

            $sessionId = $session->getSessionId();
            $sessionToken = $session->getClientSessionToken();

            $iframeUrl = "https://api.yoti.com/idverify/v1/web/index.html?sessionID={$sessionId}&sessionToken={$sessionToken}";

            \DB::table('Yoti')->updateOrInsert(
                ['userId' => $loggedUserID],
                ['sessionId' => $sessionId, 'updated_at' => now()]
            );

            return response()->json(['iframeUrl' => $iframeUrl]);

        } catch (\Throwable $e) {
            \Log::error('Error creating Yoti session (candidateIdvIframe)', [
                'message' => $e->getMessage(),
                'class'   => get_class($e),
                'code'    => $e->getCode(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to create YOTI session, please contact screening@thinkbitgroup.co.uk'
            ], 500);
        }
    }

    /*public function yotiPage()
    {
        try {
            $loggedUserID = Auth::user()->id;

            // Create the passport restriction
            $passportRestriction = (new \Yoti\DocScan\Session\Create\Filters\Document\DocumentRestrictionBuilder())
                //->withCountries(['GBR', 'IRL']) // UK and Ireland
                ->withDocumentTypes(['PASSPORT']) // Passport only
                ->build();

            $passportFilter = (new \Yoti\DocScan\Session\Create\Filters\Document\DocumentRestrictionsFilterBuilder())
                ->withDocumentRestriction($passportRestriction)
                ->forWhitelist() // Whitelist these restrictions
                ->build();

            $requiredPassportDocument = (new \Yoti\DocScan\Session\Create\Filters\RequiredIdDocumentBuilder())
                ->withFilter($passportFilter)
                ->build();

            // Create the driving license/photo ID restriction
            $photoIdRestriction = (new \Yoti\DocScan\Session\Create\Filters\Document\DocumentRestrictionBuilder())
                //->withCountries(['GBR', 'IRL']) // UK and Ireland
                ->withDocumentTypes(['DRIVING_LICENCE', 'NATIONAL_ID']) // Driving license or other photo ID
                ->build();

            $photoIdFilter = (new \Yoti\DocScan\Session\Create\Filters\Document\DocumentRestrictionsFilterBuilder())
                ->withDocumentRestriction($photoIdRestriction)
                ->forWhitelist() // Whitelist these restrictions
                ->build();

            $requiredPhotoIdDocument = (new \Yoti\DocScan\Session\Create\Filters\RequiredIdDocumentBuilder())
                ->withFilter($photoIdFilter)
                ->build();

            $documentComparisonCheck = (new RequestedIdDocumentComparisonCheckBuilder())->build();
            $thirdPartyIdentityCheck = (new RequestedThirdPartyIdentityCheckBuilder())->build();

            // Build the Watchlist Screening Check for AML
            $watchScreeningConfig = (new RequestedWatchlistScreeningConfigBuilder())
                ->withSanctionsCategory()
                ->withAdverseMediaCategory()
                ->build();

            $watchlistScreeningCheck = (new RequestedWatchlistScreeningCheckBuilder())
                ->withConfig($watchScreeningConfig)
                ->build();
                

            // Build the session specification with both required documents
            $sessionSpec = (new \Yoti\DocScan\Session\Create\SessionSpecificationBuilder())
                ->withClientSessionTokenTtl(900)
                ->withResourcesTtl(7678400)
                ->withUserTrackingId('ClassifIeD: ' . $loggedUserID)
                ->withRequestedCheck(
                    (new \Yoti\DocScan\Session\Create\Check\RequestedDocumentAuthenticityCheckBuilder())
                        ->withManualCheckFallback()
                        ->build()
                )
                ->withRequestedCheck(
                    (new \Yoti\DocScan\Session\Create\Check\RequestedLivenessCheckBuilder())
                        ->forStaticLiveness()
                        ->withMaxRetries(3)
                        ->build()
                )
                ->withRequestedCheck(
                    (new \Yoti\DocScan\Session\Create\Check\RequestedFaceMatchCheckBuilder())
                        ->withManualCheckFallback()
                        ->build()
                )
                ->withRequestedTask(
                    (new \Yoti\DocScan\Session\Create\Task\RequestedTextExtractionTaskBuilder())
                        ->withManualCheckFallback()
                        ->build()
                )
                ->withRequiredDocument($requiredPassportDocument)
                ->withRequiredDocument($requiredPhotoIdDocument)
                ->withRequestedCheck($documentComparisonCheck)
                ->withRequestedCheck($thirdPartyIdentityCheck)
                ->withRequestedCheck($watchlistScreeningCheck)
                

                ->withSdkConfig(
                    (new \Yoti\DocScan\Session\Create\SdkConfigBuilder())
                        ->withAllowsCamera()
                        ->withPrimaryColour('#2c3c64')
                        ->withLocale('en-GB')
                        ->withPresetIssuingCountry('GBR')
                        ->withSuccessUrl(env('APP_URL') . 'success')
                        ->withErrorUrl(env('APP_URL') . 'error')
                        ->withAllowHandoff(true)
                        ->withIdDocumentTextExtractionGenericAttempts(3)
                        ->build()
                )
                ->build();

            $session = $this->docScanClient->createSession($sessionSpec);

            // Retrieve session ID and token
            $sessionId = $session->getSessionId();
            $sessionToken = $session->getClientSessionToken();

            // Construct the user interface URL
            $iframeUrl = "https://api.yoti.com/idverify/v1/web/index.html?sessionID={$sessionId}&sessionToken={$sessionToken}";

            \DB::table('Yoti')->updateOrInsert(
                ['userId' => $loggedUserID],
                ['sessionId' => $sessionId, 'updated_at' => now()]
            );

            // Return iframe view to the user
            return view('applicant.yotiSession', compact('iframeUrl'));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error creating Yoti session: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            // Return an error response
            return response()->json(['error' => 'Failed to create Yoti session, please contact us at: screening@thinkbitgroup.co.uk'], 500);
        }
    }*/

    public function yotiSuccess() {
        
        $loggedUserID = Auth::user()->id;

        \DB::table('users')
            ->where('id', $loggedUserID)
            ->update(['useYoti' => 2]);

        \DB::table('Yoti')
            ->where('userId', $loggedUserID)
            ->update(['completed_at' => now()]);
        
        //add notification
        $requestingUserDetails = \DB::table('users')->select('*')->where('id', '=', $loggedUserID)->first();
                    
        $organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', $requestingUserDetails->organisationID)->first()->organisationName;

        $notificationDetails = [
            'title' => 'A user has completed YOTI - '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName,
            'body' => 'A user has successfuly completed their YOTI identity check. The user is '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName.', email: '.$requestingUserDetails->email.'.<br />Organisation: '.$organisationName,
            'category' => 0,
            'relatedUserId' => $loggedUserID,
            'relatedAction' => 'New YOTI submission',
            'emailTemplate' => 'notification_new_user_signup',
            'emailTitle' => 'New YOTI submission',
            'emailBody' => 'A user has successfuly completed their YOTI identity check. The user is '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName.', email: '.$requestingUserDetails->email.'.<br />Organisation: '.$organisationName,
        ];
        $this->addNotification('superuser', $requestingUserDetails->organisationID, $notificationDetails, true);

        return redirect("/login");
    }

    public function yotiError() {
        
        $loggedUserID = Auth::user()->id;
        
        \DB::table('users')
            ->where('id', $loggedUserID)
            ->update(['useYoti' => 3]);

         //add notification
         $requestingUserDetails = \DB::table('users')->select('*')->where('id', '=', $loggedUserID)->first();
                    
         $organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', $requestingUserDetails->organisationID)->first()->organisationName;
 
         $notificationDetails = [
             'title' => 'A user has failed YOTI - '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName,
             'body' => 'A user has failed completing their YOTI identity check. The user is '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName.', email: '.$requestingUserDetails->email.'.<br />Organisation: '.$organisationName,
             'category' => 0,
             'relatedUserId' => $loggedUserID,
             'relatedAction' => 'New YOTI submission',
             'emailTemplate' => 'notification_new_user_signup',
             'emailTitle' => 'New YOTI submission',
             'emailBody' => 'A user has failed completing their YOTI identity check. The user is '.$requestingUserDetails->firstName.' '.$requestingUserDetails->lastName.', email: '.$requestingUserDetails->email.'.<br />Organisation: '.$organisationName,
         ];
         $this->addNotification('superuser', $requestingUserDetails->organisationID, $notificationDetails, true);

        return redirect("/login");
    }

    public function yotiStart() {
        return view('applicant.yotiStart');
    }

    public function showYotiReport($userID)
    {
        if (!Auth::check()) return redirect("/login");
        if (!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) return redirect("/login");
        $payload = $this->buildYotiReportPayload($userID);

        if ($payload instanceof \Illuminate\Http\RedirectResponse) {
            return $payload;
        }

        return view('applicant.yotiSessionResults', $payload);
    }

    public function downloadYotiPdf($userID)
    {
        if (!Auth::check()) return redirect("/login");
        if (!$this->checkAccess('adminoperator') && !$this->checkAccess('siteuser')) return redirect("/login");

        set_time_limit(6000);
        ini_set('memory_limit', '-1');

        $payload = $this->buildYotiReportPayload($userID);

        if ($payload instanceof \Illuminate\Http\RedirectResponse) {
            return $payload;
        }

        // Nice filename base
        $candidateNameForFile = 'Candidate';
        try {
            $candidateNameForFile = preg_replace(
                '/[^A-Za-z0-9_-]+/',
                '_',
                trim((string)($payload['candidateSummary']['fullName'] ?? 'Candidate'))
            );
            $candidateNameForFile = $candidateNameForFile ?: 'Candidate';
        } catch (\Throwable $e) {}

        $originalTitle = 'YOTI_' . $candidateNameForFile . '.pdf';

        // Create Html2Pdf
        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [8,8,8,8]);
        $html2pdf->setTestIsImage(false);
        $html2pdf->pdf->SetTitle($originalTitle);

        $html2pdf->writeHTML(view('yotiReport_templates.yoti-report', $payload));

        // 1) Get PDF bytes (string)
        $pdfBinary = $html2pdf->output($originalTitle, 'S'); // 'S' = return as string

        // 2) Store to disk + DB (auto-upload)
        try {
            $realname = pathinfo($originalTitle, PATHINFO_FILENAME); // e.g. YOTI_Ben_Waters
            $extension = 'pdf';

            // Match your naming convention
            $new_name = "{$userID}_" . now()->format('YmdHis') . "_{$realname}_1.{$extension}";

            // Store the PDF
            Storage::disk('public_images')->put(
                'new_applicant_documents/' . $new_name,
                $pdfBinary,
                'public'
            );

            // Insert record
            \DB::table('new_applicant_documents')->insert([
                'applicantID'     => 0,
                'userID'          => $userID,
                'document_name'   => $realname,
                'document_type'   => 'pdf',
                'document_path'   => $new_name,
                'file_type'       => 'yoti',
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Failed to auto-save Yoti PDF to user files', [
                'user_id' => $userID,
                'error'   => $e->getMessage(),
            ]);
            // Don’t block the download if saving fails
        }

        // 3) Return the PDF to browser (download/inline as Html2Pdf decides)
        return response($pdfBinary, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$originalTitle.'"',
        ]);
    }

    /**
     * Single source of truth for BOTH screen + PDF.
     * Returns array payload OR RedirectResponse.
     */
    private function buildYotiReportPayload($userID)
    {
        $sessionRow = \DB::table('Yoti')
            ->select('sessionId', 'updated_at', 'completed_at')
            ->where('userId', '=', $userID)
            ->first();

        if (!$sessionRow) {
            return redirect()->back()->with('error', 'No session found for this user.');
        }

        $sessionId = $sessionRow->sessionId;

        $userDetails = \DB::table('applications')
            ->join('users', 'users.id', '=', 'applications.userID')
            ->leftJoin('organisations', 'organisations.id', '=', 'users.organisationId') // adjust if needed
            ->where('applications.userID', $userID)
            ->select([
                'applications.forename',
                'applications.presentSurname',
                'users.applicationCode',
                'organisations.logo',
                'organisations.organisationName',
            ])
            ->first();

        $userDetails->issueDate = \carbon\Carbon::parse(now())->format('d M Y');

        try {
            $sessionResult = $this->docScanClient->getSession($sessionId);
        } catch (\Throwable $e) {
            \Log::error('Yoti session fetch failed', [
                'user_id'    => $userID,
                'session_id' => $sessionId,
                'error'      => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Unable to fetch Yoti session results right now.');
        }

        // IMPORTANT: get latest application row (prevents stale-address mismatch)
        $applicationRow = \DB::table('applications')
            ->where('userID', $userID)
            ->orderByDesc('current_address_from')
            ->orderByDesc('id')
            ->first();

        $state           = $sessionResult->getState();
        $resources       = $sessionResult->getResources();
        $checks          = $sessionResult->getChecks();
        $identityProfile = $sessionResult->getIdentityProfile();

        // ---------------------------------------------
        // Helpers
        // ---------------------------------------------
        $isList = function ($arr): bool {
            if (!is_array($arr)) return false;
            $keys = array_keys($arr);
            return $keys === range(0, count($arr) - 1);
        };

        $formatAddress = function ($addr) {
            if (is_string($addr) && trim($addr) !== '') return trim($addr);

            if (is_array($addr)) {
                foreach (['formatted_address', 'formattedAddress', 'formatted'] as $k) {
                    if (!empty($addr[$k]) && is_string($addr[$k])) return trim($addr[$k]);
                }

                $parts = array_filter([
                    $addr['address_line_1'] ?? $addr['line1'] ?? $addr['building_number'] ?? null,
                    $addr['address_line_2'] ?? $addr['line2'] ?? null,
                    $addr['address_line_3'] ?? $addr['line3'] ?? null,
                    $addr['street'] ?? $addr['street_name'] ?? null,
                    $addr['town'] ?? $addr['city'] ?? $addr['post_town'] ?? null,
                    $addr['county'] ?? $addr['region'] ?? null,
                    $addr['postal_code'] ?? $addr['postcode'] ?? $addr['post_code'] ?? null,
                    $addr['country'] ?? $addr['country_iso'] ?? null,
                ], fn ($v) => is_string($v) && trim($v) !== '');

                if (!empty($parts)) return implode(', ', array_map('trim', $parts));
            }

            return null;
        };

        $findAddressDeep = function ($node) use (&$findAddressDeep, $formatAddress) {
            if (is_string($node)) {
                $s = trim($node);
                if ($s !== '' && (
                    preg_match('/\d+\s+\w+/', $s) ||
                    preg_match('/[A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2}/i', $s)
                )) {
                    return $s;
                }
                return null;
            }

            if (is_array($node)) {
                $formatted = $formatAddress($node);
                if ($formatted) return $formatted;

                foreach ($node as $k => $v) {
                    if (is_string($k) && preg_match('/address|postal|postcode|post_code|post_town|town|city/i', $k)) {
                        $hit = $findAddressDeep($v);
                        if ($hit) return $hit;
                    }
                }

                foreach ($node as $v) {
                    $hit = $findAddressDeep($v);
                    if ($hit) return $hit;
                }
            }

            return null;
        };

        // ---------------------------------------------
        // Documents + media
        // ---------------------------------------------
        $documentsData = [];
        $idDocuments   = $resources ? ($resources->getIdDocuments() ?: []) : [];

        $mediaUriCache = [];

        $getDataUri = function ($mediaId) use ($sessionId, &$mediaUriCache) {
            if (!$mediaId) return null;

            if (array_key_exists($mediaId, $mediaUriCache)) {
                return $mediaUriCache[$mediaId];
            }

            try {
                $media = $this->docScanClient->getMediaContent($sessionId, $mediaId);
            } catch (\Throwable $e) {
                \Log::warning('Yoti media fetch failed', [
                    'session_id' => $sessionId,
                    'media_id'   => $mediaId,
                    'error'      => $e->getMessage(),
                ]);
                return $mediaUriCache[$mediaId] = null;
            }

            if (!$media) return $mediaUriCache[$mediaId] = null;

            $mime = method_exists($media, 'getMimeType')
                ? ($media->getMimeType() ?: 'image/jpeg')
                : 'image/jpeg';

            if (method_exists($media, 'getBase64Content')) {
                $base64 = $media->getBase64Content();
                if ($base64) {
                    return $mediaUriCache[$mediaId] =
                        (strpos($base64, 'data:') === 0) ? $base64 : "data:{$mime};base64,{$base64}";
                }
            }

            if (method_exists($media, 'getContent')) {
                $raw = $media->getContent();
                if ($raw) {
                    if (is_string($raw) && strpos($raw, 'data:') === 0) {
                        return $mediaUriCache[$mediaId] = $raw;
                    }
                    return $mediaUriCache[$mediaId] = "data:{$mime};base64," . base64_encode($raw);
                }
            }

            return $mediaUriCache[$mediaId] = null;
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
                            } elseif (is_object($item)) {
                                if (method_exists($item, 'getId') && $item->getId()) {
                                    $ids[] = $item->getId();
                                } else {
                                    $arr = json_decode(json_encode($item), true);
                                    if (is_array($arr) && !empty($arr['id']) && is_string($arr['id'])) {
                                        $ids[] = $arr['id'];
                                    }
                                }
                            } elseif (is_array($item) && !empty($item['id']) && is_string($item['id'])) {
                                $ids[] = $item['id'];
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // fallback below
                }
            }

            if (empty($ids)) {
                try {
                    $arr = json_decode(json_encode($check), true);
                    if (is_array($arr) && !empty($arr['resources_used']) && is_array($arr['resources_used'])) {
                        foreach ($arr['resources_used'] as $id) {
                            if (is_string($id) && $id !== '') $ids[] = $id;
                        }
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            return array_values(array_unique($ids));
        };

        // ---------------------------------------------
        // Classify checks
        // ---------------------------------------------
        $allChecks               = is_array($checks) ? $checks : [];
        $livenessChecks          = [];
        $watchlistChecks         = [];
        $documentCandidateChecks = [];

        foreach ($allChecks as $check) {
            $type = strtolower((string) $check->getType());

            if (str_contains($type, 'liveness')) {
                $livenessChecks[] = $check;
                continue;
            }

            if (str_contains($type, 'watchlist')) {
                $watchlistChecks[] = $check;
                continue;
            }

            if (str_contains($type, 'profile') || str_contains($type, 'applicant')) {
                continue;
            }

            $documentCandidateChecks[] = $check;
        }

        // ---------------------------------------------
        // Build documents model with stable IDs
        // ---------------------------------------------
        $documentsById = [];
        $documentOrder = [];

        foreach ($idDocuments as $docIndex => $document) {
            $docId = null;

            if (method_exists($document, 'getId')) {
                $docId = $document->getId();
            } elseif (method_exists($document, 'getDocumentId')) {
                $docId = $document->getDocumentId();
            }

            if (!$docId) {
                $docId = 'doc_' . $docIndex . '_' . spl_object_id($document);
            }

            $documentOrder[] = $docId;

            $images = [];

            $idPhoto = method_exists($document, 'getDocumentIdPhoto') ? $document->getDocumentIdPhoto() : null;
            if ($idPhoto && method_exists($idPhoto, 'getMedia') && $idPhoto->getMedia()) {
                $uri = $getDataUri($idPhoto->getMedia()->getId());
                if ($uri) $images[] = ['label' => 'Document Photo', 'src' => $uri];
            }

            if (method_exists($document, 'getPages')) {
                $pages = $document->getPages();
                if (is_array($pages)) {
                    foreach ($pages as $idx => $page) {
                        if ($page && method_exists($page, 'getMedia') && $page->getMedia()) {
                            $uri = $getDataUri($page->getMedia()->getId());
                            if ($uri) {
                                $images[] = ['label' => 'Document Page ' . ($idx + 1), 'src' => $uri];
                            }
                        }
                    }
                }
            }

            // Document fields (IMPORTANT: normalise if Yoti returns document_fields list objects)
            $fieldsData = [];
            $docFields  = method_exists($document, 'getDocumentFields') ? $document->getDocumentFields() : null;

            if ($docFields && method_exists($docFields, 'getMedia') && $docFields->getMedia()) {
                try {
                    $fieldsMedia = $this->docScanClient->getMediaContent($sessionId, $docFields->getMedia()->getId());
                    if ($fieldsMedia && method_exists($fieldsMedia, 'getContent')) {
                        $raw     = $fieldsMedia->getContent();
                        $decoded = json_decode($raw, true);
                        $fieldsData = is_array($decoded) ? $decoded : [];

                        if (isset($fieldsData['document_fields']) && is_array($fieldsData['document_fields'])) {
                            $flat = [];
                            foreach ($fieldsData['document_fields'] as $field) {
                                if (!is_array($field)) continue;
                                $k = $field['field_type'] ?? $field['type'] ?? $field['name'] ?? null;
                                $v = $field['value'] ?? $field['parsed'] ?? $field['text'] ?? $field['content'] ?? null;
                                if ($k && $v !== null) $flat[$k] = $v;
                            }
                            if (!empty($flat)) $fieldsData = $flat;
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Yoti fields fetch failed', [
                        'session_id' => $sessionId,
                        'error'      => $e->getMessage(),
                    ]);
                }
            }

            $documentsById[$docId] = [
                'type'           => method_exists($document, 'getDocumentType') ? $document->getDocumentType() : 'ID Document',
                'issuingCountry' => method_exists($document, 'getIssuingCountry') ? $document->getIssuingCountry() : 'N/A',
                'images'         => $images,
                'fieldsData'     => $fieldsData,
                'documentId'     => $docId,
                'relevantChecks' => [],
            ];
        }

        // ---------------------------------------------
        // Deterministic check -> document mapping
        // ---------------------------------------------
        $validDocumentIds = array_fill_keys(array_keys($documentsById), true);

        foreach ($documentCandidateChecks as $check) {
            $resourceIds = $getCheckResourceIds($check);

            $matchedDocIds = [];
            foreach ($resourceIds as $rid) {
                if (isset($validDocumentIds[$rid])) $matchedDocIds[] = $rid;
            }

            $matchedDocIds = array_values(array_unique($matchedDocIds));
            if (!empty($matchedDocIds)) {
                foreach ($matchedDocIds as $docId) {
                    $documentsById[$docId]['relevantChecks'][] = $check;
                }
            }
        }

        foreach ($documentOrder as $docId) {
            if (isset($documentsById[$docId])) $documentsData[] = $documentsById[$docId];
        }

        // ---------------------------------------------
        // Liveness face image
        // ---------------------------------------------
        $faceImageBase64 = null;

        if ($resources && method_exists($resources, 'getLivenessCapture')) {
            $faceCaptures = $resources->getLivenessCapture();
            if (is_array($faceCaptures) && !empty($faceCaptures)) {
                $firstCapture = $faceCaptures[0];
                if ($firstCapture && method_exists($firstCapture, 'getFrames')) {
                    $frames = $firstCapture->getFrames();
                    if (is_array($frames) && !empty($frames)) {
                        $firstFrame = $frames[0];
                        if ($firstFrame && method_exists($firstFrame, 'getMedia') && $firstFrame->getMedia()) {
                            $faceImageBase64 = $getDataUri($firstFrame->getMedia()->getId());
                        }
                    }
                }
            }
        }

        $sessionLivenessChecks = method_exists($sessionResult, 'getLivenessChecks')
            ? ($sessionResult->getLivenessChecks() ?: [])
            : $livenessChecks;

        // ---------------------------------------------
        // Identity summary + candidate summary
        // ---------------------------------------------
        $identitySummary = [
            'frameworkType'      => 'N/A',
            'levelOfAssurance'   => 'N/A',
            'dbsObjective'       => 'N/A',
            'dbsRequirementsMet' => null,
            'rtwRequirementsMet' => null,
        ];

        $candidateSummary = [
            'fullName' => null,
            'dob'      => null,
            'address'  => null,
        ];

        try {
            if ($identityProfile && method_exists($identityProfile, 'getIdentityProfileReport')) {
                $ipReport = $identityProfile->getIdentityProfileReport();
                $arr      = json_decode(json_encode($ipReport), true);

                // If report points to media, prefer media content (more complete)
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
                            if (is_array($decoded)) $arr = $decoded;
                        }
                    } catch (\Throwable $e) {
                        \Log::warning('Yoti identity profile media fetch failed', [
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

                    // Candidate summary (identity assertion variants + deep scan fallback)
                    try {
                        $identityAssertion =
                            $root['identity_assertion']
                            ?? $arr['identity_assertion']
                            ?? $root['identity_assertions']
                            ?? $arr['identity_assertions']
                            ?? null;

                        if (is_array($identityAssertion) && $isList($identityAssertion)) {
                            $identityAssertion = $identityAssertion[0] ?? null;
                        }

                        if (is_array($identityAssertion)) {
                            // Name
                            $currentName = $identityAssertion['current_name'] ?? $identityAssertion['name'] ?? null;
                            if (is_array($currentName)) {
                                $parts = array_filter([
                                    $currentName['given_names'] ?? $currentName['givenNames'] ?? null,
                                    $currentName['family_name'] ?? $currentName['familyName'] ?? null,
                                ], fn ($v) => is_string($v) && trim($v) !== '');
                                if (!empty($parts)) $candidateSummary['fullName'] = trim(implode(' ', $parts));
                            }

                            // DOB
                            if (!empty($identityAssertion['date_of_birth']) && is_string($identityAssertion['date_of_birth'])) {
                                $candidateSummary['dob'] = $identityAssertion['date_of_birth'];
                            } elseif (!empty($identityAssertion['dob']) && is_string($identityAssertion['dob'])) {
                                $candidateSummary['dob'] = $identityAssertion['dob'];
                            }

                            // Address (support current_address/current_addresses/address/addresses + deep scan)
                            $addr =
                                $identityAssertion['current_address'] ?? null
                                ?? $identityAssertion['current_addresses'] ?? null
                                ?? $identityAssertion['address'] ?? null
                                ?? $identityAssertion['addresses'] ?? null;

                            if (is_array($addr) && $isList($addr)) {
                                $addr = $addr[0] ?? null;
                            }

                            $formatted = $formatAddress($addr);
                            if (!$formatted) $formatted = $findAddressDeep($identityAssertion);

                            if ($formatted) $candidateSummary['address'] = $formatted;
                        }
                    } catch (\Throwable $e) {
                        \Log::warning('Yoti candidate summary parse failed', [
                            'session_id' => $sessionId,
                            'error'      => $e->getMessage(),
                        ]);
                    }

                    // Verification report extraction
                    $vr = (isset($root['verification_report']) && is_array($root['verification_report']))
                        ? $root['verification_report']
                        : $root;

                    $identitySummary['frameworkType'] =
                        $vr['trust_framework'] ?? ($root['trust_framework'] ?? 'N/A');

                    // schemes_compliance
                    $schemes = $vr['schemes_compliance'] ?? [];
                    if (is_array($schemes)) {
                        foreach ($schemes as $item) {
                            if (!is_array($item)) continue;
                            $scheme = $item['scheme'] ?? [];
                            $type   = strtoupper((string)($scheme['type'] ?? ''));
                            $met    = $item['requirements_met'] ?? null;

                            if ($type === 'DBS') {
                                $identitySummary['dbsObjective'] = $scheme['objective'] ?? 'N/A';
                                if (is_bool($met)) $identitySummary['dbsRequirementsMet'] = $met;
                            }

                            if ($type === 'RTW' || $type === 'RTWR' || $type === 'RTW/R') {
                                if (is_bool($met)) $identitySummary['rtwRequirementsMet'] = $met;
                            }
                        }
                    }

                    $loa =
                        $vr['assurance_process']['level_of_assurance']
                        ?? $vr['assuranceProcess']['levelOfAssurance']
                        ?? $vr['level_of_assurance']
                        ?? $root['assurance_process']['level_of_assurance']
                        ?? null;

                    if (is_string($loa) && trim($loa) !== '') {
                        $identitySummary['levelOfAssurance'] = strtoupper(trim($loa));
                    }

                    // FINAL: if identity assertion didn’t yield address, deep scan the whole report once
                    if (empty($candidateSummary['address'])) {
                        $candidateSummary['address'] = $findAddressDeep($root) ?: null;
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Yoti identity summary parse failed', [
                'session_id' => $sessionId,
                'error'      => $e->getMessage(),
            ]);
        }

        // ---------------------------------------------
        // Compare Yoti identity vs DB application
        // ---------------------------------------------
        $identityComparison = [
            'name'    => ['db' => 'N/A', 'yoti' => 'N/A', 'match' => null, 'message' => 'No data'],
            'dob'     => ['db' => 'N/A', 'yoti' => 'N/A', 'match' => null, 'message' => 'No data'],
            'address' => ['db' => 'N/A', 'yoti' => 'N/A', 'match' => null, 'message' => 'No data'],
        ];

        $normText = function ($v): string {
            $v = mb_strtolower(trim((string)($v ?? '')));
            $v = preg_replace('/\s+/', ' ', $v);
            return $v;
        };

        $normPostcode = function ($v): string {
            return preg_replace('/[^a-z0-9]/', '', strtolower((string)($v ?? '')));
        };

        $normDob = function ($v): ?string {
            if ($v === null || $v === '') return null;
            try {
                return \Carbon\Carbon::parse($v)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        };

        // Pull first doc with usable fields
        $yotiGivenNames  = null;
        $yotiFirstName   = null;
        $yotiMiddleName  = null;
        $yotiLastName    = null;
        $yotiDobRaw      = null;

        $yAddrLine1         = null;
        $yAddrLine2         = null;
        $yAddrTown          = null;
        $yAddrPostcode      = null;
        $yotiAddressDisplay = null;

        foreach ($documentsData as $d) {
            if (empty($d['fieldsData']) || !is_array($d['fieldsData'])) continue;
            $f = $d['fieldsData'];

            $yotiGivenNames ??= $f['given_names'] ?? null;
            $yotiFirstName  ??= $f['first_name'] ?? $f['forenames'] ?? null;
            $yotiMiddleName ??= $f['middle_name'] ?? $f['middle_names'] ?? null;
            $yotiLastName   ??= $f['family_name'] ?? $f['last_name'] ?? $f['surname'] ?? null;
            $yotiDobRaw     ??= $f['date_of_birth'] ?? $f['dob'] ?? null;

            $addr =
                $f['structured_postal_address']
                ?? $f['structured_postal_address_list']
                ?? $f['postal_address']
                ?? $f['postal_address_list']
                ?? $f['address']
                ?? null;

            if (is_array($addr) && $isList($addr)) {
                $addr = $addr[0] ?? null;
            }

            $formatted = $formatAddress($addr);
            if ($formatted) {
                $yotiAddressDisplay = $yotiAddressDisplay ?: $formatted;
            }

            if (is_array($addr)) {
                $yAddrLine1    = $addr['address_line_1'] ?? $addr['line1'] ?? $yAddrLine1;
                $yAddrLine2    = $addr['address_line_2'] ?? $addr['line2'] ?? $yAddrLine2;
                $yAddrTown     = $addr['town'] ?? $addr['city'] ?? $addr['post_town'] ?? $yAddrTown;
                $yAddrPostcode = $addr['postal_code'] ?? $addr['postcode'] ?? $addr['post_code'] ?? $yAddrPostcode;
            } elseif (is_string($addr) && trim($addr) !== '') {
                $yotiAddressDisplay = $yotiAddressDisplay ?: trim($addr);
            }

            if (!$yotiAddressDisplay && $addr) {
                $yotiAddressDisplay = is_string($addr) ? $addr : json_encode($addr);
            }
        }

        // ALSO: if docs didn’t yield an address but candidateSummary did, use it
        if (!$yotiAddressDisplay && !empty($candidateSummary['address'])) {
            $yotiAddressDisplay = $candidateSummary['address'];
        }

        if (!$yotiAddressDisplay) $yotiAddressDisplay = 'N/A';

        // DB display
        $dbForename   = $applicationRow->forename ?? null;
        $dbMiddlename = $applicationRow->middlename ?? null;
        $dbSurname    = $applicationRow->presentSurname ?? null;
        $dbDobRaw     = $applicationRow->dob ?? null;

        $dbAddressLine1    = $applicationRow->address_line_1 ?? null;
        $dbAddressLine2    = $applicationRow->address_line_2 ?? null;
        $dbAddressTown     = $applicationRow->address_town ?? null;
        $dbAddressCounty   = $applicationRow->address_county ?? null;   // ignored in match
        $dbAddressPostcode = $applicationRow->address_postcode ?? null;
        $dbAddressCountry  = $applicationRow->address_country ?? null;  // ignored in match

        $dbAddressDisplayParts = array_filter([
            $dbAddressLine1, $dbAddressLine2, $dbAddressTown, $dbAddressCounty, $dbAddressPostcode, $dbAddressCountry
        ], fn ($v) => trim((string)$v) !== '');

        $dbAddressDisplay = !empty($dbAddressDisplayParts) ? implode(', ', $dbAddressDisplayParts) : 'N/A';

        // Name compare
        $dbNameFull   = trim(implode(' ', array_filter([$dbForename, $dbMiddlename, $dbSurname])));
        $yotiNameFull = trim(implode(' ', array_filter([($yotiGivenNames ?: $yotiFirstName), $yotiLastName])));

        $dbNameDisplay   = $dbNameFull !== '' ? $dbNameFull : 'N/A';
        $yotiNameDisplay = $yotiNameFull !== '' ? $yotiNameFull : 'N/A';

        $nameMatch = null;
        if ($dbForename || $dbSurname || $yotiLastName || $yotiGivenNames || $yotiFirstName) {
            $dbForenameNorm = $normText($dbForename);
            $dbSurnameNorm  = $normText($dbSurname);

            $yotiGiven       = $normText($yotiGivenNames ?: $yotiFirstName);
            $yotiSurnameNorm = $normText($yotiLastName);

            $givenTokens = preg_split('/[^a-z0-9]+/i', $yotiGiven, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $forenameOk  = ($dbForenameNorm !== '' && in_array($dbForenameNorm, $givenTokens, true));
            $surnameOk   = ($dbSurnameNorm !== '' && $yotiSurnameNorm !== '' && $dbSurnameNorm === $yotiSurnameNorm);

            $nameMatch = ($forenameOk && $surnameOk);
        }

        // DOB compare
        $dbDobNorm   = $normDob($dbDobRaw);
        $yotiDobNorm = $normDob($yotiDobRaw);

        $dbDobDisplay   = $dbDobNorm ? \Carbon\Carbon::parse($dbDobNorm)->format('d M Y') : 'N/A';
        $yotiDobDisplay = $yotiDobNorm ? \Carbon\Carbon::parse($yotiDobNorm)->format('d M Y') : 'N/A';

        $dobMatch = null;
        if ($dbDobNorm !== null || $yotiDobNorm !== null) {
            $dobMatch = ($dbDobNorm !== null && $yotiDobNorm !== null && $dbDobNorm === $yotiDobNorm);
        }

        // Address compare (IGNORE county + country)
        $dbL1   = $normText($dbAddressLine1);
        $dbL2   = $normText($dbAddressLine2);
        $dbTown = $normText($dbAddressTown);
        $dbPc   = $normPostcode($dbAddressPostcode);

        $yDisplayNorm = $normText($yotiAddressDisplay);
        $yPcNorm      = $normPostcode($yAddrPostcode);

        if ($yPcNorm === '' && $yotiAddressDisplay !== 'N/A') {
            if (preg_match('/([A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2})/i', $yotiAddressDisplay, $m)) {
                $yPcNorm = $normPostcode($m[1]);
            }
        }

        $addressMatch = null;
        if ($dbAddressDisplay !== 'N/A' || $yotiAddressDisplay !== 'N/A') {
            $line1Ok = ($dbL1 !== '' && str_contains($yDisplayNorm, $dbL1));
            $townOk  = ($dbTown !== '' && str_contains($yDisplayNorm, $dbTown));
            $pcOk    = ($dbPc !== '' && $yPcNorm !== '' && $dbPc === $yPcNorm);

            $line2Ok = true;
            if ($dbL2 !== '') $line2Ok = str_contains($yDisplayNorm, $dbL2);

            $addressMatch = ($line1Ok && $townOk && $pcOk && $line2Ok);
        }

        $identityComparison['name'] = [
            'db'      => $dbNameDisplay,
            'yoti'    => $yotiNameDisplay,
            'match'   => $nameMatch,
            'message' => $nameMatch === null ? 'No data' : ($nameMatch ? 'Match' : 'Mismatch'),
        ];

        $identityComparison['dob'] = [
            'db'      => $dbDobDisplay,
            'yoti'    => $yotiDobDisplay,
            'match'   => $dobMatch,
            'message' => $dobMatch === null ? 'No data' : ($dobMatch ? 'Match' : 'Mismatch'),
        ];

        $identityComparison['address'] = [
            'db'      => $dbAddressDisplay,
            'yoti'    => $yotiAddressDisplay ?: 'N/A',
            'match'   => $addressMatch,
            'message' => $addressMatch === null ? 'No data' : ($addressMatch ? 'Match' : 'Mismatch'),
        ];

        // FINAL: ensure candidateSummary address is never empty if we have *any* Yoti address display
        if (empty($candidateSummary['address']) && $yotiAddressDisplay !== 'N/A') {
            $candidateSummary['address'] = $yotiAddressDisplay;
        }

        // ✅ One payload used by BOTH screen + PDF
        return [
            'state'                 => $state,
            'resources'             => $resources,
            'checks'                => $checks,
            'identityProfile'       => $identityProfile,
            'sessionResult'         => $sessionResult,
            'documentsData'         => $documentsData,
            'faceImageBase64'       => $faceImageBase64,
            'userID'                => $userID,
            'sessionId'             => $sessionId,
            'createdAt'             => $sessionRow->updated_at,
            'completedAt'           => $sessionRow->completed_at,
            'sessionLivenessChecks' => $sessionLivenessChecks,
            'identitySummary'       => $identitySummary,
            'identityComparison'    => $identityComparison,
            'candidateSummary'      => $candidateSummary,
            'userDetails'           => $userDetails,

            // Optional if you later want it in the PDF template:
            'watchlistChecks'       => $watchlistChecks,
        ];
    }

    public function yotiSendBack($userID) {
        \DB::table('users')
            ->where('id', $userID)
            ->update(['useYoti' => 1]);

        return redirect('/login');
    }
}


