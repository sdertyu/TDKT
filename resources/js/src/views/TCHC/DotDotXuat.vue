<template>
    <div class="card m-4">
        <div class="card-header">
            <h3 class="card-title">Danh sách đợt thi đua khen thưởng đột xuất</h3>
        </div>

        <div class="card-body">

            <DataTable :value="dotDotXuatList" :paginator="true" :rows="10" :rowsPerPageOptions="[5, 10, 20]"
                responsiveLayout="scroll" filterDisplay="menu" stripedRows
                :globalFilterFields="['ngayBatDau', 'ngayKetThuc', 'hanNopDeXuat', 'hanNopMinhChung', 'hanNopBienBan']"
                v-model:filters="filters" class="p-datatable-sm">
                <template #header>
                    <div class="d-flex justify-content-between">
                        <IconField>
                            <InputIcon>
                                <i class="bi bi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Tìm kiếm" />
                        </IconField>
                        <button type="button" class="btn btn-primary" @click="showAddModal" data-bs-target="#dotModal"
                            data-bs-toggle="modal">
                            <i class="fas fa-plus"></i> Thêm
                        </button>
                    </div>
                </template>

                <Column header="STT" bodyStyle="text-align: center"
                    :pt="{ columnHeaderContent: 'justify-content-center' }">
                    <template #body="slotProps">
                        {{ slotProps.index + 1 }}
                    </template>
                </Column>
                <Column header="Bắt đầu" sortable>
                    <template #body="slotProps">
                        {{ formatDateDisplay(slotProps.data.ngayBatDau) }}
                    </template>
                </Column>

                <Column header="Kết thúc" sortable>
                    <template #body="slotProps">
                        {{ formatDateDisplay(slotProps.data.ngayKetThuc) }}
                    </template>
                </Column>

                <Column header="Hạn nộp đề xuất" sortable>
                    <template #body="slotProps">
                        {{ formatDateDisplay(slotProps.data.hanNopDeXuat) }}
                    </template>
                </Column>

                <Column header="Hạn nộp minh chứng" sortable>
                    <template #body="slotProps">
                        {{ formatDateDisplay(slotProps.data.hanNopMinhChung) }}
                    </template>
                </Column>

                <Column header="Hạn nộp biên bản hội đồng" sortable>
                    <template #body="slotProps">
                        {{ formatDateDisplay(slotProps.data.hanNopBienBan) }}
                    </template>
                </Column>

                <Column header="Trạng thái" bodyStyle="text-align: center"
                    :pt="{ columnHeaderContent: 'justify-content-center' }">
                    <template #body="slotProps">
                        <span :class="getStatusBadgeClass(slotProps.data.bTrangThai)">
                            {{ slotProps.data.bTrangThai == 1 ? "Hoạt động" : "Tạm ngưng" }}
                        </span>
                    </template>
                </Column>

                <Column header="Thao tác" bodyStyle="text-align: center"
                    :pt="{ columnHeaderContent: 'justify-content-center' }">
                    <template #body="slotProps">
                        <button class="btn btn-secondary btn-sm me-2"
                            :class="slotProps.data.bTrangThai == 0 ? 'bg-blend-color' : 'bg-success'"
                            @click="toggleAccountStatus(slotProps.data)">
                            <i :class="slotProps.data.bTrangThai == 0 ? 'fas fa-lock-open' : 'fas fa-lock'"></i>
                        </button>
                        <button class="btn btn-warning btn-sm me-2" @click="showEditModal(slotProps.data)"
                            data-bs-toggle="modal" data-bs-target="#dotModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" @click="confirmDelete(slotProps.data)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>

    <!-- Modal thêm/sửa đợt đột xuất -->
    <div class="modal fade" id="dotModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ isEditing ? 'Cập nhật đợt thi đua đột xuất' : 'Thêm đợt thi đua đột xuất mới' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveDotDotXuat">
                        <div class="mb-3">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="date" class="form-control" v-model="dotDotXuat.ngayBatDau" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="date" class="form-control" v-model="dotDotXuat.ngayKetThuc" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hạn nộp đề xuất</label>
                            <input type="date" class="form-control" v-model="dotDotXuat.hanNopDeXuat" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hạn nộp minh chứng</label>
                            <input type="date" class="form-control" v-model="dotDotXuat.hanNopMinhChung" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hạn nộp biên bản hội đồng</label>
                            <input type="date" class="form-control" v-model="dotDotXuat.hanNopBienBan" required>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { FilterMatchMode } from '@primevue/core/api';
import { useRoute } from 'vue-router';
import Swal from 'sweetalert2';
import { toastSuccess, toastError } from '@/utils/toast';

const dotDotXuatList = ref([]);
const dotDotXuat = ref({
    id: null,
    ngayBatDau: '',
    ngayKetThuc: '',
    hanNopDeXuat: '',
    hanNopMinhChung: '',
    hanNopBienBan: '',
    bTrangThai: null
});
const isEditing = ref(false);
const maDot = useRoute().params.id;

const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const getStatusBadgeClass = (status) => {
    return status == 1 ? "badge bg-success" : "badge bg-secondary";
};

onMounted(() => {
    loadDotDotXuat();
});

const loadDotDotXuat = async () => {
    try {
        const response = await axios.get(`/api/dotthiduakhenthuong/listdotdotxuat/${maDot}`, {
            headers: {
                Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200) {
            if (response.data.data) {
                dotDotXuatList.value = response.data.data.map(item => ({
                    id: item.PK_MaDotXuat,
                    ngayBatDau: item.dNgayBatDau,
                    ngayKetThuc: item.dNgayKetThuc,
                    hanNopDeXuat: item.dHanNopDeXuat,
                    hanNopMinhChung: item.dHanNopMinhChung,
                    hanNopBienBan: item.dHanBienBanHoiDong,
                    bTrangThai: item.bTrangThai
                }));
            }
        } else {
            toastError('Lỗi khi tải danh sách đợt thi đua đột xuất');
        }
    } catch (error) {
        console.error('Lỗi khi tải danh sách đợt thi đua đột xuất:', error);
        toastError('Không thể tải danh sách đợt thi đua đột xuất');
    }
};

const toggleAccountStatus = (dotdotxuat) => {
    Swal.fire({
        title: "Xác nhận",
        text: `Bạn có chắc chắn muốn ${dotdotxuat.bTrangThai == 1 ? "khóa" : "mở khóa"} đợt này?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Đồng ý",
        cancelButtonText: "Hủy",
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
    }).then((result) => {
        if (result.isConfirmed) {
            const newStatus = dotdotxuat.bTrangThai == 1 ? 0 : 1;
            console.log(newStatus);
            console.log(dotdotxuat.id);
            axios.put(`/api/dotthiduakhenthuong/updatetrangthaidotdotxuat/${dotdotxuat.id}`, {
                trangThai: newStatus
            }, {
                headers: {
                    Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
                }
            }).then((response) => {
                if (response.status === 200) {
                    dotdotxuat.bTrangThai = newStatus;
                    dotDotXuatList.value.forEach(dot => {
                        if (dot.id !== dotdotxuat.id) {
                            dot.bTrangThai = 0
                        }
                    })
                    toastSuccess(`Đã ${newStatus == 1 ? "mở khóa" : "khóa"} đợt thi đua đột xuất thành công`);
                } else {
                    toastError('Có lỗi xảy ra khi cập nhật trạng thái đợt thi đua đột xuất');
                }
            }).catch((error) => {
                if(error.response) {
                    toastError(error.response.data.message || 'Có lỗi xảy ra khi cập nhật trạng thái đợt thi đua đột xuất');
                } else {
                    toastError('Có lỗi xảy ra khi cập nhật trạng thái đợt thi đua đột xuất');
                }
            });
        }
    });
};

const showAddModal = () => {
    isEditing.value = false;
    dotDotXuat.value = {
        id: null,
        ngayBatDau: '',
        ngayKetThuc: '',
        hanNopDeXuat: '',
        hanNopMinhChung: '',
        hanNopBienBan: ''
    };
};

const formatDateDisplay = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN'); // sẽ ra: 25/04/2025
};


const showEditModal = (data) => {
    isEditing.value = true;
    dotDotXuat.value = {
        id: data.id,
        ngayBatDau: data.ngayBatDau,
        ngayKetThuc: (data.ngayKetThuc),
        hanNopDeXuat: (data.hanNopDeXuat),
        hanNopMinhChung: (data.hanNopMinhChung),
        hanNopBienBan: (data.hanNopBienBan)
    }
};

const saveDotDotXuat = async () => {
    // Kiểm tra dữ liệu hợp lệ
    if (!isValidDates()) {
        toastError('Vui lòng kiểm tra lại các ngày, đảm bảo ngày bắt đầu không sau ngày kết thúc và tất cả các trường đều được điền');
        return;
    }

    try {
        let response;
        const formData = {
            ...dotDotXuat.value,
            maDot: maDot
        };

        if (isEditing.value) {
            // Cập nhật đợt đột xuất
            console.log(dotDotXuat.value.id);
            response = await axios.put(`/api/dotthiduakhenthuong/updatedotdotxuat/${dotDotXuat.value.id}`, formData, {
                headers: {
                    Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
                }
            });
        } else {
            // Thêm đợt đột xuất mới
            response = await axios.post('/api/dotthiduakhenthuong/adddotdotxuat', formData, {
                headers: {
                    Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
                }
            });
        }

        if (response.status === 200) {
            await loadDotDotXuat(); // Tải lại danh sách sau khi thêm/sửa
            toastSuccess(isEditing.value ? 'Cập nhật đợt thi đua đột xuất thành công' : 'Thêm đợt thi đua đột xuất thành công');
            // Đóng modal
            document.getElementById("dotModal").querySelector(".btn-close").click();
        }
    } catch (error) {
        if (error.response) {
            if (error.response.status === 422) {
                const errors = error.response.data.error;
                let errorMessage = Object.values(errors).flat().join('<br>');
                toastError(errorMessage);
            } else {
                toastError(error.response.data.message || 'Có lỗi xảy ra khi lưu đợt thi đua đột xuất');
            }
        } else {
            toastError('Có lỗi xảy ra khi lưu đợt thi đua đột xuất');
        }
    }
};

const isValidDates = () => {
    const { ngayBatDau, ngayKetThuc, hanNopDeXuat, hanNopMinhChung, hanNopBienBan } = dotDotXuat.value;

    if (!ngayBatDau || !ngayKetThuc || !hanNopDeXuat || !hanNopMinhChung || !hanNopBienBan) {
        return false;
    }

    if (ngayBatDau > ngayKetThuc) {
        return false;
    }

    return true;
};

const confirmDelete = (data) => {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: `Bạn có chắc muốn xóa đợt thi đua đột xuất này?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`/api/dotthiduakhenthuong/deletedotdotxuat/${data.PK_MaDotXuat}`, {
                    headers: {
                        Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
                    }
                });

                if (response.status === 200) {
                    dotDotXuatList.value = dotDotXuatList.value.filter(d => (d.PK_MaDotXuat !== data.PK_MaDotXuat));
                    toastSuccess('Xóa đợt thi đua đột xuất thành công');
                }
            } catch (error) {
                if (error.response) {
                    toastError(error.response.data.message || 'Có lỗi xảy ra khi xóa đợt thi đua đột xuất');
                } else {
                    toastError('Có lỗi xảy ra khi xóa đợt thi đua đột xuất');
                }
            }
        }
    });
};
</script>

<style scoped>
.p-button {
    margin-right: 0.5rem;
}

.confirmation-content {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>