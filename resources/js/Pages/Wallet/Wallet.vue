<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BalanceChart from "@/Pages/Wallet/Partials/BalanceChart.vue";
import Deposit from "@/Pages/Wallet/Partials/Deposit.vue";
import {ref} from "vue";
import Withdrawal from "@/Pages/Wallet/Partials/Withdrawal.vue";
import {DuplicateIcon} from "@heroicons/vue/outline";
import Tooltip from "@/Components/Tooltip.vue";
import Input from "@/Components/Input.vue";
import Action from "@/Pages/Wallet/Partials/Action.vue";
import {XLCoinLogo} from "@/Components/Icons/outline.jsx";
import {transactionFormat} from "@/Composables/index.js";
import TransactionHistory from "@/Pages/Wallet/Transaction/TransactionHistory.vue";
import Button from "@/Components/Button.vue";
import InternalTransfer from "@/Pages/Wallet/Partials/InternalTransfer.vue";

const props = defineProps({
    wallets: Object,
    coins: Object,
    coin_price: Object,
    conversion_rate: Object,
    totalBalance: String,
    wallet_sel: Array,
    depositWalletSel: Array,
    random_address: Object,
    withdrawalFee: Object,
    gasFee: Object,
    setting_coin: Object,
    coin_price_yesterday: Object,
    coin_market_time: Object,
})

const tooltipContent = ref('Copy');
const { formatAmount } = transactionFormat();

function copyTestingCode () {
    let walletAddressCopy = document.querySelector('#XLCoinAddress')
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

        <div class="flex flex-col sm:flex-row p-5 items-center self-stretch gap-[60px] bg-white rounded-xl shadow-md dark:bg-gray-700">
            <div class="flex flex-col justify-center items-start gap-8 self-stretch w-full">
                <div class="flex flex-col items-start gap-3">
                    <div class="text-base font-semibold dark:text-gray-400">
                        {{$t('public.wallet.total_balance')}}
                    </div>
                    <div class="text-[28px] font-semibold dark:text-white">
                        $ {{ props.totalBalance }}
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 w-full">
                    <Deposit
                        :depositWalletSel="depositWalletSel"
                        :random_address="random_address"
                    />
                    <Withdrawal
                        :wallet_sel="wallet_sel"
                        :withdrawalFee="props.withdrawalFee"
                    />
                    <div class="col-span-2">
                        <InternalTransfer
                            :wallets="wallets"
                        />
                    </div>
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
                v-for="coin in coins"
                class="flex flex-col overflow-hidden rounded-[20px] w-80 border border-gray-400 dark:border-gray-600"
            >
                <div class="flex justify-between pt-5 pb-2.5 px-4 shadow-md" style="background: linear-gradient(251deg, #00095E 2.14%, #0359E8 97.82%);">
                    <div class="space-y-2">
                        <div class="text-base font-semibold dark:text-white">
                            {{ coin.setting_coin.name }}
                        </div>
                        <div class="text-xl font-semibold dark:text-white">
                            {{ coin.unit }} {{ coin.setting_coin.name }}
                        </div>
                        <div class="text-sm font-normal dark:text-white">
                            ≈ $ {{ formatAmount(coin.unit * props.coin_price.price) }}
                        </div>
                    </div>
                </div>
                <div class="inline-flex justify-between items-center gap-3 self-stretch bg-gray-700 pt-3 pb-2 px-4">
                    <Action
                        :coin="coin"
                        :coin_price="coin_price"
                        :conversion_rate="conversion_rate"
                        :wallet_sel="wallet_sel"
                        :setting_coin="setting_coin"
                        :coin_price_yesterday="coin_price_yesterday"
                        :coin_market_time="coin_market_time"
                    />
                    <div>
                        <div class="inline-flex justify-center w-full items-center gap-2 text-center text-gray-500 dark:text-gray-400 break-all">
                            <span class="text-xs">{{ coin.address }}</span>
                            <input type="hidden" id="XLCoinAddress" :value="coin.address">
                            <Tooltip :content="tooltipContent" placement="top">
                                <DuplicateIcon aria-hidden="true" :class="['w-4 h-4 text-gray-500 dark:text-gray-400']" @click.stop.prevent="copyTestingCode" style="cursor: pointer" />
                            </Tooltip>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 my-5 mb-28 bg-white overflow-hidden md:overflow-visible rounded-xl shadow-md dark:bg-gray-700">
            <TransactionHistory
                :conversion_rate="conversion_rate"
            />
        </div>

        <template #asideRight>
            <div class="inset-y-0 p-6 flex flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 w-96 fixed right-0">
                <h3 class="text-xl font-semibold leading-tight">
                    {{$t('public.wallet.your_assets')}}
                </h3>

                <div
                    v-for="coin in coins"
                    class="flex flex-col overflow-hidden rounded-[20px] border border-gray-400 dark:border-gray-600"
                >
                    <div class="flex justify-between pt-5 pb-2.5 px-4 shadow-md" style="background: linear-gradient(251deg, #00095E 2.14%, #0359E8 97.82%);">
                        <div class="space-y-2">
                            <div class="text-base font-semibold dark:text-white">
                                {{ coin.setting_coin.name }} Coin
                            </div>
                            <div class="text-xl font-semibold dark:text-white">
                                {{ coin.unit.toFixed(8) }} {{ coin.setting_coin.name }}
                            </div>
                            <div class="text-sm font-normal dark:text-white">
                                ≈ $ {{ formatAmount(coin.unit / props.coin_price.price) }}
                            </div>
                        </div>
                    </div>
                    <div class="inline-flex justify-between items-center gap-3 self-stretch bg-gray-700 pt-3 pb-2 px-4">
                        <Action
                            :coin="coin"
                            :coin_price="coin_price"
                            :gasFee="gasFee"
                            :conversion_rate="conversion_rate"
                            :wallet_sel="wallet_sel"
                            :setting_coin="setting_coin"
                            :coin_price_yesterday="coin_price_yesterday"
                            :coin_market_time="coin_market_time"
                        />
                        <div>
                            <div class="inline-flex justify-center w-full items-center gap-2 text-center text-gray-500 dark:text-gray-400 break-all">
                                <span class="text-xs">{{ coin.address }}</span>
                                <input type="hidden" id="XLCoinAddress" :value="coin.address">
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
