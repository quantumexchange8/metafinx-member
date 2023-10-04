<script setup>
import {InternalWalletIcon} from "@/Components/Icons/outline.jsx";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {transactionFormat} from "@/Composables/index.js";

const props = defineProps({
    withdrawals: Object
})

const transactionModal = ref(false);
const selectedDeposit = ref();
const { formatDate } = transactionFormat();

const openTransactionModal = (deposit) => {
    selectedDeposit.value = deposit;
    transactionModal.value = true;
}

const closeModal = () => {
    transactionModal.value = false
}
</script>

<template>
    <tr
        v-for="withdrawal in withdrawals.data"
        class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600 hover:cursor-pointer dark:hover:bg-gray-600"
        @click="openTransactionModal(withdrawal)"
    >
        <td class="pl-5 py-3 inline-flex items-center gap-2">
            <div class="bg-gradient-to-t from-pink-300 to-pink-600 dark:shadow-pink-500 rounded-full w-4 h-4 shrink-0 grow-0">
                <InternalWalletIcon class="mt-0.5 ml-0.5"/>
            </div>
            {{ withdrawal.wallet.name }}
        </td>
        <td class="py-3">
            {{ withdrawal.transaction_id }}
        </td>
        <td class="py-3">
            {{ formatDate(withdrawal.created_at) }}
        </td>
        <td class="py-3">
            {{ withdrawal.to_wallet_address }}
        </td>
        <td class="py-3">
            {{ withdrawal.amount }}
        </td>
        <td class="py-3 text-center">
            <span v-if="withdrawal.status === 'Success'" class="flex w-2 h-2 bg-green-500 dark:bg-success-500 mx-auto rounded-full"></span>
            <span v-else-if="withdrawal.status === 'Pending'" class="flex w-2 h-2 bg-red-500 dark:bg-warning-500 mx-auto rounded-full"></span>
            <span v-else-if="withdrawal.status === 'Processing'" class="flex w-2 h-2 bg-red-500 dark:bg-[#007AFF] mx-auto rounded-full"></span>
            <span v-else-if="withdrawal.status === 'Rejected'" class="flex w-2 h-2 bg-red-500 dark:bg-error-500 mx-auto rounded-full"></span>
        </td>
    </tr>

    <Modal :show="transactionModal" title="Transaction Details" @close="closeModal">
        <div v-if="selectedDeposit">
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">Transaction Type</span>
                <span class="text-black dark:text-white py-2">{{ selectedDeposit.type }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">Transaction ID</span>
                <span class="text-black dark:text-white py-2">{{ selectedDeposit.transaction_id }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">Date & Time</span>
                <span class="text-black dark:text-white py-2">{{ formatDate(selectedDeposit.created_at) }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">To</span>
                <span class="text-black dark:text-white py-2">{{ selectedDeposit.to_wallet_address }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">Amount (unit)</span>
                <span class="text-black dark:text-white py-2">{{ selectedDeposit.amount }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">Price</span>
                <span class="text-black dark:text-white py-2">$ {{ selectedDeposit.price }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">Transaction Status</span>
                <span class="text-black dark:text-white py-2">{{ selectedDeposit.status }}</span>
            </div>
        </div>
    </Modal>
</template>
