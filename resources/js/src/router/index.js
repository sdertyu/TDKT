import { createRouter, createWebHistory } from "vue-router";
import AccountView from "../views/AccountView.vue";
import LoginView from "../views/LoginView.vue";
import HomeView from "../views/HomeView.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "home",
            component: () => import("../layouts/LayoutView.vue"),
            redirect: "/home",
            children: [
                {
                    path: "/home",
                    component: HomeView,
                },
                {
                    path: '/quanlytaikhoan',
                    component: AccountView
                }
            ],
        },
        {
            path: "/login",
            name: "login",
            component: LoginView,
        },
    ],
});

export default router;
