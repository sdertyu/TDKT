<template>
    <div class="card card-primary card-outline m-4">

        <div class="card-header">
            <div class="card-title">Đổi mật khẩu</div>
        </div>

        <form @submit.prevent="changePassword">

            <div class="card-body">
                <div class="mb-3">
                    <label for="currentPassword" class="form-label">Mật khẩu hiện tại</label>
                    <div class="input-group">
                        <input :type="showPassword.currentPassword ? 'text' : 'password'" class="form-control"
                            id="currentPassword" v-model="currentPassword"
                            :class="{ 'is-invalid': errors.currentPassword }" required />
                        <button class="btn btn-outline-secondary" type="button"
                            @click="togglePassword('currentPassword')">
                            <i class="fa-solid fa-eye-slash" v-show="showPassword.currentPassword"></i>
                            <i class="fa-solid fa-eye" v-show="!showPassword.currentPassword"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" v-if="errors.currentPassword">
                        {{ errors.currentPassword }}
                    </div>
                </div>

                <!-- New Password -->
                <div class="mb-3">
                    <label for="newPassword" class="form-label">Mật khẩu mới</label>
                    <div class="input-group">
                        <input :type="showPassword.newPassword ? 'text' : 'password'" class="form-control"
                            id="newPassword" v-model="newPassword" :class="{ 'is-invalid': errors.newPassword }"
                            required />
                        <button class="btn btn-outline-secondary" type="button" @click="togglePassword('newPassword')">
                            <i :class="showPassword.newPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" v-if="errors.newPassword">
                        {{ errors.newPassword }}
                    </div>
                    <div class="form-text">
                        Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số.
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Xác nhận mật khẩu mới</label>
                    <div class="input-group">
                        <input :type="showPassword.confirmPassword ? 'text' : 'password'" class="form-control"
                            id="confirmPassword" v-model="confirmPassword"
                            :class="{ 'is-invalid': errors.confirmPassword }" required />
                        <button class="btn btn-outline-secondary" type="button"
                            @click="togglePassword('confirmPassword')">
                            <i class="fa-solid fa-eye-slash" v-show="showPassword.confirmPassword"></i>
                            <i class="fa-solid fa-eye" v-show="!showPassword.confirmPassword"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" v-if="errors.confirmPassword">
                        {{ errors.confirmPassword }}
                    </div>
                </div>

                <!-- Alert for success/error messages -->
                <div v-if="message.text" :class="`alert alert-${message.type} mt-3`" role="alert">
                    {{ message.text }}
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status"
                            aria-hidden="true"></span>
                        {{ isSubmitting ? 'Đang xử lý...' : 'Đổi mật khẩu' }}
                    </button>
                </div>
            </div>

        </form>

    </div>
</template>

<script setup>

import { ref, reactive } from 'vue'

// Reactive state
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const isSubmitting = ref(false)
const errors = reactive({
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
})
const message = reactive({
    text: '',
    type: 'info'
})
const showPassword = reactive({
    currentPassword: false,
    newPassword: false,
    confirmPassword: false
})

// Toggle password visibility
const togglePassword = (field) => {
    showPassword[field] = !showPassword[field]
    console.log(showPassword[field]);
}

// Form validation
const validateForm = () => {
    let isValid = true

    // Reset errors
    errors.currentPassword = ''
    errors.newPassword = ''
    errors.confirmPassword = ''

    // Validate current password
    if (!currentPassword.value) {
        errors.currentPassword = 'Vui lòng nhập mật khẩu hiện tại'
        isValid = false
    }

    // Validate new password
    if (!newPassword.value) {
        errors.newPassword = 'Vui lòng nhập mật khẩu mới'
        isValid = false
    } else if (newPassword.value.length < 8) {
        errors.newPassword = 'Mật khẩu phải có ít nhất 8 ký tự'
        isValid = false
    } else if (!/[A-Z]/.test(newPassword.value) ||
        !/[a-z]/.test(newPassword.value) ||
        !/[0-9]/.test(newPassword.value)) {
        errors.newPassword = 'Mật khẩu phải bao gồm chữ hoa, chữ thường và số'
        isValid = false
    }

    // Validate confirm password
    if (!confirmPassword.value) {
        errors.confirmPassword = 'Vui lòng xác nhận mật khẩu mới'
        isValid = false
    } else if (confirmPassword.value !== newPassword.value) {
        errors.confirmPassword = 'Mật khẩu xác nhận không khớp'
        isValid = false
    }

    return isValid
}

// Submit handler
const changePassword = async () => {
    // Clear previous messages
    message.text = ''

    // Validate form
    if (!validateForm()) {
        return
    }


    isSubmitting.value = true

    // Simulate API call
    await axios.put('/api/doi-mat-khau', {
        username: localStorage.getItem('user_name'),
        old_password: currentPassword.value,
        new_password: newPassword.value
    }, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    }).then(response => {
        // if(response.status == 200) {
        //     console.log("object");
        // }
        // Success message

        console.log(response);
        message.text = 'Đổi mật khẩu thành công!'
        message.type = 'success'

        // Reset form
        currentPassword.value = ''
        newPassword.value = ''
        confirmPassword.value = ''
    }).catch(error => {
        console.log(error);
    }).finally(() => {
        isSubmitting.value = false
    });
}
</script>

<style scoped>
.card {
    border-radius: 8px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
}

.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    border-color: #86b7fe;
}
</style>
