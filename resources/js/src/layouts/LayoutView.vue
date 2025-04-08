<template>
  <div class="app-wrapper">
    <HeaderApp />
    <SidebarApp />
    <ContentApp />
    <FooterApp />
    <LoadingView v-if="loadingStore.loading" />
  </div>
</template>

<script setup>
import SidebarApp from "../components/common/SidebarApp.vue";
import HeaderApp from "../components/common/HeaderApp.vue";
import FooterApp from "../components/common/FooterApp.vue";
import ContentApp from "../components/common/ContentApp.vue";
import LoadingView from "../components/common/LoadingView.vue";

import { onMounted } from 'vue'

const loadingStore = useGlobalStore()

onMounted(async () => {
  const adminlte = await import('admin-lte/dist/js/adminlte.min.js');
});

</script>
