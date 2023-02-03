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

      /**
     * @OA\Post(
     *      path="/api/register",
     *      operationId="signup",
     *      tags={"Connexion"},
     *      summary="Register",
     *      description="Register",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name","email", "password"},
     *            @OA\Property(property="name", type="string", format="string", example="User Name"),
     *            @OA\Property(property="email", type="string", format="string", example="User Email"),
     *            @OA\Property(property="password", type="string", format="string", example="Password"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
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

                $reponse =   response()->json(['status' => true, 'message' => 'User crée avec succés', 'data' => [$user]], 200);
            }

            return $reponse;
        } catch (\Exception $e) {
            return response()->json(['status' => true, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }


    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Connexion"},
     *      summary="Connexion",
     *      description="Connexion",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"email", "password"},
     *            @OA\Property(property="email", type="string", format="string", example="User Email"),
     *            @OA\Property(property="password", type="string", format="string", example="Password"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
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


    /**
     * @OA\Post(
     *    security={{"bearer_token":{}}}, 
     *      path="/api/logout",
     *      operationId="logout",
     *      tags={"Connexion"},
     *      summary="Logout",
     *      description="Logout",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id"},
     *            @OA\Property(property="id", type="string", format="string", example="User "),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
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
