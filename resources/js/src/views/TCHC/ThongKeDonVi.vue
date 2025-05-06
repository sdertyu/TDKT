<template>
    <div class="card m-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0"><i class="bi bi-building me-2"></i>Báo cáo thống kê theo đơn vị</h4>
            <div>
                <Button icon="bi bi-file-earmark-excel" label="Xuất Excel" class="p-button-success"
                    @click="exportExcel" />
                <!-- <Button icon="bi bi-printer" label="In báo cáo" class="p-button-info ms-2" /> -->
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

                    <!-- Bộ lọc loại đơn vị -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Loại đơn vị</label>
                        <MultiSelect v-model="filters.loaiDonVi" :options="donViOptions" optionLabel="name"
                            placeholder="Chọn loại đơn vị" class="w-100" />
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
                    <!-- Biểu đồ cột: Số lượng danh hiệu theo đơn vị -->
                    <div class="col-lg-8 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Thống kê số lượng danh hiệu theo đơn vị</h5>
                            </div>
                            <div class="card-body">
                                <canvas ref="unitChartRef" id="unitChartRef" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Biểu đồ tròn: Tỷ lệ theo loại đơn vị -->
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Tỷ lệ theo loại đơn vị</h5>
                            </div>
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <canvas ref="typeChartRef" id="typeChartRef" height="230"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng chi tiết -->
            <div class="table-section">
                <h5 class="mb-3"><i class="bi bi-table me-2"></i>Dữ liệu chi tiết theo đơn vị</h5>
                <DataTable :value="filteredData" v-model:filters="tableFilters" :paginator="true" :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    :globalFilterFields="['tenDonVi', 'loaiDonVi', 'danhHieu', 'namHoc', 'capDanhHieu']" stripedRows
                    rowHover class="p-datatable-sm" responsiveLayout="scroll">
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
                    <Column field="tenDonVi" header="Tên đơn vị" sortable>
                        <template #body="slotProps">
                            <span class="fw-semibold">{{ slotProps.data.tenDonVi }}</span>
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
import Chart from 'chart.js/auto';

// Biến tham chiếu đến các canvas cho biểu đồ
const unitChartRef = ref(null);
const typeChartRef = ref(null);
let unitChart = null;
let typeChart = null;

// Dữ liệu bộ lọc
const filters = ref({
    namHoc: [],
    loaiDonVi: [],
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
        tenDonVi: 'Khoa Công nghệ thông tin',
        loaiDonVi: 'Khoa',
        danhHieu: 'Tập thể lao động xuất sắc',
        namHoc: '2022-2023',
        capDanhHieu: 'Cấp trường',
        soLuongDat: 1
    },
    {
        id: 2,
        tenDonVi: 'Phòng Đào tạo',
        loaiDonVi: 'Phòng',
        danhHieu: 'Tập thể lao động xuất sắc',
        namHoc: '2022-2023',
        capDanhHieu: 'Cấp tỉnh',
        soLuongDat: 1
    },
    {
        id: 3,
        tenDonVi: 'Khoa Điện - Điện tử',
        loaiDonVi: 'Khoa',
        danhHieu: 'Tập thể lao động xuất sắc',
        namHoc: '2022-2023',
        capDanhHieu: 'Cấp bộ',
        soLuongDat: 1
    },
    {
        id: 4,
        tenDonVi: 'Trung tâm Tin học - Ngoại ngữ',
        loaiDonVi: 'Trung tâm',
        danhHieu: 'Đơn vị tiêu biểu',
        namHoc: '2022-2023',
        capDanhHieu: 'Cấp trường',
        soLuongDat: 1
    },
    {
        id: 5,
        tenDonVi: 'Phòng Tổ chức hành chính',
        loaiDonVi: 'Phòng',
        danhHieu: 'Tập thể lao động tiên tiến',
        namHoc: '2022-2023',
        capDanhHieu: 'Cấp trường',
        soLuongDat: 1
    },
    {
        id: 6,
        tenDonVi: 'Khoa Cơ khí',
        loaiDonVi: 'Khoa',
        danhHieu: 'Tập thể lao động tiên tiến',
        namHoc: '2022-2023',
        capDanhHieu: 'Cấp trường',
        soLuongDat: 1
    },
    {
        id: 7,
        tenDonVi: 'Bộ môn Toán',
        loaiDonVi: 'Bộ môn',
        danhHieu: 'Tập thể lao động tiên tiến',
        namHoc: '2023-2024',
        capDanhHieu: 'Cấp trường',
        soLuongDat: 1
    },
    {
        id: 8,
        tenDonVi: 'Khoa Công nghệ thông tin',
        loaiDonVi: 'Khoa',
        danhHieu: 'Tập thể lao động xuất sắc',
        namHoc: '2023-2024',
        capDanhHieu: 'Cấp trường',
        soLuongDat: 1
    },
    {
        id: 9,
        tenDonVi: 'Phòng Kế hoạch tài chính',
        loaiDonVi: 'Phòng',
        danhHieu: 'Tập thể lao động xuất sắc',
        namHoc: '2023-2024',
        capDanhHieu: 'Cấp tỉnh',
        soLuongDat: 1
    },
    {
        id: 10,
        tenDonVi: 'Trung tâm Thông tin thư viện',
        loaiDonVi: 'Trung tâm',
        danhHieu: 'Đơn vị tiêu biểu',
        namHoc: '2023-2024',
        capDanhHieu: 'Cấp trường',
        soLuongDat: 1
    },
    {
        id: 11,
        tenDonVi: 'Phòng Đào tạo',
        loaiDonVi: 'Phòng',
        danhHieu: 'Tập thể lao động xuất sắc',
        namHoc: '2024-2025',
        capDanhHieu: 'Cấp nhà nước',
        soLuongDat: 1
    },
    {
        id: 12,
        tenDonVi: 'Khoa Điện - Điện tử',
        loaiDonVi: 'Khoa',
        danhHieu: 'Tập thể lao động xuất sắc',
        namHoc: '2024-2025',
        capDanhHieu: 'Cấp bộ',
        soLuongDat: 1
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
            axios.get('/api/baocaothongke/datathongkedonvi', {
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
                tenDonVi: item.tenDonVi,
                // loaiDonVi: 'Khoa',
                danhHieu: item.danhHieu,
                namHoc: item.namHoc,
                capDanhHieu: item.capDanhHieu,
                soLuongDat: item.soLuongDat,
                hinhThuc: item.hinhThuc,
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

        // Lọc theo loại đơn vị
        if (filters.value.loaiDonVi && filters.value.loaiDonVi.length > 0) {
            if (!filters.value.loaiDonVi.some(d => d.name === item.loaiDonVi)) {
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
        loaiDonVi: [],
        danhHieu: [],
        capDanhHieu: []
    };
    filteredData.value = [...allData.value];
    updateCharts();
};

// Hàm khởi tạo biểu đồ
const initCharts = () => {
    // Biểu đồ cột theo đơn vị
    if (unitChartRef.value) {
        const ctx = unitChartRef.value.getContext('2d');

        // Tính toán dữ liệu cho biểu đồ - top 10 đơn vị
        const unitData = countByUnit(10);

        unitChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(unitData),
                datasets: [{
                    label: 'Số lượng danh hiệu',
                    data: Object.values(unitData),
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
                },
                indexAxis: 'y',
                plugins: {
                    title: {
                        display: true,
                        text: 'Top 10 đơn vị có nhiều danh hiệu nhất'
                    }
                }
            }
        });
    }

    // Biểu đồ tròn theo loại đơn vị
    if (typeChartRef.value) {
        const ctx = typeChartRef.value.getContext('2d');

        // Tính toán dữ liệu cho biểu đồ
        const typeData = countByType();

        typeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(typeData),
                datasets: [{
                    data: Object.values(typeData),
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(255, 99, 132, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)'
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
    if (unitChart) {
        const unitData = countByUnit(10);
        unitChart.data.labels = Object.keys(unitData);
        unitChart.data.datasets[0].data = Object.values(unitData);
        unitChart.update();
    }

    if (typeChart) {
        const typeData = countByType();
        typeChart.data.labels = Object.keys(typeData);
        typeChart.data.datasets[0].data = Object.values(typeData);
        typeChart.update();
    }
};

// Hàm đếm số lượng danh hiệu theo đơn vị (lấy top N)
const countByUnit = (topN = 10) => {
    const data = {};

    // Đếm số lượng danh hiệu cho mỗi đơn vị
    filteredData.value.forEach(item => {
        if (!data[item.tenDonVi]) {
            data[item.tenDonVi] = 0;
        }
        data[item.tenDonVi] += item.soLuongDat;
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

// Hàm đếm số lượng danh hiệu theo loại đơn vị
const countByType = () => {
    const data = {};
    filteredData.value.forEach(item => {
        if (!data[item.tenDonVi]) {
            data[item.tenDonVi] = 0;
        }
        data[item.tenDonVi] += item.soLuongDat;
    });
    return data;
};

const getHinhThucBadgeClass = (hinhThuc) => {
    switch (hinhThuc?.toLowerCase()) {
        case 'theo đợt':
            return 'badge bg-success text-white fw-normal px-2 py-1';
        case 'đột xuất':
            return 'badge bg-primary text-white fw-normal px-2 py-1';
        default:
            return 'badge bg-secondary text-white fw-normal px-2 py-1';
    }
}

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

const exportExcel = async () => {
    if (filteredData.value.length > 0) {
        try {
            // Get the charts as base64 images
            const unitChartRef = document.getElementById("unitChartRef");
            const typeChartRef = document.getElementById("typeChartRef");

            const unitChartImage = unitChartRef.toDataURL("image/png");
            const typeChartImage = typeChartRef.toDataURL("image/png");

            // Send data to server for Excel generation
            const response = await axios.post("/api/baocaothongke/donviexcel", {
                data: filteredData.value,
                unitChartImage,
                typeChartImage,
            }, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`,
                    'Content-Type': 'application/json',
                    Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                },
                responseType: 'blob'
            });

            if (response.status === 200) {
                // Create and trigger download
                const blob = new Blob([response.data], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'thongke_canhan.xlsx');
                document.body.appendChild(link);
                link.click();

                // Clean up
                window.URL.revokeObjectURL(url);
                document.body.removeChild(link);
            }
        } catch (error) {
            console.error('Error exporting to Excel:', error);
            // Assuming you have a toast notification system
            toastError('Xuất Excel thất bại: ' + error.message);
        }
    } else {
        toastError('Không có dữ liệu để xuất');
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