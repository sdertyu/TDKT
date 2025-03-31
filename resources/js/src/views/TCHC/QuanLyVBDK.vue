<template>
    <div class="card my-3">
        <div class="card-header">
            <h3 class="card-title">Danh sách văn bản đính kèm năm học {{ maDot }}</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary" @click="showFileModal" data-bs-toggle="modal"
                    data-bs-target="#themFileModal">
                    <i class="fas fa-plus"></i> Thêm file
                </button>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th class="text-center">Tên văn bản</th>
                        <th class="text-center">Tên file</th>
                        <th class="text-center">Ngày tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in listFile" :key="index">
                        <td class="text-center">{{ index + 1 }}</td>
                        <td class="text-center">{{ item.sTenVanBan }}</td>
                        <td class="text-center">{{ item.sTenFile }}</td>
                        <td class="text-center">{{ item.dNgayTao }}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-2" @click="showEditModal(item)"
                                data-bs-toggle="modal" data-bs-target="#themFileModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" @click="xoaVanBanDinhKem(item)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="themFileModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form @submit.prevent="saveChangeFiles">
                        <div class="modal-header">
                            <h5 class="modal-title">Văn bản đính kèm cho năm học {{ maDot }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div v-if="!isEditing" class="modal-body">
                            <div v-for="(block, index) in blocks" :key="index" class="file-description-block row mb-3">
                                <div class="form-group col-5">
                                    <label for="">Tên văn bản</label>
                                    <input type="text" class="form-control" v-model="block.tenvanban" required />
                                    <span v-if="validationErrors[`tenvanban.${index}`]" class="text-danger">
                                        {{ validationErrors[`tenvanban.${index}`][0] }}
                                    </span>
                                    <span v-if="clientErrors[`tenvanban.${index}`]" class="text-danger">
                                        {{ clientErrors[`tenvanban.${index}`][0] }}
                                    </span>
                                </div>

                                <div class="form-group col-5">
                                    <label for="">Văn bản đính kèm</label>
                                    <input type="file" class="form-control" @change="handleFileUpload($event, index)" />
                                    <span v-if="clientErrors[`file.${index}`]" class="text-danger">
                                        {{ clientErrors[`file.${index}`][0] }}
                                    </span>
                                </div>

                                <div v-if="!isEditing"
                                    class="col-1 d-flex align-items-center justify-content-center flex-column">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger" @click="xoaVanBanDinhKem(block)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary" @click="addBlock">Thêm</button>
                        </div>

                        <div v-else class="modal-body">
                            <div class="file-description-block row mb-3">
                                <div class="form-group col-5">
                                    <label for="">Tên văn bản</label>
                                    <input type="text" class="form-control" v-model="currenFile.sTenVanBan" required />
                                    <span v-if="validationErrors[`tenvanban.${index}`]" class="text-danger">
                                        {{ validationErrors[`tenvanban.${index}`][0] }}
                                    </span>
                                    <span v-if="clientErrors[`tenvanban.${index}`]" class="text-danger">
                                        {{ clientErrors[`tenvanban.${index}`][0] }}
                                    </span>
                                </div>

                                <div class="form-group col-5">
                                    <label for="">Văn bản đính kèm</label>
                                    <input type="file" class="form-control" @change="handleFileUpload($event, index)" />
                                    <p v-if="clientErrors[`file.${index}`]" class="text-danger">
                                        {{ clientErrors[`file.${index}`][0] }}
                                    </p>
                                    <p class="form-text text-muted">File hiện tại: {{ currenFile.sTenFile }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, reactive, ref, warn } from 'vue';
import Swal from 'sweetalert2';
import { useRoute } from 'vue-router';

const maDot = useRoute().params.id;
const isEditing = ref(false);

const listFile = ref([]);
const currenFile = reactive({
    sTenVanBan: '',
    PK_MaVanBan: null,
    sDuongDan: '',
    sTenFile: ''
});

onMounted(() => {
    checkDot(maDot);
});

const checkDot = (dot) => {
    if (dot) {
        const check = axios.get(`/api/dotthiduakhenthuong/thong-tin-dot/${dot}`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        });
        check.then(res => {
            if (res.status === 200) {
                getListFile(dot)
            }
        }).catch(err => {
            console.error('Lỗi khi lấy thông tin đợt:', err);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Năm học không tồn tại'
            });
        });
    }
};

const getListFile = (dot) => {
    const list = axios.get(`/api/dotthiduakhenthuong/list-van-ban/${dot}`, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    });

    list.then(res => {
        listFile.value = res.data.data;
    }).catch(err => {
        console.error('Lỗi khi lấy danh sách văn bản đính kèm:', err);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không thể lấy danh sách văn bản đính kèm'
        });
    });
}

const showEditModal = (file) => {
    isEditing.value = true;
    currenFile.sTenVanBan = file.sTenVanBan;
    currenFile.PK_MaVanBan = file.PK_MaVanBan;
    currenFile.sDuongDan = file.sDuongDan;
    currenFile.sTenFile = file.sTenFile;
};


// ========== Văn bản đính kèm ==========
const blocks = ref([{ tenvanban: '', file: null }]);
const validationErrors = ref({});
const clientErrors = ref({});

const showFileModal = () => {
    isEditing.value = false;
    blocks.value = [{ tenvanban: '', file: null }];
};

// Hàm để thêm một khối mới
const addBlock = () => {
    blocks.value.push({ tenvanban: '', file: null });
};

const handleFileUpload = (event, index) => {
    if (!isEditing.value) {
        blocks.value[index].file = event.target.files[0];
    }
    else {
        currenFile.file = event.target.files[0];
    }
};


const saveChangeFiles = () => {

    clientErrors.value = {}; // Reset lỗi

    let hasError = false;
    if (!isEditing.value) {
        blocks.value.forEach((block, index) => {
            if (!block.tenvanban || block.tenvanban.trim() === '') {
                clientErrors.value[`tenvanban.${index}`] = ['Vui lòng nhập tên văn bản'];
                hasError = true;
            }

            if (!block.file) {
                clientErrors.value[`file.${index}`] = ['Vui lòng chọn file đính kèm'];
                hasError = true;
            }
        });

        if (hasError) {
            Swal.fire({
                icon: 'error',
                title: 'Vui lòng điền đầy đủ thông tin trước khi lưu!',
                timer: 2000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timerProgressBar: true
            });
            return;
        }

        const formData = new FormData();
        formData.append('madot', maDot); // Thêm mã đợt (giả sử bạn có `madot` trong data)
        blocks.value.forEach((block, index) => {
            formData.append(`tenvanban[${index}]`, block.tenvanban);
            formData.append(`file[${index}]`, block.file);
            return
        });

        const themVBDK = axios.post('/api/dotthiduakhenthuong/them-van-ban', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        });

        themVBDK.then(res => {
            if (res.status === 200) {
                Swal.fire({
                    position: 'top-end',
                    toast: true,
                    icon: 'success',
                    title: 'Thành công',
                    showConfirmButton: false,
                    timer: 3000, timerProgressBar: true
                });
                if (res.data.data) {
                    let addList = res.data.data;
                    console.log(addList);
                    addList.forEach(element => {
                        console.log(element);
                        listFile.value.push({
                            PK_MaVanBan: element.PK_MaVanBan,
                            sTenVanBan: element.sTenVanBan,
                            sTenFile: element.sTenFile,
                            dNgayTao: element.dNgayTao
                        });
                    });
                }
            }
            document.getElementById("themFileModal").querySelector(".btn-close").click();
        }).catch(err => {
            if (err.response && err.response.status === 422) {
                validationErrors.value = err.response.data.error;

                // Lỗi xác thực
                Swal.fire({
                    position: 'top-end',
                    toast: true,
                    icon: 'error',
                    title: 'Lỗi xác thực: Dữ liệu không hợp lệ',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            } else if (err.response && err.response.status === 500) {
                // Lỗi server
                Swal.fire({
                    position: 'top-end',
                    toast: true,
                    icon: 'error',
                    title: 'Lỗi server: ' + (err.response.data.message || 'Có lỗi xảy ra trên server'),
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                // Lỗi khác
                Swal.fire({
                    position: 'top-end',
                    toast: true,
                    icon: 'error',
                    title: 'Lỗi: ' + (err.response?.data?.message || 'Không thể xử lý yêu cầu'),
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        });
    }

    else {
        if (!currenFile.sTenVanBan || currenFile.sTenVanBan.trim() === '') {
            clientErrors.value[`tenvanban.${index}`] = ['Vui lòng nhập tên văn bản'];
            hasError = true;
        }
        if (hasError) {
            Swal.fire({
                icon: 'error',
                title: 'Vui lòng điền đầy đủ thông tin trước khi lưu!',
                timer: 2000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timerProgressBar: true
            });
            return;
        }

        const formData2 = new FormData();
        formData2.append('madot', maDot); // Thêm mã đợt (giả sử bạn có `madot` trong data)
        formData2.append('tenvanban', currenFile.sTenVanBan);
        formData2.append('mavanban', currenFile.PK_MaVanBan);
        if (currenFile.file) {
            formData2.append('file', currenFile.file); // có thể null nếu không đổi
        }

        const suaVBDK = axios.post('/api/dotthiduakhenthuong/update-van-ban', formData2, {
            headers: {
                'Content-Type': 'multipart/form-data',
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        });

        suaVBDK.then(res => {
            if (res.status === 200) {
                Swal.fire({
                    position: 'top-end',
                    toast: true,
                    icon: 'success',
                    title: 'Thành công',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        })

    }
    
    document.getElementById("themFileModal").querySelector(".btn-close").click();
}

const xoaVanBanDinhKem = (vb) => {
    if (vb.PK_MaVanBan) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: `Bạn có chắc muốn xóa văn bản đính kèm này không?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    // Xóa văn bản đính kèm
                    const xoaVB = axios.delete(`/api/dotthiduakhenthuong/xoa-van-ban/${vb.PK_MaVanBan}`, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('api_token')}`
                        }
                    });

                    xoaVB.then(res => {
                        if (res.status === 200) {
                            listFile.value = listFile.value.filter(b => b !== vb);
                            console.log(blocks.value);
                            Swal.fire({
                                position: 'top-end',
                                toast: true,
                                icon: 'success',
                                title: 'Xóa thành công',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    }).catch(err => {
                        console.error('Lỗi khi xóa văn bản đính kèm:', err);
                        Swal.fire({
                            position: 'top-end',
                            toast: true,
                            icon: 'error',
                            title: 'Không thể xóa văn bản đính kèm',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    });
                } catch (error) {
                    console.error('Lỗi khi xóa văn bản đính kèm:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Không thể xóa văn bản đính kèm'
                    });
                }
            }
        });
    } else {
        blocks.value = blocks.value.filter(b => b !== vb);
    }
}

</script>
