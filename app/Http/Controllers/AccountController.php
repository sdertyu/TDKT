<?php

namespace App\Http\Controllers;

use App\Models\AccountModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AccountController extends Controller
{
    public function __construct() {}
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:tblTaiKhoan,sUsername',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }

        $user = AccountModel::where('sUsername', '=', $request->username)->first();

        if (!$user || Hash::check($request->password, $user->sPassword)) {
            return response()->json([
                'message' => 'Cant find account'
            ]);
        }
        $user->makeHidden(['sPassword']);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'success',
            'token' => $token,
            'user' => $user
        ]);
    }
}
