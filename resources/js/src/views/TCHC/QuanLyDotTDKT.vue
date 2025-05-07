<template>
    <div class="card m-4">
        <div class="card-header">
            <h3 class="card-title">Danh sách đợt thi đua khen thưởng</h3>
        </div>

        <div class="card-body">
            <!-- Replace regular table with PrimeVue DataTable -->
            <DataTable v-model:filters="filters" :value="listDot" :paginator="true" :rows="itemsPerPage"
                :rowsPerPageOptions="pageSizes" responsiveLayout="scroll" stripedRows class="p-datatable-sm"
                :globalFilterFields="['iNamBatDau', 'iNamKetThuc', 'bTrangThai']">
                <template #header>
                    <div class="d-flex justify-content-between align-items-center">
                        <IconField>
                            <InputIcon>
                                <i class="bi bi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Tìm kiếm" />
                        </IconField>
                        <button type="button" class="btn btn-primary" @click="openAddModal" data-bs-toggle="modal"
                            data-bs-target="#dotModal">
                            <i class="fas fa-plus"></i> Thêm đợt
                        </button>
                    </div>
                </template>
                <Column header="STT" bodyStyle="text-align: center"
                    :pt="{ columnHeaderContent: 'justify-content-center' }">
                    <template #body="slotProps">
                        {{ slotProps.index + 1 }}
                    </template>
                </Column>
                <Column field="iNamBatDau" header="Năm bắt đầu" sortable bodyStyle="text-align: center"
                    :pt="{ columnHeaderContent: 'justify-content-center' }" />
                <Column field="iNamKetThuc" header="Năm kết thúc" sortable bodyStyle="text-align: center"
                    :pt="{ columnHeaderContent: 'justify-content-center' }" />
                <Column header="Trạng thái" bodyStyle="text-align: center"
                    :pt="{ columnHeaderContent: 'justify-content-center' }">
                    <template #body="slotProps">
                        <span :class="slotProps.data.bTrangThai == 1 ? 'badge bg-success' : 'badge bg-secondary'">
                            {{ slotProps.data.bTrangThai == 1 ? 'Hoạt động' : 'Tạm ngưng' }}
                        </span>
                    </template>
                </Column>
                <Column field="dNgayTao" header="Ngày tạo" bodyStyle="text-align: center"
                    :pt="{ columnHeaderContent: 'justify-content-center' }" />
                <Column header="Thao tác" bodyStyle="text-align: center"
                    :pt="{ columnHeaderContent: 'justify-content-center' }">
                    <template #body="slotProps">
                        <router-link :to="`/dotdotxuat/${slotProps.data.PK_MaDot}`" class="btn btn-primary btn-sm me-2">
                            <i class="fa-solid fa-bolt"></i>
                        </router-link>
                        <button class="btn btn-warning btn-sm me-2" @click="showEditModal(slotProps.data)"
                            data-bs-toggle="modal" data-bs-target="#dotModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        <router-link :to="`/quanlyvanban/${slotProps.data.PK_MaDot}`"
                            class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-file"></i>
                        </router-link>
                        <button class="btn btn-secondary btn-sm me-2"
                            :class="slotProps.data.bTrangThai == 0 ? 'bg-blend-color' : 'bg-success'"
                            @click="trangThaiDot(slotProps.data)">
                            <i :class="slotProps.data.bTrangThai == 0 ? 'fas fa-lock-open' : 'fas fa-lock'"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" @click="confirmDelete(slotProps.data)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </template>
                </Column>
            </DataTable>
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
                                <label for="namBatDau" class="form-label text">Năm học</label>
                                <div class="input-group">
                                    <input type="number" min="1993" :max="new Date().getFullYear()" class="form-control"
                                        id="namBatDau" required v-model="currentDot.iNamBatDau" />
                                    <span class="input-group-text"> - </span>
                                    <input type="number" class="form-control" :value="namKetThuc" disabled>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="namBatDau" class="form-label text">Hạn nộp biên bản cấp đơn vị</label>
                                <input type="date" class="form-control" v-model="currentDot.dHanBienBanDonVi">
                            </div>
                            <div class="mb-3">
                                <label for="namBatDau" class="form-label text">Hạn nộp minh chứng</label>
                                <input type="date" class="form-control" v-model="currentDot.dHanNopMinhChung">
                            </div>
                            <div class="mb-3">
                                <label for="namBatDau" class="form-label text">Hạn nộp biển bản phê duyệt cấp hội
                                    đồng</label>
                                <input type="date" class="form-control" v-model="currentDot.dHanBienBanHoiDong">
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
import { computed, onMounted, reactive, ref, warn, watch } from 'vue';
import Swal from 'sweetalert2';

// Import PrimeVue components
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { FilterMatchMode } from '@primevue/core/api';

// Filter configuration for PrimeVue DataTable
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const currentDot = reactive({
    iNamBatDau: '',
    iNamKetThuc: '',
    bTrangThai: 0,
    dNgayTao: '',
    PK_MaDot: '',
    dHanBienBanDonVi: '',
    dHanNopMinhChung: '',
    dHanBienBanHoiDong: ''
});

// ============ Xử lý đợt ============
const listDot = ref([]);

const isEditing = ref(false);
const modalInstance = ref(null);

onMounted(() => {
    loadDotList();
    modalInstance.value = document.getElementById('dotModal');
});

const namKetThuc = computed(() => {
    currentDot.iNamKetThuc = currentDot.iNamBatDau + 1;
    return currentDot.iNamBatDau + 1;
})

// Pagination state
const itemsPerPage = ref(10);
const pageSizes = ref([5, 10, 20, 50, 100]);

// Make sure currentPage is reset when data is reloaded
const loadDotList = () => {
    axios.get('/api/dotthiduakhenthuong/list', {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
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
    currentDot.iNamKetThuc = ''
    currentDot.dHanBienBanDonVi = ''
    currentDot.dHanNopMinhChung = ''
    currentDot.dHanBienBanHoiDong = ''
};

const showEditModal = (dot) => {
    isEditing.value = true;
    currentDot.id = dot.id;
    currentDot.iNamBatDau = dot.iNamBatDau;
    currentDot.iNamKetThuc = dot.iNamKetThuc;
    currentDot.bTrangThai = dot.bTrangThai;
    currentDot.dNgayTao = dot.dNgayTao;
    currentDot.PK_MaDot = dot.PK_MaDot
    currentDot.dHanBienBanDonVi = dot.dHanBienBanDonVi;
    currentDot.dHanNopMinhChung = dot.dHanNopMinhChung;
    currentDot.dHanBienBanHoiDong = dot.dHanBienBanHoiDong;
};

const validateDate = () => {
    // Convert academic years to Date objects for comparison
    const startYearDate = new Date(currentDot.iNamBatDau, 9, 1); // September 1st of start year
    const endYearDate = new Date(currentDot.iNamKetThuc, 7, 31); // July 31st of end year
    
    if (currentDot.dHanBienBanDonVi) {
        const hanBienBanDonViDate = new Date(currentDot.dHanBienBanDonVi);
        if (hanBienBanDonViDate < startYearDate) {
            toastError('Hạn nộp biên bản cấp đơn vị không được trước thời gian bắt đầu năm học');
            return false;
        }
        if (hanBienBanDonViDate > endYearDate) {
            toastError('Hạn nộp biên bản cấp đơn vị không được sau thời gian kết thúc năm học');
            return false;
        }
    }
    
    if (currentDot.dHanNopMinhChung) {
        const hanNopMinhChungDate = new Date(currentDot.dHanNopMinhChung);
        if (hanNopMinhChungDate < startYearDate) {
            toastError('Hạn nộp minh chứng không được trước thời gian bắt đầu năm học');
            return false;
        }
        if (hanNopMinhChungDate > endYearDate) {
            toastError('Hạn nộp minh chứng không được sau thời gian kết thúc năm học');
            return false;
        }
    }

    if (currentDot.dHanBienBanHoiDong) {
        const hanBienBanHoiDongDate = new Date(currentDot.dHanBienBanHoiDong);
        if (hanBienBanHoiDongDate < startYearDate) {
            toastError('Hạn nộp biên bản hội đồng không được trước thời gian bắt đầu năm học');
            return false;
        }
        if (hanBienBanHoiDongDate > endYearDate) {
            toastError('Hạn nộp biên bản hội đồng không được sau thời gian kết thúc năm học');
            return false;
        }
    }
    
    // Additional check: ensure logical sequence of deadlines
    if (currentDot.dHanBienBanDonVi && currentDot.dHanNopMinhChung) {
        if (new Date(currentDot.dHanNopMinhChung) < new Date(currentDot.dHanBienBanDonVi)) {
            toastError('Hạn nộp minh chứng không được trước hạn nộp biên bản cấp đơn vị');
            return false;
        }
    }

    if (currentDot.dHanNopMinhChung && currentDot.dHanBienBanHoiDong) {
        if (new Date(currentDot.dHanBienBanHoiDong) < new Date(currentDot.dHanNopMinhChung)) {
            toastError('Hạn nộp biên bản hội đồng không được trước hạn nộp minh chứng');
            return false;
        }
    }
    
    return true;
}

const saveDot = () => {
    // Validate the date fields
    if (!validateDate()) {
        return; // Stop execution if validation fails
    }
    if (isEditing.value) {
        const update = axios.put(`/api/dotthiduakhenthuong/update`, currentDot,
            {
                headers: {
                    Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
                }
            });

        update.then(response => {
            if (response.status === 200) {
                toastSuccess('Cập nhật đợt thành công');
                const data = response.data.data;
                const targetDot = listDot.value.find(dottd => dottd.PK_MaDot === data.PK_MaDot);
                if (targetDot) {
                    targetDot.dHanBienBanDonVi = data.dHanBienBanDonVi;
                    targetDot.dHanNopMinhChung = data.dHanNopMinhChung;
                    targetDot.dHanBienBanHoiDong = data.dHanBienBanHoiDong;
                }
            }
        }).catch(error => {
            console.log(error);

            // Nếu có lỗi validation từ Laravel
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.error
                const errorMessages = Object.values(errors).flat().join('<br>')

                toastError(errorMessages)
            } else {
                toastError('Không thể cập nhật đợt!');
            }
        })
    } else {
        // Thêm đợt mới
        const add = axios.post('/api/dotthiduakhenthuong/add', currentDot,
            {
                headers: {
                    Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
                }
            })

        add.then(response => {
            if (response.status === 200) {
                listDot.value.push(response.data.data);
                toastSuccess('Thêm đợt thành công');
            }
        }).catch(error => {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.error
                const errorMessages = Object.values(errors).flat().join('<br>')

                toastError(errorMessages)
            } else {
                // Các lỗi khác (server, mạng, v.v.)
                toastError('Không thể thêm đợt!');
            }
        })
    }
    document.getElementById("dotModal").querySelector(".btn-close").click();
};

const trangThaiDot = (item) => {
    let trangThai = item.bTrangThai == 0 ? "mở khóa" : "khóa";
    let data = { ...item };
    data.bTrangThai = item.bTrangThai == 0 ? 1 : 0;
    Swal.fire({
        icon: 'warning',
        title: 'Bạn có chắc muốn thực hiện ' + trangThai + " đợt " + item.PK_MaDot,
        confirmButtonText: item.bTrangThai == 0 ? "Mở khóa" : "Khóa",
        showCancelButton: true,
        cancelButtonText: "Hủy",
    }).then((result) => {
        if (result.isConfirmed) {
            const thayDoiTrangThai = axios.put(`/api/dotthiduakhenthuong/update-trang-thai`, data, {
                headers: {
                    Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
                }
            })

            thayDoiTrangThai.then(response => {
                if (response.status === 200) {
                    item.bTrangThai = data.bTrangThai;
                    listDot.value.forEach(dot => {
                        if (dot.PK_MaDot !== item.PK_MaDot) {
                            dot.bTrangThai = 0
                        }
                    })
                    toastSuccess('Thay đổi trạng thái đợt thành công');
                }
            }).catch(error => {
                console.log(error);
                toastError('Không thể thay đổi trạng thái đợt!');
            })
        }
    })
}

const confirmDelete = (dot) => {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: `Bạn có chắc muốn xóa đợt từ năm ${dot.iNamBatDau} đến năm ${dot.iNamKetThuc}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            const response = axios.delete(`/api/dotthiduakhenthuong/delete/${dot.PK_MaDot}`, {
                headers: {
                    Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
                }
            })

            response.then(res => {
                if (res.status === 200) {
                    listDot.value = listDot.value.filter(item => item.PK_MaDot !== dot.PK_MaDot);
                    toastSuccess('Xóa đợt thành công');
                }
            }).catch(error => {
                toastError('Không thể xóa đợt!');
                if (error.response) {
                    if (error.response.status === 422) {
                        const errors = error.response.data.error
                        const errorMessages = Object.values(errors).flat().join('<br>')

                        toastError(errorMessages)
                    } else {
                        toastError(error.response.data.message)
                    }
                } else {
                    toastError('Không thể xóa đợt!');
                }
            });
        }
    });
};

// Add toast functions if they don't exist already
const toastSuccess = (message) => {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: 'success',
        text: message,
        timerProgressBar: true,
    });
};

const toastError = (message) => {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: 'error',
        text: message,
        timerProgressBar: true,
    });
};
</script>
