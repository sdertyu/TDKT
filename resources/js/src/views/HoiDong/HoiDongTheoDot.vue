<template>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Thông tin Hội đồng Thi đua Khen thưởng năm học {{ madot }}</h4>
            </div>
            <div class="card-body">
                <form @submit.prevent="saveHoiDong">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Theo hướng dẫn số</label>
                                <input v-model="hoiDong.huongDanSo" type="text" class="form-control"
                                    placeholder="Nhập số hướng dẫn" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Thời gian</label>
                                <input v-model="hoiDong.thoiGian" type="datetime-local" class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Địa chỉ</label>
                                <input v-model="hoiDong.diaChi" type="text" class="form-control"
                                    placeholder="Nhập địa chỉ" />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Tổng số người triệu tập</label>
                                <input v-model.number="hoiDong.tongNguoiTrieuTap" type="number" min="0"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Số người có mặt</label>
                                <input v-model.number="hoiDong.soNguoiCoMat" type="number" min="0"
                                    class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Chủ tịch hội đồng</label>
                                <select v-model="hoiDong.chuTichId" class="form-select">
                                    <option value="">-- Chọn chủ tịch hội đồng --</option>
                                    <option v-for="canhan in danhSachCanhan" :key="canhan.taikhoan.PK_MaTaiKhoan"
                                        :value="canhan.taikhoan.PK_MaTaiKhoan">
                                        {{ canhan.sTenCaNhan }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Phó chủ tịch thường trực hội đồng</label>
                                <select v-model="hoiDong.phoThuongTrucId" class="form-select">
                                    <option value="">-- Chọn phó chủ tịch thường trực --</option>
                                    <option v-for="canhan in danhSachCanhan" :key="canhan.taikhoan.PK_MaTaiKhoan"
                                        :value="canhan.taikhoan.PK_MaTaiKhoan">
                                        {{ canhan.sTenCaNhan }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Phó chủ tịch hội đồng</label>
                                <select v-model="hoiDong.phoChuTichId" class="form-select">
                                    <option value="">-- Chọn phó chủ tịch hội đồng --</option>
                                    <option v-for="canhan in danhSachCanhan" :key="canhan.taikhoan.PK_MaTaiKhoan"
                                        :value="canhan.taikhoan.PK_MaTaiKhoan">
                                        {{ canhan.sTenCaNhan }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Thư ký</label>
                                <select v-model="hoiDong.thuKyId" class="form-select">
                                    <option value="">-- Chọn thư ký --</option>
                                    <option v-for="canhan in danhSachCanhan" :key="canhan.taikhoan.PK_MaTaiKhoan"
                                        :value="canhan.taikhoan.PK_MaTaiKhoan">
                                        {{ canhan.sTenCaNhan }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Tệp đính kèm biên bản họp</label>
                                <input type="file" class="form-control" @change="e => handleFileUpload('bienBan', e)"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" />
                                <small class="text-muted">Hỗ trợ file: PDF, Word, Excel, hình ảnh (tối đa 10MB)</small>
                                <p v-if="hoiDong.tenBienBan" class="mt-2 fst-italic">
                                    <i class="fas fa-file-alt me-1"></i> File đã tải lên: <strong>{{
                                        hoiDong.tenBienBan }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Tệp đính kèm biên bản kiểm phiếu</label>
                                <input type="file" class="form-control" @change="e => handleFileUpload('kiemPhieu', e)"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" />
                                <small class="text-muted">Hỗ trợ file: PDF, Word, Excel, hình ảnh (tối đa 10MB)</small>
                                <p v-if="hoiDong.tenKiemPhieu" class="mt-2 fst-italic">
                                    <i class="fas fa-file-alt me-1"></i> File đã tải lên: <strong>{{
                                        hoiDong.tenKiemPhieu }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Ghi chú</label>
                                <textarea v-model="hoiDong.ghiChu" class="form-control" rows="3"
                                    placeholder="Nhập ghi chú"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                        <button type="submit" class="btn btn-primary">Lưu thông tin</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Danh sách đề xuất cá nhân</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-outline-success me-2" @click="setAllCaNhanStatus('1')">
                        <i class="fas fa-check me-1"></i> Đạt
                    </button>
                    <button type="button" class="btn btn-outline-danger" @click="setAllCaNhanStatus('0')">
                        <i class="fas fa-times me-1"></i> Không đạt
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px">STT</th>
                                <th>Tên danh hiệu</th>
                                <th>Người đề xuất</th>
                                <th>Đơn vị</th>
                                <th style="width: 200px">Tổng số người bầu</th>
                                <th style="width: 120px">Tỷ lệ %</th>
                                <th style="width: 150px">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(deXuat, index) in danhSachDeXuatCaNhan" :key="deXuat.PK_MaDeXuat">
                                <td class="text-center">{{ index + 1 }}</td>
                                <td>{{ deXuat.danh_hieu }}</td>
                                <td>{{ deXuat.ca_nhan.ten_ca_nhan }}</td>
                                <td>{{ deXuat.ca_nhan.don_vi }}</td>
                                <td>
                                    <input v-model.number="deXuat.so_nguoi_bau" type="number" min="0"
                                        class="form-control" :max="hoiDong.soNguoiCoMat" />
                                </td>
                                <td class="text-center fw-bold">
                                    {{ hoiDong.soNguoiCoMat > 0 ? ((deXuat.so_nguoi_bau / hoiDong.soNguoiCoMat) * 100).toFixed(2) : 0 }}%
                                </td>
                                <td>
                                    <select v-model="deXuat.trang_thai" class="form-select">
                                        <option value="1">Đạt</option>
                                        <option value="0">Không đạt</option>
                                    </select>
                                </td>
                            </tr>
                            <tr v-if="danhSachDeXuatCaNhan.length === 0">
                                <td colspan="7" class="text-center">Không có đề xuất cá nhân nào trong đợt này</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Danh sách đề xuất đơn vị</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-outline-success me-2" @click="setAllDonViStatus('1')">
                        <i class="fas fa-check me-1"></i> Đạt
                    </button>
                    <button type="button" class="btn btn-outline-danger" @click="setAllDonViStatus('0')">
                        <i class="fas fa-times me-1"></i> Không đạt
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px">STT</th>
                                <th>Tên danh hiệu</th>
                                <th>Đơn vị</th>
                                <th style="width: 200px">Tổng số người bầu</th>
                                <th style="width: 120px">Tỷ lệ %</th>
                                <th style="width: 150px">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(deXuat, index) in danhSachDeXuatDonVi" :key="deXuat.PK_MaDeXuat">
                                <td class="text-center">{{ index + 1 }}</td>
                                <td>{{ deXuat.danh_hieu }}</td>
                                <td>{{ deXuat.don_vi.ten_don_vi }}</td>
                                <td>
                                    <input v-model.number="deXuat.so_nguoi_bau" type="number" min="0"
                                        class="form-control" :max="hoiDong.soNguoiCoMat" />
                                </td>
                                <td class="text-center fw-bold">
                                    {{ hoiDong.soNguoiCoMat > 0 ? ((deXuat.so_nguoi_bau / hoiDong.soNguoiCoMat) * 100).toFixed(2) : 0 }}%
                                </td>
                                <td>
                                    <select v-model="deXuat.trang_thai" class="form-select">
                                        <option value="1">Đạt</option>
                                        <option value="0">Không đạt</option>
                                    </select>
                                </td>
                            </tr>
                            <tr v-if="danhSachDeXuatDonVi.length === 0">
                                <td colspan="6" class="text-center">Không có đề xuất đơn vị nào trong đợt này</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                    <button type="button" class="btn btn-success" @click="saveDeXuat">Lưu danh sách</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { get } from 'jquery';
import { ref, onMounted, watch } from 'vue';
import Swal from 'sweetalert2';
// Import axios if needed for API communication
// import axios from 'axios';

// Hội đồng data structure
const hoiDong = ref({
    maHoiDong: null,
    huongDanSo: '',
    thoiGian: '',
    diaChi: '',
    tongNguoiTrieuTap: 0,
    soNguoiCoMat: 0,
    ghiChu: '',
    chuTichId: '',
    phoThuongTrucId: '',
    phoChuTichId: '',
    thuKyId: '',
    fileBienBan: null,
    fileKiemPhieu: null,
});

const danhSachCanhan = ref([]);

// Sample data for đề xuất - Replace with actual API call
const danhSachDeXuatCaNhan = ref([]);
const danhSachDeXuatDonVi = ref([]);

const madot = ref('');

// Handle file upload
const handleFileUpload = (type, event) => {
    const file = event.target.files[0];
    if (!file) return;

    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
        toastError('Kích thước file không được vượt quá 10MB!');
        event.target.value = '';
        return;
    }

    if (type === 'bienBan') {
        hoiDong.value.fileBienBan = file;
    } else if (type === 'kiemPhieu') {
        console.log("object");
        hoiDong.value.fileKiemPhieu = file;
    }
};

// Functions
const saveHoiDong = async () => {
    try {
        // Validate inputs if needed
        if (hoiDong.value.soNguoiCoMat > hoiDong.value.tongNguoiTrieuTap) {
            alert('Số người có mặt không thể nhiều hơn tổng số người triệu tập!');
            return;
        }

        // Check if required fields are filled
        if (!hoiDong.value.chuTichId || !hoiDong.value.thuKyId) {
            alert('Vui lòng chọn Chủ tịch hội đồng và Thư ký!');
            return;
        }

        // Create FormData for file upload
        const formData = new FormData();
        for (const key in hoiDong.value) {
            // console.log(hoiDong.value[key]);
            if (key === 'fileBienBan' && hoiDong.value[key] != null) {
                formData.append('fileBienBan', hoiDong.value[key] == null ? '' : hoiDong.value[key]);
            }
            else if (key === 'fileKiemPhieu' && hoiDong.value[key] != null) {
                formData.append('fileKiemPhieu', hoiDong.value[key] == null ? '' : hoiDong.value[key]);
            } else {
                formData.append(key, hoiDong.value[key]);
            }
        }

        let maHD = localStorage.getItem('user_name') + '-' + madot.value
        let maDot = madot.value
        formData.append('maHoiDong', maHD); // Include maHoiDong if exists
        formData.append('maDot', maDot); // Include madot if exists

        // API call to save Hội đồng information
        const add = await axios.post('/api/hoidong/add', formData, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`,
                'Content-Type': 'multipart/form-data'
            }
        });

        if (add.status === 200) {
            toastSuccess('Lưu thông tin hội đồng thành công!');
            // Optionally, reset the form or redirect
        } else {
            toastError('Có lỗi xảy ra khi lưu thông tin hội đồng!');
        }

    } catch (error) {
        toastError('Có lỗi xảy ra khi lưu thông tin hội đồng!');
    }
};

const saveDeXuat = async () => {
    try {
        // Validate before saving
        for (const deXuat of danhSachDeXuatCaNhan.value) {
            if (deXuat.so_nguoi_bau > hoiDong.value.soNguoiCoMat) {
                alert(`Đề xuất cá nhân "${deXuat.danh_hieu}" có số người bầu vượt quá số người có mặt!`);
                return;
            }
        }

        for (const deXuat of danhSachDeXuatDonVi.value) {
            if (deXuat.so_nguoi_bau > hoiDong.value.soNguoiCoMat) {
                alert(`Đề xuất đơn vị "${deXuat.danh_hieu}" có số người bầu vượt quá số người có mặt!`);
                return;
            }
        }

        let formData = new FormData();
        danhSachDeXuatCaNhan.value.forEach(item => {
            formData.append('deXuat[]', JSON.stringify(item));
        });
        danhSachDeXuatDonVi.value.forEach(item => {
            formData.append('deXuat[]', JSON.stringify(item));
        });

        let maHD = localStorage.getItem('user_name') + '-' + madot.value
        formData.append('maHoiDong', maHD); // Include maHoiDong if exists
        for (let [key, value] of formData.entries()) {
            console.log(`${key}:`, value);
        }
        console.log(localStorage);
        // return
        const save = await axios.post('/api/dexuat/xetduyetdexuat', formData, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`,
            }
        });

        if (save.status === 200) {
            toastSuccess('Lưu danh sách đề xuất thành công!');
            // Optionally, reset the form or redirect
        }
    } catch (error) {
        if (error.response) {
            if (error.response.status === 422) {
                const errors = error.response.data.errors;
                let errorMessage = Object.values(errors).flat().join('<br>')
                console.log(errors);
                toastError(errorMessage)
            } else {
                toastError(error.response.data.message)
            }
        }
        else {
            toastError('Có lỗi xảy ra khi lưu danh hiệu')
        }
    }
};

const getListCaNhan = () => {
    axios.get('/api/taikhoan/getlistcanhan', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    })
        .then(response => {
            if (response.status === 200) {
                danhSachCanhan.value = response.data.data;
            }
        })
        .catch(error => {
            if (error.response) {
                if (error.response.status === 422) {
                    const errors = error.response.data.errors;
                    let errorMessage = Object.values(errors).flat().join('<br>')
                    console.log(errors);
                    toastError(errorMessage)
                } else {
                    toastError(error.response.data.message)
                }
            }
            else {
                toastError('Có lỗi xảy ra khi lưu danh hiệu')
            }
        });
}

const getListDeXuat = () => {
    axios.get('/api/dexuat/getlistdexuatxetduyet', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    })
        .then(response => {
            if (response.status === 200) {
                danhSachDeXuatCaNhan.value = response.data.data.de_xuat_ca_nhan;
                danhSachDeXuatDonVi.value = response.data.data.de_xuat_don_vi || [];
                let hoiDongInfo = response.data.data.hoi_dong;
                if (hoiDongInfo !== null) {
                    hoiDong.value.huongDanSo = hoiDongInfo.sSoHD;
                    hoiDong.value.thoiGian = hoiDongInfo.dThoiGianHop;
                    hoiDong.value.diaChi = hoiDongInfo.sDiaChi;
                    hoiDong.value.tongNguoiTrieuTap = hoiDongInfo.iSoThanhVien;
                    hoiDong.value.soNguoiCoMat = hoiDongInfo.iSoNguoiThamDu;
                    hoiDong.value.ghiChu = hoiDongInfo.sGhiChu;
                    hoiDong.value.chuTichId = hoiDongInfo.FK_ChuTri;
                    hoiDong.value.phoThuongTrucId = hoiDongInfo.FK_PhoChuTichTT;
                    hoiDong.value.phoChuTichId = hoiDongInfo.FK_PhoChuTich;
                    hoiDong.value.thuKyId = hoiDongInfo.FK_ThuKy;
                    hoiDong.value.tenBienBan = hoiDongInfo.sTenBienBan;
                    hoiDong.value.tenKiemPhieu = hoiDongInfo.sTenKiemPhieu;
                }

            }
        })
        .catch(error => {
            if (error.response) {
                if (error.response.status === 422) {
                    const errors = error.response.data.errors;
                    let errorMessage = Object.values(errors).flat().join('<br>')
                    console.log(errors);
                    toastError(errorMessage)
                } else {
                    toastError(error.response.data.message)
                }
            }
            else {
                toastError('Có lỗi xảy ra')
            }
        });
}

// Functions to set status for all entries at once
const setAllCaNhanStatus = (status) => {
    if (!danhSachDeXuatCaNhan.value.length) return;

    Swal.fire({
        title: status === '1'
            ? 'Duyệt tất cả đề xuất cá nhân?'
            : 'Từ chối tất cả đề xuất cá nhân?',
        text: status === '1'
            ? 'Bạn có chắc chắn muốn duyệt tất cả đề xuất cá nhân?'
            : 'Bạn có chắc chắn muốn từ chối tất cả đề xuất cá nhân?',
        icon: status === '1' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: status === '1' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: status === '1' ? 'Đồng ý duyệt' : 'Đồng ý từ chối',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            danhSachDeXuatCaNhan.value.forEach(item => {
                item.trang_thai = status;
            });

            // Update styling after state change
            setTimeout(() => applySelectStyling(), 100);

            Swal.fire({
                title: 'Thành công!',
                text: status === '1'
                    ? 'Tất cả đề xuất cá nhân đã được duyệt.'
                    : 'Tất cả đề xuất cá nhân đã bị từ chối.',
                icon: 'success',
                timer: 1500,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timerProgressBar: true,
            });
        }
    });
};

const setAllDonViStatus = (status) => {
    if (!danhSachDeXuatDonVi.value.length) return;

    Swal.fire({
        title: status === '1'
            ? 'Duyệt tất cả đề xuất đơn vị?'
            : 'Từ chối tất cả đề xuất đơn vị?',
        text: status === '1'
            ? 'Bạn có chắc chắn muốn duyệt tất cả đề xuất đơn vị?'
            : 'Bạn có chắc chắn muốn từ chối tất cả đề xuất đơn vị?',
        icon: status === '1' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: status === '1' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: status === '1' ? 'Đồng ý duyệt' : 'Đồng ý từ chối',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            danhSachDeXuatDonVi.value.forEach(item => {
                item.trang_thai = status;
            });

            // Update styling after state change
            setTimeout(() => applySelectStyling(), 100);

            Swal.fire({
                title: 'Thành công!',
                text: status === '1'
                    ? 'Tất cả đề xuất đơn vị đã được duyệt.'
                    : 'Tất cả đề xuất đơn vị đã bị từ chối.',
                icon: 'success',
                timer: 1500,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timerProgressBar: true,
            });
        }
    });
};

// Fetch data when component mounted
onMounted(async () => {
    try {
        madot.value = useGlobalStore().dotActive;
        // Fetch hội đồng data if exists
        // const response = await axios.get('/api/hoi-dong');
        // hoiDong.value = response.data;

        // Fetch danh sách cá nhân
        // getHoiDongDeXuat();
        getListCaNhan();

        // Fetch proposals list
        getListDeXuat();
    } catch (error) {
        console.error('Lỗi khi tải dữ liệu:', error);
    }
});

// Add watchers to apply styling based on selected values
watch(danhSachDeXuatCaNhan, () => {
    setTimeout(() => applySelectStyling(), 100);
}, { deep: true });

// Watch for changes in đơn vị list and apply styling
watch(danhSachDeXuatDonVi, () => {
    setTimeout(() => applySelectStyling(), 100);
}, { deep: true });

// Function to apply styling to select elements based on their value
const applySelectStyling = () => {
    document.querySelectorAll('.form-select').forEach(select => {
        // Remove existing status classes
        select.classList.remove('select-1', 'select-khong-1');

        // Add appropriate class based on selected value
        if (select.value === '1') {
            select.classList.add('select-1');
        } else if (select.value === '0') {
            select.classList.add('select-khong-1');
        }
    });
};

// Call this function after mounting and after any data changes
onMounted(() => {
    // Existing code...

    // Add event listener to update styling when selects change
    document.addEventListener('change', (event) => {
        if (event.target.classList.contains('form-select')) {
            applySelectStyling();
        }
    });
});
</script>

<style scoped>
.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

/* Custom styling for status selects */
.form-select {
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select option[value="1"] {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
    font-weight: 500;
}

.form-select option[value="0"] {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    font-weight: 500;
}

/* Apply custom styling to selects based on their selected value */
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Add a watcher in the script to apply these classes dynamically */
.select-1 {
    border-color: #28a745 !important;
    color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2328a745' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
}

.select-khong-1 {
    border-color: #dc3545 !important;
    color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23dc3545' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
}
</style>