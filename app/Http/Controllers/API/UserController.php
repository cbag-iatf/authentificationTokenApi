<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/users",
     *    operationId="index",
     *    tags={"Users"},
     *    summary="Get list of Users",
     *    description="Get list of Users",
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function index()
    {
        //
        $users = User::all();
        return response()->json($users);
    }


    /**
     * @OA\Post(
     *      path="/api/users",
     *      operationId="store",
     *      tags={"Users"},
     *      summary="Store User in DB",
     *      description="Store User in DB",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name", "email", "password"},
     *            @OA\Property(property="name", type="string", format="string", example=" User name"),
     *            @OA\Property(property="email", type="string", format="string", example="User Email"),
     *            @OA\Property(property="password", type="string", format="string", example="Password"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);

        // On crée un nouvel utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // On retourne les informations du nouvel utilisateur en JSON
        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     *    security={{"bearer_token":{}}}, 
     *    path="/api/users/{id}",
     *    operationId="show",
     *    tags={"Users"},
     *    summary="Get User Detail",
     *    description="Get User Detail",
     *    @OA\Parameter(name="id", in="path", description="Id of User", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data",type="object")
     *           ),
     *        )
     *       )
     *  )
     */
    public function show(User $user)
    {
        //
        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     operationId="update",
     *     tags={"Users"},
     *     summary="Update users in DB",
     *     description="Update user in DB",
     *     @OA\Parameter(name="id", in="path", description="Id of Article", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *           required={"name", "email", "password"},
     *           @OA\Property(property="name", type="string", format="string", example="User name"),
     *           @OA\Property(property="email", type="string", format="string", example="Email User"),
     *           @OA\Property(property="password", type="string", format="string", example="User Password"),
     *        ),
     *     ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function update(Request $request, User $user)
    {
        //
        // La validation de données
        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        // On modifie les informations de l'utilisateur
        $user->update([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);

        // On retourne la réponse JSON
        return response()->json();
    }

    /**
     * @OA\Delete(
     *    path="/api/users/{id}",
     *    operationId="destroy",
     *    tags={"Users"},
     *    summary="Delete User",
     *    description="Delete user",
     *    @OA\Parameter(name="id", in="path", description="Id of User", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *         @OA\Property(property="status_code", type="integer", example="200"),
     *         @OA\Property(property="data",type="object")
     *          ),
     *       )
     *      )
     *  )
     */
    public function destroy(User $user)
    {
        //
        // On supprime l'utilisateur
        $user->delete();

        // On retourne la réponse JSON
        return response()->json();
    }

    public function getProfile(Request $request)
    {
        return response()->json(auth()->user());
        // try {
        //     $user = User::find($request->user()->id);
        //     return response()->json(['status' => true, 'message' => 'Logged In', 'data' => [$user]], 200);
        // } catch (\Exception $e) {
        //     return response()->json(['status' => true, 'message' => $e->getMessage(), 'data' => []], 500);
        // }
    }
}
