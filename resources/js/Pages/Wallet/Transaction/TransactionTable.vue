<script setup>
import {ref, watch, watchEffect} from "vue";
import {ArrowLeftIcon, ArrowRightIcon} from "@heroicons/vue/outline";
import {TailwindPagination} from "laravel-vue-pagination";
import {transactionFormat} from "@/Composables/index.js";
import debounce from "lodash/debounce.js";
import Loading from "@/Components/Loading.vue";
import Modal from "@/Components/Modal.vue";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    walletId: Number,
    search: String,
    type: String,
    date: String,
    refresh: Boolean,
    isLoading: Boolean,
})

const transactionLoading = ref(props.isLoading);
const refreshTransaction = ref(props.refresh);
const transactions = ref({data: []})
const currentPage = ref(1);
const transactionModal = ref(false);
const selectedTransaction = ref();

const openTransactionModal = (deposit) => {
    selectedTransaction.value = deposit;
    transactionModal.value = true;
}

const closeModal = () => {
    transactionModal.value = false
}

const emit = defineEmits(['update:loading', 'update:refresh']);
const { formatDateTime, formatAmount, formatType } = transactionFormat();

watch(
    [() => props.search, () => props.type, () => props.date],
    debounce(([searchValue, typeValue, dateValue]) => {
        getResults(1, searchValue, typeValue, dateValue);
    }, 300)
);

const getResults = async (page = 1, search = '', type = '', date = '') => {
    transactionLoading.value = true
    try {
        let url = `/wallet/getWalletHistory/` + props.walletId + `?page=${page}`;

        if (search) {
            url += `&search=${search}`;
        }

        if (type) {
            url += `&type=${type}`;
        }

        if (date) {
            url += `&date=${date}`;
        }

        const response = await axios.get(url);
        transactions.value = response.data;

    } catch (error) {
        console.error(error);
    } finally {
        transactionLoading.value = false
        emit('update:loading', false);
    }
}

getResults();

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;

        getResults(currentPage.value, props.search, props.type, props.date);
    }
};

const paginationClass = [
    'bg-transparent border-0 dark:text-gray-400 dark:enabled:hover:text-white'
];

const paginationActiveClass = [
    'border dark:border-gray-600 dark:bg-gray-600 rounded-full text-[#FF9E23] dark:text-white'
];


watch(() => props.refresh, (newVal) => {
    refreshTransaction.value = newVal;
    if (newVal) {
        // Call the getResults function when refresh is true
        getResults();
        emit('update:refresh', false);
    }
});

watchEffect(() => {
    if (usePage().props.title !== null) {
        getResults();
    }
});

</script>

<template>
    <div v-if="transactionLoading" class="w-full flex justify-center my-8">
        <Loading />
    </div>
    <div v-else class="overflow-x-auto">
        <table class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
            <thead class="text-xs font-medium text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-gray-400 border-b dark:border-gray-600">
            <tr>
                <th scope="col" class="p-3">
                    {{$t('public.wallet.date')}}
                </th>
                <th scope="col" class="p-3">
                    {{$t('public.wallet.transaction_type')}}
                </th>
                <th scope="col" class="p-3">
                    {{$t('public.wallet.transaction_id')}}
                </th>
                <th scope="col" class="p-3">
                    {{$t('public.wallet.amount')}}
                </th>
                <th scope="col" class="p-3 text-center">
                    {{$t('public.wallet.status')}}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="transactions.data.length === 0">
                <th colspan="5" class="py-4 text-lg text-center">
                    {{$t('public.wallet.no_history')}}
                </th>
            </tr>
            <tr
                v-for="transaction in transactions.data"
                class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600 hover:cursor-pointer dark:hover:bg-gray-600"
                @click="openTransactionModal(transaction)"
            >
            <td class="p-3">
                {{ formatDateTime(transaction.transaction_date) }}
            </td>
            <td class="p-3">
                <div v-if="transaction.transaction_type === 'Investment'">
                    Investment
                </div>
                <div v-else>
                    {{ formatType(transaction.transaction_type) }}
                </div>
            </td>
            <td class="p-3">
                <span v-if="['StandardRewards', 'ReferralEarnings', 'AffiliateEarnings', 'DividendEarnings', 'AffiliateDividendEarnings'].includes(transaction.transaction_type)">-</span>
                <span v-else>{{ transaction.transaction_id }}</span>
            </td>
                <td class="p-3">
                    <div :class="[
                            {'text-success-500': transaction.transaction_status === 'Success' && transaction.transaction_type === 'Deposit' || transaction.transaction_type === 'Withdrawal' || transaction.transaction_type === 'StandardRewards' || transaction.transaction_type === 'ReferralEarning' || transaction.transaction_type === 'AffiliateEarnings' || transaction.transaction_type === 'DividendEarnings' || transaction.transaction_type === 'AffiliateDividendEarnings'},
                            {'text-error-500': transaction.transaction_status === 'Success' && transaction.transaction_type === 'BuyCoin' || transaction.transaction_type === 'Investment' },
                            {'text-gray-900 dark:text-white': transaction.transaction_status === 'Rejected' && transaction.transaction_type === 'Deposit' ||transaction.transaction_type === 'Withdrawal' },
                        ]"
                    >
                        $ {{ formatAmount(transaction.transaction_amount) }}
                    </div>
                </td>
                <td class="p-3 text-center">
                    <span v-if="transaction.transaction_status === 'Success' || transaction.transaction_type === 'Investment'" class="flex w-2 h-2 bg-green-500 dark:bg-success-500 mx-auto rounded-full"></span>
                    <span v-else-if="transaction.transaction_status === 'Pending'" class="flex w-2 h-2 bg-red-500 dark:bg-warning-500 mx-auto rounded-full"></span>
                    <span v-else-if="transaction.transaction_status === 'Processing'" class="flex w-2 h-2 bg-red-500 dark:bg-[#007AFF] mx-auto rounded-full"></span>
                    <span v-else-if="transaction.transaction_status === 'Rejected'" class="flex w-2 h-2 bg-red-500 dark:bg-error-500 mx-auto rounded-full"></span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="flex justify-center mt-4" v-if="!transactionLoading">
        <TailwindPagination
            :item-classes=paginationClass
            :active-classes=paginationActiveClass
            :data="transactions"
            :limit=2
            @pagination-change-page="handlePageChange"
        >
            <template #prev-nav>
                <span class="flex gap-2"><ArrowLeftIcon class="w-5 h-5" /> <span class="hidden sm:flex">{{$t('public.previous')}}</span></span>
            </template>
            <template #next-nav>
                <span class="flex gap-2"><span class="hidden sm:flex">{{$t('public.next')}}</span> <ArrowRightIcon class="w-5 h-5" /></span>
            </template>
        </TailwindPagination>
    </div>

    <Modal :show="transactionModal" :title="$t('public.wallet.transaction_details')" @close="closeModal">
        <div v-if="selectedTransaction">
            <div class="grid grid-cols-5 items-center">
                <span class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.transaction_type')}}</span>
                <span class="text-black col-span-3 dark:text-white py-2">{{ formatType(selectedTransaction.transaction_type) }}</span>
            </div>
            <div class="grid grid-cols-5 items-center">
                <span class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.transaction_id')}}</span>
                <span class="text-black col-span-3 dark:text-white py-2" v-if="['StandardRewards', 'ReferralEarnings', 'AffiliateEarnings', 'DividendEarnings', 'AffiliateDividendEarnings'].includes(selectedTransaction.transaction_type)">-</span>
                <span class="text-black col-span-3 dark:text-white py-2" v-else>{{ selectedTransaction.transaction_id }}</span>
            </div>
            <div class="grid grid-cols-5 items-center">
                <span class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.date_time')}}</span>
                <span class="text-black col-span-3 dark:text-white py-2">{{ formatDateTime(selectedTransaction.transaction_date) }}</span>
            </div>
            <div class="grid grid-cols-5 items-center">
                <span class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.amount')}}</span>
                <span class="text-black col-span-3 dark:text-white py-2">$ {{ formatAmount(selectedTransaction.transaction_amount) }}</span>
            </div>
            <div class="grid grid-cols-5 items-center">
                <span class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.transaction_status')}}</span>
                <span class="text-black col-span-3 dark:text-white py-2">{{ selectedTransaction.transaction_status }}</span>
            </div>
            <div v-if="selectedTransaction.transaction_type === 'Deposit' || selectedTransaction.transaction_type === 'Withdrawal' || selectedTransaction.transaction_type === 'InternalTransfer'" class="grid grid-cols-5 items-center">
                <span class="col-span-2 text-sm font-semibold dark:text-gray-400">Remarks</span>
                <span class="text-black col-span-3 dark:text-white py-2">{{ selectedTransaction.transaction_remark ?? '-' }}</span>
            </div>
        </div>
    </Modal>
</template>
