<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import {WithdrawalIcon} from "@/Components/Icons/outline.jsx";
import BalanceChart from "@/Pages/Wallet/Partials/BalanceChart.vue";
import Deposit from "@/Pages/Wallet/Partials/Deposit.vue";
import Transaction from "@/Pages/Wallet/Transaction/Transaction.vue";
import Alert from "@/Components/Alert.vue";
import {ref} from "vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import Withdrawal from "@/Pages/Wallet/Partials/Withdrawal.vue";
import Wallet from "@/Components/Wallet.vue"
import EarnWallet from "@/Components/EarnWallet.vue";

const props = defineProps({
    wallets: Object,
    totalBalance: String,
    wallet_sel: Object,
    random_address: Object,
})

</script>

<template>
    <AuthenticatedLayout title="Wallet">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-2xl font-semibold leading-tight">
                    Wallet
                </h2>
            </div>
            <p class="text-base font-normal dark:text-gray-400">
                Track your current wallet balance and manage your wallets.
            </p>
        </template>

        <div class="p-5 grid md:grid-cols-2 gap-2 sm:gap-5 items-center overflow-hidden bg-white rounded-xl shadow-md dark:bg-gray-700">
            <div class="space-y-2">
                <p class="text-base font-semibold dark:text-gray-400">
                    Total Balance
                </p>
                <p class="text-[28px] font-semibold dark:text-white">
                    $ {{ props.totalBalance }}
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-6">
                    <Deposit
                        :wallet_sel="wallet_sel"
                        :random_address="random_address"
                    />
                    <Withdrawal
                        :wallet_sel="wallet_sel"
                        :wallets="props.wallets"
                    />
                </div>
            </div>
            <div class="w-full flex justify-center mt-4 md:mt-0">
                <BalanceChart />
            </div>
        </div>

        <h3 class="md:hidden text-xl font-semibold leading-tight my-5">
            Your Assets
        </h3>
        <div class="flex flex-nowrap md:hidden gap-3 overflow-x-auto md:overflow-visible w-full">
            <div
                v-for="wallet in props.wallets"
                class="flex-1 rounded-[20px] shadow-md"
                :class="{
                    'bg-gradient-to-bl from-pink-400 to-pink-600': wallet.name === 'USD Wallet',
                    'bg-gradient-to-bl from-warning-300 to-warning-500': wallet.name === 'MUSD Wallet',
                }"
            >
                <div class="p-5 flex justify-between items-center w-80">
                    <div class="space-y-2">
                        <div class="text-base font-semibold dark:text-white">
                            {{ wallet.name }}
                        </div>
                        <div class="text-xl font-semibold dark:text-white">
                            $ {{ wallet.balance }}
                        </div>
                    </div>
                    <div v-if="wallet.name === 'USD Wallet'">
                        <Wallet class="w-24 h-24"/>
                    </div>
                    <div v-else>
                        <EarnWallet class="w-24 h-24" />
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 my-5 mb-28 bg-white overflow-hidden md:overflow-visible rounded-xl shadow-md dark:bg-gray-700">
            <Transaction />
        </div>

        <template #asideRight>
            <div class="inset-y-0 p-6 flex flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 w-96 fixed right-0">
                <h3 class="text-xl font-semibold leading-tight">
                    Your Assets
                </h3>

                <div
                    v-for="wallet in props.wallets"
                    class="p-5 flex justify-between items-center overflow-hidden rounded-[20px] shadow-md w-full"
                    :class="{
                        'bg-gradient-to-bl from-pink-400 to-pink-600': wallet.name === 'USD Wallet',
                        'bg-gradient-to-bl from-warning-300 to-warning-500': wallet.name === 'MUSD Wallet',
                    }"
                >
                    <div class="space-y-2">
                        <div class="text-base font-semibold dark:text-white">
                            {{ wallet.name }}
                        </div>
                        <div class="text-xl font-semibold dark:text-white">
                            $ {{ wallet.balance }}
                        </div>
                    </div>
                    <div v-if="wallet.name === 'USD Wallet'">
                        <Wallet class="w-24 h-24"/>
                    </div>
                    <div v-else>
                        <EarnWallet class="w-24 h-24" />
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
