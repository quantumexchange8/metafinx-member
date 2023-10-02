<script setup>
import Button from "@/Components/Button.vue";
import {DepositIcon, Wallet} from "@/Components/Icons/outline.jsx";
import {ref} from "vue";
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

const props = defineProps({
    wallet_sel: Array
})
const depositModal = ref(false);
const tooltipContent = ref('Copy');

const openDepositModal = () => {
    depositModal.value = true
}

const closeModal = () => {
    depositModal.value = false
}

const form = useForm({
    wallet_id: '',
    amount: '',
    txn_hash: '',
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
    let walletAddressCopy = document.querySelector('#cryptoWalletAddress')
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
</script>

<template>
    <Button
        class="justify-center gap-2"
        variant="primary"
        @click="openDepositModal"
    >
        <DepositIcon aria-hidden="true" class="w-5 h-5" />
        <span>Deposit</span>
    </Button>

    <Modal :show="depositModal" title="Deposit" @close="closeModal">
        <div class="space-y-2">
            <div class="inline-flex items-center justify-center gap-2 w-full">
                <span class="rounded-full w-12 h-12 grow-0 shrink-0 bg-pink-600"><Wallet class="w-10 h-10 mt-1 ml-1" /></span>
                <h3 class="text-xl font-semibold dark:text-white">Internal Wallet</h3>
            </div>
            <div class="flex justify-center">
                <div class="space-y-2">
                    <p class="text-base text-center dark:text-gray-400">
                        Scan QR code to deposit
                    </p>
                    <div class="flex justify-center">
                        <qrcode-vue :class="['border-4 border-white']" value="TGqosdkka9VcHB7jT6atakNyoABV2VSQkZ" :size="200"></qrcode-vue>
                    </div>
                    <div class="inline-flex items-center gap-2 text-center dark:text-white">
                        TXzZ1zgHscuLeTqYTHZBuU5516SP3DbS8R
                        <input type="hidden" id="cryptoWalletAddress" value="TXzZ1zgHscuLeTqYTHZBuU5516SP3DbS8R">
                        <Tooltip :content="tooltipContent" placement="top">
                            <DuplicateIcon aria-hidden="true" :class="['w-6 dark:text-white']" @click.stop.prevent="copyTestingCode" style="cursor: pointer" />
                        </Tooltip>
                    </div>
                </div>
            </div>
            <form class="pt-2">
                <div class="flex gap-4">
                    <Label class="text-sm dark:text-white w-1/4 pt-0.5" for="amount" value="Select Wallet" />
                    <div class="flex flex-col w-full">
                        <BaseListbox
                            v-model="form.wallet_id"
                            :options="props.wallet_sel"
                            :error="form.errors.wallet_id"
                        />
                        <InputError :message="form.errors.wallet_id" class="mt-2" />
                    </div>
                </div>

                <div class="flex gap-4 mt-5">
                    <Label class="text-sm dark:text-white w-1/4" for="amount" value="Amount ($)" />
                    <div class="flex flex-col w-full">
                        <Input
                            id="amount"
                            type="number"
                            min="0"
                            placeholder="Min. amount $ 20.00"
                            class="block w-full"
                            :class="form.errors.amount ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            v-model="form.amount"
                        />
                        <InputError :message="form.errors.amount" class="mt-2" />
                    </div>
                </div>

                <div class="flex gap-4 mt-5">
                    <Label class="text-sm dark:text-white w-1/4" for="txn_hash" value="TXN Hash" />
                    <div class="flex flex-col w-full">
                        <Input
                            id="txn_hash"
                            type="text"
                            placeholder="Transaction Hashes"
                            class="block w-full"
                            :class="form.errors.txn_hash ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            v-model="form.txn_hash"
                            autocomplete="off"
                        />
                        <InputError :message="form.errors.txn_hash" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 pb-6 border-b dark:border-gray-700">
                    <label>
                        <div class="flex">
                            <Checkbox name="remember" v-model:checked="form.terms" />
                            <span class="ml-2 text-xs dark:text-gray-400">By proceeding, I agree that I have read the supporting documents and agree to the Terms and Conditions and Privacy Notice.</span>
                        </div>
                        <InputError v-if="form.errors.terms" :message="form.errors.terms" class="mt-2" />
                    </label>

                </div>

                <div class="py-5 grid grid-cols-2 gap-4 w-full md:w-1/3 md:float-right">
                    <Button variant="secondary" type="button" class="justify-center" @click.prevent="closeModal">
                        Cancel
                    </Button>
                    <Button class="justify-center" @click="submit" :disabled="form.processing">Confirm</Button>
                </div>
            </form>
        </div>
    </Modal>
</template>
