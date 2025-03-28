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
                                <th>Loại</th>
                                <th>Hình thức</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(danhhieu, index) in danhSachDanhHieu" :key="danhhieu.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ danhhieu.ten }}</td>
                                <td>{{ danhhieu.loai === 'canhan' ? 'Cá nhân' : 'Đơn vị' }}</td>
                                <td>{{ danhhieu.hinhthuc === 'theodot' ? 'Theo đợt' : 'Đột xuất' }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm me-2" @click="showEditModal(danhhieu)"
                                        data-bs-toggle="modal" data-bs-target="#danhHieuModal">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button class="btn btn-danger btn-sm" @click="confirmDelete(danhhieu)">
                                        <i class="fas fa-trash"></i> Xóa
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
                                <input type="text" class="form-control" v-model="danhHieuForm.ten" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Loại danh hiệu</label>
                                <select class="form-select" v-model="danhHieuForm.loai" required>
                                    <option value="canhan">Cá nhân</option>
                                    <option value="donvi">Đơn vị</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hình thức</label>
                                <select class="form-select" v-model="danhHieuForm.hinhthuc" required>
                                    <option value="theodot">Theo đợt</option>
                                    <option value="dotxuat">Đột xuất</option>
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
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const danhSachDanhHieu = ref([
    {
        id: 1,
        ten: 'Chiến sĩ thi đua cơ sở',
        loai: 'canhan',
        hinhthuc: 'theodot',
        mota: 'Danh hiệu cho cá nhân đạt thành tích xuất sắc trong năm'
    },
    {
        id: 2,
        ten: 'Tập thể lao động xuất sắc',
        loai: 'donvi',
        hinhthuc: 'theodot',
        mota: 'Danh hiệu cho đơn vị có thành tích xuất sắc trong năm'
    },
    {
        id: 3,
        ten: 'Giấy khen đột xuất',
        loai: 'canhan',
        hinhthuc: 'dotxuat',
        mota: 'Khen thưởng đột xuất cho cá nhân có thành tích đặc biệt'
    }
])

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
        const response = await axios.get('/api/danhhieu')
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
    danhHieuForm.value = {
        id: null,
        ten: '',
        loai: 'canhan',
        hinhthuc: 'theodot',
        mota: ''
    }
    const modal = new bootstrap.Modal(document.getElementById('danhHieuModal'))
    modal.show()
}

const showEditModal = (danhhieu) => {
    isEditing.value = true
    danhHieuForm.value = { ...danhhieu }
    const modal = new bootstrap.Modal(document.getElementById('danhHieuModal'))
    modal.show()
}

const saveDanhHieu = async () => {
    try {
        if (isEditing.value) {
            // Tạm thời xử lý locally
            const index = danhSachDanhHieu.value.findIndex(d => d.id === danhHieuForm.value.id)
            if (index !== -1) {
                danhSachDanhHieu.value[index] = { ...danhHieuForm.value }
            }
            // await axios.put(`/api/danhhieu/${danhHieuForm.value.id}`, danhHieuForm.value)
        } else {
            // Tạm thời xử lý locally
            const newId = Math.max(...danhSachDanhHieu.value.map(d => d.id)) + 1
            danhSachDanhHieu.value.push({ ...danhHieuForm.value, id: newId })
            // await axios.post('/api/danhhieu', danhHieuForm.value)
        }
        const modal = bootstrap.Modal.getInstance(document.getElementById('danhHieuModal'))
        modal.hide()
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: isEditing.value ? 'Cập nhật danh hiệu thành công' : 'Thêm danh hiệu thành công'
        })
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
        text: `Bạn có chắc muốn xóa danh hiệu "${danhhieu.ten}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                // Tạm thời xử lý locally
                danhSachDanhHieu.value = danhSachDanhHieu.value.filter(d => d.id !== danhhieu.id)
                // await axios.delete(`/api/danhhieu/${danhhieu.id}`)
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: 'Xóa danh hiệu thành công'
                })
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