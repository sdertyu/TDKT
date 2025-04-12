<template>
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Khen thưởng đột xuất</h1>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Tabs để chuyển đổi giữa khen thưởng cá nhân và đơn vị -->
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: activeTab === 'individual' }" href="#"
                                    @click.prevent="activeTab = 'individual'">
                                    <i class="fas fa-user-award mr-1"></i> Khen thưởng cá nhân
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: activeTab === 'unit' }" href="#"
                                    @click.prevent="activeTab = 'unit'">
                                    <i class="fas fa-building mr-1"></i> Khen thưởng đơn vị
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <!-- Form khen thưởng cá nhân -->
                        <div v-if="activeTab === 'individual'">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user-award mr-2"></i>
                                        Đề xuất khen thưởng cá nhân
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <!-- Danh sách danh hiệu đã chọn -->
                                    <div class="form-group">
                                        <label>Danh hiệu đề xuất</label>
                                        <multiselect v-model="selectedAwardsObjects" :options="individualAwards"
                                            :multiple="true" track-by="id" label="name" placeholder="Chọn danh hiệu"
                                            @input="handleSelectedAwardsChange"></multiselect>
                                        <small class="form-text text-muted">Chọn một hoặc nhiều danh hiệu</small>
                                    </div>

                                    <!-- Hiển thị form cho từng danh hiệu đã chọn -->
                                    <div v-for="award in selectedAwardsObjects" :key="award.id"
                                        class="card card-outline card-info mb-4">
                                        <div class="card-header">
                                            <h3 class="card-title">{{ award.name }}</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool"
                                                    @click="removeAward(award.id)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!-- Chọn cá nhân cho danh hiệu này -->
                                            <div class="form-group">
                                                <label>Chọn cá nhân</label>
                                                <multiselect v-model="awardIndividuals[award.id]" :options="individuals"
                                                    :multiple="true" track-by="id" label="displayName"
                                                    placeholder="Chọn cá nhân" :custom-label="customLabel"
                                                    :searchable="true" :internal-search="false"
                                                    @search-change="searchIndividuals"></multiselect>
                                                <small class="form-text text-muted">Có thể chọn nhiều cá nhân, tìm kiếm
                                                    theo mã hoặc tên</small>
                                            </div>

                                            <!-- Danh sách cá nhân đã chọn cho danh hiệu này -->
                                            <div v-if="awardIndividuals[award.id] && awardIndividuals[award.id].length > 0"
                                                class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 10px">STT</th>
                                                            <th class="text-center">Mã NV</th>
                                                            <th class="text-center">Cá nhân</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(individual, index) in awardIndividuals[award.id]"
                                                            :key="individual.id">
                                                            <td class="text-center">{{ index + 1 }}</td>
                                                            <td>{{ individual.code }}</td>
                                                            <td>{{ individual.name }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action buttons -->
                                    <div class="text-right mt-4">
                                        <button type="button" @click="handleSubmitIndividual" class="btn btn-primary">
                                            <i class="fas fa-paper-plane mr-1"></i> Gửi đề xuất
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form khen thưởng đơn vị -->
                        <div v-if="activeTab === 'unit'">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-building mr-2"></i>
                                        Đề xuất khen thưởng đơn vị
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <!-- Danh hiệu đề xuất cho đơn vị -->
                                    <div class="form-group">
                                        <label>Danh hiệu đề xuất cho đơn vị</label>
                                        <multiselect v-model="selectedUnitAwards" :options="unitAwards" :multiple="true"
                                            track-by="id" label="name" placeholder="Chọn danh hiệu"></multiselect>
                                        <small class="form-text text-muted">Có thể chọn nhiều danh hiệu</small>
                                    </div>

                                    <!-- Danh sách danh hiệu đã chọn -->
                                    <div class="form-group" v-if="selectedUnitAwards.length > 0">
                                        <label>Danh sách danh hiệu đã chọn</label>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 10px">STT</th>
                                                        <th>Danh hiệu</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(award, index) in selectedUnitAwards" :key="award.id">
                                                        <td>{{ index + 1 }}</td>
                                                        <td>{{ award.name }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Action buttons -->
                                    <div class="text-right mt-4">
                                        <button type="button" @click="handleSubmitUnit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane mr-1"></i> Gửi đề xuất
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { get } from 'jquery';
import { ref, reactive, onMounted, computed } from 'vue';
import Multiselect from 'vue-multiselect';

// Tab active
const activeTab = ref('individual');

// Danh sách cá nhân (giả lập dữ liệu)
const individuals = ref([]);

// Danh sách danh hiệu cá nhân
const individualAwards = ref([]);

// Danh sách danh hiệu đơn vị
const unitAwards = ref([]);

// Form state cho cá nhân
const selectedAwardsObjects = ref([]);
const selectedAwards = computed(() => selectedAwardsObjects.value.map(award => award.id));
const awardIndividuals = reactive({});
const awardReasons = reactive({});

// Form state cho đơn vị
const selectedUnitAwards = ref([]);
const unitReason = ref('');

// Custom label cho multiselect
const customLabel = (option) => {
    return `${option.code} - ${option.name}`;
};

// Tìm kiếm cá nhân
const searchIndividuals = (query) => {
    // Trong thực tế, đây sẽ là một API call
    // Ví dụ: axios.get(`/api/individuals?search=${query}`)
    console.log('Searching for:', query);
    // Mô phỏng tìm kiếm local
    if (!query) return;

    // Tìm kiếm theo cả mã nhân viên và tên
    const lowercaseQuery = query.toLowerCase();
    return individuals.value.filter(individual =>
        individual.code.toLowerCase().includes(lowercaseQuery) ||
        individual.name.toLowerCase().includes(lowercaseQuery)
    );
};

// Lấy tên cá nhân từ ID
const getIndividualName = (id) => {
    const individual = individuals.value.find(item => item.id === id);
    return individual ? individual.name : '';
};

// Lấy tên danh hiệu từ ID
const getAwardName = (id) => {
    const individualAward = individualAwards.value.find(item => item.id === id);
    if (individualAward) return individualAward.name;

    const unitAward = unitAwards.value.find(item => item.id === id);
    return unitAward ? unitAward.name : '';
};

// Xử lý khi danh sách danh hiệu thay đổi
const handleSelectedAwardsChange = () => {
    // Khởi tạo dữ liệu cho các danh hiệu mới được chọn
    selectedAwardsObjects.value.forEach(award => {
        if (!awardIndividuals[award.id]) {
            awardIndividuals[award.id] = [];
        }
        if (!awardReasons[award.id]) {
            awardReasons[award.id] = '';
        }
    });

    // Xóa dữ liệu của các danh hiệu đã bỏ chọn
    Object.keys(awardIndividuals).forEach(awardId => {
        if (!selectedAwardsObjects.value.some(award => award.id === awardId)) {
            delete awardIndividuals[awardId];
            delete awardReasons[awardId];
        }
    });
};

// Xóa danh hiệu
const removeAward = (awardId) => {
    selectedAwardsObjects.value = selectedAwardsObjects.value.filter(award => award.id !== awardId);
    delete awardIndividuals[awardId];
    delete awardReasons[awardId];
};

// Xử lý gửi form cá nhân
const handleSubmitIndividual = async () => {
    if (selectedAwardsObjects.value.length === 0) {
        alert('Vui lòng chọn ít nhất một danh hiệu');
        return;
    }

    // Kiểm tra xem mỗi danh hiệu đã chọn có cá nhân và lý do chưa
    let isValid = true;
    const nominations = [];

    selectedAwardsObjects.value.forEach(award => {
        if (!awardIndividuals[award.id] || awardIndividuals[award.id].length === 0) {
            alert(`Vui lòng chọn ít nhất một cá nhân cho danh hiệu "${award.name}"`);
            isValid = false;
            return;
        }

        nominations.push({
            award: award,
            individuals: awardIndividuals[award.id],
            reason: awardReasons[award.id]
        });
    });

    if (!isValid) return;

    try {
        const response = await axios.post('/api/dexuat/themdexuatdotxuat', {
            deXuat: nominations.map(nomination => ({
                danhHieu: nomination.award.id,
                caNhan: nomination.individuals.map(individual => individual.id),
            }))
        }, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200) {
            toastSuccess('Đã gửi đề xuất khen thưởng cá nhân thành công!');
        }
    } catch (error) {
        toastError('Có lỗi xảy ra khi gửi đề xuất khen thưởng cá nhân!');
    }

    // handleResetIndividual();
};

// Xử lý gửi form đơn vị
const handleSubmitUnit = () => {
    // Kiểm tra dữ liệu
    if (selectedUnitAwards.value.length === 0) {
        alert('Vui lòng chọn ít nhất một danh hiệu');
        return;
    }

    // Chuẩn bị dữ liệu gửi đi
    const data = {
        awards: selectedUnitAwards.value,
        reason: unitReason.value
    };

    console.log('Gửi đề xuất khen thưởng đơn vị:', data);
    // Gọi API để gửi dữ liệu
    // axios.post('/api/khen-thuong/don-vi', data)...

    alert('Đã gửi đề xuất khen thưởng đơn vị thành công!');
    handleResetUnit();
};


// Reset form đơn vị
const handleResetUnit = () => {
    selectedUnitAwards.value = [];
    unitReason.value = '';
};

const getThongTinDeXuatDotXuat = async () => {
    const response = await axios.get('/api/dexuat/thongtindexuatdotxuat', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    });

    if (response.status === 200) {
        if (response.data.data) {
            let caNhan = response.data.data.caNhan;
            let donVi = response.data.data.donVi;
            caNhan.forEach(item => {
                if (!selectedAwardsObjects.value.some(award => award.id === item.danhHieu.maDanhHieu)) {
                    selectedAwardsObjects.value.push({
                        id: item.danhHieu.maDanhHieu,
                        name: item.danhHieu.tenDanhHieu
                    });
                }
                if (!awardIndividuals[item.danhHieu.maDanhHieu]) {
                    awardIndividuals[item.danhHieu.maDanhHieu] = []; // Khởi tạo mảng nếu chưa tồn tại
                }
                awardIndividuals[item.danhHieu.maDanhHieu].push({
                    id: item.caNhan.taiKhoan,
                    code: item.caNhan.maCaNhan,
                    name: item.caNhan.tenCaNhan
                });


            });
        }
    } else {
        console.error('Lỗi khi lấy thông tin đề xuất đột xuất:', response.statusText);
    }

}

const getListDanhHieuDotXuat = async () => {
    const list = await axios.get('/api/danhhieu/listdanhhieudotxuat', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    });

    if (list.status === 200) {
        individualAwards.value = list.data.data.caNhan.map(item => ({
            id: item.PK_MaDanhHieu,
            name: item.sTenDanhHieu
        }));
        unitAwards.value = list.data.data.donVi.map(item => ({
            id: item.PK_MaDanhHieu,
            name: item.sTenDanhHieu
        }));
    } else {
        console.error('Lỗi khi lấy danh hiệu đột xuất:', list.statusText);
    }
}

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
            id: item.FK_MaTaiKhoan,
            code: item.PK_MaCaNhan,
            name: item.sTenCaNhan,
            displayName: `${item.PK_MaCaNhan} - ${item.sTenCaNhan}`
        }));
    }
    else {
        console.error('Lỗi khi lấy danh sách cá nhân:', response);
    }
}

// Khởi tạo sau khi component được mount
onMounted(() => {
    getThongTinDeXuatDotXuat();
    getListDanhHieuDotXuat();
    getCaNhanTrongDonVi();
});
</script>

<style scoped>
.card {
    margin-bottom: 1rem;
}

.table-responsive {
    overflow-x: auto;
}
</style>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
