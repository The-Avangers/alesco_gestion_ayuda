<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  Request  $request
     * @return \App\User
     */
    protected function store (Request $request)
    {
        $rules = [
            'name' => 'required|max:20',
            'lastname' => 'required|max:20',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:8',
            'role' => 'in:Administrador,Solicitante,Consultor'
        ];
        $messages = [
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre del usuario no puede exceder a :max caracteres',
            'lastname.required' => 'El apellido es requerido',
            'lastname.max' => 'El apellido del usuario no puede exceder a :max caracteres',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email es inválido',
            'email.unique' => 'El email ya existe',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener ocho caracteres como mínimo',
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
            $user->password = Hash::make($request->password);
            $user->save();
            $token = $user->createToken('Access Token')->accessToken;
            return response($user)->header('token', $token)->header('Access-Control-Expose-Headers', 'token');
        } catch (ValidationException $exception) {
            Log::channel('stdout')->error($exception->getMessage());
            return response()->json($exception->validator->errors(),400);
        }

    }
}
