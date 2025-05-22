<script setup>

</script>

<template>
<div>
    <nav  class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="bx bx-menu bx-md"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <!-- Search -->
            <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                    <i class="bx bx-search bx-md"></i>
                    <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="Search..." aria-label="Search..." />
                </div>
            </div>
            <!-- /Search -->

            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                <li class="nav-item lh-1 me-4">
                    <a class="github-button" href="" data-icon="octicon-star" data-size="large" data-show-count="true" >Admin</a>
                </li>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0"
                       href="javascript:void(0);"
                       data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img :src="'/admin/assets/img/avater.jpeg'" style="height: 40px!important; width: 40px !important;" alt class="w-px-40 h-auto rounded-circle object-fit-cover" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img style="height: 40px!important; width: 40px !important;" :src="'/admin/assets/img/avater.jpeg'" alt class="w-px-40 h-auto rounded-circle object-fit-cover" />
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
                            <a class="dropdown-item" href="">
                                <i class="bx bx-user bx-md me-3"></i><span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <Link class="dropdown-item" :href="route('admin.settings')"> <i class="bx bx-cog bx-md me-3"></i><span>Settings</span> </Link>
                        </li>

                        <li>
                            <div class="dropdown-divider my-1"></div>
                        </li>
                        <li>
                            <Link class="dropdown-item" href="/logout" method="post">
                                <i class="bx bx-power-off bx-md me-3"></i><span>Log Out</span>
                            </Link>

                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </nav>

</div>
</template>

<style scoped>

</style>
