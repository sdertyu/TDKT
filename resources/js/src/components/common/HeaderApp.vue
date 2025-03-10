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
                <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="bi bi-search"></i>
                    </a>
                </li>
              
            </ul>
        </div>
    </nav>
</template>
<script setup>

import ButtonLink from '../sidebar/ButtonLink.vue';
</script>

