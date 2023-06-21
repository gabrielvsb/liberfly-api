<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     *Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    /**
     * @OA\POST(
     *  tags={"User"},
     *  summary="Realize login",
     *  description="Este endpoint faz o login de um usuário existente",
     *  path="/api/auth/login",
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="gabriel@example.org"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *          )
     *      ),
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="Usuário logado",
     *    @OA\JsonContent(
     *       @OA\Property(property="access_token", type="string", example="token"),
     *       @OA\Property(property="token_type", type="string", example="bearer"),
     *       @OA\Property(property="expires_in", type="string", example="1"),
     *    )
     *  ),
     *  @OA\Response(
     *    response=400,
     *    description="Erros de validação",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The team name must be a string. (and 4 more errors)"),
     *       @OA\Property(property="errors", type="string", example="..."),
     *    )
     *  )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\POST(
     *  tags={"User"},
     *  summary="Crie um novo usuário",
     *  description="Este endpoint cria um registro de um novo usuário",
     *  path="/api/auth/register",
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *             required={"email","password","name","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Gabriel"),
     *             @OA\Property(property="email", type="string", example="gabriel@example.org"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *             @OA\Property(property="password_confirmation", type="string", example="12345678"),
     *          )
     *      ),
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="Usuário criado",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User successfully registered"),
     *       @OA\Property(property="user", type="string", example="user"),
     *       @OA\Property(property="token", type="string", example="token"),
     *    )
     *  ),
     *  @OA\Response(
     *    response=400,
     *    description="Erros de validação",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The team name must be a string. (and 4 more errors)"),
     *       @OA\Property(property="errors", type="string", example="..."),
     *    )
     *  )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Get the authenticated User.
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }


    /**
     * Log the user out (Invalidate the token).
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     * @param $token
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
