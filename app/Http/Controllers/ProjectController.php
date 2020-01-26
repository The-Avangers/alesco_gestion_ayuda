<?php

namespace App\Http\Controllers;

use App\Institution;
use App\Person;
use App\Project;
use App\ProjectInstitution;
use App\ProjectPayment;
use App\ProjectPerson;
use App\ProjectProgress;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            Log::channel('stdout')->info("Getting all projects");
            $projects = DB::table('project')->get();
            foreach ($projects as $project)
                $project->paid = $project->paid == 1;
            return $projects;
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json(['Error' => 'Error consultando proyectos'], 400);
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
        $project = new Project;
        $projectInstitution = new ProjectInstitution;
        try{
            $project->name = $request->name;
            $project->startDate = $request->startDate;
            $project->endDate = $request->endDate;
            $project->price = $request->price;
            $people = $request->people;
            $project->save();
            $projectInstitution = new ProjectInstitution;
            $projectInstitution->projectId = $project->id;
            $projectInstitution->institutionId = $request->institutionId;
            $projectInstitution->save();
            foreach ($people as $person){
                $proyectPerson = new ProjectPerson;
                $proyectPerson->projectId = $project->id;
                $proyectPerson->personId = $person['id'];
                $proyectPerson->role = $person['role'];
                $proyectPerson->save();
            }
            return $project;
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            if ($project){
                $project->delete();
            }
            if ($projectInstitution){
                $projectInstitution->delete();
            }
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
            if (count($project) == 0)
                return response()->json(['Error' => 'El projecto buscado no existe'], 400);
            $peopleInvolved = array();
            $projectProgresses = array();
            $payments = array();
            $projectPeople = ProjectPerson::where('projectId', $id)->get();
            $projectProgress = ProjectProgress::where('projectId', $id)->get();
            $projectPayments = ProjectPayment::where('projectId', $id)->get();
            $projectInstitutions = ProjectInstitution::where('projectId', $id)->get();
            $institutions = Institution::where('id', $projectInstitutions[0]->institutionId)->get();
            $project[0]->institution = $institutions[0]->name;
            $project[0]->paid = $project[0]->paid == 1;
            foreach ($projectPeople as $projectPerson ){
                $person = Person::where('id', $projectPerson->personId)->get();
                $person[0]->role = $projectPerson->role;
                array_push($peopleInvolved, $person[0]);
            }
            foreach ($projectProgress as $progress) {
                $progressProject = new \stdClass;
                $progressProject->milestone = $progress->milestone;
                $progressProject->date = $progress->date;
                array_push($projectProgresses, $progressProject);
            }
            foreach ($projectPayments as $payment) {
                $projectPayment = new \stdClass;
                $projectPayment->amount = $payment->amount;
                $projectPayment->date = $payment->paymentDate;
                array_push($payments, $projectPayment);
            }
            $project[0]->peopleInvolved = $peopleInvolved;
            $project[0]->progress = $projectProgresses;
            $project[0]->payments = $payments;
            return $project[0];
        } catch (\Exception $exception){
            Log::channel('stdout')->error($exception);
            return response()->json(['Error' => 'Error consultando el projecto'], 400);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProgress(Request $request) {
        try {
            $projectProgress = new ProjectProgress;
            $projectProgress->projectId = $request->projectId;
            $projectProgress->milestone = $request->milestone;
            $projectProgress->date = $request->date;
            $projectProgress->save();
            return $projectProgress;
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json(['Error' => 'Error agregando hito de proyecto'], 400);

        }
    }

}
