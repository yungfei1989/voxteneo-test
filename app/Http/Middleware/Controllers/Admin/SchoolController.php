<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\School;
use Yajra\Datatables\Datatables;
use DB;
use Validator;
use Auth;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.school.index');
    }

    /**
     * Get data schools.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $users = DB::table('schools')
                ->select([
                    DB::raw('@rownum := @rownum + 1 AS rownum'),
                    'schools.*'
                ]);

        $datatables = Datatables::of($users)
            ->addColumn('action', function ($data) {
                $act = '<a href="'. route("admin.school.edit", ['School' => $data->id]) .'"><i class="icon icon-pencil"></i></a>';
                $act .= ' <a><i data-id="'. $data->id .'" class="delete icon-trash"></i></a>';

                return $act;
            })
            ->removeColumn('id')
            ->rawColumns(['action']);

        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', function($query, $keyword) {
                    $sql = '@rownum + 1 like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
            });
        }

        return $datatables->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.school.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required|unique:schools',
            'phone' => 'required',
            'region' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            $data = new School;
            $data->name = $request->name;
            $data->code = $request->code;
            $data->phone = $request->phone;
            $data->region = $request->region;
            $data->address = $request->address;
            $data->created_by = Auth::id();
            $data->save();

            return redirect()->route('admin.school.index')->with('status', 'Data saved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors([$e->getMessage()]);
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
        $data = School::where('id', $id)->firstOrFail();

        return view('backend.school.form', compact('data'));
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required|unique:schools,code,' . $id,
            'phone' => 'required',
            'region' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            $data = School::where('id', $id)->firstOrFail();
            $data->name = $request->name;
            $data->code = $request->code;
            $data->phone = $request->phone;
            $data->region = $request->region;
            $data->address = $request->address;
            $data->save();

            return redirect()->route('admin.school.index')->with('status', 'Data updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['Failed to update data. Data is being used in other modules.']);
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
        try {
            School::where('id', $id)->firstOrFail()->delete();

            return response()->json([]);
        } catch (\Exception $e) {
            return response()->json([], $e->getStatusCode());
        }
    }
}
