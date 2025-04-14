<template>
    <div class="app-wrapper">
        <HeaderApp />
        <SidebarApp />

        <!-- Chỉ render ContentApp sau khi dotActive có giá trị -->
        <ContentApp v-if="isReady" />

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

import { ref, onMounted } from 'vue'
import { useGlobalStore } from '@/stores/global'
import axios from 'axios'

const loadingStore = useGlobalStore()
const isReady = ref(false)

const getDotActive = async () => {
    try {
        const response = await axios.get(`api/dotthiduakhenthuong/dot-active`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200 && response.data.data) {
            useGlobalStore().setDot(response.data.data.PK_MaDot);
        }
    } catch (err) {
        console.error('Lỗi khi lấy đợt:', err);
    }
}

onMounted(async () => {
    await import('admin-lte/dist/js/adminlte.min.js');
    await getDotActive();  // Đợi API xong
    isReady.value = true;  // Cho phép render ContentApp
});
</script>