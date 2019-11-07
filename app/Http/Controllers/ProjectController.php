<?php

namespace App\Http\Controllers;

use App\Person;
use App\Project;
use App\ProjectPerson;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $projects = DB::table('project')->get();
            foreach ($projects as $project)
                $project->paid = $project->paid == 1;
            return $projects;
        } catch (QueryException $exception) {
            return response()->json([
                'Error' => 'Error consultando proyectos'], 400);
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
        try{
            $project = new Project;
            $project->name = $request->name;
            $project->startDate = $request->startDate;
            $project->endDate = $request->endDate;
            $project->price = $request->price;
            $people = $request->people;
            $project->save();
            foreach ($people as $person){
                $proyectPerson = new ProjectPerson;
                $proyectPerson->projectId = $project->id;
                $proyectPerson->personId = $person['id'];
                $proyectPerson->role = $person['role'];
                $proyectPerson->save();
            }
            return $project;
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
        try {
            $project = Project::where('id', $id)->get();
            if (!$project)
                return response()->json(['Error' => 'El projecto buscado no existe']);
            $projectPeople = ProjectPerson::where('projectId', $id)->get();
            $project[0]->paid = $project[0]->paid == 1;
            $peopleInvolved = array();
            foreach ($projectPeople as $projectPerson ){
                $person = Person::where('id', $projectPerson->personId)->get();
                $person[0]->role = $projectPerson->role;
                array_push($peopleInvolved, $person[0]);
            }
            $project[0]->peopleInvolved = $peopleInvolved;
            return $project[0];
        } catch (QueryException $exception){
            return response()->json(['Error' => 'Error consultando el projecto']);
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
