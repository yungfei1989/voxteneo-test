<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DB;
use Validator;
use Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.user.index');
    }

    /**
     * Get data users.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $users = DB::table('users AS u')
                ->select([
                  DB::raw('@rownum := @rownum + 1 AS rownum'),
                  'u.id',
                  'u.name',
                  'u.email'
                ]);

        $datatables = Datatables::of($users)
          ->addColumn('action', function ($users) {
              $act = '<a href="'. route("admin.user.show", ['user' => $users->id]) .'"><i class="icon-magnifier"></i></a>';
              $act .= ' <a href="'. route("admin.user.edit", ['user' => $users->id]) .'"><i class="icon-pencil"></i></a>';
              $act .= ' <a><i data-id="'. $users->id .'" class="delete icon-trash"></i></a>';

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
        $roles = Role::all();
        return view('backend.user.form', compact('roles'));
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
          'email' => 'required|unique:users',
          'password' => 'required|confirmed',
          'roles' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
          $user = new User;
          $user->name = $request->name;
          $user->email = $request->email;
          $user->password = empty($request->password) ? bcrypt('admin') : bcrypt($request->password);
          $user->updated_at = date('Y-m-d H:i:s');
          $user->save();

          $user->attachRoles($request->roles);

          return redirect()->route('admin.user.index')->with('status', 'Data saved successfully!');
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
        $data = User::where('id', $id)->firstOrFail();
        return view('backend.user.detail', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = User::where('id', $id)->firstOrFail();
        $roles = Role::all();

        return view('backend.user.form', compact('data', 'roles'));
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
        'email' => 'required|unique:users,email,' . $id,
        'password' => 'confirmed',
        'roles' => 'required'
      ]);

      if ($validator->fails()) {
          return redirect()->back()->withInput()->withErrors($validator);
      }

      try {
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->updated_at = date('Y-m-d H:i:s');

        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        if (!empty($request->roles)) {
            $user->roles()->sync([]);
            $user->attachRoles($request->roles);
        }

        return redirect()->route('admin.user.index')->with('status', 'Data updated successfully!');
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
          $user = User::where('id', $id)->firstOrFail();
          $user->delete();

          return response()->json([]);
        } catch (\Exception $e) {
          return response()->json([], $e->getStatusCode());
        }
    }
}
