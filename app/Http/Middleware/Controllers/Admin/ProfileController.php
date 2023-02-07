<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Validator;

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
      $id = Auth::id();
      $data = User::where('id', $id)->firstOrFail();

      return view('backend.profile.form', compact('data'));
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
    $id = Auth::id();
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|unique:users,email,' . Auth::id(),
      'password' => 'confirmed'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withInput()->withErrors($validator);
    }
    
    try {
      $user = User::find($id)->firstOrFail();
      $user->name = $request->name;
      $user->email = $request->email;
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
