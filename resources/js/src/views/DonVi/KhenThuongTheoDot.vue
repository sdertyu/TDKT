<template>
    <div class="container-fluid" v-if="madot">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-uppercase">Biên bản bình xét thi đua năm học {{ madot }}</h3>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submitForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="soHuongDan">Theo hướng dẫn số:</label>
                                        <input type="text" class="form-control" id="soHuongDan"
                                            v-model="formData.soHuongDan" placeholder="Nhập số hướng dẫn">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="thoiGian">Vào hồi:</label>
                                        <input type="datetime-local" class="form-control" id="thoiGian"
                                            v-model="formData.thoiGian">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="diaChi">Địa chỉ:</label>
                                <input type="text" class="form-control" id="diaChi" v-model="formData.diaChi"
                                    placeholder="Nhập địa chỉ">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tongSoNguoi">Tổng số người trong đơn vị:</label>
                                        <input type="number" class="form-control" id="tongSoNguoi"
                                            v-model="formData.tongSoNguoi" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="soTrieuTap">Số người được triệu tập:</label>
                                        <input type="number" class="form-control" id="soTrieuTap"
                                            v-model="formData.soTrieuTap" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="soCoMat">Số người có mặt:</label>
                                        <input type="number" class="form-control" id="soCoMat"
                                            v-model="formData.soCoMat" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="soVang">Số người vắng:</label>
                                        <input type="number" class="form-control" id="soVang" v-model="formData.soVang"
                                            min="0" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="ghiChu">Ghi chú:</label>
                                <textarea class="form-control" id="ghiChu" v-model="formData.ghiChu"
                                    rows="3"></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="chuTri">Chủ trì:</label>
                                        <select class="form-select" id="chuTri" v-model="formData.chuTri">
                                            <option value="" disabled selected>Chọn người chủ trì</option>
                                            <option v-for="person in individuals" :key="person.id"
                                                :value="person.taiKhoan">
                                                {{ person.displayName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="thuKy">Thư ký:</label>
                                        <select class="form-select" id="thuKy" v-model="formData.thuKy">
                                            <option value="" disabled selected>Chọn thư ký</option>
                                            <option v-for="person in individuals" :key="person.id"
                                                :value="person.taiKhoan">
                                                {{ person.displayName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="form-group mb-3">
                                <label for="namHoc">Bình xét cho năm học:</label>
                                <input type="text" disabled :value="madot" class="form-control">
                            </div> -->

                            <!-- Phần bình bầu cá nhân -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="mb-0">Bình bầu danh hiệu cá nhân</h4>
                                </div>
                                <div class="card-body">
                                    <!-- Lao động tiên tiến -->
                                    <div class="mb-4" v-for="(award, index) in individualAwards" :key="index">
                                        <h5 class="border-bottom pb-2 fst-italic text-danger">Danh hiệu {{ award.name }}
                                        </h5>
                                        <div class="mb-2 align-items-center">
                                            <multiselect :model-value="selectedIndividuals[award.id]"
                                                :options="individuals" :multiple="true" track-by="id"
                                                label="displayName" placeholder="Chọn cá nhân"
                                                @update:modelValue="val => handleSelectedIndividualsChange(val, award.id)">
                                            </multiselect>
                                        </div>

                                        <div class="row">

                                            <div v-for="(individual, keyAward) in selectedIndividuals[award.id]"
                                                :key="keyAward" class="col-md-6 mb-3">
                                                <div class="row align-items-center">
                                                    <label :for="individual.id" class="col-4 col-form-label mb-0">
                                                        {{ individual.name }}
                                                    </label>
                                                    <div class="col-8">
                                                        <div class="input-group">
                                                            <input type="number" :id="individual.id" class="form-control"
                                                                placeholder="Số phiếu bầu" v-model="individual.soPhieu">
                                                            <span class="input-group-text fw-bold" 
                                                                :class="{'bg-success text-white': (individual.soPhieu / formData.soCoMat * 100) >= 50}">
                                                                {{ formData.soCoMat > 0 ? ((individual.soPhieu / formData.soCoMat) * 100).toFixed(1) : 0 }}%
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Phần bình bầu tập thể -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="mb-0">Bình bầu danh hiệu tập thể</h4>
                                </div>
                                <div class="card-body">
                                    <!-- Tập thể lao động tiên tiến/xuất sắc -->
                                    <div class="mb-4">
                                        <h5 class="border-bottom pb-2">Danh hiệu tập thể</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Danh hiệu đề xuất:</label>
                                                    <multiselect v-model="selectedUnitAwards" :options="unitAwards"
                                                        :multiple="true" track-by="id" label="name"
                                                        placeholder="Chọn danh hiệu"
                                                        @select="handleSelectedUnitAwardsChange">
                                                    </multiselect>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div v-for="(award, index) in selectedUnitAwards" :key="index"
                                                    class="col-md-6 mb-3">
                                                    <div class="row align-items-center">
                                                        <label :for="award.id" class="col-4 col-form-label mb-0">
                                                            {{ award.name }}
                                                        </label>
                                                        <div class="col-8">
                                                            <div class="input-group">
                                                                <input type="number" :id="award.id" class="form-control"
                                                                    placeholder="Số phiếu bầu"
                                                                    v-model="selectedUnitAwards[index]['soPhieu']" required>
                                                                <span class="input-group-text fw-bold"
                                                                    :class="{'bg-success text-white': (selectedUnitAwards[index]['soPhieu'] / formData.soCoMat * 100) >= 50}">
                                                                    {{ formData.soCoMat > 0 ? ((selectedUnitAwards[index]['soPhieu'] / formData.soCoMat) * 100).toFixed(1) : 0 }}%
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="mb-0">Biên bản bình xét</h4>
                                </div>
                                <div class="card-body">
                                    <input type="file" class="form-control" @change="handleFileUpload($event)">
                                    <p v-if="formData.tenFile" class="mt-2 fst-italic">
                                        <i class="fas fa-file-alt me-1"></i> File đã tải lên: <strong>{{
                                            formData.tenFile }}</strong>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Lưu biên bản</button>
                                <button type="button" class="btn btn-secondary ms-2" @click="resetForm">Làm
                                    mới</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';

import { watch } from 'vue';
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
    caNhan: {
        laoDongTienTien: [],
        chienSiThiDua: [],
        giayKhen: []
    },
    tapThe: {
        danhHieu: '',
        soPhieu: 0,
        giayKhen: {
            soPhieu: 0
        }
    },
    tenFile: ''
});

const handleFileUpload = (event) => {
    formData.value.bienBan = event.target.files[0];
};

// Tính số người vắng
const soVang = computed(() => {
    return formData.value.soTrieuTap - formData.value.soCoMat;

});

// Lấy danh sách cá nhân trong đơn vị
const getCaNhanTrongDonVi = async () => {
    // console.log(localStorage);
    const response = await axios.get(`api/taikhoan/caNhanTrongDonVi/${localStorage.getItem('maDonVi')}`, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
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

        formData.value.caNhan = selectedIndividuals;
        formData.value.tapThe = selectedUnitAwards.value;

        let form = new FormData();
        form.append('mahoidong', localStorage.getItem('maDonVi') + '-' + madot.value);
        form.append('madot', madot.value);
        form.append('machutri', formData.value.chuTri);
        form.append('mathuky', formData.value.thuKy);
        form.append('thoigian', formData.value.thoiGian);
        form.append('diadiem', formData.value.diaChi);
        form.append('maloaihoidong', 1); //Hội đồng đơn vị
        form.append('hinhthuchoidong', 1); //Theo đợt
        form.append('songuoithamdu', formData.value.soCoMat);
        form.append('sothanhvien', formData.value.tongSoNguoi);
        form.append('sohd', formData.value.soHuongDan);
        form.append('ghichu', formData.value.ghiChu);
        form.append('bienban', formData.value.bienBan);
        form.append('dexuatcanhan', JSON.stringify(formData.value.caNhan));
        form.append('dexuatdonvi', JSON.stringify(formData.value.tapThe));


        const response = await axios.post(`api/hoidong/add`, form,
            {
                'Content-Type': 'multipart/form-data',
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`
                }
            });

        if (response.status === 200) {
            Swal.fire({
                icon: 'success',
                text: 'Lưu biên bản thành công!',
                toast: true,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end'
            });
            // resetForm();
        }
        else {
            console.error('Lỗi khi lưu biên bản:', response);
        }
    } catch (error) {

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
        caNhan: {},
        tapThe: {}
    };
};

// Watch changes
const setupWatchers = () => {
    // Sử dụng watch để theo dõi thay đổi
    watch([() => formData.value.soTrieuTap, () => formData.value.soCoMat], () => {
        watchSoNguoi();
    });
};

const getHoiDongDeXuat = async () => {
    const response = await axios.get(`api/hoidong/thongtinhoidong`, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
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
            formData.value.tenFile = data.sTenFile;
        }

        // formData.value.bienBan = data.sBienBan;

        // console.log(data);
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

        console.log(selectedIndividuals);
    }
}

onMounted(() => {
    setupWatchers();
    getCaNhanTrongDonVi();
    getDotActive();
    getListDanhHieu();
    getHoiDongDeXuat();
});

const getListDanhHieu = async () => {
    const response = await axios.get(`api/danhhieu/list`, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    });

    if (response.status === 200) {
        const data = response.data.data;
        // console.log(data);
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
        console.error('Lỗi khi lấy danh sách cá nhân:', response);
    }
}

const getDotActive = async () => {
    const response = await axios.get(`api/dotthiduakhenthuong/dot-active`, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    });

    if (response.status === 200) {
        const data = response.data.data;
        // console.log(data);
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
        console.error('Lỗi khi lấy danh sách cá nhân:', response);
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
            alert("Cá nhân này đã được chọn ở danh hiệu khác!");
            return; // Dừng hàm, không cho phép thêm cá nhân vào danh hiệu này
        }
    }


    // Nếu không trùng, thì cập nhật danh sách cá nhân cho danh hiệu này
    selectedIndividuals[awardId] = val.map(individual => ({
        ...individual,
        soPhieu: individual.soPhieu || 0
    }));

    // Debug log để kiểm tra
    // console.log('updated selectedIndividuals', selectedIndividuals);
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
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-group {
    margin-bottom: 1rem;
}

label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}
</style>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>