<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spipu\Html2Pdf\Html2Pdf;

class Notifications extends Controller
{

	/*
    |--------------------------------------------------------------------------
    | Notifications Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling the notifications
    |
    */

    /**
     * index fallback
     *
     * 
     */

    public function index()
    {
        //get active notifications
        $loggedUserID = Auth::user()->id;
        $notifications = \DB::table('notifications')->select('*')
                        ->where(['userID'=>$loggedUserID])
                        ->whereIn('status', [0,1])
                        ->orderBy('id', 'desc')
                        ->get();
        return view('notification.listNotifications', ['notifications' => $notifications]);
        
    }

    /**
     * view notification
     *
     * 
     */
    public function viewNotification($notificationId){
        if (!Auth::check()) return redirect("/login");

        if(!isset($notificationId) ||empty($notificationId)) return redirect("/login");
        $loggedUserID = Auth::user()->id;

        $notificationDetailsRaw = \DB::table('notifications')
            ->select('*')
            ->where(['id'=>intval($notificationId), 'userid' => $loggedUserID])
            ->first();
        if (isset($notificationDetailsRaw) && !empty($notificationDetailsRaw->id)){
            $notificationDetails = $notificationDetailsRaw;
        } else $notificationDetails = new \stdClass();

        return view('notification.viewNotification', ['notificationDetails' => $notificationDetails]);
        
    }

    /**
     * get header notifications
     *
     * @return \Illuminate\Http\Response
     */

    public function getHeaderNotifications()
    {   
        if (!Auth::check()) return redirect("/login");
        $loggedUserID = Auth::user()->id;
        $notifications = \DB::table('notifications')->select('*')
                        ->where(['userID'=>$loggedUserID, 'status' => 1])
                        ->orderBy('id', 'desc')
                        ->get();
        return $notifications;
    }

    /**
     * dismiss notification
     *
     * 
     */
    public function dismissNotification(Request $request)
    {
        if (!Auth::check()) {
            return redirect("/login");
        }

        $loggedUserID = Auth::id();
        $notificationId = (int) $request->input('notificationId', 0);
        $isSuperuser = $this->checkAccess('superuser');

        // If superuser, target all superusers. Otherwise, only target current user.
        $targetUserIds = $isSuperuser
            ? \DB::table('user_roles')
                ->where('role', 'superuser')
                ->pluck('userID')
            : collect([$loggedUserID]);

        if ($notificationId > 0) {
            // Get the notification being dismissed by the logged-in user
            $notification = \DB::table('notifications')
                ->where('id', $notificationId)
                ->where('userid', $loggedUserID)
                ->first();

            if (!$notification) {
                return response('0');
            }

            $query = \DB::table('notifications')
                ->whereIn('userid', $targetUserIds);

            if ($isSuperuser) {
                // Delete the same notification for all superusers
                $query->where('group_id', $notification->group_id);
            } else {
                // Delete only this user's notification
                $query->where('id', $notificationId);
            }

            $deleted = $query->delete();
        } else {
            // Dismiss all notifications
            $deleted = \DB::table('notifications')
                ->whereIn('userid', $targetUserIds)
                ->delete();
        }

        return response($deleted > 0 ? '1' : '0');
    }

    /**
     * resolve notifications
     *
     * 
     */

    public function resolveNotification($notificationId){
        if (!Auth::check()) return redirect("/login");

        if(!isset($notificationId) ||empty($notificationId)) return redirect("/login");
        $loggedUserID = Auth::user()->id;

        $notificationData = \DB::table('notifications')->select('*')->where(['id'=>$notificationId, 'userid' => $loggedUserID])->first();
        if(isset($notificationData->id) && !empty($notificationData->id)){
            $notificationData->status = 2;//resolved
            $updateNotificationDetails = \DB::table('notifications')->where(['id' => $notificationData->id])->update((array)$notificationData);
        }

        return redirect("notifications/");
    }

}