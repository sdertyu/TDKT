<template>
    <div class="forbidden-container">
        <i class="fas fa-lock lock-icon"></i>
        <h1>Access Forbidden</h1>
    </div>
</template>

<script>
export default {
    name: 'ForbiddenView'
}
</script>

<style scoped>
.forbidden-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
}

.lock-icon {
    font-size: 100px;
    margin-bottom: 20px;
    animation: lock-bounce 1s infinite;
}

@keyframes lock-bounce {

    0%,
    20%,
    50%,
    80%,
    100% {
        transform: translateY(0);
    }

    40% {
        transform: translateY(-20px);
    }

    60% {
        transform: translateY(-10px);
    }
}
</style>
