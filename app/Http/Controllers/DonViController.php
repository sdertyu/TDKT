<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonViModel;

class DonViController extends Controller
{
    public function index() {}

    public function layDanhSachCaNhan($id)
    {
        $donVi = DonViModel::where('PK_MaDonVi', '=', $id)->with('canhan')->first();

        return response()->json([
            'message' => 'success',
            'data' => $donVi
        ],200);
    }
}
