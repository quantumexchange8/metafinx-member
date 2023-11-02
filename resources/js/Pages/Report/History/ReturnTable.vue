<script setup>
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import {ArrowLeftIcon, ArrowRightIcon} from "@heroicons/vue/outline";
import {InternalWalletIcon} from "@/Components/Icons/outline.jsx";

const props = defineProps({
    search: String,
    date: String,
    refresh: Boolean,
    isLoading: Boolean,
})
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const returns = ref({data: []});
const currentPage = ref(1);
const refreshDeposit = ref(props.refresh);
const depositLoading = ref(props.isLoading);
const emit = defineEmits(['update:loading', 'update:refresh']);

watch(
    [() => props.search, () => props.date],
    debounce(([searchValue, dateValue]) => {
        getResults(1, searchValue, dateValue);
    }, 300)
);

const getResults = async (page = 1, search = '', date = '') => {
    depositLoading.value = true
    try {
        // let url = `/wallet/getTransaction/Deposit?page=${page}`;
        //
        // if (search) {
        //     url += `&search=${search}`;
        // }
        //
        // if (date) {
        //     url += `&date=${date}`;
        // }
        //
        // const response = await axios.get(url);
        returns.value = {};

    } catch (error) {
        console.error(error);
    } finally {
        depositLoading.value = false
        emit('update:loading', false);
    }
}

getResults()

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;

        getResults(currentPage.value, props.search, props.date);
    }
};

watch(() => props.refresh, (newVal) => {
    refreshDeposit.value = newVal;
    if (newVal) {
        // Call the getResults function when refresh is true
        getResults();
        emit('update:refresh', false);
    }
});

const paginationClass = [
    'bg-transparent border-0 dark:text-gray-400 dark:enabled:hover:text-white'
];

const paginationActiveClass = [
    'border dark:border-gray-600 dark:bg-gray-600 rounded-full text-[#FF9E23] dark:text-white'
];
</script>

<template>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <div v-if="depositLoading" class="w-full flex justify-center my-8">
            <Loading />
        </div>
        <table v-else class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
            <thead class="text-xs font-medium text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-gray-400 border-b dark:border-gray-600">
            <tr>
                <th scope="col" class="py-3 w-1/3">
                    Date
                </th>
                <th scope="col" class="py-3 w-1/3 text-center">
                    Category
                </th>
                <th scope="col" class="py-3 w-1/3 text-center">
                    Amount
                </th>
            </tr>
            </thead>
            <tbody>
<!--            <tr-->
<!--                v-for="deposit in returns.data"-->
<!--                class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600 hover:cursor-pointer dark:hover:bg-gray-600"-->
<!--                @click="openTransactionModal(deposit)"-->
<!--            >-->
<!--                <td class="pl-5 py-3 inline-flex items-center gap-2">-->
<!--                    <div class="bg-gradient-to-t from-pink-300 to-pink-600 dark:shadow-pink-500 rounded-full w-4 h-4 shrink-0 grow-0">-->
<!--                        <InternalWalletIcon class="mt-0.5 ml-0.5"/>-->
<!--                    </div>-->
<!--                    {{ deposit.wallet.name }}-->
<!--                </td>-->
<!--                <td class="py-3">-->
<!--                    {{ deposit.transaction_id }}-->
<!--                </td>-->
<!--                <td class="py-3">-->
<!--                    {{ formatDateTime(deposit.created_at) }}-->
<!--                </td>-->
<!--                <td class="py-3">-->
<!--                    {{ deposit.amount }}-->
<!--                </td>-->
<!--                <td class="py-3 text-center">-->
<!--                    <span v-if="deposit.status === 'Success'" class="flex w-2 h-2 bg-green-500 dark:bg-success-500 mx-auto rounded-full"></span>-->
<!--                    <span v-else-if="deposit.status === 'Pending'" class="flex w-2 h-2 bg-red-500 dark:bg-warning-500 mx-auto rounded-full"></span>-->
<!--                    <span v-else-if="deposit.status === 'Processing'" class="flex w-2 h-2 bg-red-500 dark:bg-[#007AFF] mx-auto rounded-full"></span>-->
<!--                    <span v-else-if="deposit.status === 'Rejected'" class="flex w-2 h-2 bg-red-500 dark:bg-error-500 mx-auto rounded-full"></span>-->
<!--                </td>-->
<!--            </tr>-->
            </tbody>
        </table>
        <div class="flex flex-col dark:text-gray-400 mt-3 items-center">
            <img src="/assets/no_data.png" class="w-60" alt="">
            No data to show
        </div>
        <div class="flex justify-center mt-4 " v-if="!depositLoading">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="returns"
                :limit=2
                @pagination-change-page="handlePageChange"
            >
            <template #prev-nav>
                <span class="flex gap-2"><ArrowLeftIcon class="w-5 h-5" /> Previous</span>
            </template>
            <template #next-nav>
                <span class="flex gap-2">Next <ArrowRightIcon class="w-5 h-5" /></span>
            </template>
            </TailwindPagination>
        </div>
    </div>

</template>
