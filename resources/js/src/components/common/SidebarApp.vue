<script setup>
import { OverlayScrollbarsComponent } from "overlayscrollbars-vue";
import { ref } from 'vue';
import SidebarMenu from '../sidebar/SidebarMenu.vue';

const sidebarMenuItems = ref([
    {
        title: 'Dashboard',
        icon: 'bi bi-speedometer',
        isActive: true,
        isOpen: true,
        children: [
            {
                title: 'Dashboard v1',
                icon: 'bi bi-circle',
                link: '/dist/pages/',
                isActive: true
            },
            {
                title: 'Dashboard v2',
                icon: 'bi bi-circle',
                link: '/dist/pages/index2',
                isActive: false
            },
            {
                title: 'Dashboard v3',
                icon: 'bi bi-circle',
                link: '/dist/pages/index3',
                isActive: false
            }
        ]
    },
    {
        title: 'Theme Generate',
        icon: 'bi bi-palette',
        link: '/dist/pages/generate/theme',
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
    {
        title: 'Layout Options',
        icon: 'bi bi-clipboard-fill',
        isActive: false,
        isOpen: false,
        badge: '6',
        children: [
            {
                title: 'Default Sidebar',
                icon: 'bi bi-circle',
                link: '/dist/pages/layout/unfixed-sidebar',
                isActive: false
            },
            {
                title: 'Fixed Sidebar',
                icon: 'bi bi-circle',
                link: '/dist/pages/layout/fixed-sidebar',
                isActive: false
            },
            {
                title: 'Layout <small>+ Custom Area </small>',
                icon: 'bi bi-circle',
                link: '/dist/pages/layout/layout-custom-area',
                isActive: false
            },
            // Thêm các mục còn lại...
        ]
    }
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

                <img src="" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />

                <span class="brand-text fw-light">AdminLTE 4</span>

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
