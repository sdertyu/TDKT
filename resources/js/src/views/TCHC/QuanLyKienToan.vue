<template>
    <div class="m-4">
        <!-- Card for kiện toàn management -->
        <div class="card my-4 shadow-sm">
            <div class="card-header">
                <h1 class="card-title">Quản lý kiện toàn</h1>
            </div>
            <div class="card-body">
                <!-- Bảng kiện toàn -->
                <div class="table-responsive">
                    <DataTable v-model:filters="filters" :value="danhSachKienToan" :paginator="true" :rows="10"
                        :rowsPerPageOptions="[5, 10, 15, 20]" responsiveLayout="scroll" stripedRows
                        class="p-datatable-sm"
                        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :globalFilterFields="['PK_MaKienToan', 'File đính kèm']">
                        <template #header>
                            <div class="d-flex justify-content-between align-items-center">
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Tìm kiếm" />
                                </IconField>
                                <button type="button" class="btn btn-primary" @click="showAddModal"
                                    data-bs-target="#kienToanModal" data-bs-toggle="modal">
                                    <i class="fas fa-plus"></i> Thêm kiện toàn mới
                                </button>
                            </div>
                        </template>
                        <Column header="STT" bodyStyle="text-align: center"
                            :pt="{ columnHeaderContent: 'justify-content-center' }">
                            <template #body="slotProps">
                                {{ slotProps.index + 1 }}
                            </template>
                        </Column>
                        <Column field="PK_MaKienToan" header="Mã kiện toàn" sortable />
                        <!-- <Column field="sTenKienToan" header="Tên kiện toàn" sortable /> -->
                        <Column field="dNgayTao" header="Ngày kiện toàn">
                            <template #body="slotProps">
                                {{ formatDate(slotProps.data.dNgayTao) }}
                            </template>
                        </Column>
                        <Column header="File đính kèm" field="sTenFile" />
                        <Column header="Trạng thái" bodyStyle="text-align: center"
                            :pt="{ columnHeaderContent: 'justify-content-center' }">
                            <template #body="slotProps">
                                <span
                                    :class="slotProps.data.bTrangThai == 0 ? 'badge bg-secondary' : 'badge bg-success'">
                                    {{ slotProps.data.bTrangThai == 0 ? 'Tạm ngưng' : 'Hoạt động' }}
                                </span>
                            </template>
                        </Column>
                        <Column header="Thao tác" class="text-center"
                            :pt="{ columnHeaderContent: 'justify-content-center' }">
                            <template #body="slotProps">
                                <!-- <button class="btn btn-info btn-sm me-2" @click="showMemberList(slotProps.data)"
                                    title="Xem danh sách thành viên">
                                    <i class="fas fa-users"></i>
                                </button> -->
                                <button class="btn btn-warning btn-sm me-2" @click="showEditModal(slotProps.data)"
                                    data-bs-toggle="modal" data-bs-target="#kienToanModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-secondary btn-sm me-2"
                                    :class="slotProps.data.bTrangThai == 0 ? 'bg-blend-color' : 'bg-success'"
                                    @click="changeStatus(slotProps.data)">
                                    <i :class="slotProps.data.bTrangThai == 0 ? 'fas fa-lock-open' : 'fas fa-lock'"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" @click="confirmDelete(slotProps.data)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Modal thêm/sửa kiện toàn -->
        <div class="modal fade" id="kienToanModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Cập nhật kiện toàn' : 'Thêm kiện toàn mới' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveKienToan">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Mã kiện toàn <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="currentKienToan.maKienToan"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ngày kiện toàn <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" v-model="currentKienToan.ngayTao" required>
                                </div>
                            </div>

                            <div class="row mb-3">

                                <div class="col-md-12">
                                    <label class="form-label">File đính kèm</label>
                                    <input type="file" class="form-control" @change="handleFileUpload" ref="fileInput">
                                    <div class="mt-2" v-if="currentKienToan.tenFile">
                                        <p class="mb-0"><small><i class="fas fa-file-alt me-1"></i> File hiện tại: {{
                                            currentKienToan.tenFile }}</small></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Thành viên</label>
                                <multiselect v-model="currentKienToan.thanhVien" :options="listCanBo" label="sTenCaNhan"
                                    :multiple="true" track-by="PK_MaCaNhan"></multiselect>

                                <div v-if="currentKienToan.thanhVien.length" class="mt-2">
                                    <div class="input-group" v-for="(member, index) in currentKienToan.thanhVien"
                                        :key="index">
                                        <input type="text" class="form-control mb-2" :value="member.sTenCaNhan" readonly
                                            disabled>
                                        <div class="mx-2"></div>
                                        <select name="" id="" v-model="member['nhiemVu']" class="form-select mb-2">
                                            <option value="" disabled selected>-- Hãy chọn nhiệm vụ --</option>
                                            <option v-for="nhiemVu in listNhiemVu" :value="nhiemVu.PK_MaNhiemVu">{{
                                                nhiemVu.sTenNhiemVu }}</option>
                                        </select>

                                    </div>
                                </div>

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

        <!-- Modal xem danh sách thành viên -->
        <div class="modal fade" id="memberListModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Danh sách thành viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">STT</th>
                                        <th>Họ và tên</th>
                                        <th>Nhiệm vụ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(member, index) in selectedKienToanMembers" :key="index">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td>{{ member.sTenCanBo }}</td>
                                        <td>{{ member.sNhiemVu }}</td>
                                    </tr>
                                    <tr v-if="!selectedKienToanMembers.length">
                                        <td colspan="3" class="text-center">Không có thành viên nào</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { Modal } from 'bootstrap'
import Swal from 'sweetalert2'
import { toastSuccess, toastError } from '@/utils/toast'

import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import { FilterMatchMode } from '@primevue/core/api'
import Multiselect from 'vue-multiselect';

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const danhSachKienToan = ref([])
const listCanBo = ref([])
const isEditing = ref(false)
const fileInput = ref(null)
const selectedFile = ref(null)
const selectedKienToanMembers = ref([])
const memberListModal = ref(null)
const listThanhVien = ref([])
const listNhiemVu = ref([])

// Object for current kien toan being added/edited
const currentKienToan = reactive({
    id: null,
    maKienToan: '',
    ngayTao: '',
    tenFile: '',
    trangThai: 0,
    thanhVien: [],
    file: null,
})

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
}

const getFileNameFromPath = (path) => {
    if (!path) return '';
    return path.split('/').pop();
}

// Load kiện toàn data
const loadKienToan = async () => {
    try {
        const response = await axios.get('/api/kientoan/getlist', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        })

        if (response.status === 200) {
            danhSachKienToan.value = Array.isArray(response.data.data)
                ? response.data.data
                : Object.values(response.data.data);
            console.log('Danh sách kiện toàn:', danhSachKienToan.value);
        }
    } catch (error) {
        console.error('Lỗi khi tải danh sách kiện toàn:', error)
        toastError('Không thể tải danh sách kiện toàn')
    }
}

// Load cán bộ
const loadCaNhan = async () => {
    try {
        const response = await axios.get('/api/taikhoan/getlistcanhan', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        })

        if (response.status === 200) {
            listCanBo.value = Array.isArray(response.data.data)
                ? response.data.data
                : Object.values(response.data.data);
        }
    } catch (error) {
        console.error('Lỗi khi tải danh sách cán bộ:', error)
        toastError('Không thể tải danh sách cán bộ')
    }
}

// Show add modal
const showAddModal = () => {
    isEditing.value = false
    currentKienToan.id = null
    currentKienToan.maKienToan = ''
    // currentKienToan.tenkientoan = ''
    currentKienToan.ngayTao = new Date().toISOString().slice(0, 10)
    currentKienToan.tenFile = ''
    currentKienToan.file = null
    currentKienToan.thanhVien = []

    // Add first member row
    // addMemberRow()

    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

// Show edit modal
const showEditModal = async (kientoan) => {
    isEditing.value = true
    // currentKienToan.id = kientoan.PK_MaKienToan
    currentKienToan.maKienToan = kientoan.PK_MaKienToan
    currentKienToan.ngayTao = kientoan.dNgayTao
    currentKienToan.tenFile = kientoan.sTenFile
    currentKienToan.thanhVien = kientoan.thanh_vien_hoi_dong.map(member => ({
        sTenCaNhan: member.tai_khoan.ca_nhan.sTenCaNhan,
        PK_MaCaNhan: member.tai_khoan.ca_nhan.PK_MaCaNhan,
        nhiemVu: member.FK_MaNhiemVu,
        FK_MaTaiKhoan: member.FK_TaiKhoan,
    }))


    // Fetch members
    // try {
    //     const response = await axios.get(`/api/kientoan/${kientoan.PK_MaKienToan}/members`, {
    //         headers: {
    //             Authorization: `Bearer ${localStorage.getItem('api_token')}`
    //         }
    //     })

    //     if (response.status === 200 && response.data.data) {
    //         currentKienToan.thanhvien = response.data.data.map(member => ({
    //             id: member.PK_MaThanhVien,
    //             canhan: member.FK_MaCanBo,
    //             nhiemvu: member.sNhiemVu
    //         }))
    //     }

    //     if (currentKienToan.thanhvien.length === 0) {
    //         // addMemberRow()
    //     }
    // } catch (error) {
    //     console.error('Lỗi khi tải danh sách thành viên:', error)
    //     toastError('Không thể tải danh sách thành viên')
    //     currentKienToan.thanhvien = [{ canhan: '', nhiemvu: '' }]
    // }

    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

// Handle file upload
const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        currentKienToan.file = file
    }
}


// Add member row
// const addMemberRow = () => {
//     currentKienToan.thanhvien.push({
//         id: null,
//         canhan: '',
//         nhiemvu: ''
//     })
// }

// Remove member row
// const removeMemberRow = (index) => {
//     currentKienToan.thanhvien.splice(index, 1)

//     // Make sure there's at least one row
//     if (currentKienToan.thanhvien.length === 0) {
//         addMemberRow()
//     }
// }

// Save kiện toàn
const saveKienToan = async () => {
    try {
        // Validate form
        if (!validateForm()) {
            return
        }

        // Prepare form data for multipart/form-data (file upload)
        const formData = new FormData()
        formData.append('maKienToan', currentKienToan.maKienToan)
        formData.append('ngayTao', currentKienToan.ngayTao)

        // Add file if available
        if (currentKienToan.file) {
            formData.append('file', currentKienToan.file)
        }

        // Add members data as JSON
        formData.append('thanhVien', JSON.stringify(currentKienToan.thanhVien))

        
        let response
        
        if (isEditing.value) {
            formData.forEach((value, key) => {
                console.log(key, value);
            });
            response = await axios.post('/api/kientoan/update', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': `Bearer ${localStorage.getItem('api_token')}`
                }
            })
        } else {
            response = await axios.post('/api/kientoan/create', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': `Bearer ${localStorage.getItem('api_token')}`
                }
            })
        }

        document.getElementById("kienToanModal").querySelector(".btn-close").click()
        if (response.status === 200) {
            toastSuccess(isEditing.value ? 'Cập nhật kiện toàn thành công' : 'Thêm kiện toàn thành công')
            await loadKienToan() // Reload the list
        }
    } catch (error) {
        console.log();
        if (error.response) {
            if (error.response.status === 422) {
                const errors = error.response.data.error
                let errorMessage = Object.values(errors).flat().join('<br>')
                toastError(errorMessage)
            } else {
                toastError(error.response.data.message || 'Có lỗi xảy ra khi lưu kiện toàn')
            }
        } else {
            console.log(error.message);
            toastError('Có lỗi xảy ra khi lưu kiện toàn')
        }
    }
}

// Validate form before submission
const validateForm = () => {
    // Check if any member entries are incomplete
    const invalidMembers = currentKienToan.thanhVien.filter(
        member => !member.sTenCaNhan || !member.nhiemVu
    )

    if (invalidMembers.length > 0) {
        toastError('Vui lòng điền đầy đủ thông tin thành viên và nhiệm vụ')
        return false
    }

    // Check for duplicate members
    const memberIds = currentKienToan.thanhVien.map(m => m.PK_MaCaNhan)
    const hasDuplicates = memberIds.some((id, index) =>
        memberIds.indexOf(id) !== index
    )

    if (hasDuplicates) {
        toastError('Không thể chọn cùng một thành viên nhiều lần')
        return false
    }

    return true
}

// Show member list modal
const showMemberList = async (kientoan) => {
    try {
        const response = await axios.get(`/api/kientoan/${kientoan.PK_MaKienToan}/members`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        })

        if (response.status === 200) {
            selectedKienToanMembers.value = response.data.data || []

            // Initialize modal if needed
            if (!memberListModal.value) {
                memberListModal.value = new Modal(document.getElementById('memberListModal'))
            }

            // Show modal
            memberListModal.value.show()
        }
    } catch (error) {
        console.error('Lỗi khi tải danh sách thành viên:', error)
        toastError('Không thể tải danh sách thành viên')
    }
}

// Change status
const changeStatus = (kientoan) => {
    Swal.fire({
        title: 'Xác nhận thay đổi trạng thái?',
        text: `Bạn có chắc muốn ${kientoan.bTrangThai === 0 ? 'kích hoạt' : 'tạm ngưng'} kiện toàn "${kientoan.PK_MaKienToan}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: kientoan.bTrangThai === 0 ? 'Kích hoạt' : 'Tạm ngưng',
        cancelButtonText: 'Hủy'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.put(`/api/kientoan/updatestatus/${kientoan.PK_MaKienToan}`, null, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('api_token')}`
                    }
                })

                if (response.status === 200) {
                    kientoan.bTrangThai = kientoan.bTrangThai === 0 ? 1 : 0;
                    if (kientoan.bTrangThai === 1) {
                        danhSachKienToan.value.forEach(item => {
                            if (item.PK_MaKienToan !== kientoan.PK_MaKienToan) {
                                item.bTrangThai = 0
                            }
                        })
                    }

                    toastSuccess('Cập nhật trạng thái thành công')
                }
            } catch (error) {
                console.error('Lỗi khi cập nhật trạng thái:', error)
                if (error.response) {
                    toastError(error.response.data.message || 'Có lỗi xảy ra khi cập nhật trạng thái')
                } else {
                    toastError('Có lỗi xảy ra khi cập nhật trạng thái')
                }
            }
        }
    })

}

// Delete kiện toàn
const confirmDelete = (kientoan) => {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: `Bạn có chắc muốn xóa kiện toàn "${kientoan.sTenKienToan}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`/api/kientoan/delete/${kientoan.PK_MaKienToan}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('api_token')}`
                    }
                })

                if (response.status === 200) {
                    danhSachKienToan.value = danhSachKienToan.value.filter(
                        d => d.PK_MaKienToan !== kientoan.PK_MaKienToan
                    )
                    toastSuccess('Xóa kiện toàn thành công')
                }
            } catch (error) {
                console.error('Lỗi khi xóa kiện toàn:', error)
                if (error.response) {
                    toastError(error.response.data.message || 'Có lỗi xảy ra khi xóa kiện toàn')
                } else {
                    toastError('Có lỗi xảy ra khi xóa kiện toàn')
                }
            }
        }
    })
}

const loadNhiemVu = async () => {
    try {
        const response = await axios.get('/api/kientoan/getlistnhiemvu', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        })

        console.log(response.status);

        if (response.status === 200) {
            listNhiemVu.value = Array.isArray(response.data.data)
                ? response.data.data
                : Object.values(response.data.data);
        }
    } catch (error) {
        console.error('Lỗi khi tải danh sách nhiệm vụ:', error)
        toastError('Không thể tải danh sách nhiệm vụ')
    }
}

onMounted(() => {
    loadKienToan()
    loadCaNhan()
    loadNhiemVu()
})
</script>
