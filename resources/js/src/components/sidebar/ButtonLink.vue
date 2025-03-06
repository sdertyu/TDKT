<template>
    <li class="nav-item d-none d-md-block">
        <a :href="link" class="nav-link">{{ name }}</a>
    </li>
</template>

<script setup>
const props = defineProps({
    "name": String,
    "link": String
})
</script>