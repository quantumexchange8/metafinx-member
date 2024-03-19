<script setup>
import GuestLayout from "@/Layouts/Guest.vue";
import {Rank1Icon, Rank2Icon, Rank3Icon, Rank4Icon} from "@/Components/Icons/outline.jsx";
import {transactionFormat} from "@/Composables/index.js";
import Tooltip from "@/Components/Tooltip.vue";
import {DuplicateIcon} from "@heroicons/vue/outline";
import {ref} from "vue";
import Button from "@/Components/Button.vue";
import {useForm} from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    transaction: Object,
    transaction_fee: Object,
    profile_photo_url: String,
    payment_slip: String,
})
const { formatAmount, formatWalletAddress } = transactionFormat();
const tooltipContent = ref('copy');
const copyWalletAddress = ref(null);
const copyTxID = ref(null);

const copyAddress = () => {
    if (copyWalletAddress.value) {
        const textArea = document.createElement('textarea');
        textArea.value = props.transaction.to_wallet_address;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);

        tooltipContent.value = 'copied';
        setTimeout(() => {
            tooltipContent.value = 'copy'; // Reset tooltip content to 'Copy' after 2 seconds
        }, 1000);
    }
}

const copyTxnHash = () => {
    if (copyTxID.value) {
        const textArea = document.createElement('textarea');
        textArea.value = props.transaction.txn_hash;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);

        tooltipContent.value = 'copied';
        setTimeout(() => {
            tooltipContent.value = 'copy'; // Reset tooltip content to 'Copy' after 2 seconds
        }, 1000);
    }
}

const form = useForm({
    id: props.transaction.id,
    status: ''
})

const submitApproval = (status) => {
    form.status = status

    form.post(route('deposit_approval'))
}

const receiptModal = ref(false);
const openReceiptModal = () => {
    receiptModal.value = true
}

const closeModal = () => {
    receiptModal.value = false
}
</script>

<template>
    <GuestLayout :title="$t('public.wallet.deposit_approval')">
        <div class="text-center">
            <h2 class="text-4xl	font-sans font-bold mb-2 text-gray-800 dark:text-white">{{ $t('public.wallet.deposit_approval') }}</h2>
            <p class="font-sans text-gray-600 dark:text-gray-400">{{ $t('public.wallet.deposit_approval_message') }}</p>
        </div>

        <form>
            <div class="flex items-center p-5 rounded-xl bg-gray-300 dark:bg-gray-700 gap-2 self-stretch w-full my-5">
                <img :src="profile_photo_url ? profile_photo_url : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-10 h-10 rounded-full" alt="">
                <div class="flex flex-col items-start gap-1">
                    <div class="flex items-center gap-2">
                        <div class="text-gray-700 dark:text-white text-sm font-semibold">
                            {{ transaction.user.name }}
                        </div>
                        <Rank1Icon class="h-5 w-5" v-if="transaction.user.setting_rank_id === 2" />
                        <Rank2Icon class="h-5 w-5" v-if="transaction.user.setting_rank_id === 3" />
                        <Rank3Icon class="h-5 w-5" v-if="transaction.user.setting_rank_id === 4" />
                        <Rank4Icon class="h-5 w-5" v-if="transaction.user.setting_rank_id === 5" />
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        {{ transaction.user.email }}
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-semibold dark:text-gray-400">{{ $t('public.wallet.deposit') }} {{ $t('public.wallet.amount') }}</span>
                <span class="text-black dark:text-white py-2">$ {{ formatAmount(transaction.amount) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-semibold dark:text-gray-400">{{ $t('public.wallet.transaction_fee') }} ({{ transaction_fee.value }}%)</span>
                <span class="text-black dark:text-white py-2">$ {{ formatAmount(transaction.transaction_charges) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-semibold dark:text-gray-400">{{ $t('public.wallet.receive') }} {{ $t('public.wallet.amount') }}</span>
                <span class="text-black dark:text-white py-2">$ {{ formatAmount(transaction.transaction_amount) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-semibold dark:text-gray-400">{{ $t('public.wallet.transaction_number') }}</span>
                <span class="text-black dark:text-white py-2">{{ transaction.transaction_number }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-semibold dark:text-gray-400">{{ $t('public.wallet.to') }} {{ $t('public.wallet.wallet_address') }}</span>
                <div class="flex items-center gap-1">
                    <div ref="copyWalletAddress" class="text-black dark:text-white py-2">{{ formatWalletAddress(transaction.to_wallet_address) }}</div>
                    <Tooltip :content="$t('public.' + tooltipContent)" placement="top">
                        <DuplicateIcon aria-hidden="true" class="cursor-pointer w-6 text-white" @click.stop.prevent="copyAddress" />
                    </Tooltip>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-semibold dark:text-gray-400">{{ $t('public.wallet.txn_hash') }}</span>
                <div class="flex items-center gap-1">
                    <div ref="copyTxID" class="text-black dark:text-white py-2">{{ formatWalletAddress(transaction.txn_hash) }}</div>
                    <Tooltip :content="$t('public.' + tooltipContent)" placement="top">
                        <DuplicateIcon aria-hidden="true" class="cursor-pointer w-6 text-white" @click.stop.prevent="copyTxnHash" />
                    </Tooltip>
                </div>
            </div>

            <div v-if="payment_slip" class="flex justify-between items-center">
                <span class="text-sm font-semibold dark:text-gray-400">{{ $t('public.payment_slip') }}</span>
                <span class="text-black dark:text-white py-2 dark:hover:text-blue-600 cursor-pointer" @click="openReceiptModal">{{ $t('public.click_to_view') }}</span>
            </div>

            <div
                v-if="transaction.status === 'Processing'"
                class="grid grid-cols-2 gap-2 my-5"
            >
                <Button
                    type="button"
                    variant="success"
                    class="flex justify-center"
                    @click.prevent="submitApproval('Success')"
                    :disabled="form.processing"
                >
                    {{ $t('public.approve') }}
                </Button>
                <Button
                    type="button"
                    variant="danger"
                    class="flex justify-center"
                    @click.prevent="submitApproval('Rejected')"
                    :disabled="form.processing"
                >
                    {{ $t('public.reject') }}
                </Button>
            </div>
            <div v-else class="flex justify-center my-5">
                 <div
                     class="py-2 px-4 rounded-md text-white"
                     :class="transaction.status === 'Success' ? 'bg-success-500' : 'bg-error-500'"
                 >
                     {{ $t('public.wallet.status') }} - {{ transaction.status }}
                 </div>
            </div>
        </form>

        <Modal :show="receiptModal" @close="closeModal">
            <div class="flex justify-center">
                <img class="rounded" :src="payment_slip" alt="Payment Receipt">
            </div>
        </Modal>
    </GuestLayout>
</template>
