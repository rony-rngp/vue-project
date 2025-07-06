<script setup>

import {useSettings} from "../admin/useSettings.js";

const {getSettings} = useSettings();



</script>

<template>


    <div>
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                <div class="navbar-brand app-brand demo d-flex py-0 me-4 me-xl-8">
                    <!-- Mobile menu toggle: Start-->

                    <!-- Mobile menu toggle: End-->
                    <Link :href="route('home_page')" class="app-brand-link">
                      <span class="app-brand-logo demo">

                              <img src="" alt="">

                      </span>
                        <span class=" demo menu-text fw-bold ms-2 ps-1" style="font-size: 20px">{{ getSettings('website_name')}}</span>
                    </Link>
                </div>

                <ul class="navbar-nav flex-row align-items-center ms-auto">

                    <!-- User -->
                    <li v-if="$page.props.auth.user" class="nav-item lh-1 me-4">
                        <a class="github-button show_name" data-icon="octicon-star" data-size="large" data-show-count="true">{{ $page.props.auth.user.name }}</a>
                    </li>
                    <li v-if="$page.props.auth.user" class="nav-item navbar-dropdown dropdown-user dropdown">
                        <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                            <div class="avatar avatar-online">
                                <img :src="'/admin/assets/img/avatars/1.png'" style="height: 40px !important; width: 40px !important; object-fit: cover" class="w-px-40 h-auto rounded-circle" />
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-online">
                                                <img :src="'/admin/assets/img/avatars/1.png'" style="height: 40px !important;width: 40px !important; object-fit: cover" class="w-px-40 h-auto rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $page.props.auth.user.name }}</h6>
                                            <small class="text-muted">{{ $page.props.auth.user.email }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <div class="dropdown-divider my-1"></div>
                            </li>
                            <li>
                                <Link v-if="$page.props.auth.user.user_type == 'admin'" class="dropdown-item" :href="route('admin.dashboard')">
                                    <i class="bx bxs-dashboard bx-md me-3"></i><span>Dashboard</span>
                                </Link>
                                <Link v-else class="dropdown-item" :href="route('user.dashboard')">
                                    <i class="bx bxs-dashboard bx-md me-3"></i><span>Dashboard</span>
                                </Link>
                            </li>

                            <li>
                                <div class="dropdown-divider my-1"></div>
                            </li>
                            <li>
                                <Link class="dropdown-item"  :href="route('logout')" method="post">
                                    <i class="bx bx-power-off bx-md me-3"></i><span>Log Out</span>
                                </Link>

                            </li>
                        </ul>
                    </li>
                    <!--/ User -->

                    <Link v-if="!$page.props.auth.user" :href="route('login')" class="btn btn-primary me-4">
                        <span class="tf-icons bx bx-log-in-circle scaleX-n1-rtl me-md-1"></span>
                        <span class="d-none d-md-block">Login/Register</span>
                    </Link>

                </ul>
            </div>
        </nav>
    </div>

</template>

<style scoped>

</style>
