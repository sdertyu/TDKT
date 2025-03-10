<script setup>
import { OverlayScrollbarsComponent } from "overlayscrollbars-vue";
import { ref } from 'vue';
import SidebarMenu from '../sidebar/SidebarMenu.vue';

const sidebarMenuItems = ref([
    {
        title: 'Quản lý tài khoản',
        icon: 'bi bi-palette',
        link: '/quanlytaikhoan',
        isActive: false
    },
    {
        title: 'Widgets',
        icon: 'bi bi-box-seam-fill',
        isActive: false,
        isOpen: false,
        children: [
            {
                title: 'Small Box',
                icon: 'bi bi-circle',
                link: '/dist/pages/widgets/small-box',
                isActive: false
            },
            {
                title: 'info Box',
                icon: 'bi bi-circle',
                link: '/dist/pages/widgets/info-box',
                isActive: false
            },
            {
                title: 'Cards',
                icon: 'bi bi-circle',
                link: '/dist/pages/widgets/cards',
                isActive: false
            }
        ]
    },
]);
</script>

<style scoped>
:deep(.os-scrollbar .os-scrollbar-handle) {
    background-color: #a3a6a9 !important;
    /* Màu tay nắm */
}
</style>

<template>
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

        <div class="sidebar-brand">

            <a class='brand-link' href='/dist/pages/'>

                <img src="/images/logo_hou.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />

                <span class="brand-text fw-light">Thi đua khen thưởng</span>

            </a>

        </div>

        <OverlayScrollbarsComponent :options="{ scrollbars: { autoHide: 'leave', handle: { color: 'red' } } }" defer>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <sidebar-menu :menuItems="sidebarMenuItems"></sidebar-menu>
                </nav>
            </div>
        </OverlayScrollbarsComponent>

        <!--end::Sidebar Wrapper-->
    </aside>
</template>
