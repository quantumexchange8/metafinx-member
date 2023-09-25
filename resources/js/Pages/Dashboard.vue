<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { GithubIcon } from '@/Components/Icons/brands'
</script>

<template>
    <AuthenticatedLayout title="Dashboard">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-2xl font-semibold leading-tight">
                    Welcome Back, {{ $page.props.auth.user.name }}!
                </h2>
            </div>
        </template>

        <div class="p-6 overflow-hidden bg-white rounded-xl shadow-md dark:bg-gray-700">
            BANNER
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-6">
            <div class="grid grid-cols-1 md:grid-cols-2 rounded-xl bg-gradient-to-b from-warning-300 to-warning-500">
                <div class="p-5 space-y-2">
                    <p class="text-white text-base font-semibold">
                        Wallet Balance
                    </p>
                    <p class="text-white text-[28px] font-semibold">
                        $ {{ $page.props.auth.user.cash_wallet }}
                    </p>
                </div>
            </div>
            <div class="bg-gray-700">123</div>
        </div>
    </AuthenticatedLayout>
</template>
