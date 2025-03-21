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
                <div class="row">
                    <!-- Đề xuất cá nhân -->
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user-award mr-2"></i>
                                    Đề xuất khen thưởng cá nhân
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Chọn cá nhân</label>
                                    <select v-model="formState.selectedIndividual" class="form-control select2">
                                        <option value="">-- Chọn cá nhân --</option>
                                        <!-- Options sẽ được thêm sau -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Danh hiệu đề xuất</label>
                                    <select v-model="formState.individualAward" class="form-control select2">
                                        <option value="">-- Chọn danh hiệu --</option>
                                        <option value="chien_si_thi_dua">Chiến sĩ thi đua</option>
                                        <option value="lao_dong_tien_tien">Lao động tiên tiến</option>
                                        <option value="bang_khen">Bằng khen</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Lý do đề xuất</label>
                                    <textarea v-model="formState.individualReason" class="form-control" rows="3"
                                        placeholder="Nhập lý do đề xuất..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Đề xuất đơn vị -->
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-building mr-2"></i>
                                    Đề xuất khen thưởng đơn vị
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Danh hiệu đề xuất cho đơn vị</label>
                                    <select v-model="formState.unitAward" class="form-control select2">
                                        <option value="">-- Chọn danh hiệu --</option>
                                        <option value="tap_the_lao_dong_xuat_sac">Tập thể lao động xuất sắc</option>
                                        <option value="co_thi_dua">Cờ thi đua</option>
                                        <option value="bang_khen_tap_the">Bằng khen tập thể</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Lý do đề xuất</label>
                                    <textarea v-model="formState.unitReason" class="form-control" rows="3"
                                        placeholder="Nhập lý do đề xuất..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="float-right">
                            <button type="button" @click="handleReset" class="btn btn-default mr-2">
                                <i class="fas fa-redo mr-1"></i> Làm mới
                            </button>
                            <button type="button" @click="handleSubmit" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-1"></i> Gửi đề xuất
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue'

const formState = reactive({
    selectedIndividual: '',
    individualAward: '',
    individualReason: '',
    unitAward: '',
    unitReason: ''
})

const handleSubmit = () => {
    // Xử lý logic gửi form
    console.log('Form data:', formState)
}

const handleReset = () => {
    Object.assign(formState, {
        selectedIndividual: '',
        individualAward: '',
        individualReason: '',
        unitAward: '',
        unitReason: ''
    })
}

onMounted(() => {
    // Initialize Select2 Elements
    $('.select2').select2({
        theme: 'bootstrap4'
    })
})
</script>

<style scoped>
.card {
    margin-bottom: 1rem;
}

.select2-container--bootstrap4 {
    width: 100% !important;
}
</style>
