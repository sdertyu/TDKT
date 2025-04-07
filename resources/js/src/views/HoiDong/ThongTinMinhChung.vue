<template>
    <div class="container my-4">
        <h2 class="mb-4">Danh sách đề xuất danh hiệu</h2>

        <!-- Accordion chính cho đơn vị -->
        <div class="accordion" id="donViAccordion">
            <div v-for="(donVi, index) in listDeXuat" :key="donVi.ma_don_vi" class="accordion-item">
                <!-- Header của đơn vị -->
                <h2 class="accordion-header" :id="'heading-' + donVi.ma_don_vi">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        :data-bs-target="'#collapse-' + donVi.ma_don_vi" :aria-expanded="index === 0 ? 'true' : 'false'"
                        :aria-controls="'collapse-' + donVi.ma_don_vi">
                        <strong>{{ donVi.ten_don_vi }}</strong>
                    </button>
                </h2>

                <!-- Nội dung của đơn vị -->
                <div :id="'collapse-' + donVi.ma_don_vi" class="accordion-collapse collapse"
                    :class="{ show: index === 0 }" :aria-labelledby="'heading-' + donVi.ma_don_vi"
                    data-bs-parent="#donViAccordion">
                    <div class="accordion-body">
                        <!-- Danh hiệu của đơn vị -->
                        <div v-if="donVi.de_xuat_don_vi && donVi.de_xuat_don_vi.length > 0" class="mb-4">
                            <h5 class="mb-3">Danh hiệu đơn vị</h5>
                            <div class="list-group">
                                <div v-for="deXuat in donVi.de_xuat_don_vi" :key="deXuat.PK_MaDeXuat"
                                    class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between"
                                        @click="xemMinhChung(deXuat.PK_MaDeXuat)">
                                        <h6 class="mb-1">{{ deXuat.danh_hieu.sTenDanhHieu }}</h6>
                                        <small>{{ formatDate(deXuat.dNgayTao) }}</small>
                                    </div>
                                    <!-- <p class="mb-1">Số người bầu: {{ deXuat.iSoNguoiBau }}</p> -->
                                </div>
                            </div>
                        </div>

                        <!-- Accordion cho các cá nhân trong đơn vị -->
                        <h5 class="mb-3">Danh hiệu cá nhân</h5>
                        <div class="accordion" :id="'caNhanAccordion-' + donVi.ma_don_vi">
                            <div v-for="caNhan in donVi.ca_nhan" :key="caNhan.ma_ca_nhan" class="accordion-item">
                                <!-- Header của cá nhân -->
                                <h2 class="accordion-header" :id="'heading-' + caNhan.ma_ca_nhan">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        :data-bs-target="'#collapse-' + caNhan.ma_ca_nhan" aria-expanded="false"
                                        :aria-controls="'collapse-' + caNhan.ma_ca_nhan">
                                        {{ caNhan.ten_ca_nhan }}
                                    </button>
                                </h2>

                                <!-- Nội dung của cá nhân -->
                                <div :id="'collapse-' + caNhan.ma_ca_nhan" class="accordion-collapse collapse"
                                    :aria-labelledby="'heading-' + caNhan.ma_ca_nhan"
                                    :data-bs-parent="'#caNhanAccordion-' + donVi.ma_don_vi">
                                    <div class="accordion-body">
                                        <div v-if="caNhan.de_xuat && caNhan.de_xuat.length > 0" class="list-group">
                                            <div v-for="deXuat in caNhan.de_xuat" :key="deXuat.PK_MaDeXuat"
                                                class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between" @click="xemMinhChung(deXuat.PK_MaDeXuat)">
                                                    <h6 class="mb-1">{{ deXuat.danh_hieu.sTenDanhHieu }}</h6>
                                                    <small>{{ formatDate(deXuat.dNgayTao) }}</small>
                                                </div>
                                                <!-- <p class="mb-1">Số người bầu: {{ deXuat.iSoNguoiBau }}</p> -->
                                            </div>
                                        </div>
                                        <div v-else class="alert alert-info">
                                            Không có đề xuất danh hiệu cho cá nhân này
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import router from '../../router';

const listDeXuat = ref([]);

const getAllDeXuat = () => {
    axios
        .get("/api/dexuat/getalldexuat", {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("api_token")}`,
            },
        })
        .then((response) => {
            if (response.status === 200) {
                listDeXuat.value = response.data.data;
            }
        })
        .catch((error) => {
            toastError("Có lỗi xảy ra khi lấy danh sách minh chứng");
        });
};

const xemMinhChung = (id) => {
    router.push(`/themminhchung/${id}`);
};

// Format date function
const formatDate = (dateString) => {
    if (!dateString) return "";

    // Parse date string format "15:54:48 06/04/25"
    const parts = dateString.split(" ");
    const datePart = parts[1].split("/");
    const timePart = parts[0].split(":");

    const day = datePart[0];
    const month = datePart[1];
    const year = "20" + datePart[2]; // Thêm "20" vào năm "25" để thành "2025"

    return `${day}/${month}/${year}`;
};

// Hàm thông báo lỗi (được giả định là tồn tại từ trước)
const toastError = (message) => {
    // Implement toast error theo library bạn đang sử dụng
    console.error(message);
};

onMounted(() => {
    getAllDeXuat();
});
</script>

<style scoped>
.accordion-button:not(.collapsed) {
    background-color: rgba(0, 123, 255, 0.1);
    color: #0d6efd;
}

.list-group-item:hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>
