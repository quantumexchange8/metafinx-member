<script setup>
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Input from "@/Components/Input.vue";
import { ref, watch } from "vue";
import { transactionFormat } from "@/Composables/index.js";
import BaseListbox from "@/Components/BaseListbox.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";

const props = defineProps({
    coin: Object,
    coin_price: Object,
    gasFee: Object,
    setting_coin: Object,
    wallet_sel: Array,
    coin_market_time: Object,
    coin_price_yesterday: Object,
});

const { formatAmount } = transactionFormat();
const emit = defineEmits(['update:coinModal']);

const coinAmount = ref();
const coinUnit = ref();
const updatingCoinAmount = ref(false);
const updatingCoinUnit = ref(false);
const transactionFee = ref();
const transactionAmount = ref();

const form = useForm({
    wallet_id: props.wallet_sel[0].value,
    coin_id: props.coin.id,
    amount: '',
    gas_fee: '',
    transaction_amount: '',
    unit: '',
    terms: false,
    setting_coin_id: props.setting_coin.id,
    price: '',
});

const submit = () => {
    form.wallet_id = props.wallet_sel[0].value;
    form.coin_id = props.coin.id;
    form.unit = coinUnit.value;
    form.price = props.coin_price.price;
    form.amount = coinAmount.value;
    form.gas_fee = transactionFee.value;
    form.transaction_amount = transactionAmount.value;

    form.post(route('wallet.swap_coin'), {
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    });
};

const closeModal = () => {
    emit('update:coinModal', false);
}

const updateTransactionDetails = () => {
    transactionFee.value = (coinAmount.value * props.gasFee.value / 100).toFixed(2);
    transactionAmount.value = (parseFloat(coinAmount.value) - parseFloat(transactionFee.value)).toFixed(2);
};

const handleButtonClick = () => {
    if (!coinUnit.value) {
        coinUnit.value = parseInt(props.coin.unit);
    } else {
        coinUnit.value = '';
    }
}

watch(coinUnit, (newUnit) => {
    if (newUnit !== null && !updatingCoinUnit.value) {
        updatingCoinAmount.value = true;
        coinAmount.value = Number(newUnit / props.coin_price.price).toFixed(2);
        updateTransactionDetails();
    }
    updatingCoinUnit.value = false;
});

</script>

<template>
    <div class="flex flex-col gap-8 mt-3">
        <div class="flex justify-between p-5 rounded-xl shadow-md"
            style="background: linear-gradient(251deg, #00095E 2.14%, #0359E8 97.82%);">
            <div class="space-y-2">
                <div class="text-base font-semibold dark:text-white">
                    {{ coin.setting_coin.name }}
                </div>
                <div class="text-xl font-semibold dark:text-white">
                    {{ coin.unit }} {{ coin.setting_coin.name }}
                </div>
                <div class="text-sm font-normal dark:text-white">
                    ≈ $&nbsp;{{ formatAmount(coin.unit * props.coin_price.price) }}
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-5">
            <div class="flex flex-col sm:flex-row gap-1 space-y-2 sm:space-y-0 items-start self-stretch">
                <Label class="text-sm dark:text-white w-52" for="amount">
                    <div class="flex items-center gap-1">
                        <div>{{ $t('public.wallet.to') }}</div>
                    </div>
                </Label>
                <div class="flex flex-col w-full">
                    <BaseListbox 
                        v-model="form.wallet_id" 
                        :options="[props.wallet_sel[0]]" 
                        :error="form.errors.wallet_id" 
                    />
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-1 space-y-2 sm:space-y-0 items-start self-stretch">
                <Label class="text-sm dark:text-white w-52" for="unit">
                    <div class="flex items-center gap-1">
                        <div>{{ $t('public.wallet.unit') }}</div>
                        <div class="inline-flex items-center gap-1">
                            <span>({{ setting_coin.name }})</span>
                        </div>
                    </div>
                </Label>
                <div class="relative flex flex-col w-full">
                    <InputIconWrapper>
                            <Input
                                id="unit"
                                type="number"
                                min="0"
                                placeholder="0"
                                class="block w-full"
                                :class="form.errors.unit ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                                v-model="coinUnit"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                                <Button
                                    type="button"
                                    variant="custom"
                                    size="sm"
                                    class="flex justify-center"
                                    :class="{
                                        'bg-gray-400 hover:bg-gray-500 text-white dark:bg-gray-500 dark:hover:bg-gray-700' : !coinUnit,
                                        'bg-error-600 text-white hover:bg-error-700' : coinUnit
                                    }"
                                    @click="handleButtonClick"
                                >
                                {{ !coinUnit ? $t('public.wallet.full_amount') : $t('public.wallet.clear') }}
                                </Button>
                            </div>
                        </InputIconWrapper>

                    <InputError :message="form.errors.unit" class="mt-2" />
                </div>
            </div>
            <div
                class="flex flex-col items-start gap-3 self-stretch pt-5 pb-5 border-t border-b border-gray-400 dark:border-gray-700">
                <div class="flex justify-between items-start self-stretch">
                    <div class="text-gray-600 dark:text-gray-400 font-normal text-sm">
                        {{ $t('public.gas_fee') }} ({{ props.gasFee.value }}%)
                    </div>
                    <div class="text-sm text-gray-900 dark:text-white">
                        $&nbsp;{{ transactionFee ?? '0.00' }}
                    </div>
                </div>
                <div class="flex justify-between items-start self-stretch">
                    <div class="text-gray-600 dark:text-gray-400 font-normal text-sm">
                        {{ $t('public.wallet.amount') }} {{ $t('public.wallet.receive') }}
                    </div>
                    <div class="text-sm text-gray-900 dark:text-white">
                        $&nbsp;{{ transactionAmount ?? '0.00' }}
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
                <Button variant="secondary" type="button" class="justify-center" @click.prevent="closeModal">
                    {{ $t('public.cancel') }}
                </Button>
                <Button class="justify-center" @click="submit" :disabled="form.processing">
                    {{ $t('public.confirm') }}
                </Button>
            </div>
        </div>
    </div>
</template>
