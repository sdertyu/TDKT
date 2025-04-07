<template>
    <div class="card m-4" v-if="thongBao.tieuDe !== ''">
        <div class="card-body">
            <p style="font-weight: 600;">Tiêu đề: {{ thongBao.tieuDe }}</p>
            <p>{{ thongBao.noiDung }}</p>
            <p class="text-end">Đã được đang lúc {{ thongBao.ngayTao }}</p>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRoute } from 'vue-router';

import { toastError } from '@/utils/toast.js';


const id = useRoute().params.id;

const thongBao = reactive({
    tieuDe: '',
    noiDung: '',
    ngayTao: '',
});

const getThongBao = () => {
    axios.get('/api/thongbao/getthongbao/' + id, {
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('api_token')
        }
    }).then((response) => {
        if (response.status === 200) {
            thongBao.tieuDe = response.data.data.sTieuDe;
            thongBao.noiDung = response.data.data.sNoiDung;
            thongBao.ngayTao = response.data.data.dNgayTao;
        } else {
            toastError(response.data.message);
        }
    }).catch((error) => {
        toastError("Đã có lỗi xảy ra, vui lòng thử lại sau!");
    });
}

onMounted(() => {
    getThongBao();
})
</script>