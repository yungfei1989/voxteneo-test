<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use Yajra\Datatables\Datatables;
use DB;
use Validator;

class PermissionController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      return view('backend.permission.index');
  }

  /**
   * Get data permissions.
   *
   * @return \Illuminate\Http\Response
   */
  public function data(Request $request)
  {
      DB::statement(DB::raw('set @rownum=0'));
      $users = DB::table('permissions')
              ->select([
                DB::raw('@rownum := @rownum + 1 AS rownum'),
                'permissions.*'
              ]);

      $datatables = Datatables::of($users)
        ->addColumn('action', function ($data) {
            $act = '<a href="'. route("admin.permission.edit", ['Permission' => $data->id]) .'"><i class="icon icon-pencil"></i></a>';
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
      return view('backend.permission.form');
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
        'display_name' => 'required',
        'description' => 'required'
      ]);

      if ($validator->fails()) {
          return redirect()->back()->withInput()->withErrors($validator);
      }

      try {
        $data = new Permission;
        $data->name = $request->name;
        $data->display_name = $request->display_name;
        $data->description = $request->description;
        $data->save();

        return redirect()->route('admin.permission.index')->with('status', 'Data saved successfully!');
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
      $data = Permission::where('id', $id)->firstOrFail();

      return view('backend.permission.form', compact('data'));
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
      'display_name' => 'required',
      'description' => 'required'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withInput()->withErrors($validator);
    }

    try {
      $data = Permission::where('id', $id)->firstOrFail();
      $data->name = $request->name;
      $data->display_name = $request->display_name;
      $data->description = $request->description;
      $data->save();

      return redirect()->route('admin.permission.index')->with('status', 'Data updated successfully!');
    } catch (\Exception $e) {
      return redirect()->back()->withInput()->withErrors([$e->getMessage()]);
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
        Permission::where('id', $id)->firstOrFail()->delete();

        return response()->json([]);
      } catch (\Exception $e) {
        return response()->json([], $e->getStatusCode());
      }
  }
}
