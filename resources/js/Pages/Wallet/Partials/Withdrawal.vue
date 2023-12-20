<script setup>
import Button from "@/Components/Button.vue";
import {DepositIcon, WithdrawalIcon} from "@/Components/Icons/outline.jsx";
import {ref, computed } from "vue";
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
    wallet_sel: Array,
    wallets: Array
})
const withdrawalModal = ref(false);

const openWithdrawalModal = () => {
    withdrawalModal.value = true
}

const closeModal = () => {
    withdrawalModal.value = false
}

const transactionFee = computed(() => 2.00.toFixed(2));

const form = useForm({
    wallet_id: '',
    amount: '',
    wallet_address: '',
    terms: false,
    payment_charges:  transactionFee.value
})

const submit = () => {
    form.amount = calculatedBalance.value;
    form.post(route('wallet.withdrawal'), {
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    });
};

const fullWithdraw = () => {
    const selectedWallet = props.wallets.find(wallet => wallet.id === form.wallet_id);
    if (!form.wallet_id) {
        form.errors.wallet_id = "Please select a wallet before pressing Full Amount";
        return;
    }
    if (selectedWallet) {
        form.errors.wallet_id = '';
        form.amount = selectedWallet.balance || 0;
    }
};

const calculatedBalance = computed(() => {
    const calculated = form.amount - 2;
    return calculated <= 0 ? 0 : calculated
});
</script>

<template>
    <Button
        class="justify-center gap-2"
        variant="gray"
        @click="openWithdrawalModal"
    >
        <WithdrawalIcon aria-hidden="true" class="w-5 h-5" />
        <span class="uppercase">Withdrawal</span>
    </Button>

    <Modal :show="withdrawalModal" title="Withdrawal" @close="closeModal">
        <div class="space-y-2">
            <form class="pt-2">
                <div class="flex flex-col sm:flex-row gap-4">
                    <Label class="text-sm dark:text-white w-full md:w-1/4 pt-0.5" for="amount" value="Select Wallet" />
                    <div class="flex flex-col w-full">
                        <BaseListbox
                            v-model="form.wallet_id"
                            :options="props.wallet_sel"
                            :error="form.errors.wallet_id"
                        />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 mt-5">
                    <Label class="text-sm dark:text-white w-full md:w-1/4" for="wallet_address" value="Wallet Address" />
                    <div class="flex flex-col w-full">
                        <Input
                            id="wallet_address"
                            type="text"
                            placeholder="Wallet Address"
                            class="block w-full"
                            :class="form.errors.wallet_address ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            v-model="form.wallet_address"
                            autocomplete="off"
                        />
                        <InputError :message="form.errors.wallet_address" class="mt-2" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 mt-5">
                    <Label class="text-sm dark:text-white w-full md:w-1/4" for="amount" value="Amount ($)" />
                    <div class="relative flex flex-col w-full">
                        <Input
                            id="amount"
                            type="number"
                            min="50"
                            placeholder="Min. amount $ 50.00"
                            class="block w-full"
                            :class="form.errors.amount ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            v-model="form.amount"
                        />
                        <Button variant="gray" size="sm" class="absolute  top-1/2 transform -translate-y-1/2 end-3" @click.stop="fullWithdraw" @click.prevent="openWithdrawalModal">Full Amount</Button>
                        <InputError :message="form.errors.amount" class="mt-2" />
                    </div>
                </div>
                <div class="mt-6 border-t dark:border-gray-700"></div>
                    <div class="flex items-center justify-between mt-5">
                        <span class="text-sm dark:text-gray-400 font-Inter">Transaction Fee</span>
                        <span class="text-sm dark:text-white">$ {{ transactionFee }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm dark:text-gray-400 font-Inter">Balance Received</span>
                        <span class="text-sm dark:text-white">$ {{ (calculatedBalance).toFixed(2) }}</span>
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
