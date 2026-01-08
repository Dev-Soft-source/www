<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Mail\ExtraCareEligibilityMail;
use App\Mail\StudentCardVerificationMail;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\User;
use App\Services\FCMService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StudentVerificationController extends Controller
{
    use StatusResponser;

    public function index()
    {
        try{

            $users = User::query()->where('student', '!=', 0);
    
            $users = $this->whereClause($users);
            $users = $this->loadRelations($users);
            $users = $this->sortingAndLimit($users);
    
            return $this->apiSuccessResponse(UserResource::collection($users), 'Data Get Successfully!');
        } catch (\Exception $e) {
            // Log the error or return an error response
            return $this->apiErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function updateStudent($id, $student)
    {
        $user = User::whereId($id)->first();
        if ($student === '1') {
            $result = User::whereId($id)->update([
                'student' => $student,
                'charge_booking' => 2,
            ]);
            $data = ['username' => $user->first_name];
            Mail::to($user->email)->queue(new StudentCardVerificationMail($data));

            $notification = Notification::create([
                'type' => null,
                'category' => 'system',
                'receiver_id' => $user->id,
                'posted_by' => $user->id,
                'message' =>  'Student card approved',
                'status' => 'student_card',
                'notification_type' => 'student_card',
            ]);

            $fcmToken = $user->mobile_fcm_token;
            $body = $notification->message;
            $fcmService = new FCMService();

            if ($fcmToken) {
                // Send the booking notification
                $fcmService->sendNotification($fcmToken, $body);
            }

            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }
        } else {
            $result = User::whereId($id)->update([
                'student' => $student,
                'charge_booking' => 1,
            ]);
        }

        if ($result) {
            return $this->apiSuccessResponse(new UserResource($user), 'User status has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function updateChargeBooking($id, $charge_booking)
    {
        $result = User::whereId($id)->update([
            'charge_booking' => $charge_booking,
        ]);
        
        $user = User::whereId($id)->first();

        if ($result) {
            return $this->apiSuccessResponse(new UserResource($user), 'User status has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function updateEmailVerified($id, $email_verified)
    {
        $result = User::whereId($id)->update([
            'email_verified' => $email_verified,
        ]);
        
        $user = User::whereId($id)->first();

        if ($result) {
            return $this->apiSuccessResponse(new UserResource($user), 'User status has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function updatePhoneVerified($id, $phone_verified)
    {
        $result = User::whereId($id)->update([
            'phone_verified' => $phone_verified,
        ]);
        
        $user = User::whereId($id)->first();

        if ($result) {
            return $this->apiSuccessResponse(new UserResource($user), 'User status has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function updateGovernmentId($id, $government_id)
    {
        $result = User::whereId($id)->update([
            'government_id' => $government_id,
        ]);
        
        $user = User::whereId($id)->first();

        if ($result) {
            return $this->apiSuccessResponse(new UserResource($user), 'User status has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function updatePinkRideStatus(Request $request, $id)
    {
        $result = User::whereId($id)->update([
            'pink_ride' => $request->pink_ride ?? '',
        ]);
        
        $user = User::whereId($id)->first();

        if ($result) {
            return $this->apiSuccessResponse(new UserResource($user), 'User status has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function updateFolksRideStatus(Request $request, $id)
    {
        // dd($request->all());
        $user = User::whereId($id)->first();    

        $isEnablingExtraCare = ((string)$request->folks_ride === '1' && (string)$user->folks_ride !== '1');

        $result = User::whereId($id)->update([
            'folks_ride' => $request->folks_ride ?? '',
        ]);

        
        if ($result && $isEnablingExtraCare) {
            $data = ['first_name' => $user->first_name];
            if (isset($user->email_notification) && $user->email_notification == 1) {
            Mail::to($user->email)->queue(new ExtraCareEligibilityMail($data));
            }
        }
       $notification = Notification::create([
            'category' => 'system',
            'type' => null,
            'receiver_id' => $user->id,
            'posted_by' => $user->id,
            'message' => ' You are now eligible to post Extra-Care Rides',
            'status' => 'completed',
            'notification_type' => 'upcoming'
        ]);
        // Send push notification
        $fcmService = new FCMService();
        $fcm_tokens = FCMToken::where('user_id', $user->id)->get();
        $body = $notification->message;
        $fcmToken = $user->mobile_fcm_token;
        if ($fcmToken) {
            $fcmService->sendNotification($fcmToken, $body);
        }
        foreach ($fcm_tokens as $fcm_token) {
            try {
                $fcmService->sendNotification($fcm_token->token, $body);
            } catch (\Exception $e) {
                Log::error("FCM Notification failed for token: $fcm_token->token, Error: " . $e->getMessage());
            }
        }
        $user = User::whereId($id)->first();

        if ($result) {
            return $this->apiSuccessResponse(new UserResource($user), 'User status has been updated successfully.');
        }
        return $this->errorResponse();
    }

    protected function whereClause($users)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $searchParam = $_GET['searchParam'];
            
            // Apply a condition to filter records with driver = 2 and 3
            $users = $users->where(function ($query) use ($searchParam) {
                $query->where('student', '!=', 0)
                    ->where(function ($subquery) use ($searchParam) {
                        // Add conditions to search by first_name, last_name, email, or ID
                        $subquery->where('first_name', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('email', 'LIKE', '%' . $searchParam . '%')
                            ->orWhere('id', 'LIKE', '%' . $searchParam . '%');
                    });
            });
        }
    
        return $users;
    }

    protected function loadRelations($users)
    {
        return $users;
    }

    protected function sortingAndLimit($users)
    {
        if (isset($_GET['getAll']) && $_GET['getAll'] == '1') {
            return $users->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        }

        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['student_card_upload'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $users = $users->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }


        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $users->paginate($limit);
    }
}
