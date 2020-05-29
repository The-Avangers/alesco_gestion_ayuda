<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Response as Resp;
use App\Aid;
use App\User;
use App\Request as Req;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try{
            $user = Auth::user();
            if ($user->role != 'Administrador'){
                return response()->json(['Message' => 'Unauthorized'], 401);
            }
            else
            {
                $req = Req::where('id',$request->id_req)->get();
                Log::channel('stdout')->info($req[0]->id_aid);
                $aid = Aid::where('id',$req[0]->id_aid)->get();
                if ($aid[0]->unit <= $request->unit){
                    return response()->json([
                        'Error' => 'La Cantidad Asignada Excede al Disponible'], 400);
                }
            }
            $resp = new Resp();
            $resp->approved = $request->approved;
            $resp->unit = $request->unit;
            $resp->id_req = $request->id_req;
            $resp->created_at = date('Y-m-d');
            $resp->save();
            if ($request->approved == true){
                $aid[0]->unit = $aid[0]->unit - $request->unit;
                $aid[0]->save();
            }
        } catch (Exception $e)
        {
            return response()->json([
                'Error' => 'Error al Registrar Respuesta a Solicitud'], 400);
        }
        return $resp->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }
}
