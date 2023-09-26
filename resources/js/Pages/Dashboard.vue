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
            <div class="flex justify-between rounded-xl bg-gradient-to-b from-warning-300 to-warning-500">
                <div class="p-5 flex flex-col justify-between">
                    <div>
                        <p class="text-white text-base font-semibold">
                            Wallet Balance
                        </p>
                        <p class="text-white text-[28px] font-semibold">
                            $ {{ $page.props.auth.user.cash_wallet }}
                        </p>
                    </div>
                    <p class="text-xs text-white font-normal">
                        Latest updated on 
                    </p>
                </div>
                <div class="pr-1.5">
                    <img src="assets/icon.svg">
                </div>
                    
            </div>
            <div class="flex justify-between rounded-xl bg-gradient-to-b from-pink-300 to-pink-500">
                <div class="p-5 flex flex-col justify-between">
                    <div>
                        <p class="text-white text-base font-semibold">
                            Account Earning
                        </p>
                        <p class="text-white text-[28px] font-semibold">
                            $ 0.00
                            <!-- {{ $page.props.auth.user.cash_wallet }} -->
                        </p>
                    </div>
                    <p class="text-[12px] text-white">
                        Latest updated on 
                    </p>
                </div>
                <div class="pr-1.5">
                    <img src="assets/icon2.svg" >
                </div>
            </div>
        </div>

        <div class="flex flex-nowrap md:grid md:grid-cols-6 gap-3 overflow-x-auto md:overflow-visible">
                <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                    <div class="px-5 py-2.5 flex flex-col justify-between">
                        <p class="text-gray-400 w-32 md:w-full">Monthly Return</p>
                        <p class="text-white text-xl font-semibold">$ 0.00</p>
                    </div>
                </div>
                <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                    <div class="px-5 py-2.5 flex flex-col justify-between">
                        <p class="text-gray-400 w-32 md:w-full">Dividend</p>
                        <p class="text-white text-xl font-semibold">$ 0.00</p>
                    </div>
                </div>
                <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                    <div class="px-5 py-2.5 flex flex-col justify-between">
                        <p class="text-gray-400 w-32 md:w-full">Ticket Bonus (0)</p>
                        <p class="text-white text-xl font-semibold">$ 0.00</p>
                    </div>
                </div>
                <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                    <div class="px-5 py-2.5 flex flex-col justify-between">
                        <p class="text-gray-400 w-32 md:w-full">Referral Earning</p>
                        <p class="text-white text-xl font-semibold">$ 0.00</p>
                    </div>
                </div>
                <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                    <div class="px-5 py-2.5 flex flex-col justify-between">
                        <p class="text-gray-400 w-32 md:w-full">Affiliate Earning</p>
                        <p class="text-white text-xl font-semibold">$ 0.00</p>
                    </div>
                </div>
                <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                    <div class="px-5 py-2.5 flex flex-col justify-between">
                        <p class="text-gray-400 w-32 md:w-full">Dividend Earning</p>
                        <p class="text-white text-xl font-semibold">$ 0.00</p>
                    </div>
                </div>
        </div>
        
    </AuthenticatedLayout>
</template>
