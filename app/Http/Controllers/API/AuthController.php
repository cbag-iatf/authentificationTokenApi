<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class AuthController extends Controller
{
    //

    public function signup(Request $request)
    {
        // dd('cbag');
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'email' => 'required|email',
                'password' => 'required|min:8'
            ]);


            if ($validation->fails()) {
                $error = $validation->errors()->all()[0];
                response()->json(['status' => 'false', 'message' => $error, 'data' => []], 433);
            } else {

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);

                $reponse =   response()->json(['status' => 'false', 'message' => 'User crée avec succés', 'data' => [$user]], 433);
            }

            return $reponse;
        } catch (\Exception $e) {
            return response()->json(['status' => true, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:8'
            ]);


            if ($validation->fails()) {
                $error = $validation->errors()->all()[0];
                return response()->json(['status' => 'false', 'message' => $error, 'data' => []], 433);
            } else {
                $user = User::whereEmail($request->email)->first();
                // dd($user);
                if (!$user) {
                    return response()->json(['status' => false, 'message' => 'Login ou Mot de passe incorrect', 'data' => []], 433);
                } else {
                    if (Hash::check($request->password, $user->password)) {

                        $token = $user->createToken('API TOKEN')->plainTextToken;
                        $user->token = $token;
                        return response()->json(['status' => true, 'message' => 'Logged In', 'data' => [$user]], 200);
                    } else {
                        return response()->json(['status' => false, 'message' => 'Login ou Mot de passe incorrect', 'data' => []], 433);
                    }
                }

               
            }
        } catch (\Exception $e) {
            return response()->json(['status' => true, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            
            $request->user()->currentAccessToken()->delete();
            $request->user()->tokens()->delete();
            return response()->json(['status' => true, 'message' => 'Logout avec Succés', 'data' => []], 500);
             
            
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }
}
