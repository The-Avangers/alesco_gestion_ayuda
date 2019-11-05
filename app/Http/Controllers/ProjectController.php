<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectPerson;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ProjectController extends Controller
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
            $project = new Project;
            $project->name = $request->name;
            $project->startDate = $request->startDate;
            $project->endDate = $request->endDate;
            $project->price = $request->price;
            $project->save();
            $proyectPerson = new ProjectPerson;
            $proyectPerson->projectId = $project->id;
            $proyectPerson->personId = $request->personId;
            $proyectPerson->role = $request->personRole;
            $proyectPerson->save();
            return $project->id;
        } catch (QueryException $exception) {
            return response()->json([
                'Error' => 'Error al Registrar Proyecto'], 400);
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
