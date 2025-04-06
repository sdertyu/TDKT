<template>
    <router-view></router-view>
</template>

<script setup>

import { onMounted } from 'vue';
import { useGlobalStore } from '@/stores/global';

const getDotActive = async () => {
    const response = await axios.get(`api/dotthiduakhenthuong/dot-active`, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    });

    if (response.status === 200) {
        useGlobalStore().setDot(response.data.data.PK_MaDot);

    }
    else {
        console.error('Lỗi khi lấy danh sách cá nhân:', response);
    }
}
onMounted(() => {
    getDotActive();
})
</script>