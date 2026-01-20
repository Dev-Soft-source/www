<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;
use App\Models\User;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use StatusResponser;


    public function updateProfile(Request $request)
    {
        $rules = [
            'email' => ['required', 'string', 'email'],
            'admin_email' => ['required', 'string', 'email'],
        ];
        $this->validate($request, $rules);
        
        $admin = $request->user('admin');
        if (!$admin) {
            return $this->errorResponse('Admin not authenticated', 401);
        }
        
        $user = new AdminResource($admin);
        
        if ($request->email != $user->email || $request->admin_email != $user->admin_email) {
            $user = Admin::whereId($user->id)->update([
                'email' => $request->email,
                'admin_email' => $request->admin_email
            ]);
    
            if ($user) {
                return $this->successResponse([], 'Profile has been updated successfully.');
            }
            return $this->errorResponse();
        }

        $rules = [
            'current_password' => ['required', 'string', 'max:50'],
            'new_password' => ['required', 'confirmed', 'max:10'],
        ];
        $this->validate($request, $rules);
        
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse("Old password doesn't match!");
        }

        $user = Admin::whereId($user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        if ($user) {
            return $this->successResponse([], 'Profile has been updated successfully.');
        }
        return $this->errorResponse();
    }
}
