<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class User extends Controller
{

	/*
    |--------------------------------------------------------------------------
    | User Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling users and administrators
    | it contains the logic for creating a user (email/password)
    | add user details, view profile, edit profile and reset password
    | and forgot password.
    |
    */

    /**
     * Default load
     *
     * @return 404
     */

    public function index()
    {
        
        return view('404');
        
    }

    

    /**
     * Display the list of customers
     *
     * @return \Illuminate\Http\Response
     */

    public function viewUsersList()
    {   
        //only administrators can update user groups
        if(!$this->checkAccess('siteuser')) return redirect("/login");
        //get current user role
        $currentUserRoles = $this->getCurrentUserRoles();
        //get user organisations
        $userOrganisations = $this->getUserOrganisations();
        //get users details
        $allusers = \DB::table('users')->where(['userType' => 'admin']);
        if (array_search('superuser', $currentUserRoles) === false){
            $allusers = $allusers->whereIn('organisationID', $userOrganisations);
        }
        $allusers = $allusers->get();
        foreach($allusers as $key=>$user){
            $userRolesRaw = \DB::table('user_roles')->select('role')->where('userID', '=', $user->id)->get();
            $userRolesRaw = $userRolesRaw->toArray();
            $userRoles = array();
            foreach($userRolesRaw as $userRole){
                if (!in_array($userRole->role, $userRoles)) $userRoles[] = $userRole->role;
            }
            $allusers[$key]->userRoles = $userRoles;
            
            //Prevent error if teh organisation as been deleted.
            $checkOrg = \DB::table('organisations')->select('id')->where('id', '=', $user->organisationID)->first();
            if (isset($checkOrg->id) && is_numeric($checkOrg->id)) {
                    $allusers[$key]->organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', $user->organisationID)->first()->organisationName;
            } else {
                    $allusers[$key]->organisationName = 'ORGANISATION DELETED!!!';
            }

        }
        //echo'<pre>';print_r($allusers);echo'</pre>';

        return view('user.searchUsers', ['allusers' => $allusers]);
        
    }

    public function viewApplicantsList()
    {   
        //only administrators can update user groups
        if(!$this->checkAccess('siteuser')) return redirect("/login");
        //get users details
        $allusers = \DB::table('users')->where(['userType' => 'applicant']);
        $currentUserRoles = $this->getCurrentUserRoles();
        $userOrganisations = $this->getUserOrganisations();
        if (array_search('superuser', $currentUserRoles) === false){
            $allusers = $allusers->whereIn('organisationID', $userOrganisations);
        }

        $allusers = $allusers->get();
        foreach($allusers as $key=>$user){
            $allusers[$key]->organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', $user->organisationID)->first()->organisationName;
        }
        //echo'<pre>';print_r($allusers);echo'</pre>';

        return view('user.searchApplicants', ['allusers' => $allusers]);
        
    }

    /**
     * Update the user role
     *
     * @return json 
     */

    public function updateUserRole(Request $request)
    {
        //only administrators can update user groups
        if(!$this->checkAccess('superuser')) return redirect("/login");

        $requestVars = $request->all();
        $userID = $requestVars['userID'];
        $roleID = $requestVars['roleID'];
        $updateType = $requestVars['updateType'];

        if ($updateType == 'addRole'){
            \DB::table('user_roles')->insert(
                ['userID' => $userID, 'role' => $roleID]
            );
            return 1;
        } else if ($updateType == 'removeRole'){
            \DB::table('user_roles')->where(['userID' => $userID, 'role' =>$roleID])->delete();
            return 1;
        }
        return null;
    }

    /**
     * Update the extra user organisations
     *
     * @return json 
     */

    public function updateUserOrganisation(Request $request)
    {
        //only administrators can update user groups
        if(!$this->checkAccess('superuser')) return redirect("/login");

        $requestVars = $request->all();
        $userID = $requestVars['userID'];
        $organisationID = $requestVars['organisationID'];
        $updateType = $requestVars['updateType'];

        if ($updateType == 'addOrganisation'){
            \DB::table('user_organisations')->insert(
                ['userID' => $userID, 'organisationID' => $organisationID, 'assignedBy' => Auth::user()->id, 'assignedDate' => date('Y-m-d H:i:s')]
            );
            return 1;
        } else if ($updateType == 'removeOrganisation'){
            \DB::table('user_organisations')->where(['userID' => $userID, 'organisationID' =>$organisationID])->delete();
            return 1;
        }
        return null;
    }




    /**
     * Get User Details
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

   public function getUserDetails($id)
    {
        if($this->checkAccess('siteuser') || !isset($id) || ($id == Auth::user()->id)){
            $userID = (isset($id) && $id > 0) ? $id : Auth::user()->id;
            $userDetails = \DB::table('users')
                ->join('organisations', 'organisations.id', '=', 'users.organisationID')
                ->select('users.*', 'organisations.id AS organisations.organisationID', 'organisations.organisationName')
                ->where(["users.id" => $userID])
                ->first();
            $userRolesRaw = \DB::table('user_roles')->select('role')->where('userID', '=', $userID)->get();
            $userRolesRaw = $userRolesRaw->toArray();
            $userRoles = array();
            foreach($userRolesRaw as $userRole){
                if (!in_array($userRole->role, $userRoles)) $userRoles[] = $userRole->role;
            }
            $userDetails->userRoles = $userRoles;
            //echo'<pre>';print_r($userDetails);echo'</pre>';

            return $userDetails;
        }
        
    }

    /**
     * Administrator view of the user details
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

   public function viewProfile($id = null)
    {
        if($this->checkAccess('siteuser') || !isset($id) || ($id == Auth::user()->id)){
            $userID = (isset($id) && $id > 0) ? $id : Auth::user()->id;
            $userDetails = $this->getUserDetails($id);

            return view('user.viewProfile', ['userDetails' => $userDetails]);
        }
        
    }

    /**
     * Edit user profile
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

   public function editProfile($userID)
    {
        if($this->checkAccess('siteuser') || (Auth::user()->id == $userID)){
            $userDetails = $this->getUserDetails($userID);
            $availableOrganisations = \DB::table('organisations')->orderBy('organisationName', 'asc');
            $currentUserRoles = $this->getCurrentUserRoles();
            $userOrganisations = $this->getUserOrganisations($userID);
            $userExtraOrganisations = $userOrganisations;
            if (array_search($userDetails->organisationID, $userExtraOrganisations) !== false){
                unset($userExtraOrganisations[array_search($userDetails->organisationID, $userExtraOrganisations)]);
            }

            if (array_search('superuser', $currentUserRoles) === false){
                $availableOrganisations = $availableOrganisations->whereIn('id', $userOrganisations);
            }
            $availableOrganisations = $availableOrganisations->get();

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

            //get signature (if any)
            $signaturePath = '';
            $signature = \DB::table('user_signatures')->select('id', 'signature_path')->where(['userid'=>$userDetails->id])->first();
            if(isset($signature->id) && !empty($signature->id)){
                $signaturePath = $signature->signature_path;
            }
            return view('user.editProfile', ['userDetails' => $userDetails, 'userExtraOrganisations' => $userExtraOrganisations, 'availableOrganisations' => $availableOrganisations, 'signaturePath' => $signaturePath, 'currentTime' => time()]);
        }
        return null;
        
    }

    
     /**
     * update user details
     * 
     * @param  int  $id
     */

    public function updateUserDetails(Request $request)
    {
        $requestVars = $request->all();

        /*
        |--------------------------------------------------------------------------
        | Find the user being edited
        |--------------------------------------------------------------------------
        */

        $editedUser = \DB::table('users')
            ->where('id', $requestVars['userID'])
            ->first();

        if (!$editedUser) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Access Check
        |--------------------------------------------------------------------------
        */

        $userOrganisations = $this->getUserOrganisations();

        $canEdit =
            $this->checkAccess('superuser')
            || Auth::user()->id == $editedUser->id
            || in_array($editedUser->organisationID, $userOrganisations);


        if (!$canEdit) {
            abort(403);
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validateArray = [

            'userTitle' => [
                'nullable',
                'string',
                'max:50'
            ],

            'firstName' => [
                'required',
                'string',
                'max:50'
            ],

            'lastName' => [
                'required',
                'string',
                'max:50'
            ],

            'userEmail' => [
                'required',
                'email',
                'max:255',

                \Illuminate\Validation\Rule::unique('users', 'email')
                    ->ignore($editedUser->id)
            ],

            'password' => [
                'nullable',
                'confirmed',
                'min:14',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],

            'applicationCode' => [
                'nullable',
                'string',
                'max:100'
            ],

            'position' => [
                'nullable',
                'string',
                'max:50'
            ],

            'phoneNumber' => [
                'nullable',
                'string',
                'max:30'
            ],

            'signature' => [
                'nullable',
                'mimes:png'
            ],

        ];


        /*
        * Screening check values only need validating for applicants.
        */
        if ($editedUser->userType != 'admin') {

            $validateArray['dbsApplication'] = 'nullable|in:0,1';
            $validateArray['bpssApplication'] = 'nullable|in:0,1';
            $validateArray['REVALonsite'] = 'nullable|in:0,1';
            $validateArray['REVALoffsite'] = 'nullable|in:0,1';
            $validateArray['useYoti'] = 'nullable|in:0,1';

        }


        $messages = [

            'userEmail.unique' =>
                'email_exists',

            'password.min' =>
                'The password must be at least 14 characters long.',

            'password.confirmed' =>
                'The password and confirmation do not match.',

            'password.regex' =>
                'The password must contain an uppercase letter, lowercase letter, number and special character.',

            'signature.mimes' =>
                'The signature must be a PNG file.',

        ];


        $this->validate(
            $request,
            $validateArray,
            $messages
        );


        /*
        |--------------------------------------------------------------------------
        | Standard User Details
        |--------------------------------------------------------------------------
        */

        $userDetails = [

            'email' =>
                $request->input('userEmail'),

            'title' =>
                $request->input('userTitle'),

            'firstName' =>
                $request->input('firstName'),

            'lastName' =>
                $request->input('lastName'),

        ];


        /*
        |--------------------------------------------------------------------------
        | Applicant Details
        |--------------------------------------------------------------------------
        */

        if ($editedUser->userType != 'admin') {

            $userDetails['applicationCode'] =
                $request->input('applicationCode');

        }


        /*
        |--------------------------------------------------------------------------
        | Admin Details
        |--------------------------------------------------------------------------
        */

        if ($editedUser->userType == 'admin') {

            $userDetails['position'] =
                $request->input('position', '');

            $userDetails['phoneNumber'] =
                $request->input('phoneNumber', '');

        }


        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        if (!empty($requestVars['password'])) {

            $userDetails['password'] =
                bcrypt($requestVars['password']);

        }


        /*
        |--------------------------------------------------------------------------
        | Screening Checks
        |--------------------------------------------------------------------------
        |
        | Only superusers can alter requested checks.
        |
        | Existing non-zero values are preserved where possible. This is important
        | because fields such as useYoti can contain states other than simply 1.
        |
        */

        if (
            $this->checkAccess('superuser')
            && $editedUser->userType != 'admin'
        ) {

            $checkFields = [
                'dbsApplication',
                'bpssApplication',
                'REVALonsite',
                'REVALoffsite',
                'useYoti'
            ];


            foreach ($checkFields as $field) {

                $isChecked =
                    (int)$request->input($field, 0) === 1;

                $currentValue =
                    $editedUser->$field;


                if (!$isChecked) {

                    /*
                    * Checkbox switched off.
                    */
                    $userDetails[$field] = 0;

                } else {

                    /*
                    * Preserve an existing state such as 2, 3 etc.
                    *
                    * If it previously wasn't enabled, start it at 1.
                    */
                    $userDetails[$field] =
                        (!is_null($currentValue)
                        && (int)$currentValue !== 0)
                            ? $currentValue
                            : 1;

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Update User
        |--------------------------------------------------------------------------
        */

        \DB::table('users')
            ->where('id', $editedUser->id)
            ->update($userDetails);


        /*
        |--------------------------------------------------------------------------
        | Signature
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('signature')) {

            $signatureFile =
                $request->file('signature');

            $extension =
                strtolower(
                    $signatureFile->getClientOriginalExtension()
                );

            $newName =
                $editedUser->id . '.' . $extension;


            \Storage::disk('public_images')->put(
                'user_signatures/' . $newName,
                file_get_contents($signatureFile),
                'public'
            );


            $signatureDetails = [

                'userID' =>
                    $editedUser->id,

                'signature_path' =>
                    $newName,

            ];


            \DB::table('user_signatures')
                ->where('userID', $editedUser->id)
                ->delete();


            \DB::table('user_signatures')
                ->insert($signatureDetails);

        }


        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        session()->flash(
            'success',
            $editedUser->userType == 'admin'
                ? 'User details updated successfully.'
                : 'Applicant details updated successfully.'
        );


        return $this->viewProfile($editedUser->id);
    }

    /**
     * Delete user profile
     * 
     * @param   \Illuminate\Http\Reques
     * @return json
     */

   public function deleteUser(Request $request)
    {
        $requestVars = $request->all();
        if ($this->checkAccess('siteuser') && (Auth::user()->id != $requestVars['userID'])) {//cannot delete own user
            if ($requestVars['userID'] > 0){
                $testDelete = \DB::table('users')->where('id', '=', $requestVars['userID'])->delete();
                if ($testDelete>0){
                    echo 1;
                    \DB::table('user_roles')->where('userID', '=', $requestVars['userID'])->delete();

                    //check if there is an appliation and related data
                    $DBSApplication = \DB::table('applications')->select('applications.id')->where(['applications.userID'=>$requestVars['userID']])->first();
                    if(isset($DBSApplication->id) && !empty($DBSApplication->id)){
                        \DB::table('applications')->where('id', '=', $DBSApplication->id)->delete();
                        \DB::table('application_extra_names')->where('applicationID', '=', $DBSApplication->id)->delete();
                        \DB::table('application_previous_addresses')->where('applicationID', '=', $DBSApplication->id)->delete();
                    }

                    $BPSSApplication = \DB::table('bpss_applications')->select('bpss_applications.id')->where(['bpss_applications.userID'=>$requestVars['userID']])->first();
                    if(isset($BPSSApplication->id) && !empty($BPSSApplication->id)){
                        \DB::table('bpss_applications')->where('id', '=', $BPSSApplication->id)->delete();
                        \DB::table('bpss_application_employment_history')->where('applicationID', '=', $BPSSApplication->id)->delete();
                        \DB::table('bpss_application_passports')->where('applicationID', '=', $BPSSApplication->id)->delete();
                        \DB::table('bpss_application_personal_referee')->where('applicationID', '=', $BPSSApplication->id)->delete();
                    }

                    $BPSSVRApplication = \DB::table('bpss_vr')->select('bpss_vr.id')->where(['bpss_vr.userID'=>$requestVars['userID']])->first();
                    if(isset($BPSSVRApplication->id) && !empty($BPSSVRApplication->id)){
                        \DB::table('bpss_vr')->where('id', '=', $BPSSVRApplication->id)->delete();
                        \DB::table('bpss_vr_identity_documents')->where('verificationRecordID', '=', $BPSSVRApplication->id)->delete();
                        \DB::table('bpss_vr_references')->where('verificationRecordID', '=', $BPSSVRApplication->id)->delete();
                    }
                } else echo 0;
            } else echo 0;
        }
        
    }

    /**
     * Display the list of customers
     *
     * @return \Illuminate\Http\Response
     */

    public function addUser()
    {   
        if(!$this->checkAccess('siteuser')) return redirect("/login");
        $availableOrganisations = \DB::table('organisations')->orderBy('organisationName', 'asc');
            $currentUserRoles = $this->getCurrentUserRoles();
            $userOrganisations = $this->getUserOrganisations();
            if (array_search('superuser', $currentUserRoles) === false ){
                $availableOrganisations = $availableOrganisations->whereIn('id', $userOrganisations);
            }
            $availableOrganisations = $availableOrganisations->get();

        return view('user.addUser', ['availableOrganisations' => $availableOrganisations]);
        
    }

    /**
     * update user details
     * 
     * @param  int  $id
     */

    public function saveUser(Request $request)
    {
        if(!$this->checkAccess('siteuser')) return redirect("/login");
        
        $requestVars = $request->all();
        $validateArray = [
            'userTitle'             => 'max:50',
            'firstName'             => 'required|max:50',
            'lastName'              => 'required|max:50',
            'userEmail'             => 'required|email|max:255',
            'userEmail'             => 'unique:users,email',
            'password'              => 'required|max:50',
        ];
        $messsages = array(
            'userEmail.unique'=>'email_exists',
        );
        $this->validate($request, $validateArray, $messsages); 

        //echo'<pre>';print_r($requestVars);echo'</pre>';


        $userDetails = array(
            'email'     => $requestVars['userEmail'],
            'userType'  => 'admin',
            'password'  => bcrypt($requestVars['password']),
            'title'     => $requestVars['userTitle'],
            'firstName' => $requestVars['firstName'],
            'lastName'  => $requestVars['lastName'],
            'organisationID' => $requestVars['organisationID'],
            'userStatus'=> 1,
            'createdOn' => date("Y-m-d"),
            'createdBy' => Auth::user()->id,
        );
       
        $newUserID = \DB::table('users')->insertGetId($userDetails);
        if (!empty($newUserID) && is_numeric($newUserID)){
            \DB::table('user_roles')->insert(
                ['userID' => $newUserID, 'role' => 'siteuser']
            );
            return redirect()->route('admin.organisationSettings');
        } else {
            return back()->withInput();
        }
    }
}