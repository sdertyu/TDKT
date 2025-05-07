<template>
    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">HOU</div>
        <strong>Đồ án tốt nghiệp &copy;</strong>
        <span>2024-2025&nbsp; </span>
        <strong> Ngô Minh Phương & Phạm Duy Trường.</strong>
        All rights reserved.
    </footer>
</template>

<script setup>
import { onMounted } from 'vue';
import Swal from 'sweetalert2';

onMounted(async () => {
    const hasShownAlert = sessionStorage.getItem('hasShownLoginAlert');

    if (!hasShownAlert) {
        await Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            icon: 'success',
            text: 'Đăng nhập thành công',
            timerProgressBar: true,
        });
        sessionStorage.setItem('hasShownLoginAlert', 'true');
    }
});
</script>
