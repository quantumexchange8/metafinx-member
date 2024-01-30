<script setup>
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Input from "@/Components/Input.vue";
import { InternalUSDWalletIcon, SwitchVerticalIcon, XLCoinLogo } from "@/Components/Icons/outline.jsx";
import {computed, ref, watch} from "vue";
import { transactionFormat } from "@/Composables/index.js";

const props = defineProps({
    coin: Object,
    coin_price: Object,
    gasFee: Object,
    setting_coin: Object,
    conversion_rate: Object,
    wallet_sel: Array,
    coin_market_time: Object,
    coin_price_yesterday: Object,
})
const emit = defineEmits(['update:coinModal']);

const coinAmount = ref();
const coinUnit = ref();
const updatingCoinAmount = ref(false);
const updatingCoinUnit = ref(false);
const { formatAmount } = transactionFormat();
const transactionFee = ref();
const payableAmount = ref();

const form = useForm({
    wallet_id: '',
    coin_id: '',
    amount: '',
    gas_fee: '',
    transaction_amount: '',
    unit: '',
    terms: false,
    setting_coin_id: props.setting_coin.id,
    price: '',
})

const submit = () => {
    form.wallet_id = props.wallet_sel[0].value;
    form.coin_id = props.coin.id
    form.unit = coinUnit.value;
    form.price = props.coin_price.price;
    form.amount = coinAmount.value;
    form.gas_fee = transactionFee.value;
    form.transaction_amount = payableAmount.value;

    form.post(route('wallet.buy_coin'), {
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    });
};

watch(coinAmount, (newAmount) => {
    if (newAmount !== null && !updatingCoinAmount.value) {
        updatingCoinUnit.value = true;
        transactionFee.value = (newAmount * props.gasFee.value / 100).toFixed(2);
        coinUnit.value = parseFloat((newAmount - parseFloat(transactionFee.value)) / props.coin_price.price).toFixed(2);
        payableAmount.value = parseFloat(newAmount).toFixed(2);
    }
    updatingCoinAmount.value = false; // Reset flag after update
});

watch(coinUnit, (newUnit) => {
    if (newUnit !== null && !updatingCoinUnit.value) {
        updatingCoinAmount.value = true;
        transactionFee.value = ((newUnit * props.coin_price.price) * props.gasFee.value / 100).toFixed(2);
        coinAmount.value = (newUnit * props.coin_price.price + parseFloat(transactionFee.value)).toFixed(2);
        payableAmount.value = parseFloat(coinAmount.value).toFixed(2);
    }
    updatingCoinUnit.value = false; // Reset flag after update
});

const closeModal = () => {
    emit('update:coinModal', false);
}

const priceDiffPercentage = computed(() => {
    const rawPercentage = props.coin_price.price / props.coin_price_yesterday.price;
    return rawPercentage === 1 ? 0 : rawPercentage.toFixed(2);
});

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
</script>

<template>
    <div class="flex flex-col gap-8 mt-3">
        <div class="flex flex-col items-center gap-2 self-stretch p-3 rounded-lg bg-gray-300 dark:bg-gray-700">
            <div class="flex flex-col items-center gap-0.5">
                <div class="rounded-full w-10 h-10 grow-0 shrink-0 bg-gray-100"></div>
                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ setting_coin.name }}
                </div>
                <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                    {{ setting_coin.symbol }}
                </div>
            </div>
            <div class="flex flex-col items-center">
                <div class="text-[28px] font-semibold text-gray-900 dark:text-white">
                    $ {{ coin_price.price }}
                </div>
                <div
                    class="text-xs font-medium"
                    :class="getAmountClass"
                >
                    {{ getAmountPrefix }}{{ formatAmount(priceDiffPercentage) }} % today
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-5">
            <div class="flex flex-col sm:flex-row gap-1 space-y-2 sm:space-y-0 items-start self-stretch">
                <Label
                    class="text-sm dark:text-white w-52"
                    for="amount"
                >
                    <div class="flex items-center gap-1">
                        <div>{{ $t('public.wallet.pay') }}</div>
                        <div class="inline-flex items-center gap-1">
                            <div class="bg-gradient-to-b from-pink-400 to-pink-500 dark:shadow-pink-500 rounded-full w-4 h-4 shrink-0 grow-0">
                                <InternalUSDWalletIcon class="mt-0.5 ml-0.5"/>
                            </div>
                            <span>{{props.wallet_sel[0].label}}</span>
                        </div>
                    </div>
                </Label>
                <div class="flex flex-col w-full">
                    <Input
                        id="amount"
                        type="number"
                        min="0"
                        :max="props.wallet_sel[0].balance"
                        placeholder="$ 0.00"
                        class="block w-full"
                        :class="form.errors.amount ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                        v-model="coinAmount"
                    />
                    <InputError :message="form.errors.amount" class="mt-2" />
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Current balance: $ {{ props.wallet_sel[0].balance }}
                    </div>
                </div>
            </div>
            <div class="flex justify-center">
                <SwitchVerticalIcon />
            </div>
            <div class="flex flex-col sm:flex-row gap-1 space-y-2 sm:space-y-0 items-start self-stretch">
                <Label
                    class="text-sm dark:text-white w-52"
                    for="unit"
                >
                    <div class="flex items-center gap-1">
                        <div>{{ $t('public.wallet.receive') }}</div>
                        <div class="inline-flex items-center gap-1">
                            <div class="bg-white dark:shadow-pink-500 rounded-full w-4 h-4 shrink-0 grow-0">
                            </div>
                            <span>{{ setting_coin.name }}</span>
                        </div>
                    </div>
                </Label>
                <div class="flex flex-col w-full">
                    <Input
                        id="unit"
                        type="number"
                        min="0"
                        placeholder="$ 0.00"
                        class="block w-full"
                        :class="form.errors.unit ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                        v-model="coinUnit"
                    />
                    <InputError :message="form.errors.unit" class="mt-2" />
                </div>
            </div>
            <div class="flex flex-col items-start gap-3 self-stretch pt-5 pb-5 border-t border-b border-gray-400 dark:border-gray-700">
                <div class="flex justify-between items-start self-stretch">
                    <div class="text-gray-600 dark:text-gray-400 font-normal text-sm">
                        {{ $t('public.gas_fee') }} ({{ gasFee.value }}%)
                    </div>
                    <div class="text-sm text-gray-900 dark:text-white">
                        $ {{ transactionFee ?? '0.00' }}
                    </div>
                </div>
                <div class="flex justify-between items-start self-stretch">
                    <div class="text-gray-600 dark:text-gray-400 font-normal text-sm">
                        {{ $t('public.payable') }} {{ $t('public.wallet.amount') }}
                    </div>
                    <div class="text-sm text-gray-900 dark:text-white">
                        $ {{ payableAmount ?? '0.00' }}
                    </div>
                </div>
                <div class="mt-6">
                    <label>
                    <div class="flex">
                        <Checkbox name="remember" v-model:checked="form.terms" />
                        <span class="ml-2 text-xs dark:text-gray-400">{{ $t('public.agreement') }}</span>
                    </div>
                    <InputError v-if="form.errors.terms" :message="form.errors.terms" class="mt-2" />
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-4">
                <Button
                    variant="secondary"
                    type="button"
                    class="justify-center"
                    @click.prevent="closeModal"
                >
                    {{ $t('public.cancel') }}
                </Button>
                <Button
                    class="justify-center"
                    @click="submit"
                    :disabled="form.processing"
                >
                    {{ $t('public.confirm') }}
                </Button>
            </div>
        </div>
    </div>
</template>

