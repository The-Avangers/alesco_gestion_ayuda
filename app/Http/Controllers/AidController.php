<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Aid;
use \Illuminate\Database\QueryException;

class AidController extends Controller
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
        try
        {
            $aid = new Aid;
            $aid->name = $request->name;
            $aid->measure = $request->measure;
            $aid->type = $request->type;
            $aid->unit = $request->unit;
            $aid->save();
        } catch (QueryException $e)
        {
            return response()->json([
                'Error' => 'Error al Registrar Insumo'], 404);
        }
        return $aid->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $aid = Aid::where('id', $id)->get();
        if ( Aid::where('id', $id)->count() == 0)
        {
            return response()->json([
                'Error' => 'Insumo No Existe'], 404);
        }
        return $aid;
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
