<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import {Wallet, WithdrawalIcon} from "@/Components/Icons/outline.jsx";
import BalanceChart from "@/Pages/Wallet/Partials/BalanceChart.vue";
import Deposit from "@/Pages/Wallet/Partials/Deposit.vue";
import Transaction from "@/Pages/Wallet/Transaction/Transaction.vue";
import Alert from "@/Components/Alert.vue";
import {ref} from "vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import Withdrawal from "@/Pages/Wallet/Partials/Withdrawal.vue";

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

        <div class="p-5 grid grid-cols-2 items-center overflow-hidden bg-white rounded-xl shadow-md dark:bg-gray-700">
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
                    />
                </div>
            </div>
            <div>
                <BalanceChart
                    :wallets="wallets"
                />
            </div>
        </div>

        <div class="p-5 mt-8 bg-white overflow-hidden md:overflow-visible rounded-xl shadow-md dark:bg-gray-700">
            <Transaction />
        </div>

        <template #asideRight>
            <div class="inset-y-0 p-6 flex flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 w-96 fixed right-0">
                <h3 class="text-xl font-semibold leading-tight">
                    Your Assets
                </h3>

                <div v-for="wallet in props.wallets" class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-[20px] shadow-md bg-gradient-to-bl from-pink-400 to-pink-600">
                    <div class="space-y-2">
                        <div class="text-base font-semibold dark:text-white">
                            {{ wallet.name }}
                        </div>
                        <div class="text-xl font-semibold dark:text-white">
                            $ {{ wallet.balance }}
                        </div>
                    </div>
                    <Wallet class="w-24 h-24"/>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
