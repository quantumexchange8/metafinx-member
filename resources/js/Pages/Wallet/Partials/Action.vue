<script setup>
import Button from "@/Components/Button.vue";
import Tooltip from "@/Components/Tooltip.vue";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {CoinIcon, LineChartIcon} from "@/Components/Icons/outline.jsx"
import BuyCoin from "@/Pages/Wallet/Partials/BuyCoin.vue";
import CoinChart from "@/Pages/Wallet/Partials/CoinChart.vue";
import ViewMarket from "@/Pages/Wallet/Partials/ViewMarket.vue";

const props = defineProps({
    coin: Object,
    coin_price: Object,
    conversion_rate: Object,
    wallet_sel: Array,
    setting_coin: Object,
    coin_price_yesterday: Object,
    coin_market_time: Object,
})

const coinModal = ref(false);
const modalComponent = ref(null);

const openMemberModal = (componentType) => {
    coinModal.value = true;
    if (componentType === 'buy_coin') {
        modalComponent.value = 'Buy Coin';
    }
    else if (componentType === 'view_market') {
        modalComponent.value = 'View Market';
    }
}

const closeModal = () => {
    coinModal.value = false
    modalComponent.value = null;
}

</script>

<template>
    <div class="flex gap-3">
        <Tooltip content="Buy coin" placement="bottom">
            <Button
                type="button"
                class="flex justify-center w-8 h-8 relative focus:outline-none"
                variant="gray"
                @click="openMemberModal('buy_coin')"
                pill
            >
                <CoinIcon aria-hidden="true" class="w-4 h-4 absolute" />
                <span class="sr-only">Buy coin</span>
            </Button>
        </Tooltip>
        <Tooltip content="View market" placement="bottom">
            <Button
                type="button"
                class="flex justify-center w-8 h-8 relative focus:outline-none"
                variant="gray"
                @click="openMemberModal('view_market')"
                pill
            >
                <LineChartIcon aria-hidden="true" class="w-4 h-4 absolute" />
                <span class="sr-only">View market</span>
            </Button>
        </Tooltip>
    </div>


    <Modal :show="coinModal" :title="modalComponent" @close="closeModal" max-width="2xl">
        <template v-if="modalComponent === 'Buy Coin'">
            <BuyCoin
                :coin="coin"
                :coin_price="coin_price"
                :setting_coin="setting_coin"
                :conversion_rate="conversion_rate"
                :wallet_sel="wallet_sel"
                :coin_market_time="coin_market_time"
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
    </Modal>
</template>
