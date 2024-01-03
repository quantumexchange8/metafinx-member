<script setup>
import Label from "@/Components/Label.vue";
import {useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Input from "@/Components/Input.vue";
import {InternalUSDWalletIcon, SwitchVerticalIcon} from "@/Components/Icons/outline.jsx";
import {ref, watch} from "vue";
import {transactionFormat} from "@/Composables/index.js";

const props = defineProps({
    coin: Object,
    coin_price: Object,
    conversion_rate: Object,
})

const coinAmount = ref();
const coinUnit = ref();
const updatingCoinAmount = ref(false);
const updatingCoinUnit = ref(false);
const { formatTime } = transactionFormat();

const form = useForm({
    wallet_id: '',
    amount: '',
    unit: '',
    terms: false
})

watch(coinAmount, (newAmount) => {
    if (newAmount !== null && !updatingCoinAmount.value) {
        updatingCoinUnit.value = true;
        // Update unit based on form.amount and fix to 8 decimal places
        coinUnit.value = Number(newAmount * 0.67).toFixed(8);
    }
});

watch(coinUnit, (newUnit) => {
    if (newUnit !== null && !updatingCoinUnit.value) {
        updatingCoinAmount.value = true;
        // Update amount based on form.unit and fix to 2 decimal places
        coinAmount.value = Number(newUnit / 0.67).toFixed(2);
    }
});

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
                            <div class="bg-gradient-to-t from-pink-300 to-pink-600 dark:shadow-pink-500 rounded-full w-4 h-4 shrink-0 grow-0">
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
                        placeholder="$ 0.00"
                        class="block w-full"
                        :class="form.errors.amount ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                        v-model="coinAmount"
                    />
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
                            <div class="bg-gradient-to-t from-pink-300 to-pink-600 dark:shadow-pink-500 rounded-full w-4 h-4 shrink-0 grow-0">
                                <InternalUSDWalletIcon class="mt-0.5 ml-0.5"/>
                            </div>
                            <span>XLC Coin</span>
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
            <div class="flex flex-col items-start gap-3 self-stretch pt-5 border-t border-gray-400 dark:border-gray-700">
                <div class="flex justify-between items-start self-stretch">
                    <div class="text-gray-600 dark:text-gray-400 font-normal text-sm">
                        {{ $t('public.wallet.market_time') }}
                    </div>
                    <div class="text-sm text-gray-900 dark:text-white">
                        {{ formatTime(coin_price.open_time) + ' - ' + formatTime(coin_price.close_time) + ' daily' }}
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
            </div>
        </div>
    </div>
</template>
