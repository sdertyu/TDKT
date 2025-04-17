<template>
    <div class="card my-3 shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0 fw-bold">
                <i class="fas fa-award me-2"></i>Danh sách danh hiệu được đề xuất đợt {{ dot }}
            </h5>
        </div>
        <div class="card-body">
            <div v-if="danhHieuDeXuats.length === 0" class="text-center py-4">
                <i class="fas fa-info-circle fs-4 text-muted mb-2"></i>
                <p class="text-muted">Không có danh hiệu nào được đề xuất</p>
            </div>
            <div v-else>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="80">STT</th>
                                <th>Tên danh hiệu</th>
                                <th class="text-center" width="180">Ngày tạo</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in danhHieuDeXuats" :key="item.id" class="border-bottom"
                                :class="{ 'table-row-hover': true }">
                                <td class="text-center fw-bold">{{ index + 1 }}</td>
                                <td>{{ item.tenDanhHieu }}</td>
                                <td class="text-center">{{ formatDate(item.NgayTao) }}</td>
                                <td class="text-center">
                                    <span v-if="item.trangThai === 1" class="badge bg-success">Đạt</span>
                                    <span v-else-if="item.trangThai === 0" class="badge bg-danger">Không đạt</span>
                                    <span v-else class="badge bg-secondary">Chưa duyệt</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary" @click="themMinhChung(item)"
                                        title="Thêm minh chứng">
                                        <i class="fas fa-plus-circle me-1"></i> Minh chứng
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Hiển thị {{ danhHieuDeXuats.length }} danh hiệu</small>
                    <div class="pagination-controls">
                        <!-- Phân trang có thể được thêm vào đây nếu cần -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useGlobalStore } from '@/stores/global';
import router from '../../router';
import axios from 'axios';

// Danh sách danh hiệu đề xuất
const danhHieuDeXuats = ref([]);
const dot = useGlobalStore().dotActive;
const loading = ref(false);

// Hàm lấy danh sách danh hiệu đề xuất
const getDanhHieuDeXuat = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/dexuat/getlisttheodot', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200) {
            danhHieuDeXuats.value = response.data.data;
        }
    } catch (error) {
        if(error.response) {
            if (error.response.status === 404) {
                toastError(error.response.data.message);
            } 
            else {
                toastError('Có lỗi xảy ra, vui lòng thử lại sau');
            }
        } else if (error.request) {
            toastError('Không thể kết nối đến máy chủ');
        } else {
            toastError('Có lỗi xảy ra, vui lòng thử lại sau');
        }
    } finally {
        loading.value = false;
    }
};

// Hàm định dạng ngày tháng
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
};

const themMinhChung = (item) => {
    router.push(`/themminhchung/${item.PK_MaDeXuat}`);
};

onMounted(() => {
    getDanhHieuDeXuat();
});
</script>

<style scoped>
.table-row-hover:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transition: background-color 0.2s ease;
    cursor: pointer;
}

.card {
    border-radius: 0.5rem;
}

.card-header {
    border-radius: calc(0.5rem - 1px) calc(0.5rem - 1px) 0 0;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}
</style>