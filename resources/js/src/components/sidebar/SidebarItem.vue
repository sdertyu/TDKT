<!-- SidebarItem.vue - Component cho từng mục menu -->
<template>
    <li :class="['nav-item', { 'menu-open': item.isOpen }]">
        <!-- Menu cha (có submenu) -->
        <template v-if="hasChildren">
            <a href="#" :class="['nav-link', { 'active': item.isActive }]">
                <i :class="['nav-icon', item.icon]"></i>
                <p>
                    {{ item.title }}
                    <span v-if="item.badge" class="nav-badge badge text-bg-secondary me-3">{{ item.badge }}</span>
                    <i class="nav-arrow bi bi-chevron-right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <sidebar-item v-for="(child, index) in item.children" :key="index" :item="child">
                </sidebar-item>
            </ul>
        </template>

        <!-- Menu con (không có submenu) -->
        <template v-else>
            <router-link :to="item.link" :class="['nav-link', { 'active': item.isActive }]">
                <i :class="['nav-icon', item.icon]"></i>
                <p v-html="item.title"></p>
            </router-link>
        </template>
    </li>
</template>

<script setup>
import { defineProps, computed } from 'vue';

const props = defineProps({
    "item": {
        type: Object,
        required: true
    }
});

const hasChildren = computed(() => {
    return props.item.children && props.item.children.length > 0;
});
</script>