<template>
    <div class="card m-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Danh sách thông báo</h3>
            <button class="btn btn-primary" @click="openAddModal">
                <i class="fas fa-plus"></i> Thêm thông báo
            </button>
        </div>
        <div class="card-body">
            <div class="" v-for="(item, index) in listThongBao" :key="index">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card" style="max-width: 100%;">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>{{ item.sTieuDe }}</strong>
                                <div>
                                    <button class="btn btn-warning btn-sm me-2" @click="openEditModal(item)">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button class="btn btn-danger btn-sm" @click="confirmDelete(item)">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                            <div class="card-body text-primary">
                                <p class="card-text">{{ item.sNoiDung }}</p>
                                <p class="card-text"><small class="text-muted">{{ item.dNgayTao }}</small></p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr v-if="index !== listThongBao.length - 1" class="my-4" style="border-top: 1px solid #ccc;">
            </div>

            <!-- Modal thêm/sửa thông báo -->
            <div class="modal fade" id="thongBaoModal" tabindex="-1" aria-labelledby="thongBaoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="thongBaoModalLabel">
                                {{ isEditing ? 'Sửa thông báo' : 'Thêm thông báo mới' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="mb-3">
                                    <label for="sTieuDe" class="form-label">Tiêu đề</label>
                                    <input type="text" class="form-control" id="sTieuDe" v-model="formData.sTieuDe">
                                </div>
                                <div class="mb-3">
                                    <label for="sNoiDung" class="form-label">Nội dung</label>
                                    <textarea class="form-control" id="sNoiDung" rows="5"
                                        v-model="formData.sNoiDung"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="dNgayTao" class="form-label">Dành cho</label>
                                    <multiselect v-model="formData.quyen" :options="quyenOptions" :multiple="true"
                                        track-by="value" label="text" placeholder="Chọn đối tượng"></multiselect>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="button" class="btn btn-primary" @click="saveThongBao">Lưu</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal xác nhận xóa -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Bạn có chắc chắn muốn xóa thông báo "<strong>{{ selectedItem.sTieuDe }}</strong>" không?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-danger" @click="deleteThongBao">Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { toastSuccess, toastError } from '@/utils/toast.js';
import { Modal } from 'bootstrap';
import Multiselect from 'vue-multiselect';

const listThongBao = ref([]);
const isEditing = ref(false);
const selectedItem = ref({});
const quyenOptions = ref([
    { value: 3, text: 'Hội đồng thi đua khen thưởng' },
    { value: 4, text: 'Đơn vị' },
    { value: 5, text: 'Cá nhân' },
]);
const formData = ref({
    id: null,
    sTieuDe: '',
    sNoiDung: '',
    quyen: []
});

let thongBaoModal = null;
let deleteConfirmModal = null;

onMounted(() => {
    getListThongBao();
    thongBaoModal = new Modal(document.getElementById('thongBaoModal'));
    deleteConfirmModal = new Modal(document.getElementById('deleteModal'));
});

const getListThongBao = () => {
    axios.get('/api/thongbao/getlisttheoquyen', {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
        }
    })
        .then(response => {
            if (response.status === 200) {
                console.log(response.data.data);
                listThongBao.value = response.data.data.map(item => {
                    return {
                        id: item.PK_MaThongBao,
                        sTieuDe: item.sTieuDe,
                        sNoiDung: item.sNoiDung,
                        quyen: item.maQuyen,
                    };
                });
            }
        })
        .catch(error => {
            toastError("Có lỗi xảy ra khi lấy danh sách thông báo");
        });
};

const openAddModal = () => {
    isEditing.value = false;
    formData.value = {
        id: null,
        sTieuDe: '',
        sNoiDung: ''
    };
    thongBaoModal.show();
};

const openEditModal = (item) => {
    isEditing.value = true;
    formData.value = {
        id: item.id,
        sTieuDe: item.sTieuDe,
        sNoiDung: item.sNoiDung,
        quyen: item.quyen.map(q => {
            return {
                value: q,
                text: quyenOptions.value.find(option => option.value === q).text
            };
        })
    };
    thongBaoModal.show();
};

const saveThongBao = () => {
    if (!formData.value.sTieuDe.trim()) {
        toastError("Vui lòng nhập tiêu đề");
        return;
    }

    if (!formData.value.sNoiDung.trim()) {
        toastError("Vui lòng nhập nội dung");
        return;
    }

    const url = isEditing.value
        ? `/api/thongbao/update/${formData.value.id}`
        : '/api/thongbao/create';

    const method = isEditing.value ? 'put' : 'post';

    axios.post(url, {
        tieuDe: formData.value.sTieuDe,
        noiDung: formData.value.sNoiDung,
        quyen: JSON.stringify(formData.value.quyen)
    }, {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
        }
    }).then(response => {
        if (response.status === 200) {
            console.log("object");
            thongBaoModal.hide();
            getListThongBao();
            toastSuccess(isEditing.value ? "Cập nhật thông báo thành công" : "Thêm thông báo thành công");
        }
    })
        .catch(error => {
            toastError(isEditing.value ? "Cập nhật thông báo thất bại" : "Thêm thông báo thất bại");
        });
};

const confirmDelete = (item) => {
    selectedItem.value = item;
    deleteConfirmModal.show();
};

const deleteThongBao = () => {
    axios.delete(`/api/thongbao/delete/${selectedItem.value.id}`, {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
        }
    })
        .then(response => {
            if (response.status === 200) {
                deleteConfirmModal.hide();
                getListThongBao();
                toastSuccess("Xóa thông báo thành công");
            }
        })
        .catch(error => {
            toastError("Xóa thông báo thất bại");
        });
};
</script>

<style scoped>
.card-header {
    background-color: #f8f9fa;
}
</style>