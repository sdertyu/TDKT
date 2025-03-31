<template>
    <div class="container">
        <!-- Card for danh hiệu management -->
        <div class="card my-4 shadow-sm">
            <div class="card-header">
                <h1 class="card-title">Quản lý danh hiệu</h1>
            </div>
            <div class="card-body">
                <!-- Button thêm danh hiệu mới -->
                <div class="mb-3">
                    <button class="btn btn-primary" @click="showAddModal" data-bs-target="#danhHieuModal"
                        data-bs-toggle="modal">
                        <i class="fas fa-plus"></i> Thêm danh hiệu mới
                    </button>
                </div>

                <!-- Bảng danh hiệu -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Tên danh hiệu</th>
                                <th>Dành cho</th>
                                <th>Hình thức</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(danhhieu, index) in danhSachDanhHieu" :key="danhhieu.id">
                                <td class="text-center">{{ ++index }}</td>
                                <td>{{ danhhieu.sTenDanhHieu }}</td>
                                <td>{{ danhhieu.sTenLoaiDanhHieu }}</td>
                                <td>{{ danhhieu.sTenHinhThuc }}</td>
                                <!-- <td>{{  }}</td> -->
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm me-2" @click="showEditModal(danhhieu)"
                                        data-bs-toggle="modal" data-bs-target="#danhHieuModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" @click="confirmDelete(danhhieu)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal thêm/sửa danh hiệu -->
        <div class="modal fade" id="danhHieuModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Cập nhật danh hiệu' : 'Thêm danh hiệu mới' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveDanhHieu">
                            <div class="mb-3">
                                <label class="form-label">Tên danh hiệu</label>
                                <input type="text" class="form-control" v-model="currentDanhHieu.tendanhhieu" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Danh hiệu dành cho</label>
                                <select class="form-select" v-model="currentDanhHieu.loaidanhhieu" required>
                                    <option value="" disabled selected>- Chọn loại danh hiệu -</option>
                                    <option value="1">Cá nhân</option>
                                    <option value="2">Đơn vị</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hình thức</label>
                                <select class="form-select" v-model="currentDanhHieu.hinhthuc" required>
                                    <option value="" disabled selected>- Chọn hình thức -</option>
                                    <option value="1">Theo đợt</option>
                                    <option value="2">Đột xuất</option>
                                </select>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-secondary me-2"
                                    data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const currentDanhHieu = reactive({
    id: null,
    tendanhhieu: '',
    loaidanhhieu: '',
    hinhthuc: '',
})

const danhSachDanhHieu = ref([])

const isEditing = ref(false)
const danhHieuForm = ref({
    id: null,
    ten: '',
    loai: 'canhan',
    hinhthuc: 'theodot',
    mota: ''
})

const loadDanhHieu = async () => {
    try {
        const response = await axios.get('/api/danhhieu/list', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        })

        if (response.status === 200) {
            if (Array.isArray(response.data.data)) {
                danhSachDanhHieu.value = response.data.data; // Gán mảng trực tiếp
            } else {
                // Nếu dữ liệu là đối tượng, chuyển thành mảng
                danhSachDanhHieu.value = Object.values(response.data.data);
            }
            console.log(danhSachDanhHieu.value);
        }


        // Tạm thời comment lại để dùng dữ liệu mẫu
        // danhSachDanhHieu.value = response.data
    } catch (error) {
        console.error('Lỗi khi tải danh sách danh hiệu:', error)
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không thể tải danh sách danh hiệu'
        })
    }
}

const showAddModal = () => {
    isEditing.value = false
    currentDanhHieu.id = null
    currentDanhHieu.tendanhhieu = ''
    currentDanhHieu.hinhthuc = ''
    currentDanhHieu.loaidanhhieu = ''
}

const showEditModal = (danhhieu) => {
    isEditing.value = true
    currentDanhHieu.id = danhhieu.PK_MaDanhHieu
    currentDanhHieu.tendanhhieu = danhhieu.sTenDanhHieu
    currentDanhHieu.loaidanhhieu = danhhieu.PK_MaLoaiDanhHieu
    currentDanhHieu.hinhthuc = danhhieu.PK_MaHinhThuc
}

const saveDanhHieu = async () => {
    try {
        let response = null;
        if (isEditing.value) {
            response = await axios.put(`/api/danhhieu/update`, currentDanhHieu, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`
                }
            });
        } else {
            response = await axios.post('/api/danhhieu/add', currentDanhHieu, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`
                }
            });
        }
        if (response.status === 200) {
            if (isEditing.value) {
                const index = danhSachDanhHieu.value.findIndex(d => d.PK_MaDanhHieu === currentDanhHieu.id)
                danhSachDanhHieu.value[index] = {
                    PK_MaDanhHieu: currentDanhHieu.id,
                    sTenDanhHieu: currentDanhHieu.tendanhhieu,
                    sTenLoaiDanhHieu: currentDanhHieu.loaidanhhieu,
                    sTenHinhThuc: currentDanhHieu.hinhthuc
                }
            }

            else {
                danhSachDanhHieu.value.push({
                    PK_MaDanhHieu: response.data.data.PK_MaDanhHieu,
                    sTenDanhHieu: response.data.data.sTenDanhHieu,
                    sTenLoaiDanhHieu: response.data.data.sTenLoaiDanhHieu,
                    sTenHinhThuc: response.data.data.sTenHinhThuc
                })
            }

            Swal.fire({
                icon: 'success',
                title: 'Lưu danh hiệu thành công',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            })
        }

        document.getElementById("danhHieuModal").querySelector(".btn-close").click();
    } catch (error) {
        console.error('Lỗi khi lưu danh hiệu:', error)
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không thể lưu danh hiệu'
        })
    }
}

const confirmDelete = (danhhieu) => {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: `Bạn có chắc muốn xóa danh hiệu "${danhhieu.sTenDanhHieu}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`/api/danhhieu/delete/${danhhieu.PK_MaDanhHieu}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('api_token')}`
                    }
                })
                if (response.status === 200) {
                    danhSachDanhHieu.value = danhSachDanhHieu.value.filter(d => d.PK_MaDanhHieu !== danhhieu.PK_MaDanhHieu)
                    Swal.fire({
                        icon: 'success',
                        title: 'Xóa danh hiệu thành công',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    })
                }
            } catch (error) {
                console.error('Lỗi khi xóa danh hiệu:', error)
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể xóa danh hiệu'
                })
            }
        }
    })
}

onMounted(() => {
    loadDanhHieu()
})
</script>