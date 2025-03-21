<template>
    <div class="card my-2">
        <div class="card-header">
            <h5 class="card-title">Danh sách danh hiệu đề xuất</h5>
        </div>
        <div class="card-body">
            <div v-if="danhHieuDeXuats.length === 0" class="text-center py-3">
                <p>Không có danh hiệu nào được đề xuất</p>
            </div>
            <div v-else>
                <div v-for="(danhHieu, index) in danhHieuDeXuats" :key="index" class="mb-4 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">{{ danhHieu.ten }}</h6>
                        <span class="badge bg-primary">{{ danhHieu.loai }}</span>
                    </div>
                    <div class="form-group">
                        <label :for="`minhChung-${index}`" class="form-label">Minh chứng:</label>
                        <textarea :id="`minhChung-${index}`" v-model="danhHieu.minhChung" class="form-control" rows="3"
                            placeholder="Nhập thông tin minh chứng cho danh hiệu này..."></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button @click="luuMinhChung" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu minh chứng
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// Danh sách danh hiệu đề xuất
const danhHieuDeXuats = ref([]);

// Hàm lấy danh sách danh hiệu đề xuất
const fetchDanhHieuDeXuat = async () => {
    try {
        // Giả lập dữ liệu, thay thế bằng API call thực tế
        danhHieuDeXuats.value = [
            { id: 1, ten: 'Chiến sĩ thi đua cơ sở', loai: 'Cá nhân', minhChung: '' },
            { id: 2, ten: 'Lao động tiên tiến', loai: 'Cá nhân', minhChung: '' },
            { id: 3, ten: 'Tập thể lao động xuất sắc', loai: 'Tập thể', minhChung: '' },
        ];
    } catch (error) {
        console.error('Lỗi khi lấy danh sách danh hiệu:', error);
    }
};

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
    fetchDanhHieuDeXuat();
});
</script>