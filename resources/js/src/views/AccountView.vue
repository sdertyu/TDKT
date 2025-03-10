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
                                    <tr v-for="account in paginatedAccounts" :key="account.id">
                                        <td class="text-center">{{ account.id }}</td>
                                        <td>{{ account.username }}</td>
                                        <!-- <td>{{ account.email }}</td> -->
                                        <!-- <td>
                                            <span :class="getRoleBadgeClass(account.role)">{{
                                                account.role
                                                }}</span>
                                        </td> -->
                                        <td class="text-center">
                                            <span :class="getStatusBadgeClass(account.status)">{{
                                                account.status ? "Hoạt động" : "Tạm ngưng"
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
                                <input type="text" class="form-control" id="username" v-model="currentAccount.username"
                                    required />
                            </div>
                            <!-- <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" v-model="currentAccount.email"
                                    required />
                            </div> -->
                            <div class="mb-3" v-if="!isEditMode">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <input :type="isCheckPass ? 'text' : 'password'" class="form-control" id="password"
                                        v-model="currentAccount.password" required />
                                    <button @click="checkPass" class="input-group-text bg-light">
                                        <i :class="isCheckPass ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Vai trò</label>
                                <select class="form-select" id="role" v-model="currentAccount.role" required>
                                    <!-- <option value="Admin"></option> -->
                                    <option value="" disabled selected>Lựa chọn vai trò</option>
                                    <option value="Cá nhân">Cá nhân</option>
                                    <option value="Đơn vị">Đơn vị</option>
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

                            <div v-if="currentAccount.role == 'Cá nhân'">
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Mã cá nhân</label>
                                    <input type="text" class="form-control" id="maCaNhan"
                                         required />
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Tên cá nhân</label>
                                    <input type="text" class="form-control" id="fullName"
                                         required />
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Email</label>
                                    <input type="text" class="form-control" id="email"
                                         required />
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Tên chức vụ</label>
                                    <input type="text" class="form-control" id="tenChucVu"
                                         required />
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Giới tính</label>
                                    <div class="input-group">
                                        <input type="radio" name="gioiTinh" id="nam" value="0">
                                        <label for="nam" class="mx-2">Nam</label>
                                        <input type="radio" name="gioiTinh" id="nu" value="1">
                                        <label for="nu" class="mx-2">Nữ</label>
                                    </div>
                                </div>
                            </div>

                            <div v-if="currentAccount.role == 'Đơn vị'">
                                <div class="mb-3">
                                    <label for="username" class="form-label text">Mã đơn vị</label>
                                    <input type="text" class="form-control" id="maDonVi"
                                         required />
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label tex">Tên đơn vị</label>
                                    <input type="text" class="form-control" id="tenDonVi"
                                         required />
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
                        <strong>{{ currentAccount.username }}</strong>?
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
import { ref, computed } from 'vue';

let caNhanHtml = `
    <div class="mb-3">
        <label for="username" class="form-label tex">Tên đăng nhập</label>
        <input type="text" class="form-control" id="username" v-model="currentAccount.username"
            required />
    </div>
    `
const isCheckPass = ref(false)
const vaiTro = ref('')

const checkPass = () => {
    isCheckPass.value = !isCheckPass.value;
}

const caNhanSelect = () => {
    // vaiTro.value = 
}

const donViSelect = () => {

}

// Data
const accounts = ref([
    {
        id: 1,
        username: "admin",
        email: "admin@example.com",
        role: "Admin",
        status: true,
    },
    {
        id: 2,
        username: "manager",
        email: "manager@example.com",
        role: "Đơn vị",
        status: true,
    },
    {
        id: 3,
        username: "user1",
        email: "user1@example.com",
        role: "Cá nhân",
        status: true,
    },
    {
        id: 4,
        username: "user2",
        email: "user2@example.com",
        role: "Cá nhân",
        status: false,
    },
    {
        id: 5,
        username: "user3",
        email: "user3@example.com",
        role: "Cá nhân",
        status: true,
    },
    {
        id: 6,
        username: "user4",
        email: "user4@example.com",
        role: "Cá nhân",
        status: true,
    },
    {
        id: 7,
        username: "user5",
        email: "user5@example.com",
        role: "Cá nhân",
        status: false,
    },
    {
        id: 8,
        username: "user6",
        email: "user6@example.com",
        role: "Cá nhân",
        status: true,
    },
    {
        id: 9,
        username: "user7",
        email: "user7@example.com",
        role: "Cá nhân",
        status: true,
    },
    {
        id: 10,
        username: "user8",
        email: "user8@example.com",
        role: "Cá nhân",
        status: false,
    },
    {
        id: 11,
        username: "user9",
        email: "user9@example.com",
        role: "Đơn vị",
        status: true,
    },
    {
        id: 12,
        username: "user10",
        email: "user10@example.com",
        role: "Cá nhân",
        status: true,
    },
]);

const currentAccount = ref({
    id: null,
    username: "",
    email: "",
    password: "",
    role: "",
    status: true,
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
        username: "",
        email: "",
        password: "",
        role: "",
        status: true,
    };
    vaiTro.value = ''
};

const openEditModal = (account) => {
    isEditMode.value = true;
    currentAccount.value = { ...account };
};

const confirmDelete = (account) => {
    currentAccount.value = { ...account };
};

const saveAccount = () => {
    if (isEditMode.value) {
        const index = accounts.value.findIndex((a) => a.id === currentAccount.value.id);
        if (index !== -1) {
            accounts.value[index] = { ...currentAccount.value };
        }
    } else {
        const newId = Math.max(...accounts.value.map((a) => a.id), 0) + 1;
        accounts.value.push({
            ...currentAccount.value,
            id: newId,
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
</script>