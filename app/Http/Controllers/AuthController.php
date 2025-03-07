<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;

class AuthController extends Controller
{
  private $currentToken = null;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        // return $credentials;
        if (!$token = auth()->attempt($credentials)) {
            // return response()->json(['error' => 'Unauthorized'], 401);
            return response()->json(['error' => 'Unauthorized: ' . json_encode($credentials)], 401);
        }
        $this->currentToken = $token;
        // $this->currentToken = auth()::attempt($credentials);
        return $this->respondWithToken($token);
        
        
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        // return response()->json(auth()->user());
    }
    public function user(Request $request){
      try {
        $user = auth()->userOrFail();

      } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
          // do something

          return response()->json(['error' => 'Unauthorized: ' . json_encode($request->all())], 401);
      }
      $userModel = (new App\Models\User())->with(['user_basic_information', 'user_profile_photo'])->find((auth()->user())['id']);
      return response()->json(["data" => $userModel]);
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $newToken = auth()->refresh(true);
        return response()->json([
            'token' => $newToken,
        ]);
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
            'expires_in' => auth()->factory()->getTTL(), // mintues to seconds
            'user' => array_merge(auth()->user()->toArray(), get_object_vars(auth()->getPayload()->get('custom')))
            // 'payoad' => auth()->getPayload(),
        ])->header('Authorization', 'Bearer ' . $this->currentToken);;
    }
}