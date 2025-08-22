<script setup lang="ts">
import { Modal } from "momentum-modal"
import Navbar from "./partials/navbar.vue"
</script>

<template>
    <div class="container-fluid g-0">
        <Navbar />
        <div class="row">
            <div class="col-12">
                <slot />
            </div>
        </div>
    </div>
    <Modal />
</template>
