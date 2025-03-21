<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Quản lý tài khoản</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Quản lý tài khoản</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách tài khoản</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" @click="openAddModal" data-bs-toggle="modal"
                                data-bs-target="#accountModal">
                                <i class="fas fa-plus"></i> Thêm tài khoản
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%; text-align: center;">STT</th>
                                        <th style="width: 20%; text-align: center;">Tên đăng nhập</th>
                                        <!-- <th style="width: 30%">Mật khẩu</th> -->
                                        <!-- <th style="width: 15%">Vai trò</th> -->
                                        <th style="width: 15%; text-align: center;">Trạng thái</th>
                                        <th style="width: 15%; text-align: center;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(account, index) in paginatedAccounts" :key="index">
                                        <td class="text-center">{{ ++index }}</td>
                                        <td>{{ account.sUsername }}</td>
                                        <!-- <td>{{ account.email }}</td> -->
                                        <!-- <td>
                                            <span :class="getRoleBadgeClass(account.role)">{{
                                                account.role
                                                }}</span>
                                        </td> -->
                                        <td class="text-center">
                                            <span :class="getStatusBadgeClass(account.sTrangThai)">{{
                                                account.sTrangThai == 1 ? "Hoạt động" : "Tạm ngưng"
                                            }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info me-1" @click="openEditModal(account)"
                                                data-bs-toggle="modal" data-bs-target="#accountModal">
                                                <i class="fas fa-edit"></i> Sửa
                                            </button>
                                            <button class="btn btn-sm btn-danger" @click="confirmDelete(account)"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <span>Hiển thị {{ startIndex + 1 }} đến {{ endIndex }} trên tổng số
                                    {{ accounts.length }} mục</span>
                            </div>
                            <div>
                                <ul class="pagination mb-0">
                                    <li :class="['page-item', { disabled: currentPage === 1 }]">
                                        <a class="page-link" href="#" @click.prevent="changePage(1)">
                                            <i class="fas fa-angle-double-left"></i>
                                        </a>
                                    </li>
                                    <li :class="['page-item', { disabled: currentPage === 1 }]">
                                        <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                                            <i class="fas fa-angle-left"></i>
                                        </a>
                                    </li>

                                    <li v-for="page in displayedPages" :key="page"
                                        :class="['page-item', { active: currentPage === page }]">
                                        <a class="page-link" href="#" @click.prevent="changePage(page)">{{
                                            page
                                        }}</a>
                                    </li>

                                    <li :class="['page-item', { disabled: currentPage === totalPages }]">
                                        <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">
                                            <i class="fas fa-angle-right"></i>
                                        </a>
                                    </li>
                                    <li :class="['page-item', { disabled: currentPage === totalPages }]">
                                        <a class="page-link" href="#" @click.prevent="changePage(totalPages)">
                                            <i class="fas fa-angle-double-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <div class="input-group">
                                    <span class="input-group-text">Hiển thị</span>
                                    <select class="form-select" v-model="itemsPerPage" @change="currentPage = 1">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="input-group-text">mục</span>
                                </div>
                            </div>
                        </div>
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
                            <div class="mb-3">
                                <label for="username" class="form-label tex">Tên đăng nhập</label>
                                <input type="text" class="form-control" id="username" v-model="currentAccount.sUsername"
                                    required />
                            </div>
                            <!-- <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" v-model="currentAccount.email"
                                    required />
                            </div> -->
                            <div class="mb-3" v-if="isEditMode && changePass">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <input :type="isCheckPass ? 'text' : 'password'" class="form-control" id="password"
                                        v-model="currentAccount.sPassword" required />
                                    <button @click="checkPass" class="input-group-text bg-light">
                                        <i :class="isCheckPass ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3" v-if="isEditMode">
                                <!-- <label for="password" class="form-label">Đổi mật khẩu</label> -->
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
                                    <!-- <option value="Admin"></option> -->
                                    <option value="" disabled selected>-- Lựa chọn vai trò --</option>
                                    <option value="3">Hội đồng thi đua khen thưởng</option>
                                    <option value="4">Đơn vị</option>
                                    <option value="5">Cá nhân</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status"
                                        v-model="currentAccount.status" />
                                    <label class="form-check-label" for="status">
                                        {{ currentAccount.status ? "Hoạt động" : "Tạm ngưng" }}
                                    </label>
                                </div>
                            </div>

                            <div v-if="currentAccount.FK_MaQuyen == '5'">
                                <!-- <div class="mb-3">
                                    <label for="username" class="form-label tex">Mã cá nhân</label>
                                    <input type="text" class="form-control" id="maCaNhan"
                                        v-model="currentAccount.macanhan" required />
                                </div> -->
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
                                    <label for="username" class="form-label tex">Email</label>
                                    <input type="text" class="form-control" id="email" v-model="currentAccount.email"
                                        required />
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
                                    <input type="text" class="form-control" id="maDonVi"
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

        <!-- Modal xác nhận xóa -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Bạn có chắc chắn muốn xóa tài khoản
                        <strong>{{ currentAccount.sUsername }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Hủy
                        </button>
                        <button type="button" class="btn btn-danger" @click="deleteAccount" data-bs-dismiss="modal">
                            Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// import axios from 'axios';
import axios from 'axios';
import { data } from 'jquery';
import Swal from 'sweetalert2';
import { ref, computed, onMounted, watch } from 'vue';

const accounts = ref([]);
const listDonVi = ref([]);
const changePass = ref(false);

const isCheckPass = ref(false)
const vaiTro = ref('')

const checkPass = () => {
    isCheckPass.value = !isCheckPass.value;
}

watch(changePass, (newValue) => {
    // Chỉ thay đổi giá trị nếu nó khác với giá trị hiện tại
    if (newValue !== changePass.value) {
        changePass.value = !newValue;  // Lật giá trị của `changePass`
    }
    console.log(changePass.value); // In giá trị mới của `changePass`
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
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Computed properties
const totalPages = computed(() => Math.ceil(accounts.value.length / itemsPerPage.value));

const paginatedAccounts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return accounts.value.slice(start, end);
});

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);

const endIndex = computed(() => {
    const end = startIndex.value + itemsPerPage.value;
    return end > accounts.value.length ? accounts.value.length : end;
});

const displayedPages = computed(() => {
    const maxPages = 5;
    let startPage = Math.max(1, currentPage.value - Math.floor(maxPages / 2));
    let endPage = startPage + maxPages - 1;

    if (endPage > totalPages.value) {
        endPage = totalPages.value;
        startPage = Math.max(1, endPage - maxPages + 1);
    }

    let pages = [];
    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }
    return pages;
});

// Methods
const changePage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

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
    changePass.value = false
    vaiTro.value = ''
};

const openEditModal = (account) => {
    isEditMode.value = true;
    // currentAccount.value = { ...account };
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
            if (data.FK_MaQuyen == 4) {
                currentAccount.value.PK_MaDonVi = data.don_vi[0].PK_MaDonVi;
                currentAccount.value.sTenDonVi = data.don_vi[0].sTenDonVi;
            }
            else if (data.FK_MaQuyen == 5) {
                currentAccount.value.macanhan = data.ca_nhan[0].PK_MaCaNhan;
                currentAccount.value.tencanhan = data.ca_nhan[0].sTenCaNhan;
                currentAccount.value.tenchucvu = data.ca_nhan[0].sTenChucVu;
                currentAccount.value.email = data.ca_nhan[0].sEmail;
                currentAccount.value.gioitinh = data.ca_nhan[0].bGioiTinh;
                currentAccount.value.PK_MaDonVi = data.ca_nhan[0].FK_MaDonVi;
                console.log(currentAccount.value.PK_MaDonVi);
            }
            // 
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
    currentAccount.value = { ...account };
};

const saveAccount = () => {
    if (isEditMode.value) {
        let data = {};

        if (currentAccount.value.FK_MaQuyen == '3') {
            data = {
                username: currentAccount.value.sUsername,
                role: currentAccount.value.FK_MaQuyen,
            }
        }
        else if (currentAccount.value.FK_MaQuyen == '4') {
            data = {
                username: currentAccount.value.sUsername,
                role: currentAccount.value.FK_MaQuyen,
                madonvi: currentAccount.value.PK_MaDonVi,
                tendonvi: currentAccount.value.sTenDonVi
            };
        } else if (currentAccount.value.FK_MaQuyen == '5') {
            data = {
                id: currentAccount.value.id,
                username: currentAccount.value.sUsername,
                role: currentAccount.value.FK_MaQuyen,
                madonvi: currentAccount.value.PK_MaDonVi,
                tencanhan: currentAccount.value.tencanhan,
                myemail: currentAccount.value.email,
                tenchucvu: currentAccount.value.tenchucvu,
                gioitinh: currentAccount.value.gioitinh,
                macanhan: currentAccount.value.macanhan
            };
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
                        text: 'fff',
                        timerProgressBar: true,
                    });
                }
            })
            .catch(error => {
                console.error(error);
            });
    } else {
        let data = {};

        if (currentAccount.value.FK_MaQuyen == '3') {
            data = {
                username: currentAccount.value.sUsername,
                password: currentAccount.value.sPassword,
                role: currentAccount.value.FK_MaQuyen,
            }
        }
        else if (currentAccount.value.FK_MaQuyen == '4') {
            data = {
                username: currentAccount.value.sUsername,
                password: currentAccount.value.sPassword,
                role: currentAccount.value.FK_MaQuyen,
                madonvi: currentAccount.value.PK_MaDonVi,
                tendonvi: currentAccount.value.sTenDonVi
            };
        } else if (currentAccount.value.FK_MaQuyen == '5') {
            data = {
                username: currentAccount.value.sUsername,
                password: currentAccount.value.sPassword,
                role: currentAccount.value.FK_MaQuyen,
                madonvi: currentAccount.value.PK_MaDonVi,
                tencanhan: currentAccount.value.tencanhan,
                myemail: currentAccount.value.email,
                tenchucvu: currentAccount.value.tenchucvu,
                gioitinh: currentAccount.value.gioitinh
            };
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
                }
            })
            .catch(error => {
                console.error(error);
            });


    }
    document.getElementById("accountModal").querySelector(".btn-close").click();
};

const deleteAccount = () => {
    const index = accounts.value.findIndex((a) => a.id === currentAccount.value.id);
    if (index !== -1) {
        accounts.value.splice(index, 1);
    }
};

const getRoleBadgeClass = (role) => {
    switch (role) {
        case "Admin":
            return "badge bg-danger";
        case "Quản lý":
            return "badge bg-warning";
        default:
            return "badge bg-success";
    }
};

const getStatusBadgeClass = (status) => {
    return status ? "badge bg-success" : "badge bg-secondary";
};

const fetchDonViList = async () => {
    try {
        const response = await axios.get('/api/taikhoan/donvi', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200) {
            listDonVi.value = response.data.danhSachDonVi; // Giả sử API trả về danh sách trong `data`
            // console.log(response.data);
        }
    } catch (error) {
        console.error("Lỗi khi lấy danh sách đơn vị:", error);
        listDonVi.value = [];
    }
};

onMounted(() => {
    const token = localStorage.getItem('api_token');
    axios.get('/api/taikhoan/list', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
        .then(response => {


            // Kiểm tra nếu response.data có chứa danh sách tài khoản bên trong
            if (response.data && Array.isArray(response.data.data)) {
                accounts.value = response.data.data; // Gán danh sách tài khoản từ API
            } else {
                accounts.value = []; // Nếu không có dữ liệu hợp lệ, gán mảng rỗng để tránh lỗi
            }
        })
        .catch(error => {
            console.error("Lỗi khi lấy danh sách tài khoản:", error);
            accounts.value = []; // Xử lý lỗi bằng cách gán mảng rỗng để tránh lỗi .slice()
        });

    fetchDonViList();

});

</script>