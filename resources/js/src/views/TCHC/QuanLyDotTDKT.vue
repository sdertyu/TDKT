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
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Ngày tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in listDot" :key="index">
                        <td class="text-center">{{ index + 1 }}</td>
                        <td class="text-center">{{ item.iNamBatDau }}</td>
                        <td class="text-center">{{ item.iNamKetThuc }}</td>
                        <td class="text-center">
                            <span :class="item.bTrangThai == 1 ? 'badge bg-success' : 'badge bg-secondary'">{{
                                item.bTrangThai == 1 ? 'Hoạt động' : 'Tạm ngưng' }}</span>
                        </td>
                        <td class="text-center">{{ item.dNgayTao }}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-2" @click="showEditModal(item)"
                                data-bs-toggle="modal" data-bs-target="#dotModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <router-link :to="`/quanlyvanban/${item.PK_MaDot}`" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-file"></i>
                            </router-link>
                            <button class="btn btn-secondary btn-sm me-2"
                                :class="item.bTrangThai == 1 ? 'bg-blend-color' : 'bg-success'"
                                @click="trangThaiDot(item)">
                                <i :class="item.bTrangThai == 0 ? 'fas fa-lock-open' : 'fas fa-lock'"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" @click="confirmDelete(item)">
                                <i class="fas fa-trash"></i>
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
                                <input type="datetime-local" class="form-control" v-model="currentDot.dHanBienBanDonVi">
                            </div>
                            <div class="mb-3">
                                <label for="namBatDau" class="form-label text">Hạn nộp minh chứng</label>
                                <input type="datetime-local" class="form-control" v-model="currentDot.dHanNopMinhChung">
                            </div>
                            <div class="mb-3">
                                <label for="namBatDau" class="form-label text">Hạn nộp biển bản phê duyệt cấp hội
                                    đồng</label>
                                <input type="datetime-local" class="form-control"
                                    v-model="currentDot.dHanBienBanHoiDong">
                                <Datepicker v-model="datetime" :enable-time="true" :enable-seconds="true"
                                    format="yyyy-MM-dd HH:mm:ss" />

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
import { computed, onMounted, reactive, ref, warn } from 'vue';
import Swal from 'sweetalert2';


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
    currentDot.iNamKetThuc = ''
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

const saveDot = () => {

    if (isEditing.value) {
        const update = axios.put(`/api/dotthiduakhenthuong/update`, currentDot,
            {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`
                }
            });


        update.then(response => {
            if (response.status === 200) {
                Swal.fire({
                    position: 'top-end',
                    toast: true,
                    icon: 'success',
                    title: 'Thêm đợt thành công',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                })
            }
        }).catch(error => {
            console.log(error);

            // Nếu có lỗi validation từ Laravel
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.error
                const errorMessages = Object.values(errors).flat().join('<br>')

                Swal.fire({
                    position: 'top-end',
                    toast: true,
                    icon: 'error',
                    title: errorMessages,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                })
            } else {
                // Các lỗi khác (server, mạng, v.v.)
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    showConfirmButton: false,
                    title: 'Không thể thêm đợt!',
                    timer: 3000,
                    timerProgressBar: true
                })
            }
        })


    } else {
        // Thêm đợt mới
        const add = axios.post('/api/dotthiduakhenthuong/add', currentDot,
            {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`
                }
            })

        add.then(response => {
            if (response.status === 200) {
                listDot.value.push(response.data.data);
                Swal.fire({
                    position: 'top-end',
                    toast: true,
                    icon: 'success',
                    title: 'Thêm đợt thành công',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                })
            }
        }).catch(error => {
            console.log(error);

            // Nếu có lỗi validation từ Laravel
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.error
                const errorMessages = Object.values(errors).flat().join('<br>')

                Swal.fire({
                    position: 'top-end',
                    toast: true,
                    icon: 'error',
                    title: errorMessages,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                })
            } else {
                // Các lỗi khác (server, mạng, v.v.)
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    showConfirmButton: false,
                    title: 'Không thể thêm đợt!',
                    timer: 3000,
                    timerProgressBar: true
                })
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
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`
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
                    Swal.fire({
                        position: 'top-end',
                        toast: true,
                        icon: 'success',
                        title: 'Thành công',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    })
                }
            }).catch(error => {
                console.log(error);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    showConfirmButton: false,
                    title: 'Không thể thêm đợt!',
                    timer: 3000,
                    timerProgressBar: true
                })
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
