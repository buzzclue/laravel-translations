<script setup lang="ts">
const user = useAuth()

const isTranslationsActive =
    route().current("ltu.translation*") ||
    route().current("ltu.source_translation*") ||
    route().current("ltu.phrases*")

const loading = ref(false)
</script>

<template>
    <div class="pt-2">
        <div class="d-flex align-items-center justify-content-between">
            <Link :href="route('ltu.translation.index')" class="btn rounded-md"
                :class="isTranslationsActive ? 'btn-primary' : 'btn-outline-primary'">
            Translations
            </Link>
            <div class="d-flex gap-2">
                <Link :href="route('ltu.translation.import')" method="post" class="px-3 py-2 rounded-md bg-green-600 text-white font-medium
         hover:bg-green-700 focus:outline-none focus:ring-2
         focus:ring-green-500 focus:ring-offset-2 d-inline-flex align-items-center" :disabled="loading"
                    :onStart="() => (loading = true)" :onFinish="() => (loading = false)">
                <template v-if="loading">
                    <div class="spinner-border spinner-border-sm me-1" role="status" />
                    <span>Importing...</span>
                </template>
                <template v-else>
                    <IconPublish class="me-1" style="width:1rem;height:1rem;" />
                    <span>Import</span>
                </template>
                </Link>
                <Link :href="route('ltu.translation.publish')"
                    class="btn btn-danger text-white d-inline-flex align-items-center gap-1 rounded-md">
                <IconPublish class="me-1" style="width:1rem;height:1rem;" />
                <span>Publish</span>
                </Link>
            </div>
        </div>
    </div>
</template>
