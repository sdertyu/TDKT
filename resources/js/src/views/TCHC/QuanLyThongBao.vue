<template>
    <div class="card m-4">
        <div class="card-header">
            <h3 class="card-title">Danh sách thông báo</h3>
        </div>
        <div class="card-body">
            <div class="" v-for="(item, index) in listThongBao" :key="index">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card" style="max-width: 100%;">
                            <div class="card-header"><strong>{{ item.sTieuDe }}</strong></div>
                            <div class="card-body text-primary">
                                <p class="card-text">{{ item.sNoiDung }}</p>
                                <p class="card-text"><small class="text-muted">{{ item.dNgayTao }}</small></p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr v-if="index !== listThongBao.length - 1" class="my-4" style="border-top: 1px solid #ccc;">
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { toastSuccess, toastError} from '@/utils/toast.js';

const listThongBao = ref([]);

const getListThongBao = () => {
    const list = axios.get('/api/thongbao/getlisttheoquyen', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    })
    .then(response => {
        if(response.status === 200) {
            listThongBao.value = response.data.data;
        }
    })
    .catch(error => {
        toastError("Có lỗi xảy ra khi lấy danh sách thông báo")
    })
}
onMounted(() => {
    getListThongBao();
})

onMounted
</script>