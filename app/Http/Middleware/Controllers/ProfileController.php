<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Auth;
use Validator;
use DB;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $id = Auth::guard('customers')->id();
        $data = Customer::where('id', $id)->firstOrFail();

        return view('frontend.admin.profile', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = Auth::guard('customers')->id();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . Auth::id(),
            'password' => 'confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        
        try {
        $user = Customer::where('id',$id)->firstOrFail();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->last_name = $request->last_name;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->postal_code = $request->postal_code;
        $user->country = $request->country;
        $user->updated_at = date('Y-m-d H:i:s');

        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->back()->with('status', 'Data updated successfully!');
        } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->withErrors([$e->getMessage()]);
        }
    }
}
