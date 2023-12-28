<script setup>
import { InternalUSDWalletIcon, InternalMUSDWalletIcon } from "@/Components/Icons/outline.jsx";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {transactionFormat} from "@/Composables/index.js";

const props = defineProps({
    deposits: Object
})

const transactionModal = ref(false);
const selectedDeposit = ref();
const { formatDateTime } = transactionFormat();

const openTransactionModal = (deposit) => {
    selectedDeposit.value = deposit;
    transactionModal.value = true;
}

const closeModal = () => {
    transactionModal.value = false
}
</script>

<template>
    <tr v-if="deposits.data.length === 0">
        <th colspan="5" class="py-4 text-lg text-center">
            {{$t('public.wallet.no_history')}}
        </th>
    </tr>
    <tr
        v-for="deposit in deposits.data"
        class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600 hover:cursor-pointer dark:hover:bg-gray-600"
        @click="openTransactionModal(deposit)"
    >
        <td class="pl-5 py-3 inline-flex items-center gap-2">
            <div v-if="deposit.wallet.name === 'Internal Wallet'" class="bg-gradient-to-t from-pink-300 to-pink-600 dark:shadow-pink-500 rounded-full w-4 h-4 shrink-0 grow-0">
                <InternalUSDWalletIcon class="mt-0.5 ml-0.5"/>
            </div>
            <div v-else-if="deposit.wallet.name === 'MUSD Wallet'" class="bg-gradient-to-t from-warning-300 to-warning-600 dark:shadow-warning-500 rounded-full w-4 h-4 shrink-0 grow-0">
                <InternalMUSDWalletIcon class="mt-0.5 ml-0.5"/>
            </div>
            {{ deposit.wallet.name }}
        </td>
        <td class="py-3">
            {{ deposit.transaction_id }}
        </td>
        <td class="py-3">
            {{ formatDateTime(deposit.created_at) }}
        </td>
        <td class="py-3">
            $ {{ deposit.amount }}
        </td>
        <td class="py-3 text-center">
            <span v-if="deposit.status === 'Success'" class="flex w-2 h-2 bg-green-500 dark:bg-success-500 mx-auto rounded-full"></span>
            <span v-else-if="deposit.status === 'Pending'" class="flex w-2 h-2 bg-red-500 dark:bg-warning-500 mx-auto rounded-full"></span>
            <span v-else-if="deposit.status === 'Processing'" class="flex w-2 h-2 bg-red-500 dark:bg-[#007AFF] mx-auto rounded-full"></span>
            <span v-else-if="deposit.status === 'Rejected'" class="flex w-2 h-2 bg-red-500 dark:bg-error-500 mx-auto rounded-full"></span>
        </td>
    </tr>

    <Modal :show="transactionModal" :title="$t('public.wallet.transaction_details')" @close="closeModal">
        <div v-if="selectedDeposit">
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.transaction_type')}}</span>
                <span class="text-black dark:text-white py-2">{{ selectedDeposit.type }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.transaction_id')}}</span>
                <span class="text-black dark:text-white py-2">{{ selectedDeposit.transaction_id }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.date_time')}}</span>
                <span class="text-black dark:text-white py-2">{{ formatDateTime(selectedDeposit.created_at) }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.to')}}</span>
                <span class="text-black dark:text-white py-2">{{ selectedDeposit.to_wallet_address }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.amount')}}</span>
                <span class="text-black dark:text-white py-2">$ {{ selectedDeposit.amount }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.transaction_status')}}</span>
                <span class="text-black dark:text-white py-2">{{ selectedDeposit.status }}</span>
            </div>
            <div class="grid grid-cols-3 items-center">
                <span class="col-span-1 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.reject_reason')}}</span>
                <span class="text-black dark:text-white py-2">{{ selectedDeposit.remarks ?? '-' }}</span>
            </div>
        </div>
    </Modal>
</template>
