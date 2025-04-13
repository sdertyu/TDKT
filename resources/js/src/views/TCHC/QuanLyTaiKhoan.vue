<template>
    <div class="content-wrapper m-4">
        <div class="content">
            <div class="">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách tài khoản</h3>
                    </div>
                    <div class="card-body">
                        <!-- DataTable với PrimeVue -->
                        <DataTable v-model:filters="filters" :value="accounts" :paginator="true" :rows="itemsPerPage"
                            :rowsPerPageOptions="[5, 10, 25, 50, 100]" responsiveLayout="scroll" stripedRows
                            class="p-datatable-sm"
                            :globalFilterFields="['sUsername', 'bTrangThai']">
                            <template #header>
                                <div class="d-flex justify-content-between align-items-center">
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Tìm kiếm" />
                                    </IconField>
                                    <button type="button" class="btn btn-primary" @click="openAddModal" data-bs-toggle="modal"
                                        data-bs-target="#accountModal">
                                        <i class="fas fa-plus"></i> Thêm tài khoản
                                    </button>
                                </div>
                            </template>
                            <Column header="STT" bodyStyle="text-align: center" 
                                :pt="{ columnHeaderContent: 'justify-content-center' }">
                                <template #body="slotProps">
                                    {{ slotProps.index + 1 }}
                                </template>
                            </Column>
                            <Column field="sUsername" header="Tên đăng nhập" sortable />
                            <Column header="Trạng thái" bodyStyle="text-align: center"
                                :pt="{ columnHeaderContent: 'justify-content-center' }">
                                <template #body="slotProps">
                                    <span :class="getStatusBadgeClass(slotProps.data.bTrangThai)">
                                        {{ slotProps.data.bTrangThai == 1 ? "Hoạt động" : "Tạm ngưng" }}
                                    </span>
                                </template>
                            </Column>
                            <Column header="Thao tác" bodyStyle="text-align: center"
                                :pt="{ columnHeaderContent: 'justify-content-center' }">
                                <template #body="slotProps">
                                    <button class="btn btn-sm btn-warning me-1" @click="openEditModal(slotProps.data)"
                                        data-bs-toggle="modal" data-bs-target="#accountModal" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-secondary btn-sm me-2"
                                        :class="slotProps.data.bTrangThai == 0 ? 'bg-blend-color' : 'bg-success'"
                                        @click="toggleAccountStatus(slotProps.data)">
                                        <i :class="slotProps.data.bTrangThai == 0 ? 'fas fa-lock-open' : 'fas fa-lock'"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" @click="confirmDelete(slotProps.data)"
                                        title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal thêm/sửa tài khoản -->
        <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accountModalLabel">
                            {{ isEditMode ? "Sửa tài khoản" : "Thêm tài khoản mới" }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveAccount">
                            <!-- ...existing code... -->
                            <div class="mb-3">
                                <label for="username" class="form-label tex">Tên đăng nhập</label>
                                <input type="text" class="form-control" id="accountModal_username"
                                    v-model="currentAccount.sUsername" required />
                            </div>
                            <div class="mb-3" v-if="!isEditMode || changePass">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <input :type="isCheckPass ? 'text' : 'password'" class="form-control" id="password"
                                        v-model="currentAccount.sPassword" required />
                                    <button @click="checkPass" class="input-group-text bg-light">
                                        <i :class="passwordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="accountModal_email" class="form-label tex">Email</label>
                                <input type="text" class="form-control" id="accountModal_email"
                                    v-model="currentAccount.email" required />
                            </div>

                            <div class="mb-3" v-if="isEditMode">
                                <div class="input-group">
                                    <input type="checkbox" class="form-check-input" id="changePass"
                                        style="transform: scale(1.25); margin-right: 10px; border-radius: 50%;"
                                        v-model="changePass">
                                    <label for="changePass" class="form-check-label" style="font-size: 18px;">Đổi mật
                                        khẩu</label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="role" class="form-label">Vai trò</label>
                                <select class="form-select" id="role" v-model="currentAccount.FK_MaQuyen" required>
                                    <option value="" disabled selected>-- Lựa chọn vai trò --</option>
                                    <option value="3">Hội đồng thi đua khen thưởng</option>
                                    <option value="4">Đơn vị</option>
                                    <option value="5">Cá nhân</option>
                                </select>
                            </div>

                            <div v-if="currentAccount.FK_MaQuyen == '5'">
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Mã cá nhân</label>
                                    <input type="text" class="form-control" id="accountModal_maCaNhan"
                                        v-model="currentAccount.macanhan" required />
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Tên cá nhân</label>
                                    <input type="text" class="form-control" id="fullName"
                                        v-model="currentAccount.tencanhan" required />
                                </div>
                                <div class="mb-3">
                                    <label for="maDonVi" class="form-label">Thuộc đơn vị</label>
                                    <select class="form-select" id="maDonVi" v-model="currentAccount.PK_MaDonVi"
                                        required>
                                        <option value="" disabled selected>-- Lựa chọn đơn vị --
                                        </option>
                                        <option v-for="item in listDonVi" :key="item.PK_MaDonVi"
                                            :value="item.PK_MaDonVi">
                                            {{ item.sTenDonVi }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Tên chức vụ</label>
                                    <input type="text" class="form-control" id="tenChucVu"
                                        v-model="currentAccount.tenchucvu" required />
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Giới tính</label>
                                    <div class="input-group">
                                        <input type="radio" name="gioiTinh" v-model="currentAccount.gioitinh" id="nam"
                                            value="0">
                                        <label for="nam" class="mx-2">Nam</label>
                                        <input type="radio" name="gioiTinh" id="nu" value="1"
                                            v-model="currentAccount.gioitinh">
                                        <label for="nu" class="mx-2">Nữ</label>
                                    </div>
                                </div>
                            </div>

                            <div v-if="currentAccount.FK_MaQuyen == '4'">
                                <div class="mb-3">
                                    <label for="username" class="form-label text">Mã đơn vị</label>
                                    <input type="text" class="form-control" id="accountModal_maDonVi"
                                        v-model="currentAccount.PK_MaDonVi" required />
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Tên đơn vị</label>
                                    <input type="text" class="form-control" id="tenDonVi"
                                        v-model="currentAccount.sTenDonVi" required />
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Hủy
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ isEditMode ? "Cập nhật" : "Thêm mới" }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { data } from 'jquery';
import Swal from 'sweetalert2';
import { ref, computed, onMounted, watch, nextTick } from 'vue';

// Import PrimeVue components
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { FilterMatchMode } from '@primevue/core/api';

const accounts = ref([]);
const listDonVi = ref([]);
const changePass = ref(false);

// Filter configuration for PrimeVue DataTable
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const isCheckPass = ref(false);
const passwordIcon = ref('fa-solid fa-eye');
const vaiTro = ref('');

const checkPass = () => {
    isCheckPass.value = !isCheckPass.value;
    passwordIcon.value = isCheckPass.value ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
};

watch(changePass, (newValue) => {
    // Chỉ thay đổi giá trị nếu nó khác với giá trị hiện tại
    if (newValue !== changePass.value) {
        changePass.value = !newValue;  // Lật giá trị của `changePass`
    }
});

const currentAccount = ref({
    id: null,
    sUsername: "",
    email: "",
    sPassword: "",
    FK_MaQuyen: "",
    status: true,
    PK_MaDonVi: "",
    sTenDonVi: "",
    macanhan: "",
    tencanhan: "",
    tenchucvu: "",
    gioitinh: ""
});

const isEditMode = ref(false);
const itemsPerPage = ref(10);

// Methods
const openAddModal = () => {
    isEditMode.value = false;
    currentAccount.value = {
        id: null,
        sUsername: "",
        email: "",
        sPassword: "",
        FK_MaQuyen: "",
        status: true,
        PK_MaDonVi: "",
        sTenDonVi: "",
        macanhan: "",
        tencanhan: "",
        tenchucvu: "",
        gioitinh: ""
    };
    changePass.value = false;
    vaiTro.value = '';
    
    nextTick(() => {
        const un = document.getElementById("accountModal_username");
        if (un) un.disabled = false;
    });
};

const openEditModal = (account) => {
    isEditMode.value = true;
    axios.get('/api/taikhoan/account/' + account.PK_MaTaiKhoan, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    }).then(response => {
        if (response.status == 200) {
            let data = response.data.taikhoan;
            currentAccount.value.sUsername = data.sUsername;
            currentAccount.value.id = data.PK_MaTaiKhoan;
            currentAccount.value.sPassword = data.sPassword;
            currentAccount.value.FK_MaQuyen = data.FK_MaQuyen;
            currentAccount.value.email = data.sEmail;
            if (data.FK_MaQuyen == 4) {
                currentAccount.value.PK_MaDonVi = data.don_vi[0].PK_MaDonVi;
                currentAccount.value.sTenDonVi = data.don_vi[0].sTenDonVi;
            }
            else if (data.FK_MaQuyen == 5) {
                currentAccount.value.macanhan = data.ca_nhan[0].PK_MaCaNhan;
                currentAccount.value.tencanhan = data.ca_nhan[0].sTenCaNhan;
                currentAccount.value.tenchucvu = data.ca_nhan[0].sTenChucVu;
                currentAccount.value.gioitinh = data.ca_nhan[0].bGioiTinh;
                currentAccount.value.PK_MaDonVi = data.ca_nhan[0].FK_MaDonVi;
            }
            nextTick(() => {
                const dv = document.getElementById("accountModal_maDonVi");
                if (dv) dv.disabled = true;
                const un = document.getElementById("accountModal_username");
                if (un) un.disabled = true;
                const cn = document.getElementById("accountModal_maCaNhan");
                if (cn) cn.disabled = true;
            });
        }
    }).catch(error => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            icon: 'error',
            text: error.message,
            timerProgressBar: true,
        })
    });
};

const confirmDelete = (account) => {
    Swal.fire({
        title: "Xác nhận",
        text: `Bạn có chắc chắn muốn xóa tài khoản ${account.sUsername}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Đồng ý",
        cancelButtonText: "Hủy",
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
    }).then(async (result) => {
        if (result.isConfirmed) {
            const response = await axios.delete(`/api/taikhoan/delete/${account.PK_MaTaiKhoan}`, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`
                }
            });
            if (response.status === 200) {
                // Refresh accounts list
                accounts.value = accounts.value.filter(a => a.PK_MaTaiKhoan !== account.PK_MaTaiKhoan);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'success',
                    text: 'Xóa tài khoản thành công',
                    timerProgressBar: true,
                });
            }
        }
    });
};

const saveAccount = () => {
    if (isEditMode.value) {
        let data = {
            id: currentAccount.value.id,
            password: currentAccount.value.sPassword,
            myemail: currentAccount.value.email,
            role: currentAccount.value.FK_MaQuyen,
        };

        if (currentAccount.value.FK_MaQuyen == '4') {
            data.madonvi = currentAccount.value.PK_MaDonVi;
            data.tendonvi = currentAccount.value.sTenDonVi;
        } else if (currentAccount.value.FK_MaQuyen == '5') {
            data.madonvi = currentAccount.value.PK_MaDonVi;
            data.tencanhan = currentAccount.value.tencanhan;
            data.tenchucvu = currentAccount.value.tenchucvu;
            data.gioitinh = currentAccount.value.gioitinh;
            data.macanhan = currentAccount.value.macanhan;
        }
        if (changePass.value) {
            data.password = currentAccount.value.sPassword
        }
        data.changePass = changePass.value
        axios.put('/api/taikhoan/update', data,
            {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`
                }
            }
        )
            .then(response => {
                if (response.status == 200) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: 'success',
                        text: 'Lưu thành công',
                        timerProgressBar: true,
                    });
                    
                    // Refresh accounts list
                    fetchAccounts();
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'error',
                    text: 'Có lỗi xảy ra khi cập nhật tài khoản',
                    timerProgressBar: true,
                });
            });
    } else {
        let data = {
            username: currentAccount.value.sUsername,
            password: currentAccount.value.sPassword,
            myemail: currentAccount.value.email,
            role: currentAccount.value.FK_MaQuyen,
        };

        if (currentAccount.value.FK_MaQuyen == '4') {
            data.madonvi = currentAccount.value.PK_MaDonVi;
            data.tendonvi = currentAccount.value.sTenDonVi
        } else if (currentAccount.value.FK_MaQuyen == '5') {
            data.madonvi = currentAccount.value.PK_MaDonVi;
            data.macanhan = currentAccount.value.macanhan;
            data.tencanhan = currentAccount.value.tencanhan;
            data.tenchucvu = currentAccount.value.tenchucvu;
            data.gioitinh = currentAccount.value.gioitinh;
        }

        axios.post('/api/taikhoan/add', data,
            {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`
                }
            }
        )
            .then(response => {
                if (response.status == 200) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: response.data.icon,
                        text: response.data.message,
                        timerProgressBar: true,
                    });
                    
                    // Add new account to the list
                    if (response.data.taikhoan) {
                        accounts.value.push(response.data.taikhoan);
                    } else {
                        // Refresh the entire list if the response structure doesn't include the new account
                        fetchAccounts();
                    }
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'error',
                    text: 'Có lỗi xảy ra khi thêm tài khoản',
                    timerProgressBar: true,
                });
            });
    }
    document.getElementById("accountModal").querySelector(".btn-close").click();
};

const getStatusBadgeClass = (status) => {
    return status == 1 ? "badge bg-success" : "badge bg-secondary";
};

const fetchDonViList = async () => {
    try {
        const response = await axios.get('/api/taikhoan/donvi', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200) {
            listDonVi.value = response.data.danhSachDonVi;
        }
    } catch (error) {
        console.error("Lỗi khi lấy danh sách đơn vị:", error);
        listDonVi.value = [];
    }
};

const toggleAccountStatus = (account) => {
    Swal.fire({
        title: "Xác nhận",
        text: `Bạn có chắc chắn muốn ${account.bTrangThai == 1 ? "khóa" : "mở khóa"} tài khoản này?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Đồng ý",
        cancelButtonText: "Hủy",
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
    }).then((result) => {
        if (result.isConfirmed) {
            updateAccountStatus(account);
        }
    });
};

const updateAccountStatus = (account) => {
    let newStatus = account.bTrangThai == 1 ? 0 : 1;

    axios.put(`/api/taikhoan/lock/${account.PK_MaTaiKhoan}`, { trangThai: newStatus }, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    })
        .then(response => {
            if (response.status === 200) {
                account.bTrangThai = newStatus;
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'success',
                    text: "Cập nhật trạng thái thành công",
                    timerProgressBar: true,
                });
            }
        })
        .catch(error => {
            console.error("Lỗi khi cập nhật trạng thái tài khoản:", error);
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                icon: 'error',
                text: "Có lỗi xảy ra khi cập nhật trạng thái",
                timerProgressBar: true,
            });
        });
};

const fetchAccounts = () => {
    const token = localStorage.getItem('api_token');
    axios.get('/api/taikhoan/list', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
        .then(response => {
            if (response.data && Array.isArray(response.data.data)) {
                accounts.value = response.data.data;
            } else {
                accounts.value = [];
            }
        })
        .catch(error => {
            console.error("Lỗi khi lấy danh sách tài khoản:", error);
            accounts.value = [];
        });
};

onMounted(() => {
    fetchAccounts();
    fetchDonViList();
});
</script>