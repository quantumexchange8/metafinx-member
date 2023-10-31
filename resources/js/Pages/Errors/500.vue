<script setup>
import {Head, Link} from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import PageFooter from '@/Components/PageFooter.vue'
import {Inertia} from "@inertiajs/inertia";
import Button from '@/Components/Button.vue'
</script>

<template>
    <Head title="Error 500" />
    <div
        class="flex flex-col items-center justify-center min-h-screen gap-4 py-6 bg-white dark:bg-gray-800"
    >
        <div class="fixed left-0 top-0 flex-shrink-0 inline-flex items-center gap-3">
            <Link href="/">
                <ApplicationLogo class="w-48" />
            </Link>
        </div>

        <div class="flex flex-1 items-center sm:items-start w-full sm:max-w-3xl">
            <div
                class="w-full px-6 py-4 overflow-hidden sm:rounded-lg"
            >
                <div class="flex flex-col items-center justify-center gap-8">
                    <img src="/assets/500.png" class="" alt="">
                    <div class="flex flex-col items-center mt-3 gap-5">
                        <div class="flex flex-col dark:text-gray-400 text-center gap-2">
                            <h2 class="dark:text-white font-semibold text-3xl">Internal Server Error</h2>
                            We're sorry, something went wrong on our end and we can't serve the page you're looking for at the moment. 
                            It's not your fault, but we are working diligently to fix it.
                        </div>
                        <Button
                            :href="route('dashboard')"
                            class="sm:w-1/3 justify-center"
                        >
                        Back to dashboard
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <PageFooter />

    </div>
</template>
