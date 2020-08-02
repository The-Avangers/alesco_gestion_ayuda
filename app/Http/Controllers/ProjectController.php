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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{


    public $messages = [
        "name.required" => "El nombre del proyecto es requerido",
        "name.string" => "El nombre del proyecto debe ser un string",
        "startDate.required" => "La fecha de inicio es requerida",
        "startDate.date" => "El campo de la fecha de inicio debe ser una fecha",
        "startDate.after_or_equal" => "El campo de la fecha de inicio debe ser mayor o igual a hoy",
        "endDate.required" => "La fecha de entrega es requerida",
        "endDate.date" => "El campo de la fecha de entrega debe ser una fecha",
        "endDate.after" => "La fecha de entrega debe ser mayor a la fecha de inicio",
        "people.required"=> "El campo people es requerido y debe ser un arreglo",
        "people.array" => "El campo people debe ser un arreglo",
        "people.*.id.required" => "El id de al menos una persona es requerido",
        "people.*.id.numeric" => "El id de la persona debe ser numerico",
        "people.*.role.required" => "El rol de al menos un usuario es requerido",
        "people.*.role.string" => "El rol del usuario debe ser un string",
        "institutionId.required" => "El id de la institución es requerido ",
        "institutionId.numeric" => "El id de la institucion debe ser un numero",
        "projectId.required" => "El id del proyecto es requerido",
        "projectId.numeric" => "El id del proyecto debe ser un numero",
        "milestone.required"=> "El hito es requerido",
        "milestone.string" => "El hito debe ser un string",
        "date.required" => "La fecha del hito es requerida",
        "date.date" => 'El campo de fecha de hito debe ser una fecha'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user = Auth::user();
            if ($user->role != 'Administrador' && $user->role != 'Consultor'){
                return response()->json(['Message' => 'Unauthorized'], 401);
            }
            Log::channel('stdout')->info($user);
            Log::channel('stdout')->info("Getting all projects");
            $projects = DB::table('project')->get();
            foreach ($projects as $project) {
                $project->paid = $project->paid == 1;
                $projectInstitution = ProjectInstitution::where('projectId', $project->id)->get();
                $institution = Institution::where('id', $projectInstitution[0]->institutionId)->get();
                $project->institutionName = $institution[0]->name;
            }
            return $projects;
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json(['Message' => 'Error consultando proyectos'], 400);
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
        $user = Auth::user();
        if ($user->role != 'Administrador'){
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        $projectInstitution = new ProjectInstitution;
        try{
            $request->validate([
                'name' => 'required',
                'startDate' => 'required|date|after_or_equal:yesterday',
                'endDate'=> 'required|date|after:startDate',
                'price'=> 'required|numeric',
                'people'=> 'required|array',
                'people.*.id'=> 'required|numeric',
                'people.*.role'=> 'required|string',
                'institutionId'=> 'required|numeric',
            ], $this->messages);

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
        } catch (ValidationException $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json($exception->validator->errors(), 400);

        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            if ($project){
                $project->delete();
            }
            if ($projectInstitution){
                $projectInstitution->delete();
            }
            return response()->json([
                'Error' => $exception->getMessage()], 400);
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
        $user = Auth::user();
        if ($user->role != 'Administrador' && $user->role != 'Consultor'){
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
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
            return response()->json(['Error' => $exception->getMessage()], 400);
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
        $user = Auth::user();
        if ($user->role != 'Administrador'){
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
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
        $user = Auth::user();
        if ($user->role != 'Administrador'){
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        try{
            $request->validate([
                'name' => 'required',
                'endDate'=> 'required|date|after:startDate',
                'price'=> 'required|numeric',
                'people'=> 'required|array',
                'people.*.id'=> 'required|numeric',
                'people.*.role'=> 'required|string',
                'institutionId'=> 'required|numeric',
            ], $this->messages);
            Log::channel('stdout')->info(['Getting project with id', $id]);
            $project = Project::find($id);
            Log::channel('stdout')->info($project);
            $project->name = $request->name;
            $project->startDate = $request->startDate;
            $project->endDate = $request->endDate;
            $project->price = $request->price;
            $people = $request->people;
            $project->save();
            $projectInstitution = ProjectInstitution::where('projectId', $id)->get();
            $projectInstitution[0]->projectId = $project->id;
            $projectInstitution[0]->institutionId = $request->institutionId;
            $projectInstitution[0]->save();
            $currentPeople = ProjectPerson::where('projectId', $id)->get();
            foreach ($people as $person){
                $proyectPerson = new ProjectPerson;
                $proyectPerson->projectId = $project->id;
                $proyectPerson->personId = $person['id'];
                $proyectPerson->role = $person['role'];
                $proyectPerson->save();
            }
            foreach ($currentPeople as $person) {
                $person->delete();
            }
            return $project;
        } catch (ValidationException $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json($exception->validator->errors(), 400);

        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json([
                'Error' => $exception->getMessage()], 400);
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
        $user = Auth::user();
        if ($user->role != 'Administrador' && $user->role != 'Consultor'){
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProgress(Request $request) {
        try {
            $request->validate([
                'projectId' => 'required|numeric',
                'milestone' => 'required|string',
                'date'=> 'sometimes|required|date',
            ], $this->messages);
            $projectProgress = new ProjectProgress;
            $projectProgress->projectId = $request->projectId;
            $projectProgress->milestone = $request->milestone;
            $projectProgress->date = $request->date;
            $projectProgress->save();
            return $projectProgress;
        } catch (ValidationException $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json($exception->validator->errors(), 400);
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json(['Error' => $exception->getMessage()], 400);

        }
    }

    /**
     * Get People in charge of a Project
     *
     * @param  int id
     * @return \Illuminate\Http\Response
     */
    public function getPeopleInCharge($id) {
        $user = Auth::user();
        if ($user->role != 'Administrador' && $user->role != 'Consultor'){
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        try {
            $people = array();
            $projectPeople = ProjectPerson::where('projectId', $id)->get();
            foreach ($projectPeople as $projectPerson) {
                if ($projectPerson->role == 'encargado') {
                    $person = Person::find($projectPerson->personId);
                    array_push($people, $person);
                }
            }
            return $people;
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception->getMessage());
            return response()->json(['Message' => 'Error obteniendo encargados del proyecto'], 400);
        }
    }

}
