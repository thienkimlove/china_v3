<?php
/**
 * Created by PhpStorm.
 * User: tieungao
 * Date: 2020-04-22
 * Time: 13:32
 */

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController
{

    public function username()
    {
        $login = request()->input('identity');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    protected function credentials()
    {
        return request()->only($this->username(), 'password');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ], 200);
    }


    public function register(Request $request)
    {

        if (!$request->input('username')) {
            $request->merge(['username' => $request->input('email')]);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => json_encode($validator->errors())
            ], 400);
        }

        try {

            $data = $request->all();
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);

            return response()->json([
                'success' => true,
                'data'      =>  $user,
                'token' => auth('api')->fromUser($user)
            ], 200);

        } catch (\Exception $exception) {

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);

        }
    }


    public function login()
    {
        $credentials = $this->credentials();

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => auth('api')->user(),
            'token' => $token
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * When Token is failed it will not come to here
     * Instead that, it be held at middleware
     */
    public function user()
    {
        return response()->json([
            'success' => true,
            'data' => auth('api')->user()
        ], 200);
    }

    public function pass(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => json_encode($validator->errors())
            ], 400);
        }

        try {

            auth('api')->user()->update([
                'password' => bcrypt($request->input('password'))
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Update password success'
            ], 200);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }

    }

    public function update(Request $request)
    {

        $allowFields = [
            'name',
            'address',
            'phone',
            'bank_account_name',
            'bank_account_number',
            'bank_name',
            'bank_branch'
        ];

        $updateData = [];

        foreach ($request->all() as $key=>$value) {
            if (in_array($key, $allowFields)) {
                $updateData[$key] = $value;
            }
        }

        try {

            auth('api')->user()->update($updateData);

            return response()->json([
                'success' => true,
                'data' => auth('api')->user(),
                'message' => 'Update user success'
            ], 200);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }

    }

    public function refresh()
    {
        return response()->json([
            'success' => true,
            'token' => auth('api')->refresh(),
        ], 200);
    }


    //=====================END OF AUTHENTICATION METHODS=====================

}