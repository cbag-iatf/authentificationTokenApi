<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class AuthController extends Controller
{


    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }



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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
        // // dd('cbag');
        // try {
        //     $validation = Validator::make($request->all(), [
        //         'name' => 'required|max:100',
        //         'email' => 'required|email',
        //         'password' => 'required|min:8'
        //     ]);


        //     if ($validation->fails()) {
        //         $error = $validation->errors()->all()[0];
        //         response()->json(['status' => 'false', 'message' => $error, 'data' => []], 433);
        //     } else {

        //         $user = User::create([
        //             'name' => $request->name,
        //             'email' => $request->email,
        //             'password' => Hash::make($request->password)
        //         ]);

        //         $reponse =   response()->json(['status' => true, 'message' => 'User crée avec succés', 'data' => [$user]], 200);
        //     }

        //     return $reponse;
        // } catch (\Exception $e) {
        //     return response()->json(['status' => true, 'message' => $e->getMessage(), 'data' => []], 500);
        // }
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
        // try {
        //     $validation = Validator::make($request->all(), [
        //         'email' => 'required|email',
        //         'password' => 'required|min:8'
        //     ]);


        //     if ($validation->fails()) {
        //         $error = $validation->errors()->all()[0];
        //         return response()->json(['status' => 'false', 'message' => $error, 'data' => []], 433);
        //     } else {
        //         $user = User::whereEmail($request->email)->first();
        //         // dd($user);
        //         if (!$user) {
        //             return response()->json(['status' => false, 'message' => 'Login ou Mot de passe incorrect', 'data' => []], 433);
        //         } else {
        //             if (Hash::check($request->password, $user->password)) {

        //                 $token = $user->createToken('API TOKEN')->plainTextToken;
        //                 $user->token = $token;
        //                 return response()->json(['status' => true, 'message' => 'Logged In', 'data' => [$user]], 200);
        //             } else {
        //                 return response()->json(['status' => false, 'message' => 'Login ou Mot de passe incorrect', 'data' => []], 433);
        //             }
        //         }
        //     }
        // } catch (\Exception $e) {
        //     return response()->json(['status' => true, 'message' => $e->getMessage(), 'data' => []], 500);
        // }
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
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
        // try {

        //     $request->user()->currentAccessToken()->delete();
        //     $request->user()->tokens()->delete();
        //     return response()->json(['status' => true, 'message' => 'Logout avec Succés', 'data' => []], 500);
        // } catch (\Exception $e) {
        //     return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        // }
    }


    public function refresh(Request $request)
    {
        return $this->respondWithToken(auth()->refresh());
        // try {

        //     $request->user()->currentAccessToken()->delete();

        //     $request->user()->tokens()->delete();
        //     $token = $request->user()->createToken('API TOKEN')->plainTextToken;
        //     $token = [
        //         'token' => $token
        //     ];

        //     return response()->json(['status' => true, 'message' => 'Refresh Token', 'data' => [$token]], 200);
        // } catch (\Exception $e) {
        //     return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []], 500);
        // }
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
