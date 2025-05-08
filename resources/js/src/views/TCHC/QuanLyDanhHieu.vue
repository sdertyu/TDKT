<template>
  <div class="m-4">
    <!-- Card for danh hiệu management -->
    <div class="card my-4 shadow-sm">
      <div class="card-header">
        <h1 class="card-title">Quản lý danh hiệu</h1>
      </div>
      <div class="card-body">
        <!-- Bảng danh hiệu -->
        <div class="table-responsive">
          <DataTable
            v-model:filters="filters"
            :value="danhSachDanhHieu"
            :paginator="true"
            :rows="10"
            :rowsPerPageOptions="[5, 10, 15, 20]"
            responsiveLayout="scroll"
            stripedRows
            class="p-datatable-sm"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :globalFilterFields="[
              'sTenDanhHieu',
              'sTenLoaiDanhHieu',
              'sTenHinhThuc',
              'sTenCap',
            ]"
          >
            <template #header>
              <div class="d-flex justify-content-between align-items-center">
                <IconField>
                  <InputIcon>
                    <i class="pi pi-search" />
                  </InputIcon>
                  <InputText v-model="filters['global'].value" placeholder="Tìm kiếm" />
                </IconField>
                <button
                  type="button"
                  class="btn btn-primary"
                  @click="showAddModal"
                  data-bs-target="#danhHieuModal"
                  data-bs-toggle="modal"
                >
                  <i class="fas fa-plus"></i> Thêm danh hiệu mới
                </button>
              </div>
            </template>
            <Column
              header="STT"
              bodyStyle="text-align: center"
              :pt="{ columnHeaderContent: 'justify-content-center' }"
            >
              <template #body="slotProps">
                {{ slotProps.index + 1 }}
              </template>
            </Column>
            <Column field="sTenDanhHieu" header="Tên danh hiệu" sortable />
            <Column field="sTenLoaiDanhHieu" header="Dành cho" />
            <Column field="sTenHinhThuc" header="Hình thức" />
            <Column field="sTenCap" header="Cấp danh hiệu" />

            <Column
              header="Trạng thái"
              bodyStyle="text-align: center"
              :pt="{ columnHeaderContent: 'justify-content-center' }"
            >
              <template #body="slotProps">
                <span
                  :class="
                    slotProps.data.bTrangThai == 0
                      ? 'badge bg-secondary'
                      : 'badge bg-success'
                  "
                >
                  {{ slotProps.data.bTrangThai == 0 ? "Tạm ngưng" : "Hoạt động" }}
                </span>
              </template>
            </Column>
            <Column
              header="Thao tác"
              class="text-center"
              :pt="{ columnHeaderContent: 'justify-content-center' }"
            >
              <template #body="slotProps">
                <button
                  class="btn btn-warning btn-sm me-2"
                  @click="showEditModal(slotProps.data)"
                  data-bs-toggle="modal"
                  data-bs-target="#danhHieuModal"
                >
                  <i class="fas fa-edit"></i>
                </button>
                <button
                  class="btn btn-secondary btn-sm me-2"
                  :class="
                    slotProps.data.bTrangThai == 0 ? 'bg-blend-color' : 'bg-success'
                  "
                  @click="changeStatus(slotProps.data)"
                >
                  <i
                    :class="
                      slotProps.data.bTrangThai == 0 ? 'fas fa-lock-open' : 'fas fa-lock'
                    "
                  ></i>
                </button>
                <button
                  class="btn btn-danger btn-sm"
                  @click="confirmDelete(slotProps.data)"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>
    </div>

    <!-- Modal thêm/sửa danh hiệu -->
    <div class="modal fade" id="danhHieuModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ isEditing ? "Cập nhật danh hiệu" : "Thêm danh hiệu mới" }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveDanhHieu">
              <div class="mb-3">
                <label class="form-label">Tên danh hiệu</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="currentDanhHieu.tendanhhieu"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Danh hiệu dành cho</label>
                <select
                  class="form-select"
                  v-model="currentDanhHieu.loaidanhhieu"
                  required
                >
                  <option value="" disabled selected>- Chọn loại danh hiệu -</option>
                  <option
                    v-for="loai in listLoaiDanhHieu"
                    :key="loai.PK_MaLoaiDanhHieu"
                    :value="loai.PK_MaLoaiDanhHieu"
                  >
                    {{ loai.sTenLoaiDanhHieu }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Hình thức</label>
                <select class="form-select" v-model="currentDanhHieu.hinhthuc" required>
                  <option value="" disabled selected>- Chọn hình thức -</option>
                  <option
                    v-for="hinhThuc in listHinhThuc"
                    :key="hinhThuc.PK_MaHinhThuc"
                    :value="hinhThuc.PK_MaHinhThuc"
                  >
                    {{ hinhThuc.sTenHinhThuc }}
                  </option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Cấp danh hiệu</label>
                <select
                  class="form-select"
                  v-model="currentDanhHieu.capdanhhieu"
                  required
                >
                  <option value="" disabled selected>- Chọn hình thức -</option>
                  <option
                    v-for="cap in listCapDanhHieu"
                    :key="cap.PK_MaHinhThuc"
                    :value="cap.PK_MaCap"
                  >
                    {{ cap.sTenCap }}
                  </option>
                </select>
              </div>

              <div class="text-end">
                <button
                  type="button"
                  class="btn btn-secondary me-2"
                  data-bs-dismiss="modal"
                >
                  Hủy
                </button>
                <button type="submit" class="btn btn-primary">Lưu</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";

import Swal from "sweetalert2";
import { toastSuccess, toastError } from "@/utils/toast";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import { FilterMatchMode } from "@primevue/core/api";

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const currentDanhHieu = reactive({
  id: null,
  tendanhhieu: "",
  loaidanhhieu: "",
  hinhthuc: "",
  capdanhhieu: "",
  trangthai: null,
});

const danhSachDanhHieu = ref([]);
const listLoaiDanhHieu = ref([]);
const listHinhThuc = ref([]);
const listCapDanhHieu = ref([]);

const isEditing = ref(false);
const danhHieuForm = ref({
  id: null,
  ten: "",
  loai: "canhan",
  hinhthuc: "theodot",
  mota: "",
});

const loadDanhHieu = async () => {
  try {
    const response = await axios.get("/api/danhhieu/list", {
      headers: {
        Authorization: `Bearer ${sessionStorage.getItem("api_token")}`,
      },
    });

    if (response.status === 200) {
      if (Array.isArray(response.data.data)) {
        danhSachDanhHieu.value = response.data.data; // Gán mảng trực tiếp
      } else {
        // Nếu dữ liệu là đối tượng, chuyển thành mảng
        danhSachDanhHieu.value = Object.values(response.data.data);
      }
      console.log(danhSachDanhHieu.value);
    }

    // Tạm thời comment lại để dùng dữ liệu mẫu
    // danhSachDanhHieu.value = response.data
  } catch (error) {
    console.error("Lỗi khi tải danh sách danh hiệu:", error);
    toastError("Không thể tải danh sách danh hiệu");
  }
};

const getHinhThuc = () => {
  axios
    .get("/api/hinhthuc/getList", {
      headers: {
        Authorization: `Bearer ${sessionStorage.getItem("api_token")}`,
      },
    })
    .then((response) => {
      if (response.status === 200) {
        listHinhThuc.value = response.data.data;
        // danhSachDanhHieu.value = response.data.data
      }
    })
    .catch((error) => {
      console.error("Lỗi khi tải hình thức:", error);
      toastError("Không thể tải hình thức");
    });
};
const getLoaiDanhHieu = () => {
  axios
    .get("/api/loaidanhhieu/getList", {
      headers: {
        Authorization: `Bearer ${sessionStorage.getItem("api_token")}`,
      },
    })
    .then((response) => {
      if (response.status === 200) {
        listLoaiDanhHieu.value = response.data.data;
        // danhSachDanhHieu.value = response.data.data
      }
    })
    .catch((error) => {
      console.error("Lỗi khi tải loại danh hiệu:", error);
      toastError("Không thể tải loại danh hiệu");
    });
};
const getCapDanhHieu = () => {
  axios
    .get("/api/capdanhhieu/getList", {
      headers: {
        Authorization: `Bearer ${sessionStorage.getItem("api_token")}`,
      },
    })
    .then((response) => {
      if (response.status === 200) {
        listCapDanhHieu.value = response.data.data;
        // danhSachDanhHieu.value = response.data.data
      }
    })
    .catch((error) => {
      console.error("Lỗi khi tải cấp danh hiệu:", error);
      toastError("Không thể tải cấp danh hiệu");
    });
};

const showAddModal = () => {
  isEditing.value = false;
  currentDanhHieu.id = null;
  currentDanhHieu.tendanhhieu = "";
  currentDanhHieu.hinhthuc = "";
  currentDanhHieu.loaidanhhieu = "";
  currentDanhHieu.capdanhhieu = "";
  currentDanhHieu.trangthai = "";
};

const showEditModal = (danhhieu) => {
  isEditing.value = true;
  currentDanhHieu.id = danhhieu.PK_MaDanhHieu;
  currentDanhHieu.tendanhhieu = danhhieu.sTenDanhHieu;
  currentDanhHieu.loaidanhhieu = danhhieu.PK_MaLoaiDanhHieu;
  currentDanhHieu.hinhthuc = danhhieu.PK_MaHinhThuc;
  currentDanhHieu.capdanhhieu = danhhieu.PK_MaCap;
  currentDanhHieu.trangthai = danhhieu.bTrangThai;
};

const saveDanhHieu = async () => {
  try {
    let response = null;
    if (isEditing.value) {
      response = await axios.put(`/api/danhhieu/update`, currentDanhHieu, {
        headers: {
          Authorization: `Bearer ${sessionStorage.getItem("api_token")}`,
        },
      });
    } else {
      response = await axios.post("/api/danhhieu/add", currentDanhHieu, {
        headers: {
          Authorization: `Bearer ${sessionStorage.getItem("api_token")}`,
        },
      });
    }
    if (response.status === 200) {
      if (isEditing.value) {
        // const index = danhSachDanhHieu.value.findIndex(d => d.PK_MaDanhHieu === currentDanhHieu.id)
        // danhSachDanhHieu.value[index] = {
        //     PK_MaDanhHieu: currentDanhHieu.id,
        //     sTenDanhHieu: currentDanhHieu.tendanhhieu,
        //     sTenLoaiDanhHieu: currentDanhHieu.loaidanhhieu,
        //     sTenHinhThuc: currentDanhHieu.hinhthuc
        // }
        setTimeout(() => {
          loadDanhHieu();
        }, 3000);
      } else {
        setTimeout(() => {
          loadDanhHieu();
        }, 3000);
        // console.log(response.data.data);
        // danhSachDanhHieu.value.push({
        //     PK_MaDanhHieu: response.data.data.PK_MaDanhHieu,
        //     sTenDanhHieu: response.data.data.sTenDanhHieu,
        //     sTenLoaiDanhHieu: response.data.data.sTenLoaiDanhHieu,
        //     sTenHinhThuc: response.data.data.sTenHinhThuc
        // })
      }

      toastSuccess(
        isEditing.value ? "Cập nhật danh hiệu thành công" : "Thêm danh hiệu thành công"
      );
    }
  } catch (error) {
    if (error.response) {
      if (error.response.status === 422) {
        const errors = error.response.data.errors;
        let errorMessage = Object.values(errors).flat().join("<br>");
        console.log(errors);
        toastError(errorMessage);
      } else {
        toastError(error.response.data.message);
      }
    } else {
      toastError("Có lỗi xảy ra khi lưu danh hiệu");
    }
  } finally {
    document.getElementById("danhHieuModal").querySelector(".btn-close").click();
  }
};

const changeStatus = async (danhhieu) => {
  try {
    const response = await axios.put(
      `/api/danhhieu/updatestatus/${danhhieu.PK_MaDanhHieu}`,
      {
        trangthai: danhhieu.bTrangThai == 0 ? 1 : 0,
      },
      {
        headers: {
          Authorization: `Bearer ${sessionStorage.getItem("api_token")}`,
        },
      }
    );
    if (response.status === 200) {
      danhhieu.bTrangThai = danhhieu.bTrangThai == 0 ? 1 : 0;
      toastSuccess("Cập nhật trạng thái thành công");
    }
  } catch (error) {
    if (error.response) {
      console.log(error.response);
      if (error.response.status === 422) {
        const errors = error.response.data.errors;
        let errorMessage = Object.values(errors).flat().join("<br>");
        toastError(errorMessage);
      } else {
        toastError(error.response.data.message);
      }
    } else {
      toastError("Có lỗi xảy ra khi cập nhật trạng thái");
    }
  }
};

const confirmDelete = (danhhieu) => {
  Swal.fire({
    title: "Xác nhận xóa?",
    text: `Bạn có chắc muốn xóa danh hiệu "${danhhieu.sTenDanhHieu}"?`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Xóa",
    cancelButtonText: "Hủy",
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        const response = await axios.delete(
          `/api/danhhieu/delete/${danhhieu.PK_MaDanhHieu}`,
          {
            headers: {
              Authorization: `Bearer ${sessionStorage.getItem("api_token")}`,
            },
          }
        );
        if (response.status === 200) {
          danhSachDanhHieu.value = danhSachDanhHieu.value.filter(
            (d) => d.PK_MaDanhHieu !== danhhieu.PK_MaDanhHieu
          );
          toastSuccess("Xóa danh hiệu thành công");
        }
      } catch (error) {
        if (error.response) {
          toastError(error.response.data.message);
        } else {
          toastError("Có lỗi xảy ra khi xóa danh hiệu");
        }
      }
    }
  });
};

onMounted(() => {
  loadDanhHieu();
  getHinhThuc();
  getLoaiDanhHieu();
  getCapDanhHieu();
});
</script>
