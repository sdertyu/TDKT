import { createApp } from 'vue';
import App from './App.vue';
import router from './src/router'; // Đường dẫn này có thể cần chỉnh sửa tùy vào dự án của bạn
import Swal from 'sweetalert2';
import axios from 'axios';

// Tạo ứng dụng Vue
const app = createApp(App);

// Sử dụng Vue Router
app.use(router);

// Thiết lập Global Properties
app.config.globalProperties.$swal = Swal; // Gọi SweetAlert2 bằng `this.$swal`
app.config.globalProperties.$axios = axios; // Gọi Axios bằng `this.$axios`
// app.config.globalProperties.$router = router; // Gán vào global để gọi từ `this.$router`


// Gắn App vào DOM
app.mount('#app');

// Import Bootstrap & FontAwesome (chạy sau khi mount app)
import './bootstrap';
import "bootstrap/dist/js/bootstrap.min.js";
import '@fortawesome/fontawesome-free/js/all.js';
