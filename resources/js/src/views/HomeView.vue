<template>
    <div class="home-container">
        <h1 class="welcome-title">Trang chủ</h1>
        <div class="welcome-message">
            <p>Chào mừng đến với hệ thống quản lý thi đua khen thưởng Trường Đại học Mở Hà Nội</p>
            <p>Hãy sử dụng các tính năng trong menu để bắt đầu.</p>
        </div>
        <div class="logo-container">
            <img src="/images/logo_hou.png" alt="Logo HOU" class="logo-image" />
        </div>
    </div>
</template>

<style scoped>
.home-container {
    padding: 2rem;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.welcome-title {
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.welcome-message {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    line-height: 1.6;
    color: #34495e;
}

.logo-container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: -1;
    height: 100%;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-image {
    max-width: 300px;
    max-height: 80%;
    opacity: 0.25;
}

.home-image img {
    max-width: 100%;
    height: auto;
    max-height: 300px;
}
</style>