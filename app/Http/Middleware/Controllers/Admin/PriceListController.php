<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PriceList;
use App\Models\PriceListLine;
use App\Models\Item;
use App\Models\School;
use Yajra\Datatables\Datatables;
use DB;
use Validator;
use Auth;

class PriceListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.pricelist.index');
    }

    /**
     * Get data pricelists.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $pricelists = Pricelist::select([
                    DB::raw('@rownum := @rownum + 1 AS rownum'),
                    'price_lists.*'
                ]);

        $datatables = Datatables::of($pricelists)
            ->addColumn('action', function ($data) {
                $act = '<a href="'. route("admin.pricelist.edit", ['pricelist' => $data->id]) .'"><i class="icon icon-pencil"></i></a>';
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
        $schools = School::pluck('name', 'code');

        return view('backend.pricelist.form', compact('schools'));
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
            'start_year' => 'required',
            'end_year' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            $data = new PriceList;
            $data->start_year = $request->start_year;
            $data->end_year = $request->end_year;
            $data->created_by = Auth::id();
            $data->save();

            $this->storeLines($request, $data);

            return redirect()->route('admin.pricelist.index')->with('status', 'Data saved successfully!');
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
        $data = PriceList::where('id', $id)->firstOrFail();
        $schools = School::pluck('name', 'code');

        return view('backend.pricelist.form', compact('data', 'schools'));
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
            'start_year' => 'required',
            'end_year' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            $data = PriceList::where('id', $id)->firstOrFail();
            $data->start_year = $request->start_year;
            $data->end_year = $request->end_year;
            $data->save();

            $this->storeLines($request, $data);

            return redirect()->route('admin.pricelist.index')->with('status', 'Data updated successfully!');
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
            $pricelist = PriceList::where('id', $id)->firstOrFail();
            $pricelist->lines()->delete();
            $pricelist->delete();

            return response()->json([]);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 500);
        }
    }

    /**
     * Insert price list line.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeLines(Request $request, $pricelist)
    {
        $pricelist->lines()->delete();
        foreach ($request->school_code as $k => $v) {
            $lines = [];
            $lines['school_code'] = $request->school_code[$k];
            $lines['price'] = isset($request->price[$k]) ? $request->price[$k] : 0;
            $pricelist->lines()->save(new PriceListLine($lines));
        }
    }
}
