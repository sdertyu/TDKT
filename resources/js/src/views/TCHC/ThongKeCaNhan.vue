<template>
    <div class="card m-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0"><i class="bi bi-person-vcard me-2"></i>Báo cáo thống kê theo cá nhân</h4>
            <div>
                <Button icon="bi bi-file-earmark-excel" label="Xuất Excel" class="p-button-success" />
                <Button icon="bi bi-printer" label="In báo cáo" class="p-button-info ms-2" />
            </div>
        </div>

        <div class="card-body">
            <!-- Bộ lọc -->
            <div class="filter-section mb-4 p-3 border rounded bg-light">
                <h5 class="mb-3"><i class="bi bi-funnel me-2"></i>Bộ lọc báo cáo</h5>
                <div class="row g-3">
                    <!-- Bộ lọc năm học (đợt) -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Năm học (Đợt)</label>
                        <MultiSelect v-model="filters.namHoc" :options="namHocOptions" optionLabel="name"
                            placeholder="Chọn năm học" class="w-100" />
                    </div>

                    <!-- Bộ lọc đơn vị -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Đơn vị</label>
                        <MultiSelect v-model="filters.donVi" :options="donViOptions" optionLabel="name"
                            placeholder="Chọn đơn vị" class="w-100" />
                    </div>

                    <!-- Bộ lọc danh hiệu -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Danh hiệu</label>
                        <MultiSelect v-model="filters.danhHieu" :options="danhHieuOptions" optionLabel="name"
                            placeholder="Chọn danh hiệu" class="w-100" />
                    </div>

                    <!-- Bộ lọc cấp danh hiệu -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Cấp danh hiệu</label>
                        <MultiSelect v-model="filters.capDanhHieu" :options="capDanhHieuOptions" optionLabel="name"
                            placeholder="Chọn cấp danh hiệu" class="w-100" />
                    </div>

                    <!-- Nút tìm kiếm -->
                    <div class="col-12 text-end">
                        <Button label="Áp dụng bộ lọc" icon="bi bi-search" class="p-button-primary"
                            @click="applyFilters" />
                        <Button label="Xóa bộ lọc" icon="bi bi-x-circle" class="p-button-secondary ms-2"
                            @click="resetFilters" />
                    </div>
                </div>
            </div>

            <!-- Khu vực biểu đồ -->
            <div class="charts-section mb-4">
                <div class="row">
                    <!-- Biểu đồ cột: Số lượng danh hiệu theo năm -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Thống kê danh hiệu theo năm học</h5>
                            </div>
                            <div class="card-body">
                                <canvas ref="yearlyChartRef" height="260"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Biểu đồ cột: Top cá nhân có nhiều danh hiệu -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Top 10 cá nhân có nhiều danh hiệu</h5>
                            </div>
                            <div class="card-body">
                                <canvas ref="individualChartRef" height="260"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Biểu đồ tròn: Phân bố danh hiệu -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Phân bố theo loại danh hiệu</h5>
                            </div>
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <canvas ref="awardChartRef" height="230"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Biểu đồ tròn: Phân bố theo đơn vị -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Phân bố theo đơn vị</h5>
                            </div>
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <canvas ref="unitChartRef" height="230"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng chi tiết -->
            <div class="table-section">
                <h5 class="mb-3"><i class="bi bi-table me-2"></i>Dữ liệu chi tiết theo cá nhân</h5>
                <DataTable :value="filteredData" v-model:filters="tableFilters" :paginator="true" :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    :globalFilterFields="['hoTen', 'donVi', 'danhHieu', 'namHoc', 'capDanhHieu']" stripedRows rowHover
                    class="p-datatable-sm" responsiveLayout="scroll">
                    <template #header>
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <IconField class="w-100 md:w-25">
                                <InputIcon>
                                    <i class="bi bi-search" />
                                </InputIcon>
                                <InputText v-model="tableFilters['global'].value" placeholder="Tìm kiếm"
                                    class="w-100" />
                            </IconField>
                            <div class="text-muted">
                                <strong>Tổng số:</strong> {{ filteredData.length }} bản ghi
                            </div>
                        </div>
                    </template>

                    <Column field="stt" header="STT" style="width: 70px" bodyClass="text-center">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
                    <Column field="hoTen" header="Họ và tên" sortable>
                        <template #body="slotProps">
                            <span class="fw-semibold">{{ slotProps.data.hoTen }}</span>
                        </template>
                    </Column>
                    <Column field="donVi" header="Đơn vị" sortable>
                        <template #body="slotProps">
                            <span class="text-secondary">{{ slotProps.data.donVi }}</span>
                        </template>
                    </Column>
                    <Column field="danhHieu" header="Danh hiệu" sortable>
                        <template #body="slotProps">
                            {{ slotProps.data.danhHieu }}
                        </template>
                    </Column>
                    <Column field="namHoc" header="Năm học" sortable style="width: 130px">
                        <template #body="slotProps">
                            <span class="text-secondary">{{ slotProps.data.namHoc }}</span>
                        </template>
                    </Column>
                    <Column field="hinhThuc" header="Hình thức" style="width: 130px">
                        <template #body="slotProps">
                            <span :class="getHinhThucBadgeClass(slotProps.data.hinhThuc)">
                                {{ slotProps.data.hinhThuc }}
                            </span>
                        </template>
                    </Column>
                    <Column field="capDanhHieu" header="Cấp danh hiệu" style="width: 150px">
                        <template #body="slotProps">
                            <span :class="getCapDanhHieuBadgeClass(slotProps.data.capDanhHieu)">
                                {{ slotProps.data.capDanhHieu }}
                            </span>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center p-4">
                            <i class="bi bi-search fs-3 text-muted"></i>
                            <p class="mt-2">Không tìm thấy kết quả nào phù hợp với điều kiện lọc.</p>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dropdown from 'primevue/dropdown';
import MultiSelect from 'primevue/multiselect';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Button from 'primevue/button';
import { FilterMatchMode } from '@primevue/core/api';
import axios from 'axios';
import Chart from 'chart.js/auto';

// Biến tham chiếu đến các canvas cho biểu đồ
const yearlyChartRef = ref(null);
const individualChartRef = ref(null);
const awardChartRef = ref(null);
const unitChartRef = ref(null);
let yearlyChart = null;
let individualChart = null;
let awardChart = null;
let unitChart = null;

// Dữ liệu bộ lọc
const filters = ref({
    namHoc: [],
    donVi: [],
    danhHieu: [],
    capDanhHieu: []
});

// Danh sách tùy chọn cho bộ lọc
const namHocOptions = ref([]);

const donViOptions = ref([]);

const danhHieuOptions = ref([]);

const capDanhHieuOptions = ref([]);

// Dữ liệu bảng và bộ lọc
const allData = ref([]);
const filteredData = ref([]);
const tableFilters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

// Dữ liệu mẫu cho testing
const sampleData = [
    {
        id: 1,
        hoTen: 'Nguyễn Văn An',
        donVi: 'Khoa Công nghệ thông tin',
        danhHieu: 'Chiến sĩ thi đua cơ sở',
        namHoc: '2022-2023',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp trường',
        ngayTrao: '2023-05-19'
    },
    {
        id: 2,
        hoTen: 'Trần Thị Bình',
        donVi: 'Khoa Điện - Điện tử',
        danhHieu: 'Giáo viên dạy giỏi',
        namHoc: '2022-2023',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp tỉnh',
        ngayTrao: '2023-06-05'
    },
    {
        id: 3,
        hoTen: 'Lê Văn Cường',
        donVi: 'Phòng Đào tạo',
        danhHieu: 'Chiến sĩ thi đua cấp tỉnh',
        namHoc: '2022-2023',
        hinhThuc: 'Đột xuất',
        capDanhHieu: 'Cấp tỉnh',
        ngayTrao: '2023-04-30'
    },
    {
        id: 4,
        hoTen: 'Phạm Thị Dung',
        donVi: 'Phòng Tổ chức hành chính',
        danhHieu: 'Lao động tiên tiến',
        namHoc: '2023-2024',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp trường',
        ngayTrao: '2024-05-20'
    },
    {
        id: 5,
        hoTen: 'Nguyễn Văn An',
        donVi: 'Khoa Công nghệ thông tin',
        danhHieu: 'Chiến sĩ thi đua cơ sở',
        namHoc: '2023-2024',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp trường',
        ngayTrao: '2024-05-19'
    },
    {
        id: 6,
        hoTen: 'Hoàng Thị Hương',
        donVi: 'Khoa Cơ khí',
        danhHieu: 'Giáo viên dạy giỏi',
        namHoc: '2023-2024',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp bộ',
        ngayTrao: '2024-06-10'
    },
    {
        id: 7,
        hoTen: 'Vũ Đức Long',
        donVi: 'Phòng Kế hoạch tài chính',
        danhHieu: 'Chiến sĩ thi đua cấp tỉnh',
        namHoc: '2022-2023',
        hinhThuc: 'Đột xuất',
        capDanhHieu: 'Cấp tỉnh',
        ngayTrao: '2023-11-15'
    },
    {
        id: 8,
        hoTen: 'Nguyễn Thị Mai',
        donVi: 'Khoa Điện - Điện tử',
        danhHieu: 'Lao động tiên tiến',
        namHoc: '2023-2024',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp trường',
        ngayTrao: '2024-05-30'
    },
    {
        id: 9,
        hoTen: 'Lê Văn Nam',
        donVi: 'Khoa Cơ khí',
        danhHieu: 'Chiến sĩ thi đua cơ sở',
        namHoc: '2022-2023',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp trường',
        ngayTrao: '2023-05-19'
    },
    {
        id: 10,
        hoTen: 'Trần Văn Phúc',
        donVi: 'Phòng Đào tạo',
        danhHieu: 'Giáo viên dạy giỏi',
        namHoc: '2024-2025',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp tỉnh',
        ngayTrao: '2025-06-05'
    },
    {
        id: 11,
        hoTen: 'Phạm Thị Quỳnh',
        donVi: 'Khoa Công nghệ thông tin',
        danhHieu: 'Lao động tiên tiến',
        namHoc: '2024-2025',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp trường',
        ngayTrao: '2025-05-20'
    },
    {
        id: 12,
        hoTen: 'Trần Thị Bình',
        donVi: 'Khoa Điện - Điện tử',
        danhHieu: 'Chiến sĩ thi đua cơ sở',
        namHoc: '2023-2024',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp trường',
        ngayTrao: '2024-05-25'
    },
    {
        id: 13,
        hoTen: 'Hoàng Thị Hương',
        donVi: 'Khoa Cơ khí',
        danhHieu: 'Chiến sĩ thi đua cấp tỉnh',
        namHoc: '2024-2025',
        hinhThuc: 'Đột xuất',
        capDanhHieu: 'Cấp tỉnh',
        ngayTrao: '2025-03-15'
    },
    {
        id: 14,
        hoTen: 'Nguyễn Văn An',
        donVi: 'Khoa Công nghệ thông tin',
        danhHieu: 'Chiến sĩ thi đua cấp bộ',
        namHoc: '2024-2025',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp bộ',
        ngayTrao: '2025-07-10'
    },
    {
        id: 15,
        hoTen: 'Lê Văn Cường',
        donVi: 'Phòng Đào tạo',
        danhHieu: 'Lao động tiên tiến',
        namHoc: '2024-2025',
        hinhThuc: 'Theo đợt',
        capDanhHieu: 'Cấp trường',
        ngayTrao: '2025-05-18'
    }
];

// Hàm gọi API lấy dữ liệu (tạm thời sử dụng dữ liệu mẫu)
const fetchData = async () => {
    try {
        const [namHoc, danhHieu, capDanhHieu, donVi, dataThongKe] = await Promise.all([
            axios.get('/api/baocaothongke/danhsachnamhoc', {
                headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
            }),
            axios.get('/api/baocaothongke/danhsachdanhhieu', {
                headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
            }),
            axios.get('/api/baocaothongke/danhsachcapdanhhieu', {
                headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
            }),
            axios.get('/api/baocaothongke/danhsachdonvi', {
                headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
            }),
            axios.get('/api/baocaothongke/datathongkecanhan', {
                headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
            })
        ]);


        if (namHoc.status === 200) {
            namHocOptions.value = namHoc.data.data.map(item => ({
                name: item.namHoc,
                code: item.namHoc
            }));
        }

        if (danhHieu.status === 200) {
            danhHieuOptions.value = danhHieu.data.data.map(item => ({
                name: item.tenDanhHieu,
                code: item.maDanhHieu
            }));
        }

        if (capDanhHieu.status === 200) {
            capDanhHieuOptions.value = capDanhHieu.data.data.map(item => ({
                name: item.tenCap,
                code: item.maCap
            }));
        }

        if (donVi.status === 200) {
            donViOptions.value = donVi.data.data.map(item => ({
                name: item.tenDonVi,
                code: item.maDonVi
            }));
        }

        if (dataThongKe.status === 200) {
            allData.value = dataThongKe.data.data.map(item => ({
                id: 15,
                hoTen: item.hoTen,
                donVi: item.donVi,
                maDanhHieu: item.maDanhHieu,
                danhHieu: item.danhHieu,
                namHoc: item.namHoc,
                hinhThuc: item.hinhThuc,
                capDanhHieu: item.capDanhHieu,
                loai: item.loai,
            }));
            filteredData.value = [...allData.value];
        }
        // allData.value = sampleData;
        // filteredData.value = [...sampleData];
        initCharts();
    } catch (error) {
        console.error('Error fetching data:', error);
    }
};

// Hàm áp dụng bộ lọc
const applyFilters = () => {
    filteredData.value = allData.value.filter(item => {
        // Lọc theo năm học
        if (filters.value.namHoc && filters.value.namHoc.length > 0) {
            if (!filters.value.namHoc.some(n => n.name === item.namHoc)) {
                return false;
            }
        }

        // Lọc theo đơn vị
        if (filters.value.donVi && filters.value.donVi.length > 0) {
            if (!filters.value.donVi.some(d => d.name === item.donVi)) {
                return false;
            }
        }

        // Lọc theo danh hiệu
        if (filters.value.danhHieu && filters.value.danhHieu.length > 0) {
            if (!filters.value.danhHieu.some(d => d.name === item.danhHieu)) {
                return false;
            }
        }

        // Lọc theo cấp danh hiệu
        if (filters.value.capDanhHieu && filters.value.capDanhHieu.length > 0) {
            if (!filters.value.capDanhHieu.some(c => c.name === item.capDanhHieu)) {
                return false;
            }
        }

        return true;
    });

    // Cập nhật biểu đồ
    updateCharts();
};

// Hàm reset bộ lọc
const resetFilters = () => {
    filters.value = {
        namHoc: [],
        donVi: [],
        danhHieu: [],
        capDanhHieu: []
    };
    filteredData.value = [...allData.value];
    updateCharts();
};

// Hàm khởi tạo biểu đồ
const initCharts = () => {
    // Biểu đồ cột theo năm học
    if (yearlyChartRef.value) {
        const ctx = yearlyChartRef.value.getContext('2d');

        // Tính toán dữ liệu cho biểu đồ
        const yearlyData = countByYear();

        yearlyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(yearlyData),
                datasets: [{
                    label: 'Số lượng danh hiệu',
                    data: Object.values(yearlyData),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ cột theo cá nhân (top 10)
    if (individualChartRef.value) {
        const ctx = individualChartRef.value.getContext('2d');

        // Tính toán dữ liệu top 10 cá nhân
        const individualData = countByIndividual(10);

        individualChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(individualData),
                datasets: [{
                    label: 'Số lượng danh hiệu',
                    data: Object.values(individualData),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ tròn theo loại danh hiệu
    if (awardChartRef.value) {
        const ctx = awardChartRef.value.getContext('2d');

        // Tính toán dữ liệu cho biểu đồ
        const awardData = countByAward();

        awardChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(awardData),
                datasets: [{
                    data: Object.values(awardData),
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }

    // Biểu đồ tròn theo đơn vị
    if (unitChartRef.value) {
        const ctx = unitChartRef.value.getContext('2d');

        // Tính toán dữ liệu cho biểu đồ
        const unitData = countByUnit();

        unitChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: Object.keys(unitData),
                datasets: [{
                    data: Object.values(unitData),
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
};

// Hàm cập nhật biểu đồ
const updateCharts = () => {
    if (yearlyChart) {
        const yearlyData = countByYear();
        yearlyChart.data.labels = Object.keys(yearlyData);
        yearlyChart.data.datasets[0].data = Object.values(yearlyData);
        yearlyChart.update();
    }

    if (individualChart) {
        const individualData = countByIndividual(10);
        individualChart.data.labels = Object.keys(individualData);
        individualChart.data.datasets[0].data = Object.values(individualData);
        individualChart.update();
    }

    if (awardChart) {
        const awardData = countByAward();
        awardChart.data.labels = Object.keys(awardData);
        awardChart.data.datasets[0].data = Object.values(awardData);
        awardChart.update();
    }

    if (unitChart) {
        const unitData = countByUnit();
        unitChart.data.labels = Object.keys(unitData);
        unitChart.data.datasets[0].data = Object.values(unitData);
        unitChart.update();
    }
};

// Hàm đếm số lượng danh hiệu theo năm học
const countByYear = () => {
    const data = {};
    filteredData.value.forEach(item => {
        if (!data[item.namHoc]) {
            data[item.namHoc] = 0;
        }
        data[item.namHoc]++;
    });
    return data;
};

// Hàm đếm số lượng danh hiệu theo cá nhân (lấy top N)
const countByIndividual = (topN = 10) => {
    const data = {};

    // Đếm số lượng danh hiệu cho mỗi cá nhân
    filteredData.value.forEach(item => {
        if (!data[item.hoTen]) {
            data[item.hoTen] = 0;
        }
        data[item.hoTen]++;
    });

    // Sắp xếp và lấy top N
    const sortedEntries = Object.entries(data).sort((a, b) => b[1] - a[1]);
    const topEntries = sortedEntries.slice(0, topN);

    // Chuyển đổi trở lại thành đối tượng
    const result = {};
    topEntries.forEach(entry => {
        result[entry[0]] = entry[1];
    });

    return result;
};

// Hàm đếm số lượng theo loại danh hiệu
const countByAward = () => {
    const data = {};
    filteredData.value.forEach(item => {
        let key = '';
        if(item.danhHieu == "Giấy khen của hiệu trưởng") {
            key = item.danhHieu + ' - ' + item.loai + ' - ' +item.hinhThuc;
        }
        else {
            key = item.danhHieu;
        }
        if (!data[key]) {
            data[key] = 0;
        }
        data[key]++;
    });
    return data;
};

// Hàm đếm số lượng theo đơn vị
const countByUnit = () => {
    const data = {};
    filteredData.value.forEach(item => {
        if (!data[item.donVi]) {
            data[item.donVi] = 0;
        }
        data[item.donVi]++;
    });
    return data;
};

// Hàm định dạng ngày tháng
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
};

// Hàm xác định class cho hình thức
const getHinhThucBadgeClass = (hinhThuc) => {
    switch (hinhThuc?.toLowerCase()) {
        case 'theo đợt':
            return 'badge bg-success text-white fw-normal px-2 py-1';
        case 'đột xuất':
            return 'badge bg-primary text-white fw-normal px-2 py-1';
        default:
            return 'badge bg-secondary text-white fw-normal px-2 py-1';
    }
};

// Hàm xác định class cho cấp danh hiệu
const getCapDanhHieuBadgeClass = (capDanhHieu) => {
    switch (capDanhHieu?.toLowerCase()) {
        case 'cấp trường':
            return 'badge bg-primary-subtle text-primary fw-normal px-2 py-1';
        case 'cấp tỉnh':
            return 'badge bg-success-subtle text-success fw-normal px-2 py-1';
        case 'cấp bộ':
            return 'badge bg-warning-subtle text-warning fw-normal px-2 py-1';
        case 'cấp nhà nước':
            return 'badge bg-danger-subtle text-danger fw-normal px-2 py-1';
        default:
            return 'badge bg-secondary-subtle text-secondary fw-normal px-2 py-1';
    }
};

// Theo dõi thay đổi trong bộ lọc và cập nhật dữ liệu
watch(filters, () => {
    applyFilters();
}, { deep: true });

// Gọi API khi component được tạo
onMounted(() => {
    fetchData();
});
</script>

<style scoped>
.filter-section {
    background-color: #f8f9fa;
    border-radius: 8px;
}

.charts-section .card {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    overflow: hidden;
}

.p-datatable .p-datatable-thead>tr>th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.p-datatable-wrapper {
    border-radius: 0.375rem;
    overflow: hidden;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        align-items: start;
    }

    .card-header div {
        margin-top: 1rem;
    }
}
</style>