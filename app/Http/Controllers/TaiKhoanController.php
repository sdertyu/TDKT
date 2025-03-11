<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountModel;
use Illuminate\Support\Facades\Validator;

class TaiKhoanController extends Controller
{
    public function index()
    {
        $dsTaiKhoan = AccountModel::all()->makeHidden(['api_token']);

        return response()->json([
            'message' => "success",
            'data' => $dsTaiKhoan
        ]);
    }
    public function themTaiKhoan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:tblTaiKhoan',
            'password' => 'required',
        ]);
    }
}
