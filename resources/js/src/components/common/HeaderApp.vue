<template>
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
                <!-- <li class="nav-item d-none d-md-block"><a href="/" class="nav-link">Home</a></li>
                <li class="nav-item d-none d-md-block">
                    <a href="/about" class="nav-link">Contact</a>
                </li> -->
                <!-- <ButtonLink link="/" name="Home"/>
                <ButtonLink link="/about" name="About"/> -->
            </ul>
            

            <ul class="navbar-nav ms-auto">
                <!-- <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="bi bi-search"></i>
                    </a>
                </li> -->

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <!-- <img src="" class="user-image rounded-circle shadow"
                            alt="User Image" /> -->
                        <span class="d-none d-md-inline">{{ ten }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-">
                        <li class="dropdown-item" @click="goTo('/profile')" type="button"><i class="fa-solid fa-gear"></i> Tài khoản của tôi</li>
                        <li class="dropdown-item" type="button" @click="logout"><i
                                class="fa-solid fa-right-from-bracket"></i> Đăng xuất</li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</template>
<script setup>
import axios from 'axios';
import { useRouter } from 'vue-router';


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
    });F
}

const goTo = (link) => {
    router.push(link);
}

</script>
