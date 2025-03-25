<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountModel;
use App\Models\CaNhanModel;
use App\Models\DonViModel;
use Exception;
use Illuminate\Support\Facades\Validator;

class TaiKhoanController extends Controller
{
    protected $messages = [
        'username.required' => 'Vui lòng nhập tên đăng nhập.',
        'username.unique' => 'Tên đăng nhập đã tồn tại.',
        'username.max' => 'Tên đăng nhập không được vượt quá 50 ký tự.',
        'password.required' => 'Vui lòng nhập mật khẩu.',
        'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        'role.required' => 'Vui lòng chọn vai trò.',
        'role.exists' => 'Vai trò không hợp lệ.',
        'madonvi.exists' => 'Không tìm thấy mã đơn vị',
        'tendonvi.required' => "Vui lòng nhập tên đơn vị",
        'tencanhan.required' => 'Vui lòng nhập tên cá nhân',
        'myemail.required' => 'Vui lòng nhập email',
        'myemail.email' => 'Email không hợp lệ',
        'tenchucvu.required' => 'Vui lòng nhập tên chức vụ',
        'gioitinh.required' => 'Vui lòng chọn giới tính',
        'id.required' => 'Thiếu mã tài khoản',
        'id.exists' => 'Không tìm thấy tài khoản',
        'macanhan.exists' => 'Không tìm thấy mã cá nhân',
    ];
    public function index()
    {
        $dsTaiKhoan = AccountModel::all()->makeHidden(['api_token']);

        return response()->json([
            'message' => "success",
            'data' => $dsTaiKhoan
        ], 200);
    }
    public function themTaiKhoan(Request $request)
    {
        $rules = [];
        if ($request->role == 3) {
            $rules = [
                'username' => 'required|unique:tbltaikhoan,sUsername|max:50',
                'password' => 'required|min:6',
                'role'      => 'required|exists:tblQuyen,PK_MaQuyen|unique:tbltaikhoan,FK_MaQuyen',
            ];
        } else if ($request->role == 4) {
            $rules = [
                'username' => 'required|unique:tbltaikhoan,sUsername|max:50',
                'password' => 'required|min:6',
                'role' => 'required|exists:tblQuyen,PK_MaQuyen',
                'madonvi' => 'required|unique:tbldonvi,PK_MaDonVi',
                'tendonvi' => 'required',
            ];
        } else if ($request->role == 5) {
            $rules = [
                'username' => 'required|unique:tbltaikhoan,sUsername|max:50',
                'password' => 'required|min:6',
                'role' => 'required|exists:tblQuyen,PK_MaQuyen',
                'madonvi' => 'required|exists:tbldonvi,PK_MaDonVi',
                'tencanhan' => 'required',
                'myemail' => 'required|email',
                'tenchucvu' => 'required',
                'gioitinh' => 'required'
            ];
        }
        $validator = Validator::make($request->all(), $rules, $this->messages);


        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'message' => $validator->errors()->all() ? implode(', ', $validator->errors()->all()) : ''
            ], 422);
        }
        if ($request->role == 3) {
            $tongTaiKhoan = count(AccountModel::all());
            $taiKhoanMoi = new AccountModel();
            $taiKhoanMoi->PK_MaTaiKhoan = 'user' . $tongTaiKhoan + 1;
            $taiKhoanMoi->sUsername = $request->username;
            $taiKhoanMoi->sPassword = bcrypt($request->password);
            $taiKhoanMoi->FK_MaQuyen = $request->role;
            $taiKhoanMoi->sTrangThai = 1;
            $taiKhoanMoi->save();
        } else if ($request->role == 4) {
            $tongTaiKhoan = count(AccountModel::all());
            $taiKhoanMoi = new AccountModel();
            $taiKhoanMoi->PK_MaTaiKhoan = 'user' . $tongTaiKhoan + 1;
            $taiKhoanMoi->sUsername = $request->username;
            $taiKhoanMoi->sPassword = bcrypt($request->password);
            $taiKhoanMoi->FK_MaQuyen = $request->role;
            $taiKhoanMoi->sTrangThai = 1;

            $tongDonVi = count(DonViModel::all());
            $donViMoi = new DonViModel();

            $donViMoi->PK_MaDonVi = $request->madonvi;
            $donViMoi->sTenDonVi = $request->tendonvi;
            $donViMoi->FK_MaTaiKhoan = $taiKhoanMoi->PK_MaTaiKhoan;

            $taiKhoanMoi->save();
            $donViMoi->save();
        } else if ($request->role == 5) {
            $tongTaiKhoan = count(AccountModel::all());
            $taiKhoanMoi = new AccountModel();
            $taiKhoanMoi->PK_MaTaiKhoan = 'user' . $tongTaiKhoan + 1;
            $taiKhoanMoi->sUsername = $request->username;
            $taiKhoanMoi->sPassword = bcrypt($request->password);
            $taiKhoanMoi->FK_MaQuyen = $request->role;
            $taiKhoanMoi->sTrangThai = 1;

            $tongCaNhan = count(CaNhanModel::all());
            $caNhanMoi = new CaNhanModel();

            $caNhanMoi->PK_MaCaNhan = 'cn_' . $tongCaNhan + 1;
            $caNhanMoi->FK_MaDonVi = $request->madonvi;
            $caNhanMoi->sTenCaNhan = $request->tencanhan;
            $caNhanMoi->sEmail = $request->myemail;
            $caNhanMoi->sTenChucVu = $request->tenchucvu;
            $caNhanMoi->bGioiTinh = $request->gioitinh;
            $caNhanMoi->FK_MaTaiKhoan = $taiKhoanMoi->PK_MaTaiKhoan;

            $taiKhoanMoi->save();
            $caNhanMoi->save();
        }
        return response()->json([
            'icon' => "success",
            'message' => "Thêm thành công",
            'taikhoan' => $taiKhoanMoi
        ], 200);
    }

    public function layThongTinTaiKhoan($id)
    {
        $taiKhoan = AccountModel::where('PK_MaTaiKhoan', $id)->first();

        if ($taiKhoan) {
            $detailTaiKhoan = $taiKhoan->FK_MaQuyen == 5 ? AccountModel::where('PK_MaTaiKhoan', $id)->with('caNhan')->first() : AccountModel::where('PK_MaTaiKhoan', $id)->with('donVi')->first();
        } else {
            return response()->json([
                'message' => "Không tìm thấy thông tin tài khoản này"
            ], 404);
        }

        return response()->json([
            'message' => "success",
            'taikhoan' => $detailTaiKhoan
        ], 200);
    }

    public function capNhatTaiKhoan(Request $request)
    {
        $rules = [
            'username' => 'required|exists:tbltaikhoan,sUsername|max:50',
            'password' => 'nullable|min:6', // mật khẩu không bắt buộc trừ khi tích ô "Đổi mật khẩu"
            'role' => 'required|exists:tblQuyen,PK_MaQuyen',
        ];

        // Kiểm tra nếu có yêu cầu "Đổi mật khẩu", thì mật khẩu sẽ là bắt buộc
        if ($request->changePass == true) {
            $rules['password'] = 'required|min:6'; // Bắt buộc nếu chọn đổi mật khẩu
        }

        if ($request->role == 4) {
            $rules['id'] = 'required|exists:tbltaikhoan,PK_MaTaiKhoan|exists:tbldonvi,FK_MaTaiKhoan';
            $rules['madonvi'] = 'required|exists:tbldonvi,PK_MaDonVi';
            $rules['tendonvi'] = 'required';
        } else if ($request->role == 5) {
            $rules['id'] = 'required|exists:tbltaikhoan,PK_MaTaiKhoan|exists:tblcanhan,FK_MaTaiKhoan';
            $rules['madonvi'] = 'required|exists:tbldonvi,PK_MaDonVi';
            $rules['macanhan'] = 'required|exists:tblcanhan,PK_MaCaNhan';
            $rules['tencanhan'] = 'required';
            $rules['myemail'] = 'required|email';
            $rules['tenchucvu'] = 'required';
            $rules['gioitinh'] = 'required|integer';
        }

        $validator = Validator::make($request->all(), $rules, $this->messages);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->role == 4) {
            $donViUpt = DonViModel::where('PK_MaDonVi', '=', $request->madonvi)->first();
            if ($donViUpt) {
                $donViUpt->update([
                    'sTenDonVi' => $request->tendonvi,
                ]);
                $donViUpt->save();

                $taiKhoanUpt = AccountModel::where('PK_MaTaiKhoan', '=', $donViUpt->FK_MaTaiKhoan)->first();
                if ($taiKhoanUpt) {
                    $taiKhoanUpt->update([
                        'sPassword' => bcrypt($request->password),
                    ]);
                    $taiKhoanUpt->save();
                }
            }
        }
        if ($request->role == 5) {
            $caNhanUpt = CaNhanModel::where('PK_MaCaNhan', '=', $request->macanhan)->first();
            if ($caNhanUpt) {
                $caNhanUpt->update([
                    'sTenCaNhan' => $request->tencanhan,
                    'sEmail' => $request->myemail,
                    'sTenChucVu' => $request->tenchucvu,
                    'bGioiTinh' => $request->gioitinh,
                ]);
                $caNhanUpt->save();

                $taiKhoanUpt = AccountModel::where('PK_MaTaiKhoan', '=', $caNhanUpt->FK_MaTaiKhoan)->first();
                if ($taiKhoanUpt) {
                    $taiKhoanUpt->update([
                        'sPassword' => bcrypt($request->password),
                    ]);
                    $taiKhoanUpt->save();
                }
            }
        }

        return response()->json([
            'success' => 'Cập nhật thành công',
        ], 200);
    }

    public function xoaTaiKhoan($id)
    {
        $taiKhoanXoa = AccountModel::where('PK_MaTaiKhoan', '=', $id)->first();


        if (!$taiKhoanXoa) {
            return response()->json([
                'message' => 'Không tìm thấy tài khoản'
            ], 404);
        } else {
            if ($taiKhoanXoa->FK_MaQuyen == 3) {
                $donViXoa = DonViModel::where('FK_MaTaiKhoan', '=', $taiKhoanXoa->PK_MaTaiKhoan)->first();
                if ($donViXoa) {
                    $donViXoa->delete();
                }
                $taiKhoanXoa->delete();
            } else if ($taiKhoanXoa->FK_MaQuyen == 4) {
                $caNhanXoa = CaNhanModel::where('FK_MaTaiKhoan', '=', $taiKhoanXoa->PK_MaTaiKhoan)->first();
                if ($caNhanXoa) {
                    $caNhanXoa->delete();
                }
                $taiKhoanXoa->delete();
            }
        }

        return response()->json([
            'message' => 'Xóa tài khoản thành công',
        ], 200);
    }

    public function layDanhSachDonVi()
    {
        $danhSachDonVi = DonViModel::all();
        return response()->json([
            'message' => 'success',
            'danhSachDonVi' => $danhSachDonVi
        ], 200);
    }
}
