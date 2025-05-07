<template>
    <div class="container-fluid" v-if="madot">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Thông tin Biên bản bình xét thi đua năm học {{ madot }}</h4>
            </div>
            <div class="card-body">
                <form @submit.prevent="submitForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Theo hướng dẫn số:</label>
                                <input type="text" class="form-control" id="soHuongDan" v-model="formData.soHuongDan"
                                    placeholder="Nhập số hướng dẫn">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Vào hồi:</label>
                                <input type="datetime-local" class="form-control" id="thoiGian"
                                    v-model="formData.thoiGian">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Địa chỉ:</label>
                        <input type="text" class="form-control" id="diaChi" v-model="formData.diaChi"
                            placeholder="Nhập địa chỉ">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Tổng số người trong đơn vị:</label>
                                <input type="number" class="form-control" id="tongSoNguoi"
                                    v-model="formData.tongSoNguoi" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Số người được triệu tập:</label>
                                <input type="number" class="form-control" id="soTrieuTap" v-model="formData.soTrieuTap"
                                    min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Số người có mặt:</label>
                                <input type="number" class="form-control" id="soCoMat" v-model="formData.soCoMat"
                                    min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Số người vắng:</label>
                                <input type="number" class="form-control" id="soVang" v-model="formData.soVang" min="0"
                                    disabled>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ghi chú:</label>
                        <textarea class="form-control" id="ghiChu" v-model="formData.ghiChu" rows="3"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Chủ trì:</label>
                                <select class="form-select" id="chuTri" v-model="formData.chuTri">
                                    <option value="" disabled selected>Chọn người chủ trì</option>
                                    <option v-for="person in individuals" :key="person.id" :value="person.taiKhoan">
                                        {{ person.displayName }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Thư ký:</label>
                                <select class="form-select" id="thuKy" v-model="formData.thuKy">
                                    <option value="" disabled selected>Chọn thư ký</option>
                                    <option v-for="person in individuals" :key="person.id" :value="person.taiKhoan">
                                        {{ person.displayName }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Biên bản bình xét</h5>
                        </div>
                        <div class="card-body">
                            <input type="file" class="form-control" @change="handleFileUpload($event)" accept=".pdf">
                            <small class="text-muted">Hỗ trợ file: PDF (tối đa 10MB)</small>
                            <p v-if="formData.tenFile" class="mt-2 fst-italic">
                                <i class="fas fa-file-alt me-1"></i> File đã tải lên: <strong>{{ formData.tenFile
                                }}</strong>
                            </p>

                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                        <button type="submit" class="btn btn-primary">Lưu thông tin</button>
                        <button type="button" class="btn btn-secondary ms-2" @click="resetForm">Làm mới</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Phần bình bầu cá nhân -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Bình bầu danh hiệu cá nhân</h4>
            </div>
            <div class="card-body">
                <div class="mb-4" v-for="(award, index) in individualAwards" :key="index">
                    <h5 class="border-bottom pb-2 fst-italic text-danger">Danh hiệu {{ award.name }}</h5>
                    <div class="mb-2 align-items-center">
                        <multiselect :model-value="selectedIndividuals[award.id]" :options="individuals"
                            :multiple="true" track-by="id" label="displayName" placeholder="Chọn cá nhân"
                            @update:modelValue="val => handleSelectedIndividualsChange(val, award.id)">
                        </multiselect>
                    </div>

                    <div class="table-responsive" v-if="selectedIndividuals[award.id]?.length > 0">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px">STT</th>
                                    <th>Tên cá nhân</th>
                                    <th style="width: 200px">Tổng số người bầu</th>
                                    <th style="width: 120px">Tỷ lệ %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(individual, idx) in selectedIndividuals[award.id]" :key="idx">
                                    <td class="text-center">{{ idx + 1 }}</td>
                                    <td>{{ individual.name }}</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="individual.soPhieu"
                                            placeholder="Số phiếu bầu" min="0" :max="formData.soCoMat">
                                    </td>
                                    <td class="text-center fw-bold"
                                        :class="{ 'text-success': (individual.soPhieu / formData.soCoMat * 100) >= 50 }">
                                        {{ formData.soCoMat > 0 ? ((individual.soPhieu / formData.soCoMat) *
                                            100).toFixed(1) : 0 }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phần bình bầu tập thể -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Bình bầu danh hiệu tập thể</h4>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Danh hiệu đề xuất:</label>
                        <multiselect v-model="selectedUnitAwards" :options="unitAwards" :multiple="true" track-by="id"
                            label="name" placeholder="Chọn danh hiệu" @select="handleSelectedUnitAwardsChange">
                        </multiselect>
                    </div>

                    <div class="table-responsive" v-if="selectedUnitAwards.length > 0">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px">STT</th>
                                    <th>Tên danh hiệu</th>
                                    <th style="width: 200px">Tổng số người bầu</th>
                                    <th style="width: 120px">Tỷ lệ %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(award, index) in selectedUnitAwards" :key="index">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td>{{ award.name }}</td>
                                    <td>
                                        <input type="number" class="form-control" placeholder="Số phiếu bầu"
                                            v-model="selectedUnitAwards[index]['soPhieu']" min="0"
                                            :max="formData.soCoMat" required>
                                    </td>
                                    <td class="text-center fw-bold"
                                        :class="{ 'text-success': (selectedUnitAwards[index]['soPhieu'] / formData.soCoMat * 100) >= 50 }">
                                        {{ formData.soCoMat > 0 ? ((selectedUnitAwards[index]['soPhieu'] /
                                            formData.soCoMat) * 100).toFixed(1) : 0 }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                    <button type="button" class="btn btn-success" @click="saveProposals">Lưu đề xuất</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive, watch } from 'vue';
import Multiselect from 'vue-multiselect';
import Swal from 'sweetalert2';

const individuals = ref([]);
const unitAwards = ref([]);
const individualAwards = ref([]);
const selectedIndividuals = reactive({});
const selectedUnitAwards = ref([]);

// Dữ liệu form
const formData = ref({
    soHuongDan: '',
    thoiGian: '',
    diaChi: '',
    tongSoNguoi: 0,
    soTrieuTap: 0,
    soCoMat: 0,
    soVang: 0,
    chuTri: '',
    thuKy: '',
    namHoc: '',
    ghiChu: '',
    bienBan: '',
    tenFile: ''
});

const handleFileUpload = (event) => {
    if (event.target.files.length > 0) {
        const file = event.target.files[0];

        // Check if file is PDF
        if (file.type !== 'application/pdf') {
            toastError('Vui lòng chọn tệp PDF!');
            event.target.value = ''; // Clear the file input
            return;
        }

        // Check file size
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            toastError('Kích thước file không được vượt quá 10MB!');
            event.target.value = '';
            return;
        }

        formData.value.bienBan = file;
    }
};

// Tính số người vắng
watch([() => formData.value.soTrieuTap, () => formData.value.soCoMat], () => {
    formData.value.soVang = formData.value.soTrieuTap - formData.value.soCoMat;
});

// Lấy danh sách cá nhân trong đơn vị
const getCaNhanTrongDonVi = async () => {
    // console.log(sessionStorage);
    const response = await axios.get(`api/taikhoan/caNhanTrongDonVi/${sessionStorage.getItem('maDonVi')}`, {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
        }
    });

    if (response.status === 200) {
        const data = response.data.data;
        individuals.value = data.map(item => ({
            id: item.PK_MaCaNhan,
            taiKhoan: item.FK_MaTaiKhoan,
            name: item.sTenCaNhan,
            displayName: `${item.PK_MaCaNhan} - ${item.sTenCaNhan}`
        }));
        formData.value.tongSoNguoi = individuals.value.length;
    }
    else {
        console.error('Lỗi khi lấy danh sách cá nhân:', response);
    }
}

const madot = ref('');

// Cập nhật số người vắng khi số người có mặt hoặc triệu tập thay đổi
const watchSoNguoi = () => {
    formData.value.soVang = soVang.value;
};

// Gửi form
const submitForm = async () => {
    try {
        // Validate inputs
        if (formData.value.soTrieuTap < formData.value.soCoMat) {
            Swal.fire({
                icon: 'error',
                text: 'Số người triệu tập phải lớn hơn hoặc bằng số người có mặt!',
                toast: true,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end'
            });
            return;
        }
        if (formData.value.soTrieuTap > formData.value.tongSoNguoi) {
            Swal.fire({
                icon: 'error',
                text: 'Số người triệu tập phải nhỏ hơn hoặc bằng tổng số người trong đơn vị!',
                toast: true,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end'
            });
            return;
        }

        // Check required fields
        if (!formData.value.chuTri || !formData.value.thuKy) {
            Swal.fire({
                icon: 'error',
                text: 'Vui lòng chọn Chủ trì và Thư ký!',
                toast: true,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end'
            });
            return;
        }

        let form = new FormData();
        form.append('mahoidong', sessionStorage.getItem('maDonVi') + '-' + madot.value);
        form.append('madot', madot.value);
        form.append('machutri', formData.value.chuTri);
        form.append('mathuky', formData.value.thuKy);
        form.append('thoigian', formData.value.thoiGian);
        form.append('diadiem', formData.value.diaChi);
        form.append('maloaihoidong', 1); // Hội đồng đơn vị
        form.append('hinhthuchoidong', 1); // Theo đợt
        form.append('songuoithamdu', formData.value.soCoMat);
        form.append('sothanhvien', formData.value.tongSoNguoi);
        form.append('sohd', formData.value.soHuongDan);
        form.append('ghichu', formData.value.ghiChu);

        if (formData.value.bienBan) {
            form.append('bienban', formData.value.bienBan);
        }

        const response = await axios.post(`api/hoidong/add`, form, {
            headers: {
                Authorization: `Bearer ${sessionStorage.getItem('api_token')}`,
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.status === 200) {
            toastSuccess('Cuộc họp đã được tạo thành công');
        }
    } catch (error) {
        if (error.response) {
            if (error.response.status === 422) {
                const errors = error.response.data.error;
                let errorMessage = Object.values(errors).flat().join('<br>')
                console.log(errors);
                toastError(errorMessage)
            } else {
                toastError(error.response.data.message)
            }
        }
        else {
            toastError('Có lỗi xảy ra khi gửi form');
        }
    }
};

// Reset form
const resetForm = () => {
    formData.value = {
        soHuongDan: '',
        thoiGian: '',
        diaChi: '',
        tongSoNguoi: 0,
        soTrieuTap: 0,
        soCoMat: 0,
        soVang: 0,
        chuTri: '',
        thuKy: '',
        namHoc: '',
        ghiChu: '',
        bienBan: '',
        tenFile: ''
    };
};

// Save proposals separately
const saveProposals = async () => {
    try {
        // Validate proposals
        for (const awardId in selectedIndividuals) {
            for (const individual of selectedIndividuals[awardId]) {
                if (individual.soPhieu > formData.value.soCoMat) {
                    Swal.fire({
                        icon: 'error',
                        text: `Số phiếu bầu của ${individual.name} không thể nhiều hơn số người có mặt!`,
                        toast: true,
                        timer: 5000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        position: 'top-end'
                    });
                    return;
                }

                if (individual.soPhieu < 0) {
                    Swal.fire({
                        icon: 'error',
                        text: `Số phiếu bầu của ${individual.name} không thể nhỏ hơn 0!`,
                        toast: true,
                        timer: 5000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        position: 'top-end'
                    });
                    return;
                }

                if (individual.soPhieu === '') {
                    Swal.fire({
                        icon: 'error',
                        text: `Số phiếu bầu của ${individual.name} không được để trống!`,
                        toast: true,
                        timer: 5000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        position: 'top-end'
                    });
                    return;
                }
            }
        }

        for (const award of selectedUnitAwards.value) {
            if (award.soPhieu > formData.value.soCoMat) {
                Swal.fire({
                    icon: 'error',
                    text: `Số phiếu bầu của ${award.name} không thể nhiều hơn số người có mặt!`,
                    toast: true,
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'top-end'
                });
                return;
            }
        }


        const payload = {
            mahoidong: sessionStorage.getItem('maDonVi') + '-' + madot.value,
            madot: madot.value,
            dexuatcanhan: JSON.stringify(selectedIndividuals),
            dexuatdonvi: JSON.stringify(selectedUnitAwards.value)
        };

        const response = await axios.post(`api/dexuat/themdexuattheodot`, payload, {
            headers: {
                Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200) {
            toastSuccess('Lưu đề xuất thành công');
        }
    } catch (error) {
        if (error.response) {
            if (error.response.status === 422) {
                const errors = error.response.data.error;
                let errorMessage = Object.values(errors).flat().join('<br>')
                console.log(errors);
                toastError(errorMessage)
            } else {
                toastError(error.response.data.message)
            }
        }
        else {
            toastError('Có lỗi xảy ra khi lưu đề xuất');
        }
    }
};

const getHoiDongDeXuat = async () => {
    const response = await axios.get(`api/hoidong/thongtinhoidong`, {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
        }
    });

    if (response.status === 200) {
        const data = response.data.data;
        if (data) {
            formData.value.soHuongDan = data.sSoHD;
            formData.value.thoiGian = data.dThoiGianHop;
            formData.value.diaChi = data.sDiaChi;
            formData.value.tongSoNguoi = data.iSoThanhVien;
            formData.value.soTrieuTap = data.iSoNguoiThamDu;
            formData.value.soCoMat = data.iSoNguoiThamDu;
            formData.value.soVang = data.iSoNguoiThamDu - data.iSoNguoiThamDu;
            formData.value.chuTri = data.FK_ChuTri;
            formData.value.thuKy = data.FK_ThuKy;
            formData.value.ghiChu = data.sGhiChu;
            formData.value.namHoc = data.FK_MaDot;
            formData.value.tenFile = data.sTenBienBan;
        }

        // Load existing individual proposals
        data.deXuatCaNhan.forEach(item => {
            const maDanhHieu = item.danhhieu.PK_MaDanhHieu;
            if (!selectedIndividuals[maDanhHieu]) {
                selectedIndividuals[maDanhHieu] = [];
            }

            selectedIndividuals[maDanhHieu].push({
                id: item.tai_khoan.ca_nhan.PK_MaCaNhan,
                taiKhoan: item.tai_khoan.PK_MaTaiKhoan,
                name: item.tai_khoan.ca_nhan.sTenCaNhan,
                displayName: `${item.tai_khoan.ca_nhan.PK_MaCaNhan} - ${item.tai_khoan.ca_nhan.sTenCaNhan}`,
                soPhieu: item.iSoNguoiBau
            });
        });

        // Load existing unit proposals
        data.deXuatDonVi.forEach(item => {
            const maDanhHieu = item.danhhieu.PK_MaDanhHieu;
            if (!selectedUnitAwards.value) {
                selectedUnitAwards.value = [];
            }

            selectedUnitAwards.value.push({
                id: maDanhHieu,
                name: item.danhhieu.sTenDanhHieu,
                soPhieu: item.iSoNguoiBau
            });
        });
    }
}

onMounted(() => {
    getCaNhanTrongDonVi();
    getDotActive();
    getListDanhHieu();
    getHoiDongDeXuat();
});

const getListDanhHieu = async () => {
    const response = await axios.get(`api/danhhieu/listdanhhieutheodot`, {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
        }
    });

    if (response.status === 200) {
        const data = response.data.data;
        data.forEach(element => {
            if (element.sTenLoaiDanhHieu == 'Cá nhân') {
                individualAwards.value.push({
                    id: element.PK_MaDanhHieu,
                    name: element.sTenDanhHieu
                });
            }
            else {
                unitAwards.value.push({
                    id: element.PK_MaDanhHieu,
                    name: element.sTenDanhHieu
                });
            }
        });
    }
    else {
        console.error('Lỗi khi lấy danh sách danh hiệu:', response);
    }
}

const getDotActive = async () => {
    const response = await axios.get(`api/dotthiduakhenthuong/dot-active`, {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
        }
    });

    if (response.status === 200) {
        const data = response.data.data;
        if (data === null) {
            Swal.fire({
                icon: 'warning',
                text: 'Hiện không có đợt thi đua khen thưởng nào được kích hoạt!',
                confirmButtonText: 'OK'
            })
        }
        else {
            madot.value = data.PK_MaDot;
        }
    }
    else {
        console.error('Lỗi khi lấy đợt thi đua khen thưởng:', response);
    }
}

const handleSelectedIndividualsChange = (val, awardId) => {
    // Kiểm tra nếu các cá nhân trong 'val' đã được chọn ở danh hiệu khác
    const awardsToCheck = [1, 2];

    if (awardsToCheck.includes(awardId)) {
        const selectedInOtherAwards = awardsToCheck
            .filter(key => key !== awardId)  // Loại bỏ danh hiệu hiện tại
            .some((key) => {
                // Kiểm tra nếu cá nhân trong 'val' đã có trong danh hiệu khác
                return val.some(individual => {
                    // Kiểm tra nếu cá nhân đã có trong danh hiệu khác
                    return selectedIndividuals[key]?.some(selected => selected.id === individual.id);
                });
            });

        // Nếu có cá nhân trùng, alert và không thay đổi dữ liệu
        if (selectedInOtherAwards) {
            Swal.fire({
                icon: 'error',
                text: 'Cá nhân này đã được chọn ở danh hiệu khác!',
                toast: true,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end'
            });
            return; // Dừng hàm, không cho phép thêm cá nhân vào danh hiệu này
        }
    }

    // Nếu không trùng, thì cập nhật danh sách cá nhân cho danh hiệu này
    selectedIndividuals[awardId] = val.map(individual => ({
        ...individual,
        soPhieu: individual.soPhieu || 0
    }));
};

const handleSelectedUnitAwardsChange = (val) => {
    const ids = selectedUnitAwards.value.map(v => v.id);

    if ((val.id === 4 && ids.includes(5)) || (val.id === 5 && ids.includes(4))) {
        Swal.fire({
            icon: 'error',
            text: 'Không thể chọn cùng lúc 2 danh hiệu "Tập thể lao động tiên tiến" và "Tập thể lao động xuất sắc"',
            toast: true,
            timer: 5000,
            timerProgressBar: true,
            showConfirmButton: false,
            position: 'top-end'
        });

        // Gỡ thằng vừa chọn ra khỏi selectedUnitAwards
        selectedUnitAwards.value = selectedUnitAwards.value.filter(v => v.id !== val.id)
    }
};
</script>

<style scoped>
.card {
    margin-top: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-group {
    margin-bottom: 1rem;
}

label.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.text-success {
    color: #28a745 !important;
}
</style>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>