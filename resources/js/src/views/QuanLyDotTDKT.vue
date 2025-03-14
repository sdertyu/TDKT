<template>
    <div class="card my-3">
        <div class="card-header">
            <h3 class="card-title">Danh sách đợt thi đua khen thưởng</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary" @click="openAddModal" data-bs-toggle="modal"
                    data-bs-target="#dotModal">
                    <i class="fas fa-plus"></i> Thêm đợt
                </button>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th class="text-center">Năm bắt đầu</th>
                        <th class="text-center">Năm kết thúc</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in listDot" :key="index">
                        <td class="text-center">{{ index + 1 }}</td>
                        <td class="text-center">{{ item.iNamBatDau }}</td>
                        <td class="text-center">{{ item.iNamKetThuc }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="dotModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dotModalLabel">Thêm đợt</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit="addDot">
                            <div class="mb-3">
                                <label for="username" class="form-label text">Năm bắt đầu</label>
                                <input type="text" class="form-control" id="namBatDau" required
                                    v-model="currentDot.iNamBatDau" />
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label text">Năm kết thúc</label>
                                <input type="text" class="form-control" id="namKetThuc" required
                                    v-model="currentDot.iNamKetThuc" />
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Hủy
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Thêm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';

const listDot = ref([]);
const currentDot = reactive({
    iNamBatDau: '',
    iNamKetThuc: ''
});

onMounted(() => {
    axios.get('/api/dotthiduakhenthuong/list', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    }).then(response => {
        if (response.status == 200) {
            listDot.value = response.data.data;
        }
    }).catch(error => {
        console.log(error);
    });
});

const addDot = () => {
    // axios.post('/api/dotthiduakhenthuong/add', {
    //     iNamBatDau: currentDot.iNamBatDau,
    //     iNamKetThuc: currentDot.iNamKetThuc
    // }, {
    //     headers: {
    //         Authorization: `Bearer ${localStorage.getItem('api_token')}`
    //     }
    // }).then(response => {
    //     if (response.status == 200) {
    //         listDot.value.push(response.data);
    //     }
    // }).catch(error => {
    //     console.log(error);
    // });
}

</script>