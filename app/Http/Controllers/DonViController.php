<?php

namespace App\Http\Controllers;

use App\Models\DonViModel;
use Illuminate\Http\Request;

class DonViController extends Controller
{
    public function index()
    {
        $dsDonVi = DonViModel::all();

        return response()->json([
            'message' => "success",
            'data' => $dsDonVi
        ]);
    }
    public function themDonVi(Request $request){
        
    }
}
