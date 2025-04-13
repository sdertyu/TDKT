<template>
    <div class="card m-4">
        <div class="card-header">
            <h4 class="card-title">Thành tích của tôi</h4>
        </div>

        <div class="card-body">
            <DataTable v-model:filters="filters" :value="listThanhTich" 
                class="p-datatable-sm" :globalFilterFields="['tenDanhHieu', 'dot', 'hinhThuc', 'capDanhHieu']"
                responsiveLayout="scroll" stripedRows>
                <template #header>
                    <div class="d-flex justify-content-between align-items-center">
                        <IconField>
                            <InputIcon>
                                <i class="bi bi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Tìm kiếm" />
                        </IconField>
                    </div>
                </template>
                <Column header="STT" bodyStyle="text-align: center"
                    :pt="{ columnHeaderContent: 'justify-content-center' }">
                    <template #body="slotProps">
                        {{ slotProps.index + 1 }}
                    </template>
                </Column>
                <Column field="tenDanhHieu" header="Tên danh hiệu" sortable>
                    <template #body="slotProps">
                        {{ slotProps.data.tenDanhHieu }}
                    </template>
                </Column>
                <Column field="dot" header="Năm học" sortable>
                    <template #body="slotProps">
                        {{ slotProps.data.dot }}
                    </template>
                </Column>
                <Column header="Hình thức">
                    <template #body="slotProps">
                        {{ slotProps.data.hinhThuc }}
                    </template>
                </Column>
                <Column header="Cấp danh hiệu">
                    <template #body="slotProps">
                        {{ slotProps.data.capDanhHieu }}
                    </template>
                </Column>

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
import { FilterMatchMode } from '@primevue/core/api';
import { onMounted, ref } from 'vue';


const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    }
);

const listThanhTich = ref([]);

const getThanhTich = async () => {
    try {
        const response = await axios.get('/api/baocaothongke/thanhtichcuatoi', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
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

onMounted(() => {
    getThanhTich();
});
</script>