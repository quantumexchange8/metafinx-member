<script setup>
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Input from "@/Components/Input.vue";
import { InternalUSDWalletIcon, SwitchVerticalIcon, XLCoinLogo } from "@/Components/Icons/outline.jsx";
import { ref, watch } from "vue";
import { transactionFormat } from "@/Composables/index.js";

const props = defineProps({
    coin: Object,
    coin_price: Object,
    setting_coin: Object,
    conversion_rate: Object,
    wallet_sel: Array,
    coin_market_time: Object,
})
const emit = defineEmits(['update:coinModal']);

const coinAmount = ref();
const coinUnit = ref();
const updatingCoinAmount = ref(false);
const updatingCoinUnit = ref(false);
const { formatTime } = transactionFormat();

const form = useForm({
    wallet_id: '',
    amount: '',
    unit: '',
    terms: false,
    setting_coin_id: props.setting_coin.id,
    price: '',
    conversion_rate: '',
    address: '',
})

const submit = () => {
    form.wallet_id = props.wallet_sel[0].value;
    form.unit = coinUnit.value;
    form.price = props.coin_price.price;
    form.amount = coinAmount.value;
    form.conversion_rate = props.conversion_rate.price;
    form.address = props.coin.address;

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
        let amont = newAmount * props.conversion_rate.price
        // Update unit based on form.amount and fix to 8 decimal places
        coinUnit.value = Number(amont * props.coin_price.price).toFixed(8);
    }
    updatingCoinAmount.value = false; // Reset flag after update
});

watch(coinUnit, (newUnit) => {
    if (newUnit !== null && !updatingCoinUnit.value) {
        updatingCoinAmount.value = true;
        let unit = newUnit / props.coin_price.price
        // Update amount based on form.unit and fix to 2 decimal places
        coinAmount.value = Number(unit / props.conversion_rate.price).toFixed(2);
    }
    updatingCoinUnit.value = false; // Reset flag after update
});

const closeModal = () => {
    emit('update:coinModal', false);
}

const fullWithdraw = () => {
    form.amount = props.wallet_sel[0].balance || 0;
    coinAmount.value = form.amount;
};
</script>

<template>
    <div class="flex flex-col gap-8 mt-3">
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
                            <span>Internal Wallet</span>
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
                    <Button variant="gray" size="sm" class="absolute end-2 justify-center mt-1 mr-5" @click="fullWithdraw" >{{$t('public.wallet.full_amount')}}</Button>
                    <InputError :message="form.errors.amount" class="mt-2" />
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
                                <XLCoinLogo class="w-4 h-4"/>
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
                        {{ $t('public.wallet.market_time') }}
                    </div>
                    <div class="text-sm text-gray-900 dark:text-white">
                        {{ formatTime(coin_market_time.open_time) + ' - ' + formatTime(coin_market_time.close_time) + ' ' + coin_market_time.frequency_type }}
                    </div>
                </div>
                <div class="flex justify-between items-start self-stretch">
                    <div class="text-gray-600 dark:text-gray-400 font-normal text-sm">
                        {{ $t('public.wallet.conversion_rate') }}
                    </div>
                    <div class="text-sm text-gray-900 dark:text-white">
                        {{ conversion_rate.usd + ' USDT = ' + conversion_rate.price + ' MYR' }}
                    </div>
                </div>
                <div>
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

