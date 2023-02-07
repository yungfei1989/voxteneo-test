<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\PriceList;
use Yajra\Datatables\Datatables;
use Intervention\Image\ImageManagerStatic as Image;
use DB;
use Validator;
use Auth;
use File;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.item.index');
    }

    /**
     * Get data items.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $users = DB::table('items')
                ->select([
                    DB::raw('@rownum := @rownum + 1 AS rownum'),
                    'items.*'
                ]);

        $datatables = Datatables::of($users)
            ->addColumn('action', function ($data) {
                $act = '<a href="'. route("admin.item.edit", ['Item' => $data->id]) .'"><i class="icon icon-pencil"></i></a>';
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
        $item_types = ItemType::pluck('name', 'id');
        $price_lists = PriceList::select('start_year', 'end_year', 'id')->get();

        return view('backend.item.form', compact('item_types', 'price_lists'));
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
            'code' => 'required|unique:items',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            $data = new Item;
            $data->name = $request->name;
            $data->code = $request->code;
            $data->is_active = ($request->is_active == 1) ? 1 : 0;
            $data->description_en = $request->description_en;
            $data->description_id = $request->description_id;
            $data->price = $request->price;
            $data->item_type_id = $request->item_type_id;
            $data->price_list_id = $request->price_list_id;
            $data->created_by = Auth::id();
            if($request->hasFile('picture') && $request->file('picture')->isValid()){
                $file = $request->file('picture');
                $fileName = 'item-'. str_slug($request->name, '-') . '-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();

                $thumbnailPath = public_path('uploads/items/200_200_' . $fileName);
                Image::make($file->getRealPath())->fit(200, 200)->save($thumbnailPath);

                $originalPath = public_path('uploads/items/' . $fileName);
                Image::make($file->getRealPath())->save($originalPath);

                $data->picture = $fileName;
            }

            $data->save();

            return redirect()->route('admin.item.index')->with('status', 'Data saved successfully!');
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
        $data = Item::where('id', $id)->firstOrFail();
        $item_types = ItemType::pluck('name', 'id');
        $price_lists = PriceList::select('start_year', 'end_year', 'id')->get();

        return view('backend.item.form', compact('data', 'item_types', 'price_lists'));
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
            'code' => 'required|unique:items,code,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            $data = Item::where('id', $id)->firstOrFail();
            $data->name = $request->name;
            $data->code = $request->code;
            $data->is_active = ($request->is_active == 1) ? 1 : 0;
            $data->description_en = $request->description_en;
            $data->description_id = $request->description_id;
            $data->price = $request->price;
            $data->item_type_id = $request->item_type_id;
            $data->price_list_id = $request->price_list_id;
            if ($request->hasFile('picture') && $request->file('picture')->isValid()){
                $oldPhotoPath = public_path('uploads/items/' . $data->picture);
                $oldThumbnailPhotoPath = public_path('uploads/items/200_200_' . $data->picture);
                
                $file = $request->file('picture');
                $fileName = 'item-'. str_slug($request->name, '-') . '-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
    
                $thumbnailPath = public_path('uploads/items/200_200_' . $fileName);
                Image::make($file->getRealPath())->fit(200, 200)->save($thumbnailPath);
    
                $originalPath = public_path('uploads/items/' . $fileName);
                Image::make($file->getRealPath())->save($originalPath);
    
                $data->picture = $fileName;
            }
    
            $data->save();

            if (isset($oldPhotoPath) && File::exists($oldPhotoPath)) {
                File::delete($oldPhotoPath);
                File::delete($oldThumbnailPhotoPath);
            }

            return redirect()->route('admin.item.index')->with('status', 'Data updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['Failed to update data. ' . $e->getMessage() ]);
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
            $data = Item::where('id', $id)->firstOrFail();
            $photoPath = public_path('uploads/items/' . $data->picture);
            $thumbnailPhotoPath = public_path('uploads/items/200_200_' . $data->picture);

            if ($data->delete()) {
                if (File::exists($photoPath)) {
                    File::delete($photoPath);
                    File::delete($thumbnailPhotoPath);
                }
            }

            return response()->json([]);
        } catch (\Exception $e) {
            return response()->json([], $e->getStatusCode());
        }
    }
}
