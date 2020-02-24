<?php

namespace App\Http\Controllers;

use App\User;
use App\Aid;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Request as Req;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role != 'Administrador'){
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        $reqs = DB::table('requests')->get();
        foreach ($reqs as $req){
            $user_req = User::where('id',$req->id_user)->get();
            $aid_req = Aid::where('id',$req->id_aid)->get();
            $req->aid = $aid_req[0]->name." ".$aid_req[0]->measure;
            $req->user_name = $user_req[0]->name." ".$user_req[0]->lastname;
            $req->email = $user_req[0]->email;
        }
        return $reqs;
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
        $user = Auth::user();
        if ($user->role != 'Administrador'){
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        try
        {
            $req = new Req;
            $req->id_user = $request->id_user;
            $req->id_aid = $request->id_aid;
            $req->created_at = $request->created_at;
            $req->save();
        } catch (Exception $e)
        {
            return response()->json([
                'Error' => 'Error al Registrar Solicitud'], 400);
        }
        return $req->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        if ($user->role != 'Administrador'){
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        $req = Req::where('id', $id)->get();
        if ( Req::where('id', $id)->count() == 0)
        {
            return response()->json([
                'Error' => 'Solicitud No Existe'], 404);
        }
        return $req;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $req = Req::where('id', $id)->get();
        if ( Req::where('id', $id)->count() == 0)
        {
            return response()->json([
                'Error' => 'Solicitud No Existe'], 404);
        }
        $req->delete();
    }
}
