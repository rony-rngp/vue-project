<script setup>
import {Head} from "@inertiajs/vue3";

</script>

<template>
<div>
    <Head>
        <title> Dashboard</title>
    </Head>

    <div>
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-md-3 mb-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-center mb-10">
                                <div class="avatar flex-shrink-0">
                                    <img :src="'/admin/assets/img/icons/unicons/wallet-info.png'" alt="chart success" class="rounded" />
                                </div>
                            </div>
                            <p class="mb-1 text-center">Total Deposit</p>
                            <h4 class="card-title text-center">100</h4>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>

</div>
</template>

<style scoped>

</style>
