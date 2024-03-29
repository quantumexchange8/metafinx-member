<script setup>
import CoinChart from "@/Pages/Wallet/Partials/CoinChart.vue";
import {computed, ref} from "vue";
import Button from "@/Components/Button.vue";
import {transactionFormat} from "@/Composables/index.js";
import {MXTIcon} from "@/Components/Icons/brands.jsx";

const props = defineProps({
    setting_coin: Object,
    coin_price: Object,
    coin_price_yesterday: Object,
})
const { formatAmount } = transactionFormat();
const getAmountClass = computed(() => {
    if (props.coin_price_yesterday.price < props.coin_price.price) {
        return 'text-success-500';
    } else if (props.coin_price_yesterday.price > props.coin_price.price) {
        return 'text-error-500';
    }
    return 'text-gray-600 dark:text-gray-400';
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
    const rawPercentage = props.coin_price.price / props.coin_price_yesterday.price;
    return rawPercentage === 1 ? 0 : rawPercentage.toFixed(2);
});

const periods = [
    {value: 1, label: 'one_month'},
    {value: 3, label: 'three_month'},
    {value: 6, label: 'six_month'},
    {value: '', label: 'all_time'},
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
            <MXTIcon class="w-10 h-10 text-white" />
            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ setting_coin.name }}
            </div>
            <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                {{ setting_coin.symbol }}
            </div>
        </div>
        <div class="flex flex-col items-center">
            <div class="text-[28px] font-semibold text-gray-900 dark:text-white">
                $&nbsp;{{ coin_price.price }}
            </div>
            <div
                class="text-xs"
                :class="getAmountClass"
            >
                {{ getAmountPrefix }}{{ formatAmount(priceDiffPercentage) }} % {{ $t('public.wallet.today') }}
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
        {{ $t('public.' + period.label ) }}
        </button>
    </div>
</template>
