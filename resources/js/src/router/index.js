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

//đơn vị
import KhenThuongTheoDot from "../views/DonVi/KhenThuongTheoDot.vue";
import KhenThuongDotXuat from "../views/DonVi/KhenThuongDotXuat.vue";
import authMiddleware from "../middleware/auth";
import DeXuatTheoDot from "../views/DonVi/DeXuatTheoDot.vue";
import DeXuatDotXuat from "../views/DonVi/DeXuatDotXuat.vue";

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
                    meta: { roles: [2] }, // Chỉ role 2 được vào
                },
                {
                    path: "quanlydottdkt",
                    component: QuanLyDotTDKT,
                    meta: { roles: [2] },
                },
                {
                    path: "profile",
                    component: ChangePassView,
                    meta: { roles: [2] },
                },
                {
                    path: "quanlydanhhieu",
                    component: QuanLyDanhHieu,
                    meta: { roles: [2] },
                },
                {
                    path: "thongkedanhhieu",
                    component: ThongKeDanhHieu,
                    meta: { roles: [2] },
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

                //đơn vị
                {
                    path: "khenthuongdot",
                    component: KhenThuongTheoDot,
                    meta: { roles: [4] },
                },
                {
                    path: "dexuattheodot",
                    component: DeXuatTheoDot,
                    meta: { roles: [4] },
                },
                {
                    path: "khenthuongdotxuat",
                    component: KhenThuongDotXuat,
                    meta: { roles: [4] },
                },
                {
                    path: "dexuatdotxuat",
                    component: DeXuatDotXuat,
                    meta: { roles: [4] },
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
