<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import BalanceChart from "@/Pages/Wallet/Partials/BalanceChart.vue";
import Deposit from "@/Pages/Wallet/Partials/Deposit.vue";
import Transaction from "@/Pages/Wallet/Transaction/Transaction.vue";
import Alert from "@/Components/Alert.vue";
import {ref} from "vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import Withdrawal from "@/Pages/Wallet/Partials/Withdrawal.vue";
import Wallet from "@/Components/Wallet.vue"
import MUSDWallet from "@/Components/MUSDWallet.vue";
import {DuplicateIcon} from "@heroicons/vue/outline";
import Tooltip from "@/Components/Tooltip.vue";
import Input from "@/Components/Input.vue";
import Action from "@/Pages/Wallet/Partials/Action.vue";

const props = defineProps({
    coins: Object,
    coin_price: Object,
    conversion_rate: Object,
    totalBalance: String,
    wallet_sel: Object,
    random_address: Object,
    withdrawalFee: Object,
})

const tooltipContent = ref('Copy');

function copyTestingCode () {
    let walletAddressCopy = document.querySelector('#cryptoWalletAddress')
    walletAddressCopy.setAttribute('type', 'text');
    walletAddressCopy.select();

    try {
        var successful = document.execCommand('copy');
        if (successful) {
            tooltipContent.value = 'Copied!';
            setTimeout(() => {
                tooltipContent.value = 'Copy'; // Reset tooltip content to 'Copy' after 2 seconds
            }, 1000);
        } else {
            tooltipContent.value = 'Try Again Later!';
        }

    } catch (err) {
        alert('Oops, unable to copy');
    }

    /* unselect the range */
    walletAddressCopy.setAttribute('type', 'hidden')
    window.getSelection().removeAllRanges()
}

</script>

<template>
    <AuthenticatedLayout :title="$t('public.wallet.wallet')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-2xl font-semibold leading-tight">
                    {{$t('public.wallet.wallet')}}
                </h2>
            </div>
            <p class="text-base font-normal dark:text-gray-400">
                {{$t('public.wallet.track_wallet')}}
            </p>
        </template>

        <div class="p-5 grid md:grid-cols-2 gap-2 sm:gap-5 items-center overflow-hidden bg-white rounded-xl shadow-md dark:bg-gray-700">
            <div class="space-y-2">
                <p class="text-base font-semibold dark:text-gray-400">
                    {{$t('public.wallet.total_balance')}}
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
                        :withdrawalFee="props.withdrawalFee"
                    />
                </div>
            </div>
            <div class="w-full flex justify-center mt-4 md:mt-0">
                <BalanceChart />
            </div>
        </div>

        <h3 class="md:hidden text-xl font-semibold leading-tight my-5">
            {{$t('public.wallet.your_assets')}}
        </h3>
        <div class="flex flex-nowrap md:hidden gap-3 overflow-x-auto md:overflow-visible w-full">
            <div
                v-for="coin in props.coins"
                class="flex-1 rounded-[20px] shadow-md"
                :class="{
                    'bg-gradient-to-bl from-pink-400 to-pink-600': coin.name === 'Internal Wallet',
                    'bg-gradient-to-bl from-warning-300 to-warning-500': coin.name === 'MUSD Wallet',
                }"
            >
                <div class="p-5 flex justify-between items-center w-80">
                    <div class="space-y-2">
                        <div class="text-base font-semibold dark:text-white">
                            {{ coin.name }}
                        </div>
                        <div class="text-xl font-semibold dark:text-white">
                            $ {{ coin.balance }}
                        </div>
                    </div>
                    <div v-if="coin.name === 'Internal Wallet'">
                        <Wallet class="w-24 h-24"/>
                    </div>
                    <div v-if="coin.name === 'MUSD Wallet'">
                        <MUSDWallet class="w-24 h-24"/>
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
                    {{$t('public.wallet.your_assets')}}
                </h3>

                <div
                    v-for="coin in coins"
                    class="flex flex-col overflow-hidden rounded-[20px]"
                >
                    <div class="flex justify-between pt-5 pb-2.5 px-4 shadow-md bg-gradient-to-bl from-error-400 to-error-600">
                        <div class="space-y-2">
                            <div class="text-base font-semibold dark:text-white">
                                {{ coin.setting_coin.name }}
                            </div>
                            <div class="text-xl font-semibold dark:text-white">
                                {{ coin.unit }} XLC
                            </div>
                            <div class="text-sm font-normal dark:text-white">
                                ≈ MYR {{ coin.unit * coin.price }}
                            </div>
                        </div>
                        <div>
                            <img src="/images/icons/icon-no-color.png" alt="" class="w-32 h-24">
                        </div>
                    </div>
                    <div class="inline-flex justify-between items-center gap-3 self-stretch bg-gray-700 pt-3 pb-2 px-4">
                        <Action
                            :coin="coin"
                            :coin_price="coin_price"
                            :conversion_rate="conversion_rate"
                            :wallet_sel="wallet_sel"
                        />
                        <div>
                            <div class="inline-flex justify-center w-full items-center gap-2 text-center text-gray-500 dark:text-gray-400 break-all">
                                <span class="text-xs">{{ coin.address }}</span>
                                <input type="hidden" id="cryptoWalletAddress" :value="coin.address">
                                <Tooltip :content="tooltipContent" placement="top">
                                    <DuplicateIcon aria-hidden="true" :class="['w-4 h-4 text-gray-500 dark:text-gray-400']" @click.stop.prevent="copyTestingCode" style="cursor: pointer" />
                                </Tooltip>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
