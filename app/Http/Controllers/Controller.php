<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Login;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;



class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        
        
    }

    
    /**
     * Checks if the use is allowed or not
     *
     * @return \Illuminate\Http\Response
     */

    public function checkAccess($accesslevel)
    {   
        # superadmin, admin, manager, stakeholder, user
        

        //only check if the user is logged in
        if (Auth::check()){
        	$testAllowed = false;
            $userRole = \DB::table('user_roles')->select('role')->where('userID', '=', Auth::user()->id)->get();
            foreach ($userRole as $role){
            	if (($role->role == $accesslevel) || ($role->role == 'superuser')) $testAllowed = true;//superuser will have access to all
            }
           
            return ($testAllowed) ? true:false; 
        }
        
        return false;
    }

    public function getCurrentUserRoles()
    {   
        if(!$this->checkAccess('siteuser')) return array();

            $userRolesRaw = \DB::table('user_roles')->select('role')->where('userID', '=', Auth::user()->id)->get();
            $userRolesRaw = $userRolesRaw->toArray();
            $userRoles = array();
            foreach($userRolesRaw as $userRole){
                if (!in_array($userRole->role, $userRoles)) $userRoles[] = $userRole->role;
            }
            return $userRoles;
    }

    /**
     * Generate Random Key
     *
     * @return \Illuminate\Http\Response
     */

    public function generateRandomKey($length = 10)
    {   
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $string = '';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, $max)];
        }
        return $string;
    }

    /**
     * Generate Random Key - Integer values only
     *
     * @return \Illuminate\Http\Response
     */

    public function generateRandomIntKey($length = 6)
    {   
        $characters = '0123456789';
        $string = '';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, $max)];
        }
        return $string;
    }

    /**
     * calculate length of time
     *
     * @return string
     */

    public function time_diff_string($from, $to, $full = false) {
        $from = new \DateTime($from);
        $to = new \DateTime($to);
        $diff = $to->diff($from);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            // 'h' => 'hour',
            // 'i' => 'minute',
            // 's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        //return $string ? implode(', ', $string) . ' ago' : 'just now';
        return $string ? implode(', ', $string)  : 'just now';
    }

    /**
     * Get User Organisations
     * 
     * @param  int  $userId
     * @return array containing organisations id
     */

   public function getUserOrganisations($userID = 0)
    {
        if($this->checkAccess('siteuser') || empty($userID) || ($userID == Auth::user()->id)){
            $userID = (isset($userID) && $userID > 0) ? $userID : Auth::user()->id;
            $mainOrganisation = \DB::table('users')
                ->select('organisationID')
                ->where(["id" => $userID])
                ->first();
            $userExtraOrganisations = \DB::table('user_organisations')->select('organisationID')->where('userID', '=', $userID)->get();
            $userExtraOrganisations = $userExtraOrganisations->toArray();
            $userOrganisations = [];
            $userOrganisations[] = $mainOrganisation->organisationID;//add main organisation
            foreach($userExtraOrganisations as $extraOrganisation){
                if (!in_array($extraOrganisation->organisationID, $userOrganisations)) $userOrganisations[] = $extraOrganisation->organisationID;
            }

            return $userOrganisations;
        }
        
    }

    /**
     * Add a new notification
     * 
     * @param  string  $userGroup
     * @param  array   $notificationDetails
     * @param  boolean $sendEmails
     * @return boolean containing notification id
     */

    public function addNotification($userGroup = 'superuser', $organisationId = 0, $notificationDetails =[], $sendEmails = true)
    {   if($userGroup == 'superuser' || $userGroup == 'siteuser' || $userGroup == 'adminoperator'){        
            try {
                $loggedUserID = Auth::user()->id;
            } catch (\Exception $e) {
                $loggedUserID = 0;
            }
            $title = (isset($notificationDetails['title']) && strlen($notificationDetails['title']) > 0) ? $notificationDetails['title'] : '';
            $body = (isset($notificationDetails['body']) && strlen($notificationDetails['body']) > 0) ? $notificationDetails['body'] : '';
            $category = (isset($notificationDetails['category']) && !empty($notificationDetails['category'])) ? intval($notificationDetails['category']) : 0;
            $relatedUserId = (isset($notificationDetails['relatedUserId']) && !empty($notificationDetails['relatedUserId'])) ? intval($notificationDetails['relatedUserId']) : null;
            $relatedAction = (isset($notificationDetails['relatedAction']) && strlen($notificationDetails['relatedAction']) > 0) ? $notificationDetails['relatedAction'] : null;
            $groupId = (string) Str::uuid();

            //get all user ids for the usergroup
            switch ($userGroup) {
                case 'siteuser':
                case 'adminoperator':
                    $userIdsAllowed = \DB::table('user_roles')->select('userID')->where('role', '=', strtolower($userGroup))->get();
                    $allowedIds = [];
                    foreach ($userIdsAllowed as $allowedUser) {
                        $allowedIds[]= $allowedUser->userID;
                    }

                    $userIds = \DB::table('users')
                        ->select('users.id AS userID')
                        ->where(['users.userType'=>'admin'])
                        ->where(function ($query) use ($organisationId)  {
                            $query->where(['users.organisationID' => $organisationId])
                                ->orWhereIn('users.id',function ($query2) use ($organisationId)  {
                                    $query2->select('userID')
                                    ->from('user_organisations')
                                    ->where(['user_organisations.organisationID' => $organisationId]);
                                });
                           })
                        ->whereIn('id',$allowedIds)
                        ->get();
                    break;
                
                case 'superuser':
                    $userIds = \DB::table('user_roles')->select('userID')->where('role', '=', strtolower($userGroup))->get();
                    break;
                case 'sitedesignatedemail':
                    $emailList = \DB::table('organisations')->select('designatedEmailList')->where('id', '=', $organisationId)->first();
                    /*
                    if(strlen($emailList->designatedEmailList)>0) {
                        if(strpos($emailList->designatedEmailList,";") !== 0){
                            $emailListRaw = explode(';',$emailList->designatedEmailList);
                            foreach ($emailListRaw as $key => $value) {
                                // code...
                            }
                            $userIds = \DB::table('user_roles')->select('userID')->where('role', '=', strtolower($userGroup))->get();
                            ->whereIn('id',$allowedemail)
              $startDate = $startDateRaw[2] . '-' . $startDateRaw[1] . '-' . $startDateRaw[0];
                        }
                    
}*/
                    $userIds = \DB::table('user_roles')->select('userID')->where('id', '=', 1)->get();
                    
                    break;
                default:
                    # code...
                    break;
            }
            if (env("NOTIFIER_EMAIL") != null) // send email for zoho helpdesk 
            {
                //dd($notificationDetails);
                Mail::send('email_templates.notification', ['details'=>$notificationDetails], function ($message) use ($notificationDetails) {
                       $message->to(env("NOTIFIER_EMAIL"));
                       $message->subject($notificationDetails["title"]);
                });
            }
            $countNotifications = 0;
            $countScheduledEmails = 0;
            foreach ($userIds as $key => $user) {
                $userEmail = \DB::table('users')->select('email')->where('id', '=', $user->userID)->first()->email;
                $newNotificationDetails = array(
                    'category'      => $category,
                    'title'         => $title,
                    'body'          => $body,
                    'userid'        => $user->userID,
                    'status'        => 1, //0-inactive, 1-active
                    'email'         => 0, // has an email been sent 1-yes, 0-no
                    'relatedUserId' => $relatedUserId, 
                    'relatedAction' => $relatedAction, 
                    'createdBy'     => $loggedUserID, 
                    'createdOn'     => date ("Y-m-d H:i:s"),
                    'group_id' => $groupId,
                );
               
                $notificationId = \DB::table('notifications')->insertGetId($newNotificationDetails);
                if(isset($notificationId) && !empty($notificationId)) $countNotifications++;

                //send emails - MOVE TO CRON
                if($sendEmails){
                    $emailFrom = (isset($notificationDetails['emailFrom']) && strlen($notificationDetails['emailFrom']) > 0) ? $notificationDetails['emailFrom'] : null;
                    $emailTemplate = (isset($notificationDetails['emailTemplate']) && strlen($notificationDetails['emailTemplate']) > 0) ? $notificationDetails['emailTemplate'] : null;
                    $emailVariables = (isset($notificationDetails['emailVariables']) && strlen($notificationDetails['emailVariables']) > 0) ? $notificationDetails['emailVariables'] : null;
                    $emailTitle = (isset($notificationDetails['emailTitle']) && strlen($notificationDetails['emailTitle']) > 0) ? $notificationDetails['emailTitle'] : null;
                    $emailBody = (isset($notificationDetails['emailBody']) && strlen($notificationDetails['emailBody']) > 0) ? $notificationDetails['emailBody'] : null;

                    //schedule emails
                    $emailDetails = array(
                        'emailTemplate'      => $emailTemplate,
                        'userid'         => $loggedUserID,
                        'emailFrom'          => $emailFrom,
                        'emailTo'        => $userEmail,
                        'emailTitle'        => $emailTitle,
                        'emailBody' => $emailBody, 
                        'emailVariables' => $emailVariables, 
                        'createdOn'     => date ("Y-m-d H:i:s"),
                    );
                   
                    $scheduledEmailId = \DB::table('email_queue')->insertGetId($emailDetails);
                    if(isset($scheduledEmailId) && !empty($scheduledEmailId)) $countScheduledEmails++;
                }

            }

            return $countNotifications;
        }
        return null;
    }
    
    public function downloadResource($resourceType = null, $resourceId = 0, $preview = 0)
    {   
        $status = 0;
        $message = '';
        $userID = Auth::user()->id;
        $documentPath = '';
        $documentType = '';
        $documentName = '';

        //check user is allowed to download this file
        $currentUserRoles = $this->getCurrentUserRoles();
        $userOrganisations = $this->getUserOrganisations();


        if(!empty($resourceType) && !empty($resourceId)){
            switch ($resourceType) {
                case 'new_applicant':
                    $documentPathData = \DB::table('new_applicant_documents')->where(['id' => intval($resourceId)])->first();
                    if (isset($documentPathData->id) && !empty($documentPathData->id) && strlen($documentPathData->document_path) > 0){
                        $documentPath = env('APP_DOCUMENT_ROOT').'/public/uploads/new_applicant_documents/'.$documentPathData->document_path;
                        $documentType = (isset($documentPathData->document_type) && !empty($documentPathData->document_type)) ? $documentPathData->document_type : 'bit';
                        $documentName = (isset($documentPathData->document_name) && !empty($documentPathData->document_name)) ? $documentPathData->document_name : 'unknown';
                        $documentUrlLoadPath = '/uploads/new_applicant_documents/'.$documentPathData->document_path;
                    }
                    break;
                
                case 'supporting_dbs':
                    $documentPathData = \DB::table('application_supporting_documents')->where(['id' => intval($resourceId)])->first();
                    if (isset($documentPathData->id) && !empty($documentPathData->id) && strlen($documentPathData->document_path) > 0){
                        $documentPath = env('APP_DOCUMENT_ROOT').'/public/uploads/supporting_documents/'.$documentPathData->document_path;
                        $documentType = (isset($documentPathData->document_type) && !empty($documentPathData->document_type)) ? $documentPathData->document_type : 'bit';
                        $documentName = (isset($documentPathData->document_name) && !empty($documentPathData->document_name)) ? $documentPathData->document_name : 'unknown';
                        $documentUrlLoadPath = '/uploads/supporting_documents/'.$documentPathData->document_path;
                    }
                    break;
                
                case 'signature':
                    $documentPathData = \DB::table('user_signatures')->where(['userid' => intval($resourceId)])->first();
                    if (isset($documentPathData->id) && !empty($documentPathData->id) && strlen($documentPathData->signature_path) > 0){
                        $documentPath = env('APP_DOCUMENT_ROOT').'/public/uploads/user_signatures/'.$documentPathData->signature_path;

                        $documentUrlLoadPath = '/uploads/user_signatures/'.$documentPathData->signature_path;
                        
                    }
                    break;

                case 'bpssvr':
                    // code...
                    break;
                
                default:
                    
                    break;
            }
            $extension = substr($documentPath, strrpos($documentPath, '.') + 1);
            if ($preview == 2 && in_array(strtolower($extension),['jpg','jpeg','png', 'gif'])) {
                return view('infopages.previewImage', ['documentPath' => $documentPath, 'documentName' => $documentName, 'resourceType' => $resourceType, 'resourceId' => $resourceId]); 
                exit;
            } else if($preview){    
                if(in_array(strtolower($extension),['jpg','jpeg','png', 'gif'])){
                    return view('infopages.previewImageOptions', ['documentPath' => $documentPath, 'documentName' => $documentName, 'resourceType' => $resourceType, 'resourceId' => $resourceId]); 
                    exit;
                } else if(strtolower($extension) == 'pdf'){
                    $this->downloadCustomFile($documentPath, $documentName, $documentType, 1); 
                    exit;
                }
            }

            $this->downloadCustomFile($documentPath, $documentName, $documentType, $preview);
            $status = 1;
        } else {
             $message = 'Document type not found!';
        }

        return ['status'=>$status, 'message' => $message];
       
    }

    public function downloadCustomFile($documentPath = null, $documentName = null, $documentType = null, $preview=0)
    {
        if(!empty($documentPath) && file_exists($documentPath)){

            $extension = substr($documentPath, strrpos($documentPath, '.') + 1);
            // Give a nice name to your download.
            $fileName = $documentName.'.'.$extension;


            

                // Maximum size of chunks (in bytes).
                $maxRead = 100 * 1024 * 1024; // 100MB
                
                // Open a file in read mode.
                $fh = fopen($documentPath, 'r');

                // These headers will force download on browser,
                // and set the custom file name for the download, respectively.
                if($preview==1 && in_array(strtolower($extension),['pdf'])){
                    header("Content-type: application/pdf");
                    header('Content-Disposition: inline; filename="' . $fileName . '"');
                }
                else if($preview==1 && in_array(strtolower($extension),['jpg','jpeg'])){
                    header("Content-type: image/jpeg");
                    header('Content-Disposition: inline; filename="' . $fileName . '"');
                }
                else if($preview==1 && in_array(strtolower($extension),['png'])){
                    header("Content-type: image/png");
                    header('Content-Disposition: inline; filename="' . $fileName . '"');
                }
                else if($preview==1 && in_array(strtolower($extension),['gif'])){
                    header("Content-type: image/gif");
                    header('Content-Disposition: inline; filename="' . $fileName . '"');
                } else {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $fileName . '"');   
                }                
                
                header('Content-Transfer-Encoding: binary');
                header('Connection: Keep-Alive');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($documentPath));
                ob_clean();
                flush(); // Flush system output buffer
                readfile($documentPath);

                // Exit to make sure not to output anything else.
                exit;

        }
        return null;
    }

    public function setGdprCookie()
    {
        setcookie('gdpr-message-cookie', 'yes', time() + (5 * 365 * 24 * 60 * 60), "/", null, TRUE, TRUE); 
        return null;
    }

    
}
