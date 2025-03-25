<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-uppercase">Biên bản bình xét thi đua năm học</h3>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submitForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="soHuongDan">Theo hướng dẫn số:</label>
                                        <input type="text" class="form-control" id="soHuongDan"
                                            v-model="formData.soHuongDan" placeholder="Nhập số hướng dẫn">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="thoiGian">Vào hồi:</label>
                                        <input type="datetime-local" class="form-control" id="thoiGian"
                                            v-model="formData.thoiGian">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="diaChi">Địa chỉ:</label>
                                <input type="text" class="form-control" id="diaChi" v-model="formData.diaChi"
                                    placeholder="Nhập địa chỉ">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tongSoNguoi">Tổng số người trong đơn vị:</label>
                                        <input type="number" class="form-control" id="tongSoNguoi"
                                            v-model="formData.tongSoNguoi" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="soTrieuTap">Số người được triệu tập:</label>
                                        <input type="number" class="form-control" id="soTrieuTap"
                                            v-model="formData.soTrieuTap" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="soCoMat">Số người có mặt:</label>
                                        <input type="number" class="form-control" id="soCoMat"
                                            v-model="formData.soCoMat" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="soVang">Số người vắng:</label>
                                        <input type="number" class="form-control" id="soVang" v-model="formData.soVang"
                                            min="0" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="ghiChu">Ghi chú:</label>
                                <textarea class="form-control" id="ghiChu" v-model="formData.ghiChu" rows="3"></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="chuTri">Chủ trì:</label>
                                        <select class="form-select" id="chuTri" v-model="formData.chuTri">
                                            <option value="" disabled selected>Chọn người chủ trì</option>
                                            <option v-for="person in danhSachCaNhan" :key="person.id"
                                                :value="person.id">
                                                {{ person.ten }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="thuKy">Thư ký:</label>
                                        <select class="form-select" id="thuKy" v-model="formData.thuKy">
                                            <option value="" disabled selected>Chọn thư ký</option>
                                            <option v-for="person in danhSachCaNhan" :key="person.id"
                                                :value="person.id">
                                                {{ person.ten }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="namHoc">Bình xét cho năm học:</label>
                                <select class="form-select" id="namHoc" v-model="formData.namHoc">
                                    <option value="" disabled selected>Chọn năm học</option>
                                    <option v-for="year in namHocList" :key="year" :value="year">{{ year }}</option>
                                </select>
                            </div>

                            <!-- Phần bình bầu cá nhân -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="mb-0">Bình bầu danh hiệu cá nhân</h4>
                                </div>
                                <div class="card-body">
                                    <!-- Lao động tiên tiến -->
                                    <div class="mb-4">
                                        <h5 class="border-bottom pb-2">Danh hiệu Lao động tiên tiến</h5>
                                        <div v-for="(person, index) in formData.caNhan.laoDongTienTien" :key="index"
                                            class="row mb-2 align-items-center">
                                            <div class="col-md-6">
                                                <select class="form-select" v-model="person.id">
                                                    <option value="" disabled selected>Chọn cá nhân</option>
                                                    <option v-for="p in danhSachCaNhan" :key="p.id" :value="p.id">{{
                                                        p.ten }}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" v-model="person.soPhieu"
                                                        min="0" placeholder="Số phiếu bầu">
                                                    <span class="input-group-text">phiếu</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger"
                                                    @click="xoaCaNhan('laoDongTienTien', index)">
                                                    Xóa
                                                </button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-secondary mt-2"
                                            @click="themCaNhan('laoDongTienTien')">
                                            Thêm cá nhân
                                        </button>
                                    </div>

                                    <!-- Chiến sĩ thi đua cơ sở -->
                                    <div class="mb-4">
                                        <h5 class="border-bottom pb-2">Danh hiệu Chiến sĩ thi đua cơ sở</h5>
                                        <div v-for="(person, index) in formData.caNhan.chienSiThiDua" :key="index"
                                            class="row mb-2 align-items-center">
                                            <div class="col-md-6">
                                                <select class="form-select" v-model="person.id">
                                                    <option value="" disabled selected>Chọn cá nhân</option>
                                                    <option v-for="p in danhSachCaNhan" :key="p.id" :value="p.id">{{
                                                        p.ten }}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" v-model="person.soPhieu"
                                                        min="0" placeholder="Số phiếu bầu">
                                                    <span class="input-group-text">phiếu</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger"
                                                    @click="xoaCaNhan('chienSiThiDua', index)">
                                                    Xóa
                                                </button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-secondary mt-2"
                                            @click="themCaNhan('chienSiThiDua')">
                                            Thêm cá nhân
                                        </button>
                                    </div>

                                    <!-- Giấy khen của hiệu trưởng -->
                                    <div class="mb-4">
                                        <h5 class="border-bottom pb-2">Giấy khen của hiệu trưởng</h5>
                                        <div v-for="(person, index) in formData.caNhan.giayKhen" :key="index"
                                            class="row mb-2 align-items-center">
                                            <div class="col-md-6">
                                                <select class="form-select" v-model="person.id">
                                                    <option value="" disabled selected>Chọn cá nhân</option>
                                                    <option v-for="p in danhSachCaNhan" :key="p.id" :value="p.id">{{
                                                        p.ten }}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" v-model="person.soPhieu"
                                                        min="0" placeholder="Số phiếu bầu">
                                                    <span class="input-group-text">phiếu</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger"
                                                    @click="xoaCaNhan('giayKhen', index)">
                                                    Xóa
                                                </button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-secondary mt-2"
                                            @click="themCaNhan('giayKhen')">
                                            Thêm cá nhân
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Phần bình bầu tập thể -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="mb-0">Bình bầu danh hiệu tập thể</h4>
                                </div>
                                <div class="card-body">
                                    <!-- Tập thể lao động tiên tiến/xuất sắc -->
                                    <div class="mb-4">
                                        <h5 class="border-bottom pb-2">Danh hiệu tập thể</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Danh hiệu đề xuất:</label>
                                                    <select class="form-select" v-model="formData.tapThe.danhHieu">
                                                        <option value="" disabled selected>Chọn danh hiệu</option>
                                                        <option value="Tập thể lao động tiên tiến">Tập thể lao động tiên
                                                            tiến</option>
                                                        <option value="Tập thể lao động xuất sắc">Tập thể lao động xuất
                                                            sắc</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Số phiếu bầu:</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control"
                                                            v-model="formData.tapThe.soPhieu" min="0">
                                                        <span class="input-group-text">phiếu</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Giấy khen của hiệu trưởng cho tập thể -->
                                    <div class="mb-4">
                                        <h5 class="border-bottom pb-2">Giấy khen của hiệu trưởng</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Số phiếu bầu:</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control"
                                                            v-model="formData.tapThe.giayKhen.soPhieu" min="0">
                                                        <span class="input-group-text">phiếu</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Lưu biên bản</button>
                                <button type="button" class="btn btn-secondary ms-2" @click="resetForm">Làm mới</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { watch } from 'vue';

// Dữ liệu form
const formData = ref({
    soHuongDan: '',
    thoiGian: '',
    diaChi: '',
    tongSoNguoi: 0,
    soTrieuTap: 0,
    soCoMat: 0,
    soVang: 0,
    chuTri: '',
    thuKy: '',
    namHoc: '',
    ghiChu: '',
    caNhan: {
        laoDongTienTien: [],
        chienSiThiDua: [],
        giayKhen: []
    },
    tapThe: {
        danhHieu: '',
        soPhieu: 0,
        giayKhen: {
            soPhieu: 0
        }
    }
});

// Tính số người vắng
const soVang = computed(() => {
    return formData.value.soTrieuTap - formData.value.soCoMat;
});

// Cập nhật số người vắng khi số người có mặt hoặc triệu tập thay đổi
const watchSoNguoi = () => {
    formData.value.soVang = soVang.value;
};

// Danh sách cá nhân trong đơn vị (giả lập)
const danhSachCaNhan = ref([]);

// Danh sách năm học
const currentYear = new Date().getFullYear();
const namHocList = ref([]);

// Khởi tạo danh sách năm học
for (let i = 0; i < 5; i++) {
    const year = currentYear - i;
    namHocList.value.push(`${year}-${year + 1}`);
}

// Thêm cá nhân vào danh sách bình bầu
const themCaNhan = (loaiDanhHieu) => {
    formData.value.caNhan[loaiDanhHieu].push({
        id: '',
        soPhieu: 0
    });
};

// Xóa cá nhân khỏi danh sách bình bầu
const xoaCaNhan = (loaiDanhHieu, index) => {
    formData.value.caNhan[loaiDanhHieu].splice(index, 1);
};

// Lấy danh sách cá nhân từ API
const fetchDanhSachCaNhan = async () => {
    try {
        // Thay thế bằng API thực tế
        // const response = await axios.get('/api/donvi/canhan');
        // danhSachCaNhan.value = response.data;

        // Dữ liệu mẫu
        danhSachCaNhan.value = [
            { id: 1, ten: 'Nguyễn Văn A' },
            { id: 2, ten: 'Trần Thị B' },
            { id: 3, ten: 'Lê Văn C' },
            { id: 4, ten: 'Phạm Thị D' },
            { id: 5, ten: 'Hoàng Văn E' }
        ];
    } catch (error) {
        console.error('Lỗi khi lấy danh sách cá nhân:', error);
    }
};

// Gửi form
const submitForm = async () => {
    try {
        // Thay thế bằng API thực tế
        // await axios.post('/api/binhbau/bienbanhop', formData.value);
        alert('Lưu biên bản thành công!');
    } catch (error) {
        console.error('Lỗi khi gửi form:', error);
        alert('Có lỗi xảy ra khi lưu biên bản!');
    }
};

// Reset form
const resetForm = () => {
    formData.value = {
        soHuongDan: '',
        thoiGian: '',
        diaChi: '',
        tongSoNguoi: 0,
        soTrieuTap: 0,
        soCoMat: 0,
        soVang: 0,
        chuTri: '',
        thuKy: '',
        namHoc: '',
        caNhan: {
            laoDongTienTien: [],
            chienSiThiDua: [],
            giayKhen: []
        },
        tapThe: {
            danhHieu: '',
            soPhieu: 0,
            giayKhen: {
                soPhieu: 0
            }
        }
    };
};

// Watch changes
const setupWatchers = () => {
    // Sử dụng watch để theo dõi thay đổi
    watch([() => formData.value.soTrieuTap, () => formData.value.soCoMat], () => {
        watchSoNguoi();
    });
};

onMounted(() => {
    fetchDanhSachCaNhan();
    setupWatchers();
});
</script>

<style scoped>
.card {
    margin-top: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-group {
    margin-bottom: 1rem;
}

label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}
</style>