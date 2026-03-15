<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    /**
     * Procesa una solicitud de inicio de sesión.
     *
     * Este método valida los datos de la solicitud, intenta autenticar al usuario y devuelve una respuesta con el token de acceso y la información del usuario.
     *
     * @param Request $request La solicitud HTTP entrante.
     * @return JsonResponse La respuesta JSON con el token de acceso y la información del usuario, o una respuesta de error en caso de fallo.
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user) {
            return response()->json([
                'errors' => ['Las credenciales proporcionadas son incorrectas.']
            ], 400);
        }

        $credentials = $request->only(['email', 'password']);
        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'errors' => ['Las credenciales proporcionadas son incorrectas.']
            ], 400);
        }

        $user = JWTAuth::user();

        return response()
            ->json([
                'accessToken' => $token,
                'fullname' => $user->name,
                'email' => $user->email,
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ], Response::HTTP_OK)
            ->header('Authorization', $token);
    }

    /**
     * Cerrar sesión del usuario actual.
     *
     * Este método invalida la sesión del usuario actualmente autenticado.
     *
     * @return Response Una respuesta HTTP sin contenido.
     */
    public function logout(): Response
    {
        JWTAuth::logout();
        return response()->noContent();
    }

    /**
     * Refresca el token de acceso del usuario.
     *
     * Este método intenta renovar el token de acceso del usuario autenticado. Si tiene éxito, devuelve un nuevo token de acceso.
     * Si el token de actualización ha caducado o está bloqueado, devuelve una respuesta de error.
     *
     * @return JsonResponse La respuesta JSON con el nuevo token de acceso o un mensaje de error.
     */
    public function refresh(): JsonResponse
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $userId = $payload->get('sub');
            $iat = $payload->get('iat');
            $user = $userId ? User::find($userId) : null;

            if ($user && $user->tokens_revoked_at && (!$iat || $iat < $user->tokens_revoked_at->timestamp)) {
                return response()->json(['error' => 'token_revoked'], 401);
            }

            $token = JWTAuth::refresh();
            if ($token) {
                return response()
                    ->json(['accessToken' => $token], 200)
                    ->header('Authorization', $token);
            }
            return response()->json(['error' => 'token_refresh_failed'], 401);
        } catch (TokenExpiredException $expiredError) {
            return response()->json(['error' => 'refresh_token_expired'], 401);
        } catch (TokenBlacklistedException $error) {
            return response()->json(['error' => 'refresh_token_error'], 401);
        } catch (JWTException $error) {
            return response()->json(['error' => 'token_invalid'], 401);
        }
    }

    /**
     * Verifica si el usuario está autenticado.
     *
     * Este método comprueba si el usuario actual está autenticado. Si lo está, devuelve una instancia de `UserResource` con los datos del usuario.
     * Si no está autenticado o el token ha caducado, devuelve una respuesta con un código de estado 401.
     *
     * @return UserResource|Response La instancia de `UserResource` si el usuario está autenticado, o una respuesta de error si no lo está.
     */
    public function isAuth()
    {
        try {
            return JWTAuth::user();
        } catch (TokenExpiredException $expiredError) {
            return response()->json(['error' => 'refresh_token_expired'], 401);
        }
    }

    /**
     * Registra un nuevo usuario en la aplicación.
     *
     * Este método valida los datos de la solicitud, verifica que el email no esté registrado,
     * crea un nuevo usuario y devuelve sus datos junto con un token de acceso.
     *
     * @param Request $request La solicitud HTTP entrante.
     * @return JsonResponse La respuesta JSON con el token de acceso y la información del usuario, o una respuesta de error en caso de fallo.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            // Crear el nuevo usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Generar token de acceso
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'accessToken' => $token,
                'fullname' => $user->name,
                'email' => $user->email,
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ], Response::HTTP_CREATED)
                ->header('Authorization', $token);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['Ocurrió un error al registrar el usuario.']
            ], 500);
        }
    }

    /**
     * Guarda la información de la sesión del usuario autenticado.
     *
     * Este método obtiene el usuario actual basado en el token JWT y guarda su información en la sesión.
     *
     * @param Request $request La solicitud HTTP entrante.
     * @return JsonResponse La respuesta JSON indicando éxito.
     */
    public function storeSession(Request $request): JsonResponse
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $userId = $payload->get('sub');
            $user = $userId ? User::find($userId) : null;

            if (!$user) {
                return response()->json([
                    'errors' => ['Usuario no autenticado.']
                ], 401);
            }

            // Guardar información en sesión
            session(['name' => $user->name, 'email' => $user->email]);

            return response()->json([
                'message' => 'Sesión guardada correctamente.',
                'name' => $user->name,
                'email' => $user->email,
            ], 200);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'errors' => ['Token expirado.']
            ], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json([
                'errors' => ['Token revocado.']
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'errors' => ['Token inválido.']
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['Ocurrió un error al guardar la sesión.']
            ], 500);
        }
    }
}
