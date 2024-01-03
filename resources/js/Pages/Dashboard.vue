<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { GithubIcon } from '@/Components/Icons/brands'
import {Link} from "@inertiajs/vue3";
import {transactionFormat} from "@/Composables/index.js";
import CryptoPriceTable from "@/Pages/Dashboard/Partials/CryptoPriceTable.vue";
import Wallet from "@/Components/Wallet.vue"
import MUSDWallet from "@/Components/MUSDWallet.vue"
import Deposit from "@/Pages/Wallet/Partials/Deposit.vue";

const props = defineProps({
    referralEarnings: [String, Number],
    wallets: Object,
    random_address: Object,
    wallet_sel: Object,
})
const { formatDateTime, formatAmount } = transactionFormat();
</script>

<template>
    <AuthenticatedLayout :title="$t('public.dashboard.dashboard')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl md:text-2xl font-semibold leading-tight">
                    {{$t('public.dashboard.welcome_back')}}
                    <!-- , {{ $page.props.auth.user.name }}! -->
                </h2>
                <Deposit
                    :wallet_sel="wallet_sel"
                    :random_address="random_address"
                />
            </div>
        </template>

        <!-- <div class="p-6 overflow-hidden bg-white rounded-xl shadow-md dark:bg-gray-700">
            BANNER
        </div> -->

        <Link :href="route('wallet.details')">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 mb-8">
                <div
                    v-for="wallet in props.wallets"
                    class="flex justify-between rounded-xl"
                    :class="{
                    'bg-gradient-to-b from-pink-400 to-pink-600': wallet.type === 'internal_wallet',
                    'bg-gradient-to-b from-warning-300 to-warning-500': wallet.type === 'musd_wallet',
                }"
                >
                    <div class="p-5 flex flex-col justify-between">
                        <div>
                            <p class="text-white text-base font-semibold">
                                {{ wallet.name }}
                            </p>
                            <p class="text-white text-[28px] font-semibold">
                                $ {{ wallet.balance }}
                            </p>
                        </div>
                        <p class="text-xs text-white font-normal">
                            {{$t('public.dashboard.latest_updated')}} {{ formatDateTime(wallet.updated_at) }}
                        </p>
                    </div>
                    <div>
                        <Wallet v-if="wallet.type === 'internal_wallet'" />
                        <MUSDWallet
                            v-if="wallet.type === 'musd_wallet'"
                            class="rounded-xl"
                        />
                    </div>
                </div>
            </div>
        </Link>

        <div class="flex flex-nowrap md:grid md:grid-cols-4 gap-3 overflow-x-auto md:overflow-visible my-8">
            <div class="flex-1 rounded-[10px] border border-gray-200 shadow dark:border-transparent dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">{{$t('public.dashboard.monthly_return')}}</p>
                    <p class="text-gray-800 dark:text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
            <div class="flex-1 rounded-[10px] border border-gray-200 shadow dark:border-transparent dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">{{$t('public.dashboard.referral_earning')}}</p>
                    <p class="text-gray-800 dark:text-white text-xl font-semibold">$ {{ formatAmount(props.referralEarnings) }}</p>
                </div>
            </div>
            <div class="flex-1 rounded-[10px] border border-gray-200 shadow dark:border-transparent dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">{{$t('public.dashboard.affiliate_earning')}}</p>
                    <p class="text-gray-800 dark:text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
            <div class="flex-1 rounded-[10px] border border-gray-200 shadow dark:border-transparent dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">{{$t('public.dashboard.dividend_earning')}}</p>
                    <p class="text-gray-800 dark:text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
        </div>

        <div class="p-5 my-8 mb-28 bg-white overflow-hidden md:overflow-visible rounded-xl border border-gray-200 dark:border-transparent shadow dark:bg-gray-700">
            <CryptoPriceTable />
        </div>

    </AuthenticatedLayout>
</template>
