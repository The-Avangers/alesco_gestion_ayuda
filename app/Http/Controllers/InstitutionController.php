<?php

namespace App\Http\Controllers;

use App\Institution;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $user = Auth::user();
            if ($user->role != 'Administrador' && $user->role != 'Consultor'){
                return response()->json(['Message' => 'Unauthorized'], 401);
            }
            Log::channel('stdout')->info('Getting all institutions');
            $institutions = DB::table('institution')->get();
            return $institutions;
        }catch (\Exception $ex) {
            Log::channel('stdout')->error($ex);
            return response()->json([
                'Error' => 'Error al Consultar instituciones'], 400);
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

        try {
            $user = Auth::user();
            if ($user->role != 'Administrador'){
                return response()->json(['Message' => 'Unauthorized'], 401);
            }
            $institution = new Institution;
            $institution->name = $request->name;
            $institution->save();
            return $institution;
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json(['Error' => 'Error al Registrar Institucion'], 400);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $user = Auth::user();
            if ($user->role != 'Administrador' && $user->role != 'Consultor'){
                return response()->json(['Message' => 'Unauthorized'], 401);
            }
            Log::channel('stdout')->info('Getting institution');
            $institution = Institution::find($id);
            return $institution;
        } catch (\Exception $exception){
            Log::channel('stdout')->error($exception);
            return response()->json(['Error' => 'Error al obtener Institucion'], 400);
        }

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
        try {
            Log::channel('stdout')->info('Edit institution');
            $user = Auth::user();
            if ($user->role != 'Administrador'){
                Log::channel('stdout')->error('Unauthorized');
                return response()->json(['Message' => 'Unauthorized'], 401);
            }
            $institution = Institution::find($id);
            $institution->name = $request->name;
            $institution->save();
            return $institution;
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json(['Error' => 'Error al Editar Institucion'], 400);
        }
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
