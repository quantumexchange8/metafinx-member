<script setup>
import Button from "@/Components/Button.vue";
import {ref, watch} from "vue";
import Modal from "@/Components/Modal.vue";
import {checkIcon} from '@/Components/Icons/outline'
import BaseListbox from "@/Components/BaseListbox.vue";
import Input from "@/Components/Input.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import {transactionFormat} from "@/Composables/index.js";
import {EyeIcon, EyeOffIcon, KeyIcon} from "@heroicons/vue/outline";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";

const props = defineProps({
    plan: Object,
    coin_price: Object,
    internal_wallet: Object,
    musd_wallet: Object,
    stackingFee: Object,
})

const subscribeModal = ref(false);
const housingPrice = ref(null);
const amountCalculation = ref(0);
const coin = usePage().props.auth.user.coins
const settingCoin = usePage().props.auth.user.coins.setting_coin
const coinUnit = ref()
const linkedPrice = ref()
const stackingFee = ref()
const { formatAmount } = transactionFormat();

const openSubscribeModal = () => {
    subscribeModal.value = true
}

const closeModal = () => {
    subscribeModal.value = false
}

const form = useForm({
    investment_plan_id: props.plan.id,
    investment_plan_type: props.plan.type,
    wallet_id: '',
    from_coin_id: '',
    unit_number: '',
    amount: '',
    stacking_fee: '',
    housing_price: '',
    unit: '',
    terms: false
})

watch(housingPrice, (newValue) => {
    let number = newValue / 100;
    // Divide by 100 and round down to the nearest 100
    amountCalculation.value = Math.floor(number / 100) * 100;
});

watch(coinUnit, (newValue) => {
    linkedPrice.value = (newValue * props.coin_price.price).toFixed(2)
    stackingFee.value = (linkedPrice.value * props.stackingFee.value /100).toFixed(2)
});

const submit = () => {
    if (props.plan.type === 'ebmi') {
        form.housing_price = housingPrice.value
        form.amount = amountCalculation.value.toFixed()
    } else if (props.plan.type === 'staking') {
        form.unit_number = undefined
        form.housing_price = undefined
        form.wallet_id = props.musd_wallet.id
        form.from_coin_id = coin.id
        form.unit = coinUnit.value
        form.amount = linkedPrice.value
        form.stacking_fee = stackingFee.value
    } else {
        form.wallet_id = props.internal_wallet.id
        form.unit_number = undefined
        form.housing_price = undefined
    }
    form.post(route('earn.subscribe'), {
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    });
};

const handleButtonClick = () => {
    if (!coinUnit.value) {
        coinUnit.value = parseInt(coin.unit);
    } else {
        coinUnit.value = null;
    }
}
</script>

<template>
    <Button
        type="button"
        class="w-full flex justify-center uppercase font-semibold text-sm"
        @click="openSubscribeModal"
    >
        {{$t('public.earn.subscribe')}}
    </Button>

    <Modal :show="subscribeModal" :title="$t('public.earn.subscribe')" @close="closeModal">
        <div class="p-5 bg-white rounded-xl shadow-md dark:bg-gray-700 my-3">
            <div class="grid grid-cols-2 gap gap-5">
                <div class="flex flex-col gap-2 items-center justify-center text-white">
                    <img class="w-10 h-10 rounded bg-white" src="/assets/icon.png" alt="Medium avatar">
                    <div class="font-semibold dark:text-white">
                        {{ plan.name }}
                    </div>
                    <div v-if="plan.type === 'staking'" class="font-semibold text-[32px] text-center">
                        {{ plan.commision_multiplier * 100 }}% <span class="flex text-base">profit share</span>
                    </div>
                    <div v-else class="font-semibold text-[32px]">
                        {{ plan.roi_per_annum }} p.a.
                    </div>
                </div>
                <div class="flex flex-col gap-2 dark:text-gray-300 space-y-3">
                    <div v-for="item in plan.descriptions">
                        <div class="flex items-center gap-2 text-xs">
                            <checkIcon />
                            {{ item.description }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form class="pt-5">
            <!-- standard plan -->
            <template v-if="plan.type === 'standard'">
                <div v-if="plan.type === 'ebmi'" class="grid sm:flex gap-4 mt-5">
                    <Label class="text-sm dark:text-white w-1/4" for="unit_number" :value="$t('public.earn.unit_number')" />
                    <div class="flex flex-col w-full">
                        <Input
                            id="unit_number"
                            type="text"
                            :placeholder="$t('public.earn.unit_number_placeholder')"
                            class="block w-full"
                            :class="form.errors.unit_number ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            v-model="form.unit_number"
                        />
                        <InputError :message="form.errors.unit_number" class="mt-2" />
                    </div>
                </div>

                <div v-if="plan.type === 'ebmi'" class="grid sm:flex gap-4 mt-5">
                    <Label class="text-sm dark:text-white w-full sm:w-1/4" for="amount" :value="$t('public.earn.housing_price')" />
                    <div class="flex flex-col w-full">
                        <Input
                            id="amount"
                            type="number"
                            min="0"
                            step="100"
                            placeholder="$ 0.00"
                            class="block w-full"
                            :class="form.errors.housing_price ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            v-model="housingPrice"
                        />
                        <InputError :message="form.errors.housing_price" class="mt-2" />
                        <span class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{$t('public.earn.investment_amount')}} $&nbsp;{{ amountCalculation }}</span>
                        <span class="text-xs text-gray-500 mt-2">{{$t('public.earn.investment_amount_rule')}}</span>
                    </div>
                </div>

                <div v-else class="grid sm:flex gap-4 mt-5">
                    <Label class="text-sm dark:text-white w-full md:w-1/4" for="amount" :value="$t('public.earn.input_amount')" />
                    <div class="flex flex-col w-full">
                        <Input
                            id="amount"
                            type="number"
                            min="0"
                            step="100"
                            placeholder="$ 0.00"
                            class="block w-full"
                            :class="form.errors.amount ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            v-model="form.amount"
                        />
                        <InputError :message="form.errors.amount" class="mt-2" />
                        <span class="text-xs text-gray-500 mt-2">{{$t('public.earn.investment_amount_rule')}}</span>
                    </div>
                </div>
            </template>

            <!-- staking -->
            <template v-if="plan.type === 'staking'">
                <div class="grid sm:flex gap-4">
                    <Label class="text-sm dark:text-white w-full sm:w-1/4" for="unit" :value="$t('public.wallet.unit') + ' (' + settingCoin.name + ')'" />
                    <div class="flex flex-col w-full">
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
                                    {{ !coinUnit ? $t('public.wallet.full_amount') : 'Clear' }}
                                </Button>
                            </div>
                        </InputIconWrapper>
                        <InputError :message="form.errors.unit" class="mt-2" />
                        <span class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{$t('public.current_amount')}}: {{ formatAmount(coin.unit, 8) }}</span>
                    </div>
                </div>

                <div class="flex flex-col items-start gap-3 self-stretch pt-5 pb-5 border-t mt-5 border-gray-400 dark:border-gray-700">
                    <div class="flex justify-between items-start self-stretch">
                        <div class="text-gray-600 dark:text-gray-400 font-normal text-sm">
                            {{$t('public.current_price')}}
                        </div>
                        <div class="text-sm text-gray-900 dark:text-white">
                            $&nbsp;{{ coin_price.price ?? '0.00' }}
                        </div>
                    </div>
                    <div class="flex justify-between items-start self-stretch">
                        <div class="text-gray-600 dark:text-gray-400 font-normal text-sm">
                            {{$t('public.linked_price')}}
                        </div>
                        <div class="text-sm text-gray-900 dark:text-white">
                            $&nbsp;{{ linkedPrice ?? '0.00' }}
                        </div>
                    </div>
                    <div class="flex justify-between items-start self-stretch">
                        <div class="text-gray-600 dark:text-gray-400 font-normal text-sm">
                            {{$t('public.staking_fee')}} ({{ props.stackingFee.value }}%) ({{$t('public.staking_pay')}})
                        </div>
                        <div class="text-sm text-gray-900 dark:text-white">
                            $&nbsp;{{ stackingFee ?? '0.00' }}
                        </div>
                    </div>
                </div>
            </template>

            <div class="mt-6 pb-6 border-b dark:border-gray-700">
                <label>
                    <div class="flex">
                        <Checkbox name="remember" v-model:checked="form.terms" />
                        <span class="ml-2 text-xs dark:text-gray-400">{{$t('public.agreement')}}</span>
                    </div>
                    <InputError v-if="form.errors.terms" :message="form.errors.terms" class="mt-2" />
                </label>

            </div>

            <div class="py-5 grid grid-cols-2 gap-4 w-full md:w-1/3 md:float-right">
                <Button variant="secondary" type="button" class="justify-center" @click.prevent="closeModal">
                    {{$t('public.cancel')}}
                </Button>
                <Button class="justify-center" @click="submit" :disabled="form.processing">{{$t('public.confirm')}}</Button>
            </div>
        </form>
    </Modal>
</template>
