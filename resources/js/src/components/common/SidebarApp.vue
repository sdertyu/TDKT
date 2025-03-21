<script setup>
import { OverlayScrollbarsComponent } from "overlayscrollbars-vue";
import { ref, onMounted, watch } from 'vue';
import SidebarMenu from '../sidebar/SidebarMenu.vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const role = ref(localStorage.getItem('role'));
const sidebarMenuItems = ref([]);

// Hàm để thiết lập trạng thái active dựa trên URL hiện tại
const setActiveMenuItem = () => {
    const currentPath = route.path;
    
    sidebarMenuItems.value.forEach(item => {
        // Kiểm tra item chính
        if (item.link === currentPath) {
            item.isActive = true;
        } else {
            item.isActive = false;
        }
        
        // Kiểm tra các item con nếu có
        if (item.children && item.children.length > 0) {
            const hasActiveChild = item.children.some(child => {
                if (child.link === currentPath) {
                    child.isActive = true;
                    return true;
                } else {
                    child.isActive = false;
                    return false;
                }
            });
            
            // Nếu có item con active, mở rộng menu cha
            if (hasActiveChild) {
                item.isOpen = true;
                item.isActive = true;
            }
        }
    });
};

switch (role.value) {
    case '1':
        break;
    case '2':
        sidebarMenuItems.value = [
            {
                title: 'Quản lý tài khoản',
                icon: 'bi bi-person-exclamation',
                link: '/quanlytaikhoan',
                isActive: false
            },
            {
                title: 'Quản lý đợt TĐKT',
                icon: 'bi bi-calendar',
                link: '/quanlydottdkt',
                isActive: false
            },
            {
                title: 'Quản lý danh hiệu',
                icon: 'bi bi-award',
                link: '/quanlydanhhieu',
                isActive: false
            },
            // {
            //     title: 'Widgets',
            //     icon: 'bi bi-box-seam-fill',
            //     isActive: false,
            //     isOpen: false,
            //     children: [
            //         {
            //             title: 'Small Box',
            //             icon: 'bi bi-circle',
            //             link: '',
            //             isActive: false
            //         },
            //         {
            //             title: 'info Box',
            //             icon: 'bi bi-circle',
            //             link: '',
            //             isActive: false
            //         },
            //         {
            //             title: 'Cards',
            //             icon: 'bi bi-circle',
            //             link: '',
            //             isActive: false
            //         }
            //     ]
            // },
        ];
        break;
    case '3':
        sidebarMenuItems.value = [
            {
                title: 'Quản lý tài khoản',
                icon: 'bi bi-person-exclamation',
                link: '/quanlytaikhoan',
                isActive: false
            },
        ];
        break;
    case '4':
        sidebarMenuItems.value = [
            {
                title: 'Quản lý bình bầu',
                icon: 'bi bi-box-seam-fill',
                isActive: false,
                isOpen: false,
                children: [
                    {
                        title: 'Khen thưởng theo đợt',
                        icon: 'bi bi-award',
                        link: '/khenthuongdot',
                        isActive: false
                    },
                    {
                        title: 'Khen thưởng đột xuất',
                        icon: 'bi bi-circle',
                        link: '/khenthuongdotxuat',
                        isActive: false
                    },
                ]
            },
            {
                title: 'Quản lý đề xuất',
                icon: 'bi bi-file-earmark-text',
                isActive: false,
                isOpen: false,
                children: [
                    {
                        title: 'Đề xuất theo đợt',
                        icon: 'bi bi-award',
                        link: '/dexuattheodot',
                        isActive: false
                    },
                    {
                        title: 'Đề xuất đột xuất',
                        icon: 'bi bi-circle',
                        link: '/dexuatdotxuat',
                        isActive: false
                    }
                ]
            }
        ];
        break;
    case '5':
        sidebarMenuItems.value = [
            {
                title: 'Quản lý tài khoản',
                icon: 'bi bi-person-exclamation',
                link: '/quanlytaikhoan',
                isActive: false
            },
        ];
        break;
    default:
        break;
}

// Theo dõi thay đổi route để cập nhật trạng thái active
watch(() => route.path, () => {
    setActiveMenuItem();
}, { immediate: true });

onMounted(() => {
    setActiveMenuItem();
});

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

            <a class='brand-link' href='/'>

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
