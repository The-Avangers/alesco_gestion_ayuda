<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            if ($userAuth->role != 'Administrador') {
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
        //
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
}
