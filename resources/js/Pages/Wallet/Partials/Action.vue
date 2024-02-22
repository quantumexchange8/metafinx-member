<script setup>
import Button from "@/Components/Button.vue";
import Tooltip from "@/Components/Tooltip.vue";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {CoinIcon, SwapCoinIcon, LineChartIcon, HistoryIcon} from "@/Components/Icons/outline.jsx"
import BuyCoin from "@/Pages/Wallet/Partials/BuyCoin.vue";
import SwapCoin from "@/Pages/Wallet/Partials/SwapCoin.vue";
import CoinChart from "@/Pages/Wallet/Partials/CoinChart.vue";
import ViewMarket from "@/Pages/Wallet/Partials/ViewMarket.vue";
import ViewTransaction from "@/Pages/Wallet/Partials/ViewTransaction.vue";
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    coin: Object,
    coin_price: Object,
    gasFee: Object,
    stackingFee: Object,
    conversion_rate: Object,
    wallet_sel: Array,
    setting_coin: Object,
    coin_price_yesterday: Object,
    coin_market_time: Object,
    coin_payment: Object,
})

const coinModal = ref(false);
const modalComponent = ref(null);
const actionType = ref(null);

const openMemberModal = (componentType) => {
    coinModal.value = true;
    if (componentType === 'buy_coin') {
        actionType.value = 'buy_coin';
        modalComponent.value = 'Buy Coin';
    }
    else if (componentType === 'swap_coin') {
        actionType.value = 'swap_coin';
        modalComponent.value = 'Swap Coin';
    }
    else if (componentType === 'view_market') {
        actionType.value = 'view_market';
        modalComponent.value = 'View Market';
    }
    else if (componentType === 'view_transaction') {
        actionType.value = 'view_transaction';
        modalComponent.value = 'View Transaction';
    }
}

const closeModal = () => {
    coinModal.value = false;
    modalComponent.value = null;
}

</script>

<template>
    <div class="flex gap-3">
        <Tooltip :content="$t('public.wallet.buy_coin')" placement="bottom">
            <Button
                type="button"
                class="flex justify-center w-6 h-6 relative focus:outline-none"
                variant="gray"
                @click="openMemberModal('buy_coin')"
                pill
                size="sm"
            >
                <CoinIcon aria-hidden="true" class="w-4 h-4 absolute" />
                <span class="sr-only">{{ $t('public.wallet.buy_coin') }}</span>
            </Button>
        </Tooltip>
        <Tooltip :content="$t('public.wallet.swap_coin')" placement="bottom">
            <Button
                type="button"
                class="flex justify-center w-6 h-6 relative focus:outline-none"
                variant="gray"
                @click="openMemberModal('swap_coin')"
                pill
                size="sm"
            >
                <SwapCoinIcon aria-hidden="true" class="w-4 h-4 absolute" />
                <span class="sr-only">{{ $t('public.wallet.swap_coin') }}</span>
            </Button>
        </Tooltip>
        <Tooltip :content="$t('public.wallet.view_market')" placement="bottom">
            <Button
                type="button"
                class="flex justify-center w-6 h-6 relative focus:outline-none"
                variant="gray"
                @click="openMemberModal('view_market')"
                pill
                size="sm"
            >
                <LineChartIcon aria-hidden="true" class="w-4 h-4 absolute" />
                <span class="sr-only">{{ $t('public.wallet.view_market') }}</span>
            </Button>
        </Tooltip>
        <Tooltip :content="$t('public.wallet.view_transaction')" placement="bottom">
            <Button
                type="button"
                class="flex justify-center w-6 h-6 relative focus:outline-none"
                variant="gray"
                @click="openMemberModal('view_transaction')"
                pill
                size="sm"
            >
                <HistoryIcon aria-hidden="true" class="w-4 h-4 absolute" />
                <span class="sr-only">{{ $t('public.wallet.view_transaction') }}</span>
            </Button>
        </Tooltip>
    </div>


    <Modal :show="coinModal" :title="$t('public.wallet.' + actionType)" @close="closeModal" max-width="2xl">
        <template v-if="modalComponent === 'Buy Coin'">
            <BuyCoin
                :coin="coin"
                :coin_price="coin_price"
                :gasFee="gasFee"
                :setting_coin="setting_coin"
                :conversion_rate="conversion_rate"
                :wallet_sel="wallet_sel"
                :coin_market_time="coin_market_time"
                :coin_price_yesterday="coin_price_yesterday"
                @update:coinModal="coinModal = $event"
            />
        </template>
        <template v-if="modalComponent === 'Swap Coin'">
            <SwapCoin
                :coin="coin"
                :coin_price="coin_price"
                :gasFee="gasFee"
                :setting_coin="setting_coin"
                :conversion_rate="conversion_rate"
                :wallet_sel="wallet_sel"
                :coin_market_time="coin_market_time"
                :coin_price_yesterday="coin_price_yesterday"
                @update:coinModal="coinModal = $event"
            />
        </template>
        <template v-if="modalComponent === 'View Market'">
            <ViewMarket
                :setting_coin="setting_coin"
                :coin_price="coin_price"
                :coin_price_yesterday="coin_price_yesterday"
            />
        </template>
        <template v-if="modalComponent === 'View Transaction'">
            <ViewTransaction
                :coin_payment="coin_payment"
                :gasFee="gasFee"
                :stackingFee="stackingFee"
                :setting_coin="setting_coin"
            />
        </template>
    </Modal>
</template>
