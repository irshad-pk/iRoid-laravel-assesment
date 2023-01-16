<?php

namespace App\Http\Controllers;

use App\Models\Employee;

class EmployeeListController extends Controller
{

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
