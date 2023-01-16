<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreEmployeeRequest;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(Employee::with('company:id,name')->select('*'))
            ->addColumn('action', 'employees.action')
            ->rawColumns(['action'])
            ->addColumn('company', function (Employee $employee) {
                return $employee->company->name;
            })
            ->addIndexColumn()
            ->make(true);
            }

        return view('employees.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::get();
        return view('employees.create',compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {        
            DB::transaction(function () use ($request) {
                Employee::create($request->all());
            });

            return redirect()->route('employees.index')
            ->with('success','Employee has been created successfully.');
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        return view('employees.show',compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        $companies = Company::get();
        return view('employees.edit',compact('employee','companies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        try {        
            DB::transaction(function () use ($request,$employee) {
                $employee->update($request->all());
            });

            return redirect()->route('employees.index')
            ->with('success','Employee has been created successfully.');
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
     
        return Response()->json($employee);
    }

    public function employeeList()
    { 
        try{
            $data = Employee::with('company')->get();
                return response()->json(["status" => true, "message" => "Employees retrieved successfully.", "data" => $data],200); 
            
        } catch(\Exception $e){
            return response()->json(['status'=>false,'message'=>$e->getMessage()],400);
        }

    }
}
