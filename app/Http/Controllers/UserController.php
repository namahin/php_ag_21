<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
//    user registration function
    function UserRegistration(Request $request)
    {
        try {
            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password')
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User Registration Successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User Registration Failed'
            ], 401);
        }
    }

//    user login function
    function UserLogin(Request $request)
    {
        $count = User::where('email', '=', $request->input('email'))
            ->where('password', '=', $request->input('password'))
            ->count();

        if ($count == 1) {
            $token = JWTToken::CreateToken($request->input('email'));
            return response()->json([
                'status' => 'success',
                'message' => 'User Login Successfully',
                'token' => $token
            ], 200);

        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized User'
            ], 401);
        }
    }

//    OTP sender function
    function SendOTPCode(Request $request)
    {
        $email = $request->input('email');
        $otp = rand(1000, 9999);
        $count = User::where('email', '=', $email)->count();

        if ($count == 1) {

            //OTP email address
            Mail::to($email)->send(new OTPMail($otp));

            //OTP code table update
            User::where('email', '=', $email)->update(['otp' => $otp]);

            return response()->json([
                'status' => 'success',
                'message' => '4 digit OTP code send successfully'
            ], 200);

        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized User'
            ], 401);
        }
    }

//    Verify OTP
    function VerifyOTP(Request $request)
    {
        $email = $request->input('email');
        $otp = $request->input('otp');
        $count = User::where('email', '=', $email)->where('otp', '=', $otp)->count();

        if ($count == 1) {
            // Database OTP Update
            User::where('email', '=', $email)->update(['otp' => 0]);

            // Pass Reset Token Issue
            $token = JWTToken::CreateTokenForSetPassword($request->input('email'));
            return response()->json([
                'status' => 'success',
                'message' => 'OTP Verified Successfully',
                'token' => $token
            ], 200);


        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized User'
            ], 401);
        }
    }

//    Reset Password
    function ResetPassword(Request $request)
    {
        try {
            $email = $request->header('email');
            $password = $request->input('password');
            User::where('email', '=', $email)->update([
                'password' => $password
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Reset Password Successfully'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Something Wrong'
            ], 401);
        }

    }

}
