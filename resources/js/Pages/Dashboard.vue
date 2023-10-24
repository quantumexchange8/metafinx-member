<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { GithubIcon } from '@/Components/Icons/brands'
import {Link} from "@inertiajs/vue3";
import {transactionFormat} from "@/Composables/index.js";
import CryptoPriceTable from "@/Pages/Dashboard/Partials/CryptoPriceTable.vue";

const props = defineProps({
    totalWalletBalance: String,
    walletLastUpdate: Object,
    investmentEarningsLastUpdate: String,
})
const { formatDateTime } = transactionFormat();
</script>

<template>
    <AuthenticatedLayout title="Dashboard">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl md:text-2xl font-semibold leading-tight">
                    Welcome Back
                    <!-- , {{ $page.props.auth.user.name }}! -->
                </h2>
            </div>
        </template>

        <!-- <div class="p-6 overflow-hidden bg-white rounded-xl shadow-md dark:bg-gray-700">
            BANNER
        </div> -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 mb-8">
            <Link :href="route('wallet.details')">
                <div class="flex justify-between rounded-xl bg-gradient-to-b from-warning-300 to-warning-500">
                    <div class="p-5 flex flex-col justify-between">
                        <div>
                            <p class="text-white text-base font-semibold">
                                Wallet Balance
                            </p>
                            <p class="text-white text-[28px] font-semibold">
                                $ {{ props.totalWalletBalance }}
                            </p>
                        </div>
                        <p class="text-xs text-white font-normal">
                            Latest updated on {{ formatDateTime(props.walletLastUpdate.updated_at) }}
                        </p>
                    </div>
                    <div class="pr-1.5">
                        <img src="/assets/wallet.png" alt="wallet">
                    </div>

                </div>
            </Link>

            <Link :href="route('earn.invest_subscription')">
                <div class="flex justify-between rounded-xl bg-gradient-to-b from-pink-300 to-pink-500">
                    <div class="p-5 flex flex-col justify-between">
                        <div>
                            <p class="text-white text-base font-semibold">
                                Account Earning
                            </p>
                            <p class="text-white text-[28px] font-semibold">
                                $ 0.00
                            </p>
                        </div>
                        <p class="text-[12px] text-white">
                            Latest updated on {{ formatDateTime(props.investmentEarningsLastUpdate) }}
                        </p>
                    </div>
                    <div class="pr-1.5">
                        <img src="/assets/money_bag.png" width="140" height="140" alt="moneybag">
                    </div>
                </div>
            </Link>
        </div>

        <div class="flex flex-nowrap md:grid md:grid-cols-6 gap-3 overflow-x-auto md:overflow-visible my-8">
            <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">Monthly Return</p>
                    <p class="text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
            <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">Dividend</p>
                    <p class="text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
            <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">Ticket Bonus (0)</p>
                    <p class="text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
            <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">Referral Earning</p>
                    <p class="text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
            <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">Affiliate Earning</p>
                    <p class="text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
            <div class="flex-1 rounded-[10px] dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">Dividend Earning</p>
                    <p class="text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
        </div>

        <div class="p-5 my-8 bg-white overflow-hidden md:overflow-visible rounded-xl shadow-md dark:bg-gray-700">
            <CryptoPriceTable />
        </div>

    </AuthenticatedLayout>
</template>
