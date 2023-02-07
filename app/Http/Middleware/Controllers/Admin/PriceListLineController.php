<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PriceListLine;

class PriceListLineController extends Controller
{
    public function getPriceListLineByPricelistId($id) {
        $pricelistLine = PriceListLine::where('price_list_id', $id)->with('school')->get();

        return response()->json($pricelistLine);
    }
}
