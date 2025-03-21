<template>
  <div class="app-wrapper">
    <HeaderApp />
    <SidebarApp />
    <ContentApp />
    <FooterApp />
  </div>
</template>

<script setup>
import SidebarApp from "../components/common/SidebarApp.vue";
import HeaderApp from "../components/common/HeaderApp.vue";
import FooterApp from "../components/common/FooterApp.vue";
import ContentApp from "../components/common/ContentApp.vue";

import { onMounted } from 'vue'

onMounted(async () => {
  const adminlte = await import('admin-lte/dist/js/adminlte.min.js');
});

</script>
