<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DB;
use Validator;

class RoleController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      return view('backend.role.index');
  }

  /**
   * Get data roles.
   *
   * @return \Illuminate\Http\Response
   */
  public function data(Request $request)
  {
      DB::statement(DB::raw('set @rownum=0'));
      $users = DB::table('roles')
              ->select([
                DB::raw('@rownum := @rownum + 1 AS rownum'),
                'roles.*'
              ]);

      $datatables = Datatables::of($users)
        ->addColumn('action', function ($data) {
            $act = '<a href="'. route("admin.role.edit", ['Role' => $data->id]) .'"><i class="icon-pencil"></i></a>';
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
      $permissions = Permission::all();
      return view('backend.role.form', compact('permissions'));
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
        'description' => 'required',
        'permissions' => 'required'
      ]);

      if ($validator->fails()) {
          return redirect()->back()->withInput()->withErrors($validator);
      }

      try {
        $data = new Role;
        $data->name = $request->name;
        $data->display_name = $request->display_name;
        $data->description = $request->description;
        $data->save();

        $data->attachPermissions($request->permissions);

        return redirect()->route('admin.role.index')->with('status', 'Data saved successfully!');
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
      $data = Role::where('id', $id)->firstOrFail();
      $permissions = Permission::all();

      return view('backend.role.form', compact('data', 'permissions'));
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
      'description' => 'required',
      'permissions' => 'required'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withInput()->withErrors($validator);
    }

    try {
      $data = Role::where('id', $id)->firstOrFail();
      $data->name = $request->name;
      $data->display_name = $request->display_name;
      $data->description = $request->description;
      $data->save();

      $data->perms()->sync([]);
      $data->attachPermissions($request->permissions);

      return redirect()->route('admin.role.index')->with('status', 'Data updated successfully!');
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
        Role::where('id', $id)->firstOrFail()->delete();

        return response()->json([]);
      } catch (\Exception $e) {
        return response()->json([], $e->getStatusCode());
      }
  }
}
