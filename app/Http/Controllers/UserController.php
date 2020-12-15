<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        try{
            $userAuth = Auth::user();
            if ($userAuth->role != 'Administrador' && $userAuth->role != 'Consultor') {
                return response()->json(['Message' => 'Unauthorized'], 401);
            }
            $users = DB::table('user')->get();
            $returnUsers = array();
            foreach ($users as $user) {
                $userData = new \stdClass();
                $userData->name = $user->name;
                $userData->lastname = $user->lastname;
                $userData->email = $user->email;
                $userData->role = $user->role;
                array_push($returnUsers, $userData);
            }
            return $returnUsers;
        } catch (\Exception $ex) {
            Log::channel('stdout')->error($ex);
            return response()->json([
                'Error' => $ex->getMessage()], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userAuth = Auth::user();
        if ($userAuth->role != 'Administrador') {
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        $rules = [
            'ci' => ' required',
            'name' => 'required|max:20',
            'lastname' => 'required|max:20',
            'email' => 'required|email|unique:user',
            'role' => 'in:Administrador,Solicitante,Consultor'
        ];
        $messages = [
            'ci.required' => 'La cedula de identidad es requerida',
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre del usuario no puede exceder a :max caracteres',
            'lastname.required' => 'El apellido es requerido',
            'lastname.max' => 'El apellido del usuario no puede exceder a :max caracteres',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email es inválido',
            'email.unique' => 'El email ya existe',
            'role.in' => 'Rol inexistente'
        ];
        try
        {
            $request->validate($rules,$messages);
            $user = new User();
            $user->name = $request->name;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->role = $request->role;
            $user->ci = $request->ci;
            $user->password = Hash::make($request->ci);
            $user->save();
            $token = $user->createToken('Access Token')->accessToken;
            return response($user)->header('token', $token)->header('Access-Control-Expose-Headers', 'token');
        } catch (ValidationException $exception) {
            Log::channel('stdout')->error($exception->getMessage());
            return response()->json($exception->validator->errors(),400);
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json([
                'Error' => $exception->getMessage()], 400);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      try {
          $userAuth = Auth::user();
          if ($userAuth->role != 'Administrador') {
              return response()->json(['Message' => 'Unauthorized'], 401);
          }
          $user = User::find($id);
          $user->name = $request->name;
          $user->lastname = $request->lastname;
          $user->email = $request->email;
          $user->role = $request->role;
          $user->save();
          return $user;
      } catch (\Exception $ex) {}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function changePassword(Request $request) {
        try{
            Log::channel('stdout')->info("start change password");
            $userAuth = Auth::user();
            $user = User::find($userAuth->id);
            if (!Hash::check($request->oldPassword, $user->password))
            {
                return response()->json([
                    'Error' => 'La contraseña actual es incorrecta'], 400);
            }
            Log::channel('stdout')->info("Password is ok");
            $user->password = Hash::make($request->newPassword);
            $user->save();
            return $user;
        } catch (\Exception $exception) {
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
    }
}
