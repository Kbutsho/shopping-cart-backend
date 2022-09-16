<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Seller;
use App\Models\Token;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function  Login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => [
                    'required',
                    'string',
                    'min:10',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&]/',
                ],
            ],
            [
                'password.regex' => 'Invalid password formate!',
                'password.required' => 'Password is required!',
                'email.required' => 'Email is required!',
                'email.email' => 'Invalid email address!',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ]);
        } else {
            $user = User::where([
                ['email', '=', $request->email],
                ['password', '=', $request->password]
            ])->first();

            if ($user) {
                if ($user->status == 'Pending') {
                    return response()->json([
                        'pending_error' => 'Your account is not approved! Try again Later!',
                    ]);
                } else {
                    $api_token = Str::random(64);
                    $token = new Token();
                    $token->role = $user->role;
                    $token->token = $api_token;
                    $token->userId = $user->id;
                    $token->created_at = new DateTime();
                    $token->save();

                    if ($user->role == 'admin') {
                        $admin = Admin::where([
                            ['userId', '=', $user->id],
                        ])->first();

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Login Successfully',
                            'token' => $api_token,
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'phone' => $user->phone,
                            'userId' => $admin->userId,
                            'image' => $admin->image,
                            'address' => $admin->address
                        ]);
                    } elseif ($user->role == 'customer') {
                        $customer = Customer::where([
                            ['userId', '=', $user->id],
                        ])->first();

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Login Successfully',
                            'token' => $api_token,
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'phone' => $user->phone,
                            'userId' => $customer->userId,
                            'image' => $customer->image,
                            'address' => $customer->address
                        ]);
                    } else if ($user->role == 'seller') {
                        $seller = Seller::where([
                            ['userId', '=', $user->id],
                        ])->first();

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Login Successfully',
                            'token' => $api_token,
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'phone' => $user->phone,
                            'userId' => $seller->userId,
                            'image' => $seller->image,
                            'address' => $seller->address
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'status' => 'notFound',
                    'message' => 'User not Found'
                ]);
            }
        }
    }
    public function Logout(Request $request)
    {
        $token = Token::where('token', $request->token)->first();
        if ($token) {
            $token->expired_at = new DateTime();
            $token->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Logout successfully!'
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid token!'
            ]);
        }
    }
}
