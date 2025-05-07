<template>
    <div class="card m-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0"><i class="bi bi-trophy me-2"></i>Thành tích của tôi</h4>
        </div>

        <div class="card-body">
            <DataTable v-model:filters="filters" :value="listThanhTich" class="p-datatable-sm"
                :globalFilterFields="['tenDanhHieu', 'dot', 'hinhThuc', 'capDanhHieu', 'ngayTrao', 'nguoiTrao']"
                responsiveLayout="scroll" stripedRows rowHover paginator :rows="10"
                :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 100%">
                <template #header>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <IconField class="w-100 md:w-25">
                            <InputIcon>
                                <i class="bi bi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Tìm kiếm" class="w-100" />
                        </IconField>
                        <Button icon="bi bi-file-earmark-excel" label="Xuất Excel" class="p-button-success" />
                    </div>
                </template>
                <Column header="STT" bodyStyle="text-align: center; width: 70px;"
                    :pt="{ columnHeaderContent: 'justify-content-center' }">
                    <template #body="slotProps">
                        <span class="fw-bold">{{ slotProps.index + 1 }}</span>
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
import InputText from 'primevue/inputtext'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import Button from 'primevue/button'
import MultiSelect from 'primevue/multiselect';
import { FilterMatchMode } from '@primevue/core/api';
import { onMounted, ref, computed } from 'vue';

const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        capDanhHieu: { value: null, matchMode: FilterMatchMode.IN },
    }
);

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

const listThanhTich = ref([]);

const getThanhTich = async () => {
    try {
        const response = await axios.get('/api/baocaothongke/thanhtichcuatoi', {
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

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
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

const viewDocument = (data) => {
    // Implement document viewing logic here
    console.log("Viewing document for:", data);
    // Could open document in new tab or modal
};

onMounted(() => {
    getThanhTich();
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
</style>