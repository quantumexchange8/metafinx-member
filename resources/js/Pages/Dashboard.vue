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
import BuyCoin from "@/Pages/Wallet/Partials/BuyCoin.vue";
import {CoinIcon} from "@/Components/Icons/outline.jsx";
import ViewMarket from "@/Pages/Wallet/Partials/ViewMarket.vue";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";

const props = defineProps({
    referralEarnings: [String, Number],
    wallets: Object,
    random_address: Object,
    wallet_sel: Array,
    coin: Object,
    setting_coin: Object,
    coin_price: Object,
    conversion_rate: Object,
    coin_market_time: Object,
    coin_price_yesterday: Object,
})
const { formatDateTime, formatAmount } = transactionFormat();

const coinModal = ref(false);
const modalComponent = ref(null);

const openCoinModal = () => {
    coinModal.value = true;
    console.log(props.wallet_sel)
}

const closeModal = () => {
    coinModal.value = false
    modalComponent.value = null;
}
</script>

<template>
    <AuthenticatedLayout :title="$t('public.dashboard.dashboard')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl md:text-2xl font-semibold leading-tight">
                    {{$t('public.dashboard.welcome_back')}}
                    <!-- , {{ $page.props.auth.user.name }}! -->
                </h2>
                <div class="flex items-start gap-3">
                    <Button
                        class="justify-center gap-2"
                        variant="gray"
                        @click="openCoinModal"
                    >
                        <CoinIcon aria-hidden="true" class="w-5 h-5" />
                        <span class="uppercase">Buy {{ coin.setting_coin.name }}</span>
                    </Button>
                    <Deposit
                        :wallet_sel="wallet_sel"
                        :random_address="random_address"
                    />
                </div>

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
                                $ {{ formatAmount(wallet.balance) }}
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

        <div class="flex flex-nowrap md:grid md:grid-cols-6 gap-3 overflow-x-auto md:overflow-visible my-8">
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
            <div class="flex-1 rounded-[10px] border border-gray-200 shadow dark:border-transparent dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">{{$t('public.dashboard.sponsor_bonus')}}</p>
                    <p class="text-gray-800 dark:text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
            <div class="flex-1 rounded-[10px] border border-gray-200 shadow dark:border-transparent dark:bg-gray-700">
                <div class="px-5 py-2.5 flex flex-col justify-between">
                    <p class="text-gray-400 text-xs md:text-sm w-32 md:w-full">{{$t('public.dashboard.pairing_bonus')}}</p>
                    <p class="text-gray-800 dark:text-white text-xl font-semibold">$ 0.00</p>
                </div>
            </div>
        </div>

        <div class="p-5 my-8 mb-28 bg-white overflow-hidden md:overflow-visible rounded-xl border border-gray-200 dark:border-transparent shadow dark:bg-gray-700">
            <CryptoPriceTable
                :coin_price="coin_price"
                :setting_coin="setting_coin"
                :coin_price_yesterday="coin_price_yesterday"
            />
        </div>

        <Modal :show="coinModal" title="Buy Coin" @close="closeModal" max-width="2xl">
            <BuyCoin
                :coin="coin"
                :coin_price="coin_price"
                :setting_coin="setting_coin"
                :conversion_rate="conversion_rate"
                :wallet_sel="wallet_sel"
                :coin_market_time="coin_market_time"
                @update:coinModal="coinModal = $event"
            />
        </Modal>

    </AuthenticatedLayout>
</template>
