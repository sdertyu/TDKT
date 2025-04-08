<template>
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
            </ul>


            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a @click="checkNotifications" href="#" class="nav-link position-relative"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-bell-fill fs-5"></i>
                        <span v-if="soChuaDoc !== 0"
                            class="position-absolute translate-middle badge rounded-pill text-bg-warning"
                            style="font-size: 0.65rem;">
                            {{ soChuaDoc }}
                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end custom-dropdown">
                        <li>
                            <span class="dropdown-item dropdown-header">Thông báo</span>
                            <hr class="dropdown-divider">
                        </li>

                        <li v-for="(item, index) in listThongBao" :key="index"
                            @click="goTo('/thongbao/' + item.PK_MaThongBao)">
                            <div class="dropdown-item notification-item"
                                :style="item.daDoc === null ? 'background-color: #d7dadc;' : ''">
                                <div class="notification-title fw-bold mb-1">
                                    <i :class="item.daDoc === null ? 'bi bi-mailbox2-flag' : 'bi bi-mailbox2'"></i>
                                    {{ item.sTieuDe }}
                                </div>
                                <div class="notification-text">
                                    {{ item.sNoiDung }}
                                </div>
                                <div class="notification-time text-muted text-end mt-1 fs-7">{{ item.dNgayTao }}</div>
                            </div>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a href="#" class="dropdown-item text-center text-primary">Xem tất cả</a></li>
                    </ul>
                </li>


                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <span class="d-none d-md-inline">{{ ten }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-xl">
                        <li class="dropdown-item" @click="goTo('/profile')" type="button"><i
                                class="fa-solid fa-gear"></i> Tài khoản của tôi</li>
                        <li class="dropdown-item" type="button" @click="logout"><i
                                class="fa-solid fa-right-from-bracket"></i> Đăng xuất</li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</template>
<script setup>

import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const listThongBao = ref([]);
const soChuaDoc = ref(0);


const ten = localStorage.getItem('ten') ? localStorage.getItem('ten') : "Phòng TCHC";
const router = useRouter();
const logout = () => {
    axios.post('/api/logout', {}, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    }).then(response => {
        localStorage.removeItem('api_token');
        localStorage.removeItem('hasShownLoginAlert');
        router.push('/login');
    });
}

const checkNotifications = () => {
    if (soChuaDoc.value !== 0) {
        const readNotifications = axios.post('/api/thongbao/markread', {}, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        }).then(response => {
            if (response.status === 200) {
                // listThongBao.value.forEach(item => {
                //     item.daDoc = 1;
                // });
                soChuaDoc.value = 0;
            } else {
                toastError("Có lỗi xảy ra khi đánh dấu thông báo đã đọc");
            }
        }).catch(error => {
            console.log("Có lỗi xảy ra khi đánh dấu thông báo đã đọc");
        });
    }

}

const goTo = (link) => {
    router.push(link);
}

const getAllNotifications = () => {
    axios.get('/api/thongbao/getlisttheoquyen', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    }).then(response => {
        if (response.status === 200) {
            listThongBao.value = response.data.data;
            listThongBao.value.forEach(item => {
                if (item.daDoc === null) {
                    soChuaDoc.value++;
                }
            });
        } else {
            toastError("Có lỗi xảy ra khi lấy danh sách thông báo");
        }
    }).catch(error => {
        toastError("Có lỗi xảy ra khi lấy danh sách thông báo");
    });
}

onMounted(() => {
    getAllNotifications();
});

</script>

<style scoped>
.custom-dropdown {
    width: 600px !important;
    /* max-width: 100%; */
    max-height: 700px;
    overflow-y: auto;
    padding: 0.5rem 0;
}

.notification-item {
    white-space: normal;
    word-wrap: break-word;
}

.notification-text {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 0.875rem;
    color: #444;
}

.notification-title {
    font-size: 0.95rem;
    color: #111;
}

@media (max-width: 576px) {
    .custom-dropdown {
        width: 100vw;
        left: 0 !important;
        right: 0 !important;
        transform: none !important;
        border-radius: 0;
    }
}
</style>