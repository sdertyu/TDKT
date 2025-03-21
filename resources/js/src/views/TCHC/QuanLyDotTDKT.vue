<template>
    <div class="card my-3">
        <div class="card-header">
            <h3 class="card-title">Danh sách đợt thi đua khen thưởng</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary" @click="openAddModal" data-bs-toggle="modal"
                    data-bs-target="#dotModal">
                    <i class="fas fa-plus"></i> Thêm đợt
                </button>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th class="text-center">Năm bắt đầu</th>
                        <th class="text-center">Năm kết thúc</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in listDot" :key="index">
                        <td class="text-center">{{ index + 1 }}</td>
                        <td class="text-center">{{ item.iNamBatDau }}</td>
                        <td class="text-center">{{ item.iNamKetThuc }}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-2" @click="showEditModal(item)" data-bs-toggle="modal" data-bs-target="#dotModal">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <button class="btn btn-danger btn-sm" @click="confirmDelete(item)">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="dotModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dotModalLabel">{{ isEditing ? 'Cập nhật đợt' : 'Thêm đợt' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveDot">
                            <div class="mb-3">
                                <label for="namBatDau" class="form-label text">Năm bắt đầu</label>
                                <input type="text" class="form-control" id="namBatDau" required
                                    v-model="currentDot.iNamBatDau" />
                            </div>
                            <div class="mb-3">
                                <label for="namKetThuc" class="form-label text">Năm kết thúc</label>
                                <input type="text" class="form-control" id="namKetThuc" required
                                    v-model="currentDot.iNamKetThuc" />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Hủy
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ isEditing ? 'Cập nhật' : 'Thêm' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';
import Swal from 'sweetalert2';

const listDot = ref([]);
const currentDot = reactive({
    id: null,
    iNamBatDau: '',
    iNamKetThuc: ''
});
const isEditing = ref(false);
const modalInstance = ref(null);

onMounted(() => {
    loadDotList();
    modalInstance.value = document.getElementById('dotModal');
});

const loadDotList = () => {
    axios.get('/api/dotthiduakhenthuong/list', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    }).then(response => {
        if (response.status == 200) {
            listDot.value = response.data.data;
        }
    }).catch(error => {
        console.log(error);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không thể tải danh sách đợt thi đua khen thưởng'
        });
    });
};

const openAddModal = () => {
    isEditing.value = false;
    currentDot.id = null;
    currentDot.iNamBatDau = '';
    currentDot.iNamKetThuc = '';
};

const showEditModal = (dot) => {
    isEditing.value = true;
    currentDot.id = dot.id;
    currentDot.iNamBatDau = dot.iNamBatDau;
    currentDot.iNamKetThuc = dot.iNamKetThuc;
    
    // Mở modal
    const modal = new bootstrap.Modal(document.getElementById('dotModal'));
    modal.show();
};

const saveDot = () => {
    try {
        if (isEditing.value) {
            // Cập nhật đợt
            // Tạm thời xử lý locally
            const index = listDot.value.findIndex(d => d.id === currentDot.id);
            if (index !== -1) {
                listDot.value[index] = { ...currentDot };
            }
            // await axios.put(`/api/dotthiduakhenthuong/${currentDot.id}`, currentDot, {
            //     headers: {
            //         Authorization: `Bearer ${localStorage.getItem('api_token')}`
            //     }
            // });
        } else {
            // Thêm đợt mới
            // Tạm thời xử lý locally
            const newId = listDot.value.length > 0 ? 
                Math.max(...listDot.value.map(d => d.id)) + 1 : 1;
            listDot.value.push({ ...currentDot, id: newId });
            // await axios.post('/api/dotthiduakhenthuong/add', currentDot, {
            //     headers: {
            //         Authorization: `Bearer ${localStorage.getItem('api_token')}`
            //     }
            // });
        }
        
        // Đóng modal
        const modalEl = document.getElementById('dotModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
        
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: isEditing.value ? 'Cập nhật đợt thành công' : 'Thêm đợt thành công'
        });
    } catch (error) {
        console.error('Lỗi khi lưu đợt:', error);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không thể lưu đợt'
        });
    }
};

const confirmDelete = (dot) => {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: `Bạn có chắc muốn xóa đợt từ năm ${dot.iNamBatDau} đến năm ${dot.iNamKetThuc}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                // Tạm thời xử lý locally
                listDot.value = listDot.value.filter(d => d.id !== dot.id);
                // await axios.delete(`/api/dotthiduakhenthuong/${dot.id}`, {
                //     headers: {
                //         Authorization: `Bearer ${localStorage.getItem('api_token')}`
                //     }
                // });
                
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: 'Xóa đợt thành công'
                });
            } catch (error) {
                console.error('Lỗi khi xóa đợt:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể xóa đợt'
                });
            }
        }
    });
};
</script>