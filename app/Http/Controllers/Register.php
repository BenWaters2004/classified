<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;

class Register extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Handle an authentication attempt.
     *
     * @return Mixed
     */
    public function authenticate(Request $request)
    {
        $email = $request->only(['emailAddress'])['emailAddress']; 
        $password = $request->only(['password'])['password']; 
        $hash = \Hash::make($password);
        $remember = ($request->only(['rememberMe'] !== null) && $request->only(['rememberMe'])['rememberMe']) ? true:false; 

        if (!Auth::check()){
            //get user password
            $applicant = User::select('id', 'password')->where('email', '=', $email)->first();

            if (isset($applicant->id) && $applicant->id > 0 && \Hash::check($password, $applicant->password)) {
                Auth::login($applicant, $remember);
            } else {
                return back()->withErrors([
                    'message' => 'Username/Password does not match!'
                ]);
            }
        } 

        return redirect('/applicant');
    }


   /**
     * display the login form.
     *
     * @return Response
     */
    public function index($accessUrlCode = null)
    {   
        if (Auth::check() && Auth::user()->userType == 'applicant'){
            return redirect('/applicant/dashboard');//->with('flash_notice', 'You are already logged in!');
        } else if (Auth::check() && Auth::user()->userType == 'admin') {
            return redirect('/login');
        } else {
            return $this->login($accessUrlCode);
        }
    }

    public function login($accessUrlCode = null)
    {   
        if (isset($accessUrlCode)){
            return view('user.signupNew', ['accessUrlCode' =>$accessUrlCode]);        
        } else {
            return view('user.loginNew');
        }
    }

    public function signupCheckCode(Request $request)
    {
        $accessUrlCode = $request->only(['accessUrlCode'])['accessUrlCode']; 
        $remember = ($request->only(['rememberMe'] !== null) && $request->only(['rememberMe'])['rememberMe']) ? true:false; 

        if (!Auth::check()){
            //get user code
            $applicant = \DB::table('applicants')->select('*')->where(['accessUrlCode' => $accessUrlCode, 'userStatus' =>0])->first();
            if (isset($applicant->id) && $applicant->id > 0) {
                //create account
                return view('user.createApplicantAccountNew', ['accessUrlCode' => $accessUrlCode]);
            } else {
                return back()->withErrors([
                    'message' => 'Your application code is not valid!'
                ]);
            }
        } 

        return redirect('/applicant');
    }

    public function createApplicantAccount(Request $request)
    {
        $requestVars = $request->all();

       // $hash = \Hash::make($requestVars['applicationCode']);
        if (!Auth::check()){
            //get user code
            $applicant = \DB::table('applicants')->select('*')->where(['accessUrlCode' => $requestVars['accessUrlCode'], 'userStatus' =>0])->first();
            if (isset($applicant->id) && $applicant->id > 0) {

                if (!isset($requestVars['password']) || empty($requestVars['password']) || !isset($requestVars['password']) || empty($requestVars['password'])){
                    return view('user.createApplicantAccountNew', ['accessUrlCode' => $requestVars['accessUrlCode']])->withErrors([//, 'applicationCode' => $requestVars['applicationCode']
                        'message' => 'You need to setup a password!'
                    ]);
                } else if(strlen($requestVars['password']) < 14 || !preg_match('/^\S*(?=\S{14,50})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/', $requestVars['password']) || strlen($requestVars['password'])>50){
                    return view('user.createApplicantAccountNew', ['accessUrlCode' => $requestVars['accessUrlCode']])->withErrors([
                        'message' => 'Password does not meet complexity requirements!'
                    ]);
                } else if($requestVars['password'] != $requestVars['password_confirmation']){
                    return view('user.createApplicantAccountNew', ['accessUrlCode' => $requestVars['accessUrlCode']])->withErrors([
                        'message' => 'Password does not match the confirm password!'
                    ]);
                }

                if (!isset($applicant->REVALoffsite)) $applicant->REVALoffsite = 0;
                if (!isset($applicant->REVALonsite)) $applicant->REVALonsite = 0;

                //create user
                $userDetails = array(
                    'userType'  => 'applicant',
                    'dbsApplication' => $applicant->dbsApplication,
                    'bpssApplication' => $applicant->bpssApplication,
                    'email'     => $applicant->email,
                    'password'  => bcrypt($requestVars['password']),
                    'title'     => '',
                    'firstName' => $applicant->forename,
                    'lastName'  => $applicant->surname,
                    'organisationID' => $applicant->organisationID,
                    'lastInvitationSent' => $applicant->lastEmailSent,
                    'applicationCode'  => $applicant->applicationCode,
                    'userStatus'=> 1,
                    'createdOn' => date("Y-m-d H:i:s"),
                    'createdBy' => $applicant->createdBy,
                    'REVALoffsite' => $applicant->REVALoffsite,
                    'REVALonsite' => $applicant->REVALonsite,
                    'useYoti' => $applicant->useYoti,
                    'phoneNumber' => $applicant->mobileNumberMain,
                );
               
                $newUserID = \DB::table('users')->insertGetId($userDetails);
                if (!empty($newUserID) && is_numeric($newUserID)){ 

                    $user = User::select('id', 'password')->where('id', '=', $newUserID)->first();
                    Auth::login($user);

                    //set the user id in the new applicant files table (if there are any records)
                    $updateNewUserDocumentsObj = array('userID'   => $newUserID);
                    $updateNewUserDocuments = \DB::table('new_applicant_documents')->where(['applicantID' => $applicant->id])->update($updateNewUserDocumentsObj);
                    
                    //remove from the applicant table
                    \DB::table('applicants')->where(['id' =>$applicant->id])->delete();


                    //add notification
                    $requestingUserDetails = \DB::table('users')->select('*')->where('id', '=', $newUserID)->first();
                    
                    $organisationName = \DB::table('organisations')->select('organisationName')->where('id', '=', $applicant->organisationID)->first()->organisationName;

                    $notificationDetails = [
                        'title' => 'New User Signup - '.$applicant->forename.' '.$applicant->surname,
                        'body' => 'A new user has accepted the invitation and registered for a DBS application. The user is '.$applicant->forename.' '.$applicant->surname.', email: '.$applicant->email.'.<br />Organisation: '.$organisationName.'<br /><br />Click here to view the details: <a href="'.env('APP_URL').'adminoperator/applicants/" target="_blank">User details</a><br /><br />',
                        'category' => 0,
                        'relatedUserId' => $newUserID,
                        'relatedAction' => 'New DBS submission',

                        'emailTemplate' => 'notification_new_user_signup',
                        'emailTitle' => 'New User Signup',
                        'emailBody' => 'A new user has accepted the invitation and registered for a DBS application. The user is '.$applicant->forename.' '.$applicant->surname.', email: '.$applicant->email.'.<br />Organisation: '.$organisationName.'<br /><br />Click here to view the details: <a href="'.env('APP_URL').'adminoperator/applicants/" target="_blank">User details</a><br /><br />',
                    ];
                    $this->addNotification('siteuser', $applicant->organisationID, $notificationDetails, true);
                    
                    return redirect('/applicant/dbsStep1');
                }
            } else {
                return view('user.createApplicantAccountNew', ['accessUrlCode' => $requestVars['accessUrlCode']])->withErrors([
                    'message' => 'Your application code is not valid!'
                ]);
            }
        } 

        return redirect('/applicant');
    }

    

   
}