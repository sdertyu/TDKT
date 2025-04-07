<template>
    <div class="card my-2">
        <div class="card-header">
            <h5 class="card-title">Danh sách danh hiệu được đề xuất đợt {{ dot }}</h5>
        </div>
        <div class="card-body">
            <div v-if="danhHieuDeXuats.length === 0" class="text-center py-3">
                <p>Không có danh hiệu nào được đề xuất</p>
            </div>
            <div v-else>
                <table class="table table-striped table-hover">
                    <thead>
                        <th>STT</th>
                        <th>Tên danh hiệu</th>
                        <th>Ngày tạo</th>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in danhHieuDeXuats" :key="item.id" @click="themMinhChung(item)">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.tenDanhHieu }}</td>
                            <td>{{ item.NgayTao }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>

import { ref, onMounted } from 'vue';
import { useGlobalStore } from '@/stores/global'


import router from '../../router';

// Danh sách danh hiệu đề xuất
const danhHieuDeXuats = ref([]);
const dot = useGlobalStore().dotActive;

// Hàm lấy danh sách danh hiệu đề xuất
const getDanhHieuDeXuat = () => {
    axios.get('/api/dexuat/getlisttheodot', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    })
        .then(response => {
            if (response.status === 200) {
                danhHieuDeXuats.value = response.data.data;
            } else {
                toastError('Lỗi khi lấy danh sách danh hiệu đề xuất:', response.message);
            }
        })
};

const themMinhChung = (item) => {
    router.push(`/themminhchung/${item.PK_MaDeXuat}`);
}

// Hàm lưu thông tin minh chứng
const luuMinhChung = async () => {
    try {
        // Xử lý lưu minh chứng vào database
        console.log('Đã lưu minh chứng:', danhHieuDeXuats.value);
        // Gọi API lưu minh chứng ở đây
    } catch (error) {
        console.error('Lỗi khi lưu minh chứng:', error);
    }
};

onMounted(() => {
    getDanhHieuDeXuat();
    console.log(useGlobalStore().dotActive);
});
</script>
