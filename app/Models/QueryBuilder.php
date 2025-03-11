<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QueryBuilder extends Model
{

    public function getAccountInfo($id_tk, $id_quyen)
    {
        $array = null;
        if ($id_quyen == 3) {
            $array = DB::table('tbltaikhoan as tk')
                ->join('tblquyen as q', 'q.PK_MaQuyen', '=', 'tk.FK_MaQuyen')
                ->select("tk.PK_MaTaiKhoan", "tk.sUsername", "tk.sTrangThai", "dv.sTenDonVi")->where('tk.PK_MaTaiKhoan', '=', $id_tk)
                ->join('tbldonvi as dv', 'dv.FK_MaTaiKhoan', '=', 'tk.PK_MaTaiKhoan')
                ->first();
        } else if ($id_quyen == 4) {
            $array = DB::table('tbltaikhoan as tk')
                ->join('tblquyen as q', 'q.PK_MaQuyen', '=', 'tk.FK_MaQuyen')
                ->select("tk.PK_MaTaiKhoan", "tk.sUsername", "tk.sTrangThai", "cn.FK_MaDonVi", "cn.sTenCaNhan", "cn.sEmail", "cn.sTenChucVu", "cn.bGioiTinh")->where('tk.PK_MaTaiKhoan', '=', $id_tk)
                ->join('tblcanhan as cn', 'cn.FK_MaTaiKhoan', '=', 'tk.PK_MaTaiKhoan')
                ->get();
        }
        return $array;
    }

    public function capNhatCaNhan(){
        
    }
}
