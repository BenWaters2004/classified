<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class Login extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Handle mfa authentication.
     *
     * @return Mixed
     */
    public function check_mfa($user,$remember = false)
    {   
        if(isset($user->id) && $user->id > 0){
            date_default_timezone_set("Europe/London");
            //check if 2FA or MFA is set for the user and if a valid code was entered
            $requireMfa = false;
            $allowLogin = false;
            $userRole = \DB::table('user_roles')->select('role')->where('userID', $user->id)->first();
            $userRolesArray = (isset($userRole->role) && strlen($userRole->role) > 0) ? (array)$userRole->role : [];
            $organisationDetails = \DB::table('organisations')->select('mfaAuthSiteUser', 'mfaAuthUser')->where('id', $user->organisationID)->first();

            if(isset($userRole) && count($userRolesArray)>0){
                if(in_array('superuser',$userRolesArray)) {
                    $requireMfa = true;
                } else if((in_array('siteuser',$userRolesArray) || in_array('adminoperator',$userRolesArray)) 
                    && isset($organisationDetails->mfaAuthSiteUser) && $organisationDetails->mfaAuthSiteUser){
                    $requireMfa = true;
                } else if(isset($organisationDetails->mfaAuthUser) && $organisationDetails->mfaAuthUser){
                   $requireMfa = true; 
                }
            } else if(isset($organisationDetails->mfaAuthUser) && $organisationDetails->mfaAuthUser){
                $requireMfa = true;
            }

            if($requireMfa){
                $mfaValidInterval = env('MFA_VALID_INTERVAL');
                $mfaExpire = (isset($mfaValidInterval) && !empty($mfaValidInterval)) ? $mfaValidInterval : "10 min";
                //set MFA
                $mfaDetails = array(
                    'userID'     => $user->id,
                    'identifier' => $this->generateRandomKey(32),
                    'mfacode' => $this->generateRandomIntKey(6),
                    'status' => 1,
                    'expire' => date("Y-m-d H:i:s", strtotime("+ $mfaExpire")),
                );
               
                $mfaID = \DB::table('users_mfa')->insertGetId($mfaDetails);
                if(isset($mfaID) && !empty($mfaID)){
                    $userDetails = \DB::table('users')->select('*')->where('id',$user->id)->first();
                    \Session::put('mfaIdentifier', $mfaDetails['identifier']);
                    //send email
                    $mailDriver = env('MAIL_DRIVER');
                    if(isset($mailDriver) && strlen($mailDriver)>0){
                        $emailTo = $userDetails->email;
                        Mail::send('email_templates.loginMfa', ['mfacode' =>$mfaDetails['mfacode']], function ($message) use ($emailTo) {
                            $message->to($emailTo);
                            $message->subject(env('APP_COMPANY_NAME').' MFA Code');
                        });
                    }
                    return true;
                    
                } else back()->withErrors(['message' => 'Your multifactor authentication has failed!']);
            } else {
                return false;
                
            }

        } else {
            back()->withErrors(['message' => 'Your multifactor authentication has failed!']);
        }
    }

    /**
     * Handle mfa authentication - test.
     *
     * @return Mixed
     */
    public function test_mfa(Request $request)
    { 
        if(\Session::has('mfaIdentifier') && !empty(\Session::get('mfaIdentifier'))){
            $requestVars = $request->all();
            $mfaIdentifier = \Session::get('mfaIdentifier');
            $updateUserDetails = \DB::table('users_mfa')->where('identifier', $mfaIdentifier);
            $mfaData = \DB::table('users_mfa')
            ->select('mfacode', 'expire', 'userid')
            ->where(['identifier'=>$mfaIdentifier, 'status'=>1])
            ->orderBy('expire', 'desc')
            ->first();

            if(isset($mfaData->mfacode) && !empty($mfaData->mfacode)){
                //check code

                if($mfaData->mfacode == $requestVars['mfacode']){
                    $this->loginMfa($mfaData->userid);
                    return redirect('/login');
                } else {
                    return view('user.MFAFailedNew', ['message'=>'Your MFA code was not correct. Please try to login again!']);
                }
            } else {
                return view('user.MFAFailedNew', ['message'=>'There was an error while we were trying to log you in. Please try again!']);
            }
        } else {
            back()->withErrors(['message' => 'Your multifactor authentication has failed!']);
        }
    }

    /**
     * Handle an authentication attempt.
     *
     * @return Mixed
     */
    private function loginMfa($userid)
    {   
        $user = User::select('id', 'password', 'organisationID', 'userStatus', 'lockExpire', 'failedAttempts', 'lastAttemptTime')->where('id', '=', $userid)->first();
        Auth::login($user);
        $updateUserDetails = \DB::table('users')->where('id', $user->id)->update(['userStatus' => 1, 'lockExpire' => null, 'failedAttempts' => 0, 'lastLogin' => date('Y-m-d H:i:s'), 'lastAttemptTime' => date('Y-m-d H:i:s', strtotime('- '.env('USER_LOGIN_LOCK_PERIOD')))]);
        $testDelete = \DB::table('users_mfa')->where('userid', $user->id)->delete();
        return null;
    }


    /**
     * Handle an authentication attempt.
     *
     * @return Mixed
     */
    public function authenticate(Request $request)
    {
        date_default_timezone_set("Europe/London");
        $email = $request->only(['emailAddressUKpesa'])['emailAddressUKpesa']; 
        $password = $request->only(['password'])['password']; 
        $hash = \Hash::make($password);
        $remember = ($request->only(['rememberMe'] !== null) && $request->only(['rememberMe'])['rememberMe']) ? true:false; 

        if (!Auth::check()){
            //get user password
            $user = User::select('id', 'password', 'organisationID', 'userStatus', 'lockExpire', 'failedAttempts', 'lastAttemptTime')->where(['email' => $email])->first();
            if(!$user) {
                return back()->withErrors(['message' => 'You do not have a valid account!']);
            }
            if(in_array($user->userStatus,[0,2,3])){
                 return back()->withErrors(['message' => 'You do not have a valid account!']);
            }
            $warning2Attempts = ((env('USER_LOGIN_ATTEMPTS_LIMIT') - 2) > 0) ? env('USER_LOGIN_ATTEMPTS_LIMIT') - 2 : 0;
            $warning1Attempt  = ((env('USER_LOGIN_ATTEMPTS_LIMIT') - 1) > 0) ? env('USER_LOGIN_ATTEMPTS_LIMIT') - 1 : 0;
            //check if user is locked
            if (isset($user->id) && $user->id > 0){
                //reset if the lock period has expired
                if ($user->userStatus == 4 && $user->failedAttempts == 5 && $user->lockExpire <= date('Y-m-d H:i:s')){
                    $newLockExpire = date('Y-m-d H:i:s', strtotime('- '.env('USER_LOGIN_LOCK_PERIOD')));
                    $updateUserDetails = \DB::table('users')->where('id', $user->id)->update(['userStatus' => 1, 'lockExpire' => $newLockExpire,'failedAttempts' => 0]);
                    $user->userStatus  =1; $user->lockExpire  =$newLockExpire; $user->failedAttempts  =0; 
                }
                
                if ($user->userStatus == 4 && $user->lockExpire > date('Y-m-d H:i:s')){
                    return back()->withErrors(['message' => 'Your user account has been locked until '.date("d M Y H:i:s", strtotime($user->lockExpire)).' because of too many failed login attempts!']);
                }
                else if (\Hash::check($password, $user->password)) {
                    $mfaRequired = $this->check_mfa($user,$remember);
                    if($mfaRequired) 
                        {
                            return view('user.MFANew');
                        } else {
                            Auth::login($user, $remember);
                            //reset user login
                            $updateUserDetails = \DB::table('users')->where('id', $user->id)->update(['userStatus' => 1, 'lockExpire' => null, 'failedAttempts' => 0, 'lastLogin' => date('Y-m-d H:i:s'), 'lastAttemptTime' => date('Y-m-d H:i:s', strtotime('- '.env('USER_LOGIN_LOCK_PERIOD')))]);
                        }
                    return redirect('/login');
                } else {
                    //increment failed attempts or lock user
                     if ($user->failedAttempts == 0 || $user->lastAttemptTime<date('Y-m-d H:i:s', strtotime('- '.env('USER_LOGIN_LOCK_PERIOD')))){
                        $updateUserDetails = \DB::table('users')->where('id', $user->id)->update(['failedAttempts' => 1, 'lastAttemptTime' => date('Y-m-d H:i:s')]);
                        return back()->withErrors(['message' => 'Username/Password does not match!']);
                     }else {
                        //if max attempts lock user, if not increment
                        $lockAttemptNumber = $user->failedAttempts+1;
                        if($lockAttemptNumber < env('USER_LOGIN_ATTEMPTS_LIMIT')){
                            $updateUserDetails = \DB::table('users')->where('id', $user->id)->update(['failedAttempts' => $lockAttemptNumber, 'lastAttemptTime' => date('Y-m-d H:i:s')]);
                            if (!empty($warning2Attempts) && $warning2Attempts == $lockAttemptNumber){
                                return back()->withErrors(['message' => 'Username/Password does not match! You have 2 attempts left before your user will be locked.']);
                            } else if (!empty($warning1Attempt) && $warning1Attempt == $lockAttemptNumber){
                                return back()->withErrors(['message' => 'Username/Password does not match! You have 1 attempt left before your user will be locked.']);
                            } else {
                                return back()->withErrors(['message' => 'Username/Password does not match!']);
                            }
                            
                        } else {
                            $updateUserDetails = \DB::table('users')->where('id', $user->id)->update(['userStatus' => 4, 'lockExpire' => date('Y-m-d H:i:s', strtotime('+ '.env('USER_LOGIN_LOCK_PERIOD'))),'failedAttempts' => env('USER_LOGIN_ATTEMPTS_LIMIT'), 'lastAttemptTime' => date('Y-m-d H:i:s')]);
                            return back()->withErrors(['message' => 'You user account is now locked for '.env('USER_LOGIN_LOCK_PERIOD').' because of too many failed login attempts!']);
                        }
                     }
                    
                }
            } else {
                return back()->withErrors(['message' => 'Username/Password does not match!']);
            }
        } 

        return redirect('/login');
    }


   /**
     * display the login form.
     *
     * @return Response
     */
    public function index()
    {   
        if (Auth::check() && Auth::user()->userType == 'admin'){
            $userRole = \DB::table('user_roles')->select('role')->where('userID', '=', Auth::user()->id)->first();
            if(isset($userRole) && count((array)$userRole)>0){
                $selectedFolder = ($userRole->role == 'superuser') ? 'admin' : $userRole->role;
            } else {
                $selectedFolder = 'applicant';
            }
            return redirect('/'.$selectedFolder.'/dashboard');//->with('flash_notice', 'You are already logged in!');
        } else if (Auth::check() && Auth::user()->userType == 'applicant'){
            return redirect('/applicant');
        } else {
            return $this->login();
        }
        
    }

    public function login()
    {   
        return view('user.loginNew');        
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
            return redirect('/login');
        } else {
            return view('user.loginNew');
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
            return redirect('/loginNew');
        } else {
            return view('user.forgotPasswordNew');
        }
        
    }

    /**
     * reset password link
     *
     * @return Response
     */
    public function sendResetLink(Request $request)
    {   
        
        if (Auth::check()){ 
            return redirect('/login');
        }

        $requestVars = $request->all();
        //check if email exist
        $userDetails = \DB::table('users')->select('users.*')->where(["users.email" => $requestVars['emailAddressUKpesaReset']])->first();
        if(isset($userDetails) && $userDetails->id > 0){
            //set new recovery code
            $resetDetails = array(
                'recoveryCode'      => $this->generateRandomKey(100),
                'recoveryCodeExpiry'=> date('Y-m-d H:i:s', strtotime('+ 1 hour')),
            );
            $updateUserDetails = \DB::table('users')->where('id', $userDetails->id)->update($resetDetails);
            $emailTo = $userDetails->email;
            if($updateUserDetails){
                //send email
                Mail::send('email_templates.dbs_password_reset_request', ['resetLinkExpire' => $resetDetails['recoveryCodeExpiry'], 'resetLink' =>env('APP_URL').'passwordReset/'.$resetDetails['recoveryCode']], function ($message) use ($emailTo) {
//                $message->from('noreply@domain.co.uk', env("APP_COMPANY_NAME"));

                $message->to($emailTo);
                $message->subject('DBS Password Reset');
            });
            }
        }

        return view('user.passwordResetSentNew');
    }

    /**
     * display the reset password form.
     *
     * @return Response
     */
    public function passwordReset($resetCode = null)
    {   
        if (Auth::check()){  return redirect('/login'); }

        if(empty($resetCode) || strlen($resetCode)<10){
            return view('user.passwordResetNew',['hideForm' => 1, 'errorMessage' => 'invalid_reset_code', 'resetCode' => $resetCode]);
        } else {
            //check reset code
            $userDetails = \DB::table('users')->select('users.*')->where(["users.recoveryCode" => $resetCode])->first();
            if(!isset($userDetails) || empty($userDetails->id)){
                return view('user.passwordResetNew',['hideForm' => 1, 'errorMessage' => 'invalid_reset_code', 'resetCode' => $resetCode]);
            } 
            //check if reset code has expired
            if($userDetails->recoveryCodeExpiry < date('Y-m-d H:i:s')){
                return view('user.passwordResetNew',['hideForm' => 1, 'errorMessage' => 'expired_reset_code', 'resetCode' => $resetCode]);           
            }

            return view('user.passwordResetNew',['hideForm' => 0, 'resetCode' => $resetCode]);
        }
    }

    public function updatePassword(Request $request)
    {   
        
        if (Auth::check()){ 
            return redirect('/login');
        }

        $requestVars = $request->all();
        //check if email exist
        $userDetails = \DB::table('users')->select('users.*')->where(["users.recoveryCode" => $requestVars['resetCode']])->first();

        if(!isset($userDetails) || empty($userDetails->id)){
            return view('user.passwordResetNew',['hideForm' => 1, 'errorMessage' => 'invalid_reset_code']);
        } 
        //check if reset code has expired
        if($userDetails->recoveryCodeExpiry < date('Y-m-d H:i:s')){
            return view('user.passwordResetNew',['hideForm' => 1, 'errorMessage' => 'expired_reset_code']);   
        }

        $validateArray = [
            'password'  => 'confirmed',
        ];

        $this->validate($request, $validateArray); 

        if (!isset($requestVars['password']) || empty($requestVars['password']) || !isset($requestVars['password']) || empty($requestVars['password'])){
            return view('user.passwordResetNew', ['hideForm' => 0, 'resetCode' => $requestVars['resetCode']])->withErrors([
                'message' => 'You need to setup a password!'
            ]);
        } else if(strlen($requestVars['password']) < 14 || !preg_match('/^\S*(?=\S{14,50})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/', $requestVars['password']) || strlen($requestVars['password'])>50){
            return view('user.passwordResetNew', ['hideForm' => 0, 'resetCode' => $requestVars['resetCode']])->withErrors([
                'message' => 'Password does not meet complexity requirements!'
            ]);
        } else if($requestVars['password'] != $requestVars['password_confirmation']){
            return view('user.passwordResetNew', ['hideForm' => 0, 'resetCode' => $requestVars['resetCode']])->withErrors([
                'message' => 'Password does not match the confirm password!'
            ]);
        }

        $newPassword['password'] = bcrypt($requestVars['password']);
        $newPassword['recoveryCode'] = '';
        $newPassword['recoveryCodeExpiry'] = date('Y-m-d H:i:s',strtotime('-1 hour'));
        //reset user lock if any
        $newPassword['userStatus'] = 1;
        $newPassword['failedAttempts'] = 0;
        $newPassword['lastAttemptTime'] = null;
        $newPassword['lockExpire'] = null;

        $updateUserDetails = \DB::table('users')->where('id', $userDetails->id)->update($newPassword);

        return view('user.passwordResetNew',['hideForm' => 1, 'successMessage' => 'reset_success', 'resetCode' => $requestVars['resetCode']]);
    }
}
