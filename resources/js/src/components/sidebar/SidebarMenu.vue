<!-- SidebarMenu.vue - Component chính -->
<template>
    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
        <sidebar-item v-for="(item, index) in menuItems" :key="index" :item="item">
        </sidebar-item>
    </ul>
</template>

<script setup>
import SidebarItem from './SidebarItem.vue';


const props = defineProps({
    "menuItems": {
        type: Array,
        required: true
    }
})

</script>