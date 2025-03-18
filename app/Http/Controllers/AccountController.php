<?php

namespace App\Http\Controllers;

use App\Models\AccountModel;
use App\Models\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AccountController extends Controller
{
    protected $messages = [
        'username.required' => 'Vui lòng nhập tên đăng nhập.',
        'username.unique' => 'Tên đăng nhập đã tồn tại.',
        'username.max' => 'Tên đăng nhập không được vượt quá 50 ký tự.',
        'password.required' => 'Vui lòng nhập mật khẩu.',
        'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
    ];
    protected $queryBuilder;
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:tblTaiKhoan,sUsername',
            'password' => 'required',
        ], $this->messages);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $user = AccountModel::where('sUsername', 'like', $request->username)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Không tìm thấy tài khoản'
            ]);
        }
        if (!Hash::check($request->password, $user->sPassword)) {
            return response()->json([
                'message' => 'Mật khẩu không chính xác'
            ]);
        }
        $user = $user->FK_MaQuyen == 4 ? AccountModel::where('PK_MaTaiKhoan', $user->PK_MaTaiKhoan)->with('caNhan')->first() : AccountModel::where('PK_MaTaiKhoan', $user->PK_MaTaiKhoan)->with('donVi')->first();
        $user->makeHidden(['sPassword']);
        $user->api_token = Str::random(60);
        // $user->ten = 
        $user->save();

        Auth::login($user);

        return response()->json([
            'message' => 'success',
            'user' => $user,
        ], 200);
    }

    public function info()
    {
        $user = auth()->user();

        $currentUserInfo = AccountModel::where('api_token', '=', $user->api_token)
            ->with('roles')->get();

        $data = $this->queryBuilder->getAccountInfo($user->PK_MaTaiKhoan, $user->FK_MaQuyen);

        return response()->json([
            'message' => 'success',
            'user' => $data
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $taiKhoanUpdate = AccountModel::where('api_token', '=', $user->api_token)->first();

            $validator = Validator::make($request->all(), [
                'username' => 'required|exists:tblTaiKhoan,sUsername',
                'old_password' => 'required',
                'new_password' => 'required|min:6',
            ], $this->messages);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 422);
            }
            if (Hash::check($request->old_password, $taiKhoanUpdate->sPassword)) {
                $taiKhoanUpdate->sPassword = bcrypt($request->new_password);
                $taiKhoanUpdate->save();
            } else {
                return response()->json([
                    'message' => 'Mật khẩu không chính xác'
                ], 422);
            }
        } else {
            return response()->json([
                'message' => 'Bạn chưa đăng nhập'
            ], 401);
        }


        return response()->json([
            'message' => 'Cập nhật thành công',
        ], 200);
    }

    public function logOut(Request $request)
    {
        $user = auth()->user();

        $logOutAccount = AccountModel::where('api_token', '=', $user->api_token)->first();

        $logOutAccount->api_token = null;
        $logOutAccount->save();

        return response()->json([
            'message' => 'Đăng xuất thành công',
        ]);
    }
}
