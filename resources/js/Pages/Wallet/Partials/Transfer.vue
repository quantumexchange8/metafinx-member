<script setup>
import Button from "@/Components/Button.vue";
import {InternalUSDWalletIcon, InternalMUSDWalletIcon, SwitchHorizontalIcon, ArrowRight} from "@/Components/Icons/outline.jsx";
import {ref, watch, onMounted, watchEffect} from "vue";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import BaseListbox from "@/Components/BaseListbox.vue";
import Input from "@/Components/Input.vue";
import Checkbox from "@/Components/Checkbox.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import Terms from "@/Components/Terms.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    setting_coin: Object,
    wallet_sel: Object,
})
const swapTerm = 'swap';

const depositModal = ref(false);
const tooltipContent = ref('Copy');

const openDepositModal = () => {
    depositModal.value = true
}

const closeModal = () => {
    depositModal.value = false
}

const plans = ref([]);

const selected = ref(null);


const form = useForm({
    wallet_id: props.wallet_sel[0].value,
    email: '',
    amount: '',
    remarks: '',
    terms: false,
})

watch(selected, (newVal) => {
    if (form.amount !== '') {
        form.amount = newVal.balance
    }
})

const submit = () => {
    form.post(route('wallet.Transfer'), {
        onSuccess: () => {
            closeModal();
            form.reset();
            fetchWallets();
        },
    });
};

const fullAmount = () => {
    form.errors.wallet_id = '';
    form.amount = props.wallet_sel[0].balance || 0;
}

</script>

<template>
    <Button
        class="justify-center gap-2 w-full"
        variant="secondary-700"
        @click="openDepositModal"
    >
        <SwitchHorizontalIcon aria-hidden="true" class="w-5 h-5" />
        <span class="uppercase text-sm font-semibold">{{$t('public.transfer')}}</span>
    </Button>

    <Modal :show="depositModal" :title="$t('public.transfer')" @close="closeModal">
        <form class="space-y-8">
            <div class="flex flex-col sm:flex-row gap-4">
                <Label class="text-sm dark:text-white w-full md:w-1/4 pt-0.5" for="amount" :value="$t('public.wallet.select_wallet')" />
                <div class="flex flex-col w-full">
                    <BaseListbox
                        v-model="form.wallet_id"
                        :options="[props.wallet_sel[0]]"
                        :error="form.errors.wallet_id"
                    />
                    <div class="text-sm font-normal text-gray-600 dark:text-gray-400 mt-1">
                        {{ props.wallet_sel[0].label }} {{ $t('public.wallet.balance') }}: $&nbsp;{{ props.wallet_sel[0].balance }}
                    </div>
                </div>
            </div>
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
                        class="absolute top-[257px] sm:top-[183px] right-6"
                        @click="fullAmount"
                    >
                        {{ $t('public.wallet.full_amount') }}
                    </Button>
                    <InputError :message="form.errors.amount" class="mt-2" />
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 mt-5">
                <Label class="text-sm dark:text-white w-full md:w-1/4" for="email" :value="$t('public.email')" />
                <div class="flex flex-col w-full">
                    <Input
                        id="email"
                        type="text"
                        min="0"
                        placeholder="$ 0.00"
                        class="block w-full"
                        :class="form.errors.email ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                        v-model="form.email"
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>
            </div>


            <!-- <div class="mt-6 pb-8 border-b dark:border-gray-700">
                <label>
                    <div class="flex">
                        <Checkbox name="remember" v-model:checked="form.terms" />
                        <span class="ml-2 text-xs dark:text-gray-400">{{ $t('public.agreement') }}
                            <Terms 
                                :type=swapTerm 
                            />
                        </span>
                    </div>
                    <InputError v-if="form.errors.terms" :message="form.errors.terms" class="mt-2" />
                </label>
            </div> -->

            <div class="pb-5 grid grid-cols-2 gap-4 w-full md:w-1/3 md:float-right">
                <Button variant="secondary" type="button" class="justify-center" @click.prevent="closeModal">
                    {{$t('public.cancel')}}
                </Button>
                <Button class="justify-center" @click="submit" :disabled="form.processing">{{$t('public.confirm')}}</Button>
            </div>
        </form>
    </Modal>
</template>
