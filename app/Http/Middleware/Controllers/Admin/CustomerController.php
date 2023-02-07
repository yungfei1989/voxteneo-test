<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DB;
use Validator;
use Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.customer.index');
    }

    /**
     * Get data customers.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $customers = DB::table('customers AS u')
                ->select([
                  DB::raw('@rownum := @rownum + 1 AS rownum'),
                  'u.id',
                  'u.name',
                  'u.email'
                ]);

        $datatables = Datatables::of($customers)
          ->addColumn('action', function ($customers) {
              $act = '<a href="'. route("admin.customer.show", ['customer' => $customers->id]) .'"><i class="icon-magnifier"></i></a>';
              $act .= ' <a href="'. route("admin.customer.edit", ['customer' => $customers->id]) .'"><i class="icon-pencil"></i></a>';
              $act .= ' <a><i data-id="'. $customers->id .'" class="delete icon-trash"></i></a>';

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
        return view('backend.customer.form');
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
          'email' => 'required|unique:customers',
          'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
          $customer = new Customer;
          $customer->name = $request->name;
          $customer->email = $request->email;
          $customer->last_name = $request->last_name;
          $customer->address = $request->address;
          $customer->city = $request->city;
          $customer->postal_code = $request->postal_code;
          $customer->country = $request->country;
          $customer->password = empty($request->password) ? bcrypt('admin') : bcrypt($request->password);
          $customer->updated_at = date('Y-m-d H:i:s');
          $customer->save();

          return redirect()->route('admin.customer.index')->with('status', 'Data saved successfully!');
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
        $data = Customer::where('id', $id)->firstOrFail();
        return view('backend.customer.detail', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Customer::where('id', $id)->firstOrFail();

        return view('backend.customer.form', compact('data'));
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
        'email' => 'required|unique:customers,email,' . $id,
        'password' => 'confirmed',
      ]);

      if ($validator->fails()) {
          return redirect()->back()->withInput()->withErrors($validator);
      }

      try {
        $customer = Customer::find($id);
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->last_name = $request->last_name;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->postal_code = $request->postal_code;
        $customer->country = $request->country;
        $customer->updated_at = date('Y-m-d H:i:s');

        if (!empty($request->password)) {
            $customer->password = bcrypt($request->password);
        }

        $customer->save();

        return redirect()->route('admin.customer.index')->with('status', 'Data updated successfully!');
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
          $customer = Customer::where('id', $id)->firstOrFail();
          $customer->delete();

          return response()->json([]);
        } catch (\Exception $e) {
          return response()->json([], $e->getStatusCode());
        }
    }
}
