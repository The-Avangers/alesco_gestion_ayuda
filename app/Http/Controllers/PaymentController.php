<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectPayment;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
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
            $project = Project::where('id', $request->projectId)->get();
            if (count($project) == 0 )
                return response()->json(['Error' => 'El projecto no existe'], 400);
            $projectPayment = new ProjectPayment;
            $projectPayment->projectId = $project[0]->id;
            $projectPayment->amount = $request->amount;
            $projectPayment->paymentDate = $request->paymentDate;
            $projectPayment->save();
            $projectPayments = ProjectPayment::where('projectId', $project[0]->id)->get();
            $amountPaid = 0;
            foreach ($projectPayments as $payment){
                $amountPaid += $payment->amount;
            }
            if ($amountPaid >= $project[0]->price)
                $project[0]->paid = true;
            $project[0]->update();
            $projectPayment->projectId = $project[0]->id;
            $projectPayment->paid = $project[0]->paid == 1 || $project[0]->paid == true;
            return $projectPayment;
        } catch (\Exception $exception) {
            Log::channel('stdout')->error($exception);
            return response()->json(['Error' => 'No se pudo registrar el pago'], 400);
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
