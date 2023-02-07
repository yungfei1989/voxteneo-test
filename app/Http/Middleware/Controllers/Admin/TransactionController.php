<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Yajra\Datatables\Datatables;
use DB;
use Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.transaction.index');
    }

    /**
     * Get data transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $transactions = Transaction::select([
                  DB::raw('@rownum := @rownum + 1 AS rownum'),
                  'transactions.*', DB::raw('case when transactions.status = 0 then "Not Paid" else "Paid" end as status_description'),
                  DB::raw('case when transactions.payment_method = 1 then "Credit card" else "" end as payment_description'),
                  DB::raw('case when transactions.instalment = 1 then "Recurring" else "full payment" end as instalment_description'),
                ]);

        $datatables = Datatables::of($transactions)
          ->addColumn('action', function ($transactions) {
              $act = '<a href="'. route("admin.transaction.show", ['transaction' => $transactions->id]) .'"><i class="icon-magnifier"></i></a> ';
              $act .= '<a target="_blank" href="'. route("admin.transaction.print", ['transaction' => $transactions->id]) .'"><i class="icon-printer"></i></a>';  
              return $act;
          })
          ->editColumn('customer_id', function ($data) {
            return $data->customer->name;
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Transaction::where('id', $id)->firstOrFail();
        return view('backend.transaction.detail', compact('data'));
    }

    /**
     * Print data transactions.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printPage($id)
    {
        $data = Transaction::where('id', $id)->firstOrFail();
        return view('backend.transaction.print', compact('data'));
    }
}