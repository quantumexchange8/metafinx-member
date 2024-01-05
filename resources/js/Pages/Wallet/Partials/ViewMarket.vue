<script setup>
import CoinChart from "@/Pages/Wallet/Partials/CoinChart.vue";
import {XLCoinLogo} from "@/Components/Icons/outline.jsx";
import {computed, ref} from "vue";
import Button from "@/Components/Button.vue";

const props = defineProps({
    setting_coin: Object,
    coin_price: Object,
    coin_price_yesterday: Object,
})

const getAmountClass = computed(() => {
    if (props.coin_price_yesterday.price < props.coin_price.price) {
        return 'text-success-500';
    } else if (props.coin_price_yesterday.price > props.coin_price.price) {
        return 'text-error-500';
    }
    return '';
});

const getAmountPrefix = computed(() => {
    if (props.coin_price_yesterday.price < props.coin_price.price) {
        return '+';
    } else if (props.coin_price_yesterday.price > props.coin_price.price) {
        return '-';
    }
    return '';
});

const priceDiffPercentage = computed(() => {
    return (props.coin_price.price / props.coin_price_yesterday.price).toFixed(2)
})

const periods = [
    {value: 1, label: '1M'},
    {value: 3, label: '3M'},
    {value: 6, label: '6M'},
    {value: '', label: 'All Time'},
]
const activeComponent = ref(1);

const setActiveComponent = (component) => {
    activeComponent.value = component;
};

const periodValue = ref();
const activePeriod = ref(1);

const changePeriod = (value) => {
    periodValue.value = value;
    activePeriod.value = value;
};

const getButtonVariant = (value) => {
    return value === activePeriod.value ? 'gray' : 'transparent';
};
</script>

<template>
    <div class="flex flex-col items-center gap-2">
        <div class="flex flex-col items-center gap-1">
            <div class="bg-white rounded-full grow-0 shrink-0">
                <XLCoinLogo
                    class="w-10 h-10"
                />
            </div>
            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ setting_coin.name }}
            </div>
            <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                {{ setting_coin.symbol }}
            </div>
        </div>
        <div class="flex flex-col items-center">
            <div class="text-[28px] font-semibold text-gray-900 dark:text-white">
                MYR {{ coin_price.price }}
            </div>
            <div
                class="text-xs"
                :class="getAmountClass"
            >
                {{ getAmountPrefix }}{{ priceDiffPercentage }} % today
            </div>
        </div>
    </div>
    <CoinChart
        :selected-month="activeComponent"
        :getAmountPrefix="getAmountPrefix"
    />
    <div class="flex px-4 items-center gap-1 self-stretch">
        <button
            v-for="period in periods"
            type="button"
            class="px-4 py-1 w-full text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
            :class="{ 'bg-transparent': activeComponent !== period.value, 'dark:bg-[#38425080] dark:text-white': activeComponent === period.value }"
            @click="setActiveComponent(period.value)"
        >
            {{ period.label }}
        </button>
    </div>
</template>
