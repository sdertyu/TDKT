import { createRouter, createWebHistory } from "vue-router";
import AccountView from "../views/TCHC/QuanLyTaiKhoan.vue";
import LoginView from "../views/LoginView.vue";
import HomeView from "../views/HomeView.vue";
import ForbiddenView from "../views/ForbiddenView.vue"; // Trang lỗi 403
import ChangePassView from "../views/ChangePassView.vue"; // Trang lỗi 403
import QuanLyDotTDKT from "../views/TCHC/QuanLyDotTDKT.vue";
import QuanLyDanhHieu from "../views/TCHC/QuanLyDanhHieu.vue";
import ThongKeDanhHieu from "../views/TCHC/ThongKeDanhHieu.vue";
import ThongKeCaNhan from "../views/TCHC/ThongKeCaNhan.vue";
import ThongKeDonVi from "../views/TCHC/ThongKeDonVi.vue";
import QuanLyVBDK from "../views/TCHC/QuanLyVBDK.vue";
import QuanLyThongBao from "../views/TCHC/QuanLyThongBao.vue"; // Trang lỗi 403
import DotDotXuat from "../views/TCHC/DotDotXuat.vue";
import QuanLyKienToan from "../views/TCHC/QuanLyKienToan.vue"; // Trang lỗi 403

//đơn vị
import KhenThuongTheoDot from "../views/DonVi/KhenThuongTheoDot.vue";
import KhenThuongDotXuat from "../views/DonVi/KhenThuongDotXuat.vue";
import authMiddleware from "../middleware/auth";
import DeXuatTheoDot from "../views/DonVi/DeXuatTheoDot.vue";
import DeXuatDotXuat from "../views/DonVi/DeXuatDotXuat.vue";
import VanBanDinhKem from "../views/DonVi/VanBan.vue";
import ThanhTichCaNhan from "../views/DonVi/ThanhTichCaNhan.vue";

//hội đồng
import MinhChungTheoDot from "../views/HoiDong/MinhChungTheoDot.vue";
import MinhChungDotXuat from "../views/HoiDong/MinhChungDotXuat.vue";
import HoiDongTheoDot from "../views/HoiDong/HoiDongTheoDot.vue";
import HoiDongDotXuat from "../views/HoiDong/HoiDongDotXuat.vue";
import ChiTietHoiDong from "../views/HoiDong/ChiTietHoiDong.vue";


//all
import ThongBaoView from "../views/ThongBaoView.vue";
import ThemMinhChung from "../views/MinhChungView.vue";
import ThanhTichCuaToi from "../views/ThanhTichCuaToi.vue";
const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "default",
            component: () => import("../layouts/LayoutView.vue"),
            redirect: "/home",
            beforeEnter: authMiddleware,
            children: [
                {
                    path: "home",
                    component: HomeView,
                },
                {
                    path: "quanlytaikhoan",
                    component: AccountView,
                    meta: { roles: [2] , title: 'Quản lý tài khoản'}, // Chỉ role 2 được vào
                },
                {
                    path: "quanlydottdkt",
                    component: QuanLyDotTDKT,
                    meta: { roles: [2] , title: 'Quản lý đợt thi đua khen thưởng'},
                },
                {
                    path: "profile",
                    component: ChangePassView,
                    meta: { roles: [2] , title: "Tài khoản"},
                },
                {
                    path: "quanlydanhhieu",
                    component: QuanLyDanhHieu,
                    meta: { roles: [2] , title: 'Quản lý danh hiệu'},
                },
                {
                    path: "thongkedanhhieu",
                    component: ThongKeDanhHieu,
                    meta: { roles: [2] , title: 'Thống kê danh hiệu'},
                },
                {
                    path: "thongkecanhan",
                    component: ThongKeCaNhan,
                    meta: { roles: [2] },
                },
                {
                    path: "thongkedonvi",
                    component: ThongKeDonVi,
                    meta: { roles: [2] },
                },
                {
                    path: "quanlyvanban/:id",
                    component: QuanLyVBDK,
                    meta: { roles: [2] , title: 'Quản lý văn bản'},
                },
                {
                    path: "quanlythongbao",
                    component: QuanLyThongBao,
                    meta: { roles: [2] , title: 'Quản lý thông báo'},

                },

                {
                    path: "dotdotxuat/:id",
                    component: DotDotXuat,
                    meta: { roles: [2] , title: 'Đợt khen thưởng đột xuất'},
                },

                {
                    path: "quanlykientoan",
                    component: QuanLyKienToan,
                    meta: { roles: [2] , title: 'Quản lý kiện toàn'},
                },

                //đơn vị
                {
                    path: "khenthuongdot",
                    component: KhenThuongTheoDot,
                    meta: { roles: [4, 5] , title: 'Khen thưởng theo đợt'},
                },
                {
                    path: "dexuattheodot",
                    component: DeXuatTheoDot,
                    meta: { roles: [4, 5] , title: "Đề xuất theo đợt"},
                },
                {
                    path: "khenthuongdotxuat",
                    component: KhenThuongDotXuat,
                    meta: { roles: [4] , title: 'Khen thưởng đột xuất'},
                },
                {
                    path: "vanban",
                    component: VanBanDinhKem,
                    meta: { roles: [4, 5] , title: 'Văn bản đính kèm'},
                },
                {
                    path: "dexuatdotxuat",
                    component: DeXuatDotXuat,
                    meta: { roles: [4] , title: 'Đề xuất đột xuất'},
                },
                {
                    path: "thanhtichcanhan",
                    component: ThanhTichCaNhan,
                    meta: { roles: [4] , title: 'Thành tích cá nhân trong đơn vị'},
                },



                //Cá nhân


                //Hội đồng
                {
                    path: 'minhchungtheodot',
                    component: MinhChungTheoDot,
                    meta: { roles: [2, 3] , title: 'Minh chứng theo đợt'},
                },
                {
                    path: 'minhchungdotxuat',
                    component: MinhChungDotXuat,
                    meta: { roles: [2, 3] , title: 'Minh chứng đột xuất'},
                },
                //chung
                {
                    path: "thongbao/:id",
                    component: ThongBaoView,
                    meta: { title: 'Thông báo'},
                },
                {
                    path: 'themminhchung/:id',
                    component: ThemMinhChung,
                    meta: { roles: [3, 4, 5] , title: 'Thêm minh chứng'},
                },
                {
                    path: "hoidongtdkttheodot",
                    component: HoiDongTheoDot,
                    meta: { roles: [3] , title: 'Hội đồng thi đua khen thưởng'},
                },
                {
                    path: "hoidongtdktdotxuat",
                    component: HoiDongDotXuat,
                    meta: { roles: [3] , title: 'Hội đồng thi đua khen thưởng'},
                },
                {
                    path: "thanhtichcuatoi",
                    component: ThanhTichCuaToi,
                    meta: { roles: [4, 5] , title: 'Thành tích của tôi'},
                },
                {
                    path: 'chitiethoidong/:id',
                    component: ChiTietHoiDong,
                    meta: { roles: [3] , title: 'Chi tiết hội đồng'},
                }
            ],
        },
        {
            path: "/login",
            name: "login",
            component: LoginView,
        },
        {
            path: "/:pathMatch(.*)*",
            redirect: "403",
        },
        {
            path: "/403",
            name: "forbidden",
            component: ForbiddenView,
        },
    ],
});

export default router;
