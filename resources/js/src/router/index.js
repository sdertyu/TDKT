import { createRouter, createWebHistory } from "vue-router";
import AccountView from "../views/ManageAccount.vue";
import LoginView from "../views/LoginView.vue";
import HomeView from "../views/HomeView.vue";
import ForbiddenView from "../views/ForbiddenView.vue"; // Trang lỗi 403
import ChangePassView from "../views/ChangePassView.vue"; // Trang lỗi 403

import authMiddleware from "../middleware/auth";

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
                    meta: { roles: [2] } // Chỉ role 2 được vào
                },
                {
                    path: "profile",
                    component: ChangePassView,
                }
            ],
        },
        {
            path: "/login",
            name: "login",
            component: LoginView,
        },
        {
            path: "/403",
            name: "forbidden",
            component: ForbiddenView, 
        },
    ],
});

export default router;
