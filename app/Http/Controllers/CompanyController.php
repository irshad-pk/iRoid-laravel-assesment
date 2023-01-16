<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCompanyRequest;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Company::select('*'))
            ->addColumn('action', 'companies.action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
            }

        return view('companies.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompanyRequest $request)
    {

            try {
                if ($request->hasFile('file')) {
                    $imageName = time() . '.' . $request->file->extension();
                    $path=$request->file->storeAs('logo', $imageName);
                
                    $postData = ['name' => $request->name, 'email' => $request->email, 'logo' => $imageName, 'website' => $request->website];
                }
                else {
                    $postData = ['name' => $request->name, 'email' => $request->email, 'website' => $request->website];
                }
            
                DB::transaction(function () use ($postData) {
                    Company::create($postData);
                });
    
                return redirect()->route('companies.index')
                ->with('success','Company has been created successfully.');
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        return view('companies.show',compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {     
        return view('companies.edit',compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCompanyRequest $request, Company $company)
    {
        try {
            if ($request->hasFile('file')) {
                $imageName = time() . '.' . $request->file->extension();
                $path=$request->file->storeAs('logo', $imageName);
            
                $postData = ['name' => $request->name, 'email' => $request->email, 'logo' => $imageName, 'website' => $request->website];
            }
            else {
                $postData = ['name' => $request->name, 'email' => $request->email, 'website' => $request->website];
            }
        
            DB::transaction(function () use ($company,$postData) {
                $company->update($postData);
            });

            return redirect()->route('companies.index')
            ->with('success','Company has been updated successfully.');
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();
     
        return Response()->json($company);
    }
}
