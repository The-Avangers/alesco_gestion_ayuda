<?php

namespace App\Http\Controllers;

use App\Person;
use App\Project;
use App\ProjectPerson;
use App\Task;
use App\TaskPerson;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public $messages = [
        "name.required" => "El nombre de la tarea es requerido",
        "projectId.required" => "El id del proyecto es requerido",
        "projectId.numeric" => "El id del proyecto debe ser un numero",
        "people.array" => "Los encargados deben ser un arreglo",
        "people.*.numeric" => "El identificador de los encargados debe ser numerico",
        "completed.boolean" => "El campo de completado debe ser un booleano",
        "completionDate.date"=> 'la fecha de completación debe ser una fecha',
        "completionDate.before_or_equal" => 'La fecha de completación debe ser menor o igual a hoy'
    ];

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role != 'Administrador' && $user->role != 'Consultor') {
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        try {
            $projectId = $request->query('projectId', null);
            $tasks = $projectId ? Task::where('projectId', $projectId)->get() : DB::table('task')->get();
            Log::channel('stdout')->info($tasks);
            foreach ($tasks as $task) {
                $taskPeople = TaskPerson::where('taskId', $task->id)->get();
                $people = array();
//                Log::channel('stdout')->info($taskPeople);
                foreach ($taskPeople as $taskPerson) {
                    $person = Person::find($taskPerson->personId);
                    Log::channel('stdout')->info($person);
                    array_push($people, $person);
                }
                $task->people = $people;
                $task->completed = !!$task->completed;
            }
            return $tasks;
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception->getMessage());

            return response()->json([
                'Error' => 'Error mostrando tareas'], 400);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role != 'Administrador') {
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        $task = new Task;

        try {
            $request->validate([
                'name'=> 'required',
                'projectId'=> 'required|numeric',
                'completed' => 'boolean',
                'completionDate' => 'date|before_or_equal:yesterday',
                'people' => 'array',
                'people.*' => 'numeric'
            ], $this->messages);
            $task->name = $request->name;
            $task->projectId = $request->projectId;
            $task->completed = $request->completed;
            $task->completionDate = $request->completionDate ? $request->completionDate : null;
            $task->save();
            if (count($request->people) > 0) {
                $projectPeople = ProjectPerson::where('projectId',$request->projectId)->get();
                foreach ($request->people as $personId) {
                    Log::channel('stdout')->info(['Id', $personId]);
                    $personInProject = false;
                    foreach ($projectPeople as $projectPerson) {
                        Log::channel('stdout')->info(['Person', $projectPerson]);
                        if ($projectPerson->personId == $personId && $projectPerson->role == 'encargado' ) {
                            $personInProject = true;
                            break;
                        }
                    }
                    if (!$personInProject) {
                        $task->delete();
                        return  response()->json([
                            'Message' => 'Todos los involucrados en la tarea deben estar involucrados en el proyecto'], 400);
                    }
                    $taskPerson = new TaskPerson;
                    $taskPerson->taskId = $task->id;
                    $taskPerson->personId = $personId;
                    $taskPerson->save();
                }
            }
            return $task;
        } catch (ValidationException $exception) {
            Log::channel('stdout')->error($exception->getMessage());
            return response()->json($exception->validator->errors(), 400);

        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception->getMessage());
            if ($task) {
                $task->delete();
            }
            return response()->json([
                'Error' => 'Error agregando tarea'], 400);
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
        Log::channel('stdout')->info(["Getting task with id ",$id]);
        $user = Auth::user();
        if ($user->role != 'Administrador' && $user->role != 'Consultor') {
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        try {
            $task = Task::find($id);
            $tasksPeople = TaskPerson::where('taskId', $task->id)->get();
            $people = array();
            foreach ($tasksPeople as $taskPerson) {
                $person = Person::where('id', $taskPerson->personId)->get();
                array_push($people, $person[0]);
            }
            $task->people = $people;
            $task->completed = !!$task->completed;
            return $task;
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception->getMessage());
            return response()->json([
                'Error' => 'Error mostrando Tarea '], 400);
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
        $user = Auth::user();
        if ($user->role != 'Administrador') {
            return response()->json(['Message' => 'Unauthorized'], 401);
        }
        try {
            $request->validate([
                'name'=> 'required',
                'completed' => 'boolean',
                'completionDate' => 'date|before_or_equal:yesterday',
                'people' => 'array',
                'people.*' => 'numeric'
            ], $this->messages);
            Log::channel('stdout')->info(['Updating task with id',$id]);
            $task = Task::find($id);
//            Log::channel('stdout')->info($task);
            $task->name = $request->name;
            $task->completed = $request->completed;
            $task->completionDate = $request->completionDate ? $request->completionDate : null;
            Log::channel('stdout')->info('Before task');
            $task->save();
            Log::channel('stdout')->info('Task Updated');
            $currentTaskPeople = TaskPerson::where('taskId', $id)->get();
            if (count($request->people) > 0) {
                foreach ($request->people as $personId) {
                    $projectPeople = ProjectPerson::where('projectId', $request->projectId)->get();
                    $personInProject = false;
                    foreach ($projectPeople as $projectPerson) {
                        if ($projectPerson->personId == $personId && $projectPerson->role == 'encargado' ) {
                            $personInProject = true;
                            break;
                        }
                    }
                    if (!$personInProject) {
                        return response()->json([
                            'Message' => 'Todos los involucrados en la tarea deben estar involucrados en el proyecto'], 400);
                    }
                    $taskPerson = new TaskPerson;
                    $taskPerson->taskId = $task->id;
                    $taskPerson->personId = $personId;
                    $taskPerson->save();
                }
            }
            if (count($currentTaskPeople) > 0) {
                foreach ($currentTaskPeople as $taskPerson) {
                    $taskPerson->delete();
                }
            }
            return $task;
        } catch (\Exception $exception ) {
            Log::channel('stdout')->error($exception->getMessage());
            return response()->json([
                'Error' => 'Error editando tarea'], 400);
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
