<script setup>
import Button from "@/Components/Button.vue";
import {InternalUSDWalletIcon, InternalMUSDWalletIcon, SwitchHorizontalIcon, ArrowRight} from "@/Components/Icons/outline.jsx";
import {ref, watch} from "vue";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import Checkbox from "@/Components/Checkbox.vue";
import {useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import {
    RadioGroup,
    RadioGroupLabel,
    RadioGroupDescription,
    RadioGroupOption,
} from '@headlessui/vue'

const props = defineProps({
    wallets: Object
})
const depositModal = ref(false);
const tooltipContent = ref('Copy');

const openDepositModal = () => {
    depositModal.value = true
}

const closeModal = () => {
    depositModal.value = false
}

const plans = props.wallets.map((wallet, index) => ({
    label: `${wallet.name} to ${props.wallets[1 - index].name}`,
    value: index === 0 ? 'InternalWalletToMUSDWallet' : 'MUSDWalletToInternalWallet',
    wallet: wallet.id,
    name: wallet.name,
    balance: wallet.balance,
}));

const selected = ref(plans[0])
const form = useForm({
    from_wallet_id: '',
    to_wallet_id: '',
    amount: '',
    remarks: '',
    terms: false
})

watch(selected, (newVal) => {
    if (form.amount !== '') {
        form.amount = newVal.balance
    }
})

const submit = () => {
    form.from_wallet_id = selected.value.wallet;
    // Find the other wallet based on the selected plan
    const otherWallet = plans.find(plan => plan.value !== selected.value.value);

    if (otherWallet) {
        form.to_wallet_id = otherWallet.wallet;
    } else {
        // Handle the case where the other wallet is not found (optional)
        console.error('Error: Other wallet not found');
        return;
    }
    form.remarks = selected.value.label

    form.post(route('wallet.internalTransfer'), {
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    });
};

const fullAmount = () => {
    const selectedWallet = props.wallets.find(wallets => wallets.id === selected.value.wallet);
    if (!selected.value.wallet) {
        form.errors.wallet_id = "Please select a wallet before pressing Full Amount";
        return;
    }
    if (selected.value.wallet) {
        form.errors.wallet_id = '';
        form.amount = selectedWallet.balance || 0;
    }
}

</script>

<template>
    <Button
        class="justify-center gap-2 w-full"
        variant="secondary-700"
        @click="openDepositModal"
    >
        <SwitchHorizontalIcon aria-hidden="true" class="w-5 h-5" />
        <span class="uppercase text-sm font-semibold">{{$t('public.wallet.internal_transfer')}}</span>
    </Button>

    <Modal :show="depositModal" :title="$t('public.wallet.internal_transfer')" @close="closeModal">
        <form class="space-y-8">
            <RadioGroup v-model="selected">
                <RadioGroupLabel class="sr-only">Server size</RadioGroupLabel>
                <div class="flex gap-5 items-center self-stretch w-full">
                    <RadioGroupOption
                        as="template"
                        v-for="(plan, index) in plans"
                        :key="index"
                        :value="plan"
                        v-slot="{ active, checked }"
                    >
                        <div
                            :class="[
                            active
                              ? 'ring-0 ring-white ring-offset-0'
                              : '',
                            checked ? 'border-2 border-gray-400 dark:border-white bg-gray-400 dark:bg-gray-600' : 'bg-gray-500 dark:bg-gray-700',
                        ]"
                            class="relative flex cursor-pointer rounded-xl p-5 shadow-md focus:outline-none w-full"
                        >
                            <div class="flex items-center w-full">
                                <div class="text-sm flex flex-col gap-3 w-full">
                                    <RadioGroupLabel
                                        as="div"
                                        class="font-medium"
                                    >
                                        <div class="flex justify-center items-center gap-3">
                                            <template v-if="plan.value === 'InternalWalletToMUSDWallet'">
                                                <div class="flex items-center justify-center bg-gradient-to-b from-pink-400 to-pink-500 dark:shadow-pink-500 rounded-full w-5 h-5 shrink-0 grow-0">
                                                    <InternalUSDWalletIcon class="w-4 h-4" />
                                                </div>
                                                <ArrowRight class="text-white" />
                                                <div class="flex items-center justify-center bg-gradient-to-b from-warning-300 to-warning-500 dark:shadow-pink-500 rounded-full w-5 h-5 shrink-0 grow-0">
                                                    <InternalMUSDWalletIcon class="w-4 h-4" />
                                                </div>
                                            </template>

                                            <template v-else-if="plan.value === 'MUSDWalletToInternalWallet'">
                                                <div class="flex items-center justify-center bg-gradient-to-b from-warning-300 to-warning-500 dark:shadow-pink-500 rounded-full w-5 h-5 shrink-0 grow-0">
                                                    <InternalMUSDWalletIcon class="w-4 h-4" />
                                                </div>
                                                <ArrowRight class="text-white" />
                                                <div class="flex items-center justify-center bg-gradient-to-b from-pink-400 to-pink-500 dark:shadow-pink-500 rounded-full w-5 h-5 shrink-0 grow-0">
                                                    <InternalUSDWalletIcon class="w-4 h-4" />
                                                </div>
                                            </template>
                                        </div>
                                    </RadioGroupLabel>
                                    <RadioGroupDescription
                                        as="span"
                                        class="text-sm font-medium text-white w-full text-center"
                                    >
                                        {{ plan.label }}
                                    </RadioGroupDescription>
                                </div>
                            </div>
                        </div>
                    </RadioGroupOption>
                </div>
                <InputError :message="form.errors.from_wallet_id" class="mt-2" />
            </RadioGroup>

            <div class="flex flex-col sm:flex-row gap-4 mt-5">
                <Label class="text-sm dark:text-white w-full md:w-1/4" for="amount" :value="$t('public.wallet.amount')  + ' ($)'" />
                <div class="flex flex-col w-full">
                    <Input
                        id="amount"
                        type="number"
                        min="0"
                        placeholder="$ 0.00"
                        class="block w-full"
                        :class="form.errors.amount ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                        v-model="form.amount"
                    />
                    <Button
                        type="button"
                        variant="gray"
                        size="sm"
                        class="absolute top-[270px] sm:top-[214px] right-6"
                        @click="fullAmount"
                    >
                        {{ $t('public.wallet.full_amount') }}
                    </Button>
                    <InputError :message="form.errors.amount" class="mt-2" />
                    <div class="text-sm font-normal text-gray-600 dark:text-gray-400 mt-1">
                        {{ selected.name }} balance: $ {{ selected.balance }}
                    </div>
                </div>
            </div>

            <div class="mt-6 pb-8 border-b dark:border-gray-700">
                <label>
                    <div class="flex">
                        <Checkbox name="remember" v-model:checked="form.terms" />
                        <span class="ml-2 text-xs dark:text-gray-400">{{$t('public.agreement')}}</span>
                    </div>
                    <InputError v-if="form.errors.terms" :message="form.errors.terms" class="mt-2" />
                </label>
            </div>

            <div class="pb-5 grid grid-cols-2 gap-4 w-full md:w-1/3 md:float-right">
                <Button variant="secondary" type="button" class="justify-center" @click.prevent="closeModal">
                    {{$t('public.cancel')}}
                </Button>
                <Button class="justify-center" @click="submit" :disabled="form.processing">{{$t('public.confirm')}}</Button>
            </div>
        </form>
    </Modal>
</template>
