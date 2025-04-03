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
        'macanhan.required' => 'Vui lòng nhập mã cá nhân',
        'macanhan.exists' => 'Đã tồn tại cá nhân này',
        'tencanhan.required' => 'Vui lòng nhập tên cá nhân',
        'myemail.required' => 'Vui lòng nhập email',
        'myemail.email' => 'Email không hợp lệ',
        'tenchucvu.required' => 'Vui lòng nhập tên chức vụ',
        'gioitinh.required' => 'Vui lòng chọn giới tính',
        'id.required' => 'Thiếu mã tài khoản',
        'id.exists' => 'Không tìm thấy tài khoản',
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
        $rules = [
            'username' => 'required|unique:tbltaikhoan,sUsername|max:50',
            'password' => 'required|min:6',
            'role' => 'required|exists:tblQuyen,PK_MaQuyen',
            'myemail' => 'required|email|Unique:tbltaikhoan,sEmail',
        ];
        if ($request->role == 2 || $request->role == 3) {
            $rules = [
                'username' => 'required|unique:tbltaikhoan,sUsername|max:50',
                'password' => 'required|min:6',
                'role'      => 'required|exists:tblQuyen,PK_MaQuyen|Unique:tbltaikhoan,FK_MaQuyen',
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
                'macanhan' => 'required|unique:tblcanhan,PK_MaCaNhan',
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
        $taiKhoanMoi = new AccountModel();
        $taiKhoanMoi->PK_MaTaiKhoan = 'user' . $request->username;
        $taiKhoanMoi->sUsername = $request->username;
        $taiKhoanMoi->sPassword = bcrypt($request->password);
        $taiKhoanMoi->FK_MaQuyen = $request->role;
        $taiKhoanMoi->sEmail = $request->myemail;
        $taiKhoanMoi->sTrangThai = 1;
        $taiKhoanMoi->save();

        if ($request->role == 4) {
            $donViMoi = new DonViModel();

            $donViMoi->PK_MaDonVi = $request->madonvi;
            $donViMoi->sTenDonVi = $request->tendonvi;
            $donViMoi->FK_MaTaiKhoan = $taiKhoanMoi->PK_MaTaiKhoan;
            
            $donViMoi->save();
        } else if ($request->role == 5) {

            $caNhanMoi = new CaNhanModel();

            $caNhanMoi->PK_MaCaNhan = $request->macanhan;
            $caNhanMoi->FK_MaDonVi = $request->madonvi;
            $caNhanMoi->sTenCaNhan = $request->tencanhan;
            $caNhanMoi->sTenChucVu = $request->tenchucvu;
            $caNhanMoi->bGioiTinh = $request->gioitinh;
            $caNhanMoi->FK_MaTaiKhoan = $taiKhoanMoi->PK_MaTaiKhoan;
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
            'password' => 'nullable|min:6', // mật khẩu không bắt buộc trừ khi tích ô "Đổi mật khẩu"
            'role' => 'required|exists:tblQuyen,PK_MaQuyen',
            'myemail' => 'required|email|Unique:tbltaikhoan,sEmail,' . $request->id . ',PK_MaTaiKhoan',
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

        $taiKhoanUpt = AccountModel::where('PK_MaTaiKhoan', '=', $request->id)->first();
        if(!$taiKhoanUpt) {
            return response()->json([
                'message' => 'Tài khoản không tồn tại'
            ], 404);
        } else {
            if ($request->changePass) {
                $taiKhoanUpt->sPassword = bcrypt($request->password);
            }
            if($request->myemail != $taiKhoanUpt->sEmail) {
                $taiKhoanUpt->sEmail = $request->myemail;
            }
            $taiKhoanUpt->FK_MaQuyen = $request->role;
            $taiKhoanUpt->save();
        }

        if ($request->role == 4) {
            $donViUpt = DonViModel::where('PK_MaDonVi', '=', $request->madonvi)->first();
            if ($donViUpt) {
                $donViUpt->update([
                    'sTenDonVi' => $request->tendonvi,
                ]);
                $donViUpt->save();
            }
        }
        if ($request->role == 5) {
            $caNhanUpt = CaNhanModel::where('PK_MaCaNhan', '=', $request->macanhan)->first();
            if ($caNhanUpt) {
                $caNhanUpt->update([
                    'sTenCaNhan' => $request->tencanhan,
                    'sTenChucVu' => $request->tenchucvu,
                    'bGioiTinh' => $request->gioitinh,
                ]);
                $caNhanUpt->save();
            }
        }

        return response()->json([
            'success' => 'Cập nhật thành công',
        ], 200);
    }

    public function khoaTaiKhoan($id, Request $request)
    {
        $rules = [
            'trangThai' => 'required|in:0,1'
        ];

        $validator = Validator::make($request->all(), $rules, $this->messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $taiKhoanKhoa = AccountModel::where('PK_MaTaiKhoan', '=', $id)->first();

        if (!$taiKhoanKhoa) {
            return response()->json([
                'message' => 'Tài khoản không tồn tại'
            ], 404);
        }

        $taiKhoanKhoa->sTrangThai = $request->trangThai;
        $taiKhoanKhoa->save();

        return response()->json([
            'message' => 'Đã khóa tài khoản',
            'taikhoan' => $taiKhoanKhoa
        ], 200);
    }

    public function xoaTaiKhoan($id)
    {
        $taiKhoanXoa = AccountModel::where('PK_MaTaiKhoan', '=', $id)->first();

        $thongBao = 

        if (!$taiKhoanXoa) {
            return response()->json([
                'message' => 'Không tìm thấy tài khoản'
            ], 404);
        } else {

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

    public function layDanhSachCaNhanTrongDonVi($id)
    {
        $donVi = DonViModel::where('PK_MaDonVi', $id)->first();
        if (!$donVi) {
            return response()->json([
                'message' => 'Không tìm thấy đơn vị'
            ], 404);
        }
        $danhSachCaNhan = CaNhanModel::where('FK_MaDonVi', $donVi->PK_MaDonVi)->whereHas('taikhoan', function ($query) {
            $query->where('sTrangThai', 1); // hoặc 'status' tùy tên cột
        })->get();

        return response()->json([
            'message' => 'success',
            'data' => $danhSachCaNhan
        ], 200);
    }
}
