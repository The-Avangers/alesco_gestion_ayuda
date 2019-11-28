<?php

namespace App\Http\Controllers;

use App\Person;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $people = DB::table('person')->get();
            return $people;
        } catch (\Exception $exception) {
            return response()->json([
                'Error' => 'Error consultando personas'], 400);
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
            $person = new Person;
            $person->firstName = $request->firstName;
            $person->lastName = $request->lastName;
            if ($request->phone > 9999999999 || $request->phone < 1000000000 ){
                return response()->json(['Error' => 'El telefono ingresado es invalido'], 400);
            }
            $person->phone = $request->phone;
            $person->ci = $request->ci;
            $person->save();
            return $person;
        } catch (\Exception $exception){
            return response()->json(['Error' => 'Error agregando persona'], 400);
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
