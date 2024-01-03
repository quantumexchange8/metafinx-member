<script setup>
import Button from "@/Components/Button.vue";
import Tooltip from "@/Components/Tooltip.vue";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {CoinIcon, LineChartIcon} from "@/Components/Icons/outline.jsx"
import BuyCoin from "@/Pages/Wallet/Partials/BuyCoin.vue";

const props = defineProps({
    coin: Object,
    coin_price: Object,
    conversion_rate: Object,
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


    <Modal :show="coinModal" :title="modalComponent" @close="closeModal" max-width="xl">
        <template v-if="modalComponent === 'Buy Coin'">
            <BuyCoin
                :coin="coin"
                :coin_price="coin_price"
                :conversion_rate="conversion_rate"
            />
        </template>
        <template v-if="modalComponent === 'View Market'">
            asqeqdad
        </template>
    </Modal>
</template>