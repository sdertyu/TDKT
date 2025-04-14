<template>
    <div class="app-wrapper">
        <HeaderApp />
        <SidebarApp />
        <ContentApp />
        <FooterApp />
        <LoadingView v-if="loadingStore.loading" />
    </div>
</template>

<script setup>
import SidebarApp from "../components/common/SidebarApp.vue";
import HeaderApp from "../components/common/HeaderApp.vue";
import FooterApp from "../components/common/FooterApp.vue";
import ContentApp from "../components/common/ContentApp.vue";
import LoadingView from "../components/common/LoadingView.vue";

import { onMounted } from 'vue'

const loadingStore = useGlobalStore()

const getDotActive = async () => {
    const response = await axios.get(`api/dotthiduakhenthuong/dot-active`, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    });

    if (response.status === 200) {
        if (response.data.data) {
            useGlobalStore().setDot(response.data.data.PK_MaDot);
        }
    }
    else {
        console.error('Lỗi khi lấy danh sách cá nhân:', response);
    }
}

onMounted(async () => {
    const adminlte = await import('admin-lte/dist/js/adminlte.min.js');
    getDotActive();
});

</script>
