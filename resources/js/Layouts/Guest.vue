<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import PageFooter from '@/Components/PageFooter.vue'

defineProps({
    title: String
})
</script>

<template>
    <Head :title="title" />

    <div
        class="flex flex-col items-center justify-center min-h-screen gap-4 py-6 bg-white dark:bg-gray-800"
    >
        <div class="fixed left-0 top-0 flex-shrink-0 inline-flex items-center gap-3">
            <Link href="/">
                <ApplicationLogo class="w-48" />
            </Link>
        </div>

        <main class="flex items-center flex-1 w-full sm:max-w-lg">
            <div
                class="w-full px-6 py-4 overflow-hidden sm:rounded-lg"
            >
                <slot />
            </div>
        </main>

        <PageFooter />

    </div>
</template>
