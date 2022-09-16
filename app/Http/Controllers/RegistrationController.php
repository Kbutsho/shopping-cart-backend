<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function Registration(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:4|max:20',
                'email' => 'required|email',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|max:14|min:11',
                'address' => 'required',
                'role' => 'required',
                'password' => [
                    'required',
                    'string',
                    'min:10',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&]/'
                ],
                'confirmPassword' => [
                    'required',
                    'same:password',
                    'min:10'
                ]
            ],
            [
                'phone.required' => 'Phone is required!',
                'phone.regex' => 'Invalid phone number!',
                'phone.max' => 'Number should 11 characters!',
                'address.required' => 'Address is required!',
                'confirmPassword.required' => 'Confirm Password is Required!',
                'confirmPassword.same' => 'Confirm Password not match!',
                'password.required' => 'Password is required!',
                'password.regex' => 'Invalid password formate!',
                'password.min' => 'Must contain 10 characters!',
                'name.required' => 'Name is required!',
                'email.required' => 'Email is required!',
                'email.email' => 'Invalid email address!',
                'name.min' => 'Insert more than 4 characters!',
                'name.max' => 'Insert less than  20 characters!',
                'role.required' => 'Select your user role!'
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ]);
        } else {
            $email = $request->email;
            $phone = $request->phone;
            $role = $request->role;

            $userEmail = User::where([['email', '=', $email]])->first();
            $userPhone = User::where([['phone', '=', $phone]])->first();


            if ($userEmail && $userPhone) {
                return response()->json([
                    'duplicate' => 'Email and phone already taken! use another one!',
                ]);
            } else if ($userEmail) {
                return response()->json([
                    'duplicateEmail' => 'Email already taken! use another one!',
                ]);
            } else if ($userPhone) {
                return response()->json([
                    'duplicatePhone' => 'Phone Number already taken! use another one!',
                ]);
            } else {
                $user = new User();
                $user->name = $request->name;
                $user->role = $request->role;
                $user->email = $request->email;
                $user->phone = $request->phone;

                if ($user->role == 'customer') {
                    $user->status = "Approved";
                } else {
                    $user->status = "Pending";
                }
                $user->password = $request->password;
                $user->save();

                if ($role == 'admin') {
                    $admin = new Admin();
                    $admin->name = $request->name;
                    $admin->userId = $user->id;
                    $admin->email = $request->email;
                    $admin->phone = $request->phone;
                    $admin->address = $request->address;
                    $admin->save();
                    return response()->json([
                        'success' => 'Registration Successful. wait for approval!',
                    ]);
                } elseif ($role == 'seller') {
                    $admin = new Seller();
                    $admin->name = $request->name;
                    $admin->userId = $user->id;
                    $admin->email = $request->email;
                    $admin->phone = $request->phone;
                    $admin->address = $request->address;
                    $admin->save();
                    return response()->json([
                        'success' => 'Registration Successful. wait for approval!',
                    ]);
                } elseif ($role == 'customer') {
                    $admin = new Customer();
                    $admin->name = $request->name;
                    $admin->userId = $user->id;
                    $admin->email = $request->email;
                    $admin->phone = $request->phone;
                    $admin->address = $request->address;
                    $admin->save();
                    return response()->json([
                        'success' => 'Registration Successful. Login here!',
                    ]);
                }
            }
        }
    }

    function deleteUser(Request $request)
    {
        $id = $request->id;
        $user = User::where([['id', '=', $id]])->first();
        if ($user) {
            $user->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Delete Successfully!',
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'user not found!',
            ]);
        }
    }
}
