<template>
    <div class="card m-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0"><i class="bi bi-trophy me-2"></i>Thành tích cá nhân trong đơn vị</h4>
        </div>

        <div class="card-body">
            <DataTable v-model:filters="filters" :value="listThanhTich" class="p-datatable-sm"
                :globalFilterFields="['tenCaNhan', 'maCaNhan', 'tenDanhHieu', 'dot', 'hinhThuc', 'capDanhHieu']"
                responsiveLayout="scroll" stripedRows rowHover paginator :rows="10"
                :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 100%">
                <template #header>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <IconField class="w-100 md:w-25">
                                <InputIcon>
                                    <i class="bi bi-search" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Tìm kiếm" class="w-100" />
                            </IconField>
                            <Button icon="bi bi-file-earmark-excel" label="Xuất Excel" class="p-button-success" />
                        </div>
                        
                        <div class="d-flex flex-wrap gap-3">
                            <div class="flex-grow-1 min-width-200">
                                <label class="form-label mb-1">Năm học</label>
                                <Dropdown v-model="filters['dot'].value" :options="uniqueDotOptions" optionLabel="label" 
                                    placeholder="Tất cả năm học" class="w-100" />
                            </div>
                            <div class="flex-grow-1 min-width-200">
                                <label class="form-label mb-1">Cấp danh hiệu</label>
                                <MultiSelect v-model="filters['capDanhHieu'].value" :options="uniqueCapDanhHieuOptions" 
                                    optionLabel="label" placeholder="Tất cả cấp danh hiệu" class="w-100" />
                            </div>
                            <div class="flex-grow-1 min-width-200">
                                <label class="form-label mb-1">Hình thức</label>
                                <MultiSelect v-model="filters['hinhThuc'].value" :options="uniqueHinhThucOptions" 
                                    optionLabel="label" placeholder="Tất cả hình thức" class="w-100" />
                            </div>
                        </div>
                    </div>
                </template>
                <Column header="STT" bodyStyle="text-align: center; width: 70px;"
                    :pt="{ columnHeaderContent: 'justify-content-center' }">
                    <template #body="slotProps">
                        <span class="fw-bold">{{ slotProps.index + 1 }}</span>
                    </template>
                </Column>
                <Column field="tenCaNhan" header="Tên cá nhân" sortable>
                    <template #body="slotProps">
                        <div>
                            <span class="fw-semibold">{{ slotProps.data.tenCaNhan }}</span>
                            <span class="d-block text-secondary small">Mã: {{ slotProps.data.maCaNhan }}</span>
                        </div>
                    </template>
                </Column>
                <Column field="tenDanhHieu" header="Tên danh hiệu" sortable>
                    <template #body="slotProps">
                        <span class="fw-semibold">{{ slotProps.data.tenDanhHieu }}</span>
                    </template>
                </Column>
                <Column field="dot" header="Năm học" sortable>
                    <template #body="slotProps">
                        <span class="text-secondary">{{ slotProps.data.dot }}</span>
                    </template>
                </Column>
                <Column field="hinhThuc" header="Hình thức">
                    <template #body="slotProps">
                        <span :class="getHinhThucBadgeClass(slotProps.data.hinhThuc)">
                            {{ slotProps.data.hinhThuc }}
                        </span>
                    </template>
                </Column>
                <Column field="capDanhHieu" header="Cấp danh hiệu">
                    <template #body="slotProps">
                        <span :class="getCapDanhHieuBadgeClass(slotProps.data.capDanhHieu)">
                            {{ slotProps.data.capDanhHieu }}
                        </span>
                    </template>
                </Column>
                <Column header="Thao tác" style="width: 120px;" :exportable="false">
                    <template #body="slotProps">
                        <Button icon="bi bi-eye" class="p-button-rounded p-button-sm p-button-text p-button-info"
                            @click="viewDetails(slotProps.data)" />
                    </template>
                </Column>
                <template #empty>
                    <div class="text-center p-4">
                        <i class="bi bi-search fs-3 text-muted"></i>
                        <p class="mt-2">Không tìm thấy thành tích nào.</p>
                    </div>
                </template>
                <template #paginatorstart>
                    <span class="text-muted">Tổng số: {{ listThanhTich.length }} thành tích</span>
                </template>
            </DataTable>
        </div>
    </div>
</template>

<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Button from 'primevue/button';
import MultiSelect from 'primevue/multiselect';
import Dropdown from 'primevue/dropdown';
import { FilterMatchMode } from '@primevue/core/api';
import { onMounted, ref, computed } from 'vue';

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    capDanhHieu: { value: null, matchMode: FilterMatchMode.IN },
    hinhThuc: { value: null, matchMode: FilterMatchMode.IN },
    dot: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const listThanhTich = ref([]);

const uniqueCapDanhHieuOptions = computed(() => {
    const seen = new Set();
    return listThanhTich.value
        .map(item => item.capDanhHieu)
        .filter(cap => {
            if (!cap || seen.has(cap)) return false;
            seen.add(cap);
            return true;
        })
        .map(cap => ({ label: cap, value: cap }));
});

const uniqueHinhThucOptions = computed(() => {
    const seen = new Set();
    return listThanhTich.value
        .map(item => item.hinhThuc)
        .filter(ht => {
            if (!ht || seen.has(ht)) return false;
            seen.add(ht);
            return true;
        })
        .map(ht => ({ label: ht, value: ht }));
});

const uniqueDotOptions = computed(() => {
    const seen = new Set();
    return listThanhTich.value
        .map(item => item.dot)
        .filter(dot => {
            if (!dot || seen.has(dot)) return false;
            seen.add(dot);
            return true;
        })
        .map(dot => ({ label: dot, value: dot }));
});

const getThanhTichCaNhan = async () => {
    try {
        const response = await axios.get('/api/baocaothongke/thanhtichcanhantrongdonvi', {
            headers: {
                Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
            }
        });
        if (response.status === 200) {
            listThanhTich.value = response.data.data;
        } else {
            console.error('Failed to fetch data:', response.statusText);
        }
    } catch (error) {
        console.error('Error fetching data:', error);
        
    }
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
};

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

const viewDetails = (data) => {
    // Implement details viewing logic
    console.log("Viewing details for:", data);
    // Could open details in modal or navigate to detail page
};

onMounted(() => {
    getThanhTichCaNhan();
});
</script>

<style scoped>
.p-datatable .p-datatable-thead>tr>th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.p-datatable-wrapper {
    border-radius: 0.375rem;
    overflow: hidden;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
}

.min-width-200 {
    min-width: 200px;
}
</style>