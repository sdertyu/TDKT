<template>
    <div class="login-page bg-light min-vh-100 d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="card shadow-lg border-0 rounded-lg mt-5">
                        <div
                            class="card-header bg-primary text-white text-center py-4 d-flex align-items-center justify-center">
                            <img src="/images/logo_hou.png" style="height: 80px;" alt="">
                            <h3 class="font-weight-bold mb-0">
                                Hệ thống quản lý thi đua khen thưởng
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <!-- Thông báo lỗi -->
                            <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show"
                                role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ errorMessage }}
                                <button type="button" class="btn-close" @click="errorMessage = ''"></button>
                            </div>

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
                                <!-- Login Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg py-2" :disabled="isLoading">
                                        <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"
                                            role="status"></span>
                                        <i v-else class="fas fa-sign-in-alt me-2"></i>
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
import axios from 'axios';
// import api from '../services/api';
import { ref } from 'vue';

// Khai báo biến reactive
const username = ref('');
const password = ref('');
const rememberMe = ref(false);
const showPassword = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');
const usernameError = ref('');
const passwordError = ref('');

// Hàm để hiển thị/ẩn mật khẩu
const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

// Kiểm tra tính hợp lệ của form
const validateForm = () => {
    let isValid = true;

    // Reset lỗi
    usernameError.value = '';
    passwordError.value = '';

    // Kiểm tra username
    if (!username.value.trim()) {
        usernameError.value = 'Vui lòng nhập tên đăng nhập';
        isValid = false;
    }

    // Kiểm tra password
    if (!password.value) {
        passwordError.value = 'Vui lòng nhập mật khẩu';
        isValid = false;
    } else if (password.value.length < 6) {
        passwordError.value = 'Mật khẩu phải có ít nhất 6 ký tự';
        isValid = false;
    }

    return isValid;
};

// Hàm xử lý khi submit form
const submitLogin = async () => {
    if (!validateForm()) return;

    // Giả lập đăng nhập
    try {
        axios.post('/api/login', {
            username: username.value,
            password: password.value,
        }).then(response => {
            console.log(response);
        }).catch(error => {
            console.error('Error fetching accounts:', error);
        })
        
        
    } catch (error) {
        console.error('Lỗi đăng nhập:', error);
        errorMessage.value = 'Đã xảy ra lỗi khi đăng nhập. Vui lòng thử lại sau.';
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
/* Tùy chỉnh thêm (ngoài các class của Bootstrap và AdminLTE) */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
}

.btn-outline-primary {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    padding: 0;
    line-height: 40px;
    text-align: center;
}

.btn-outline-primary i {
    line-height: 38px;
}

.login-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e7ef 100%);
}
</style>