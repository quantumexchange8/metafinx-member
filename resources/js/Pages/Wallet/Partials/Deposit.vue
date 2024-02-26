<script setup>
import Button from "@/Components/Button.vue";
import {DepositIcon, InternalUSDWalletIcon} from "@/Components/Icons/outline.jsx";
import {computed, ref} from "vue";
import Modal from "@/Components/Modal.vue";
import QrcodeVue from 'qrcode.vue';
import {DuplicateIcon} from "@heroicons/vue/outline";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import Checkbox from "@/Components/Checkbox.vue";
import {useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Tooltip from "@/Components/Tooltip.vue";
import BaseListbox from "@/Components/BaseListbox.vue";
import {transactionFormat} from "@/Composables/index.js";

const props = defineProps({
    wallet_sel: Array,
    random_address: Object,
    depositFee: Object,
})
const depositModal = ref(false);
const tooltipContent = ref('Copy');

const openDepositModal = () => {
    depositModal.value = true
}

const closeModal = () => {
    depositModal.value = false
}

const walletId = props.wallet_sel[0];
const { formatAmount } = transactionFormat();

const form = useForm({
    wallet_id: walletId.value,
    amount: '',
    to_wallet_address: props.random_address.wallet_address,
    // txn_hash: '',
    terms: false
})

const submit = () => {
    form.post(route('wallet.deposit'), {
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    });
};

function copyTestingCode () {
    let walletAddressCopy = document.querySelector('#cryptoWalletAddress');
    walletAddressCopy.setAttribute('type', 'text');
    walletAddressCopy.select();

    try {
        var successful = document.execCommand('copy');
        if (successful) {
            tooltipContent.value = 'Copied!';
            setTimeout(() => {
                tooltipContent.value = 'Copy'; // Reset tooltip content to 'Copy' after 2 seconds
            }, 1000);
        } else {
            tooltipContent.value = 'Try Again Later!';
        }

    } catch (err) {
        alert('Oops, unable to copy');
    }

    /* unselect the range */
    walletAddressCopy.setAttribute('type', 'hidden')
    window.getSelection().removeAllRanges()
}

const transactionFee = computed(() => props.depositFee.value);

const calculatedTransactionFee = computed(() => {
    const calculated = form.amount * (transactionFee.value / 100);
    return calculated <= 0 ? 0 : calculated
});

const calculatedBalance = computed(() => {
    const calculated = form.amount * ((100 - transactionFee.value) / 100);
    return calculated <= 0 ? 0 : calculated
});
</script>

<template>
    <Button
        class="flex-grow justify-center gap-2"
        variant="primary"
        @click="openDepositModal"
    >
        <DepositIcon aria-hidden="true" class="w-5 h-5" />
        <span class="uppercase text-sm font-semibold">{{$t('public.wallet.deposit')}}</span>
    </Button>

    <Modal :show="depositModal" :title="$t('public.wallet.deposit')" @close="closeModal">
        <div class="space-y-2">
            <div class="flex items-center justify-center gap-2 w-full">
                <span class="rounded-full w-8 h-8 grow-0 shrink-0 bg-gradient-to-b from-pink-400 to-pink-500"><InternalUSDWalletIcon class="w-6 h-6 mt-1 ml-1" /></span>
                <h3 class="text-xl font-semibold dark:text-white">{{$t('public.wallet.internal_wallet')}}</h3>
            </div>
            <div class="flex justify-center">
                <div class="space-y-2">
                    <div class="text-base text-center dark:text-gray-400">
                        {{$t('public.wallet.scan_QR')}} - <span class="font-semibold">Only USDT (TRC20)</span>
                    </div>
                    <div class="flex justify-center">
                        <qrcode-vue :class="['border-4 border-white']" :value="props.random_address.wallet_address" :size="200"></qrcode-vue>
                    </div>
                    <div class="inline-flex justify-center w-full items-center gap-2 text-center dark:text-white break-all">
                        {{ props.random_address.wallet_address }}
                        <input type="hidden" id="cryptoWalletAddress" :value="props.random_address.wallet_address">
                        <Tooltip :content="tooltipContent" placement="top">
                            <DuplicateIcon aria-hidden="true" :class="['w-6 dark:text-white']" @click.stop.prevent="copyTestingCode" style="cursor: pointer" />
                        </Tooltip>
                    </div>
                </div>
            </div>
            <form>
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- <Label class="text-sm dark:text-white w-full md:w-1/4 pt-0.5" for="amount" :value="$t('public.wallet.select_wallet')" /> -->
                    <div class="flex flex-col w-full">
                        <BaseListbox
                            v-model="form.wallet_id"
                            :options="props.wallet_sel"
                            :error="form.errors.wallet_id"
                            class="hidden"
                        />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 mt-5">
                    <Label class="text-sm dark:text-white w-full md:w-1/4" for="amount" :value="$t('public.wallet.amount')  + ' ($)'" />
                    <div class="flex flex-col w-full">
                        <Input
                            id="amount"
                            type="number"
                            min="0"
                            :placeholder="$t('public.wallet.deposit_amount_placeholder') + ' $ ' + '20'"
                            class="block w-full"
                            :class="form.errors.amount ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            v-model="form.amount"
                        />
                        <InputError :message="form.errors.amount" class="mt-2" />
                    </div>
                </div>

<!--                <div class="flex flex-col sm:flex-row gap-4 mt-5">-->
<!--                    <Label class="text-sm dark:text-white w-full md:w-1/4" for="txn_hash" :value="$t('public.wallet.txn_hash')" />-->
<!--                    <div class="flex flex-col w-full">-->
<!--                        <Input-->
<!--                            id="txn_hash"-->
<!--                            type="text"-->
<!--                            :placeholder="$t('public.wallet.txn_hash_placeholder')"-->
<!--                            class="block w-full"-->
<!--                            :class="form.errors.txn_hash ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"-->
<!--                            v-model="form.txn_hash"-->
<!--                            autocomplete="off"-->
<!--                        />-->
<!--                        <InputError :message="form.errors.txn_hash" class="mt-2" />-->
<!--                    </div>-->
<!--                </div>-->
                <div class="mt-6 border-t dark:border-gray-700"></div>
                <div class="flex items-center justify-between mt-5">
                    <span class="text-sm dark:text-gray-400 font-Inter">{{$t('public.wallet.transaction_fee')}}</span>
                    <span class="text-sm dark:text-white">$ {{ formatAmount(calculatedTransactionFee) }}</span>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-sm dark:text-gray-400 font-Inter">{{$t('public.wallet.balance_received')}}</span>
                    <span class="text-sm dark:text-white">$&nbsp;{{ formatAmount(calculatedBalance) }}</span>
                </div>
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
        </div>
    </Modal>
</template>
