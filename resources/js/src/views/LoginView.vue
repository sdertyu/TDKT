<template>
    <div class="login-page bg-light min-vh-100 d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="card shadow-lg border-0 rounded-lg mt-5">
                        <div class="card-header bg-primary text-white text-center py-4">
                            <img src="/images/logo_hou.png" style="height: 80px;" alt="">
                            <h3 class="font-weight-bold mb-0">Hệ thống quản lý thi đua khen thưởng</h3>
                        </div>
                        <div class="card-body p-4">

                            <form @submit.prevent="submitLogin">
                                <!-- Username -->
                                <div class="form-floating mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="username"
                                            placeholder="Tên đăng nhập" v-model="username" required
                                            :class="{ 'is-invalid': usernameError }" />
                                        <div class="invalid-feedback">{{ usernameError }}</div>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="form-floating mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                            id="password" placeholder="Mật khẩu" v-model="password" required
                                            :class="{ 'is-invalid': passwordError }" />
                                        <button class="input-group-text bg-light" type="button" @click="togglePassword">
                                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                        </button>
                                        <div class="invalid-feedback">{{ passwordError }}</div>
                                    </div>
                                </div>
                                <p class="mb-2">Mật khẩu test: Chithien123456@</p>

                                <!-- Thông báo lỗi -->
                                <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show"
                                    role="alert">
                                    {{ errorMessage }}
                                    <button type="button" class="btn-close" @click="errorMessage = ''"></button>
                                </div>

                                <!-- Login Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg py-2" :disabled="isLoading">
                                        <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"
                                            role="status"></span>
                                        {{ isLoading ? 'Đang đăng nhập...' : 'Đăng nhập' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="text-center mt-3 text-muted small">
                        <p>© 2025 Hệ thống quản lý Thi đua khen thưởng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// 
import { ref } from 'vue';

const username = ref('');
const password = ref('');
const showPassword = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');
const usernameError = ref('');
const passwordError = ref('');

// Hiển thị/ẩn mật khẩu
const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

// Kiểm tra tính hợp lệ của form
const validateForm = () => {
    let isValid = true;
    usernameError.value = '';
    passwordError.value = '';

    if (!username.value.trim()) {
        usernameError.value = 'Vui lòng nhập tên đăng nhập';
        isValid = false;
    }
    if (!password.value) {
        passwordError.value = 'Vui lòng nhập mật khẩu';
        isValid = false;
    }
    // if (password.value.length < 8) {
    //     passwordError.value = 'Mật khẩu phải có ít nhất 8 ký tự';
    //     isValid = false;
    // } else {
    //     // Check for uppercase, lowercase, number, and special character
    //     const hasUpperCase = /[A-Z]/.test(password.value);
    //     const hasLowerCase = /[a-z]/.test(password.value);
    //     const hasNumbers = /\d/.test(password.value);
    //     const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password.value);
        
    //     if (!hasUpperCase || !hasLowerCase || !hasNumbers || !hasSpecialChar) {
    //         passwordError.value = 'Mật khẩu phải chứa chữ hoa, chữ thường, chữ số và ký tự đặc biệt';
    //         isValid = false;
    //     }
    // }

    return isValid;
};

// Xử lý khi submit form
const submitLogin = () => {
    if (!validateForm()) return;

    isLoading.value = true;
    axios.post('/api/login', {
        username: username.value,
        password: password.value,
    })
        .then(response => {
            if (response.data.message === 'success') {
                console.log(response.data);
                sessionStorage.setItem('api_token', response.data.user.api_token);
                sessionStorage.setItem('role', response.data.user.FK_MaQuyen);
                sessionStorage.setItem('user_name', response.data.user.sUsername);
                switch (response.data.user.FK_MaQuyen) {
                    case 2:
                        sessionStorage.setItem('ten', "Phòng TCHC");
                        break;
                    case 3:
                        sessionStorage.setItem('ten', "Hội đồng TĐKT");
                        break;
                    case 4:
                        sessionStorage.setItem('ten', response.data.user.don_vi.sTenDonVi);
                        sessionStorage.setItem('maDonVi', response.data.user.don_vi.PK_MaDonVi);
                        break;
                    case 5:
                        sessionStorage.setItem('ten', response.data.user.ca_nhan.sTenCaNhan);
                        break;
                    default:
                        sessionStorage.setItem('ten', 'Unknown');
                        break;
                }
                window.location.href = '/';

            } else {
                errorMessage.value = response.data.message || 'Đăng nhập thất bại';
            }
        })
        .catch(error => {
            errorMessage.value = error.response?.data?.message || 'Đã xảy ra lỗi. Vui lòng thử lại sau.';
        })
        .finally(() => {
            isLoading.value = false;
        });
};
</script>

<style scoped>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.login-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e7ef 100%);
}
</style>
