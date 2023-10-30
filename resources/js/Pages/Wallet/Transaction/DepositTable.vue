<script setup>
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import DepositTableBody from "@/Pages/Wallet/Transaction/DepositTableBody.vue";
import {ref, watch, watchEffect} from "vue";
import { usePage } from '@inertiajs/vue3';
import debounce from "lodash/debounce.js";
import {ArrowLeftIcon, ArrowRightIcon} from "@heroicons/vue/outline";

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
const deposits = ref({data: []});
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
        let url = `/wallet/getTransaction/Deposit?page=${page}`;

        if (search) {
            url += `&search=${search}`;
        }

        if (date) {
            url += `&date=${date}`;
        }

        const response = await axios.get(url);
        deposits.value = response.data.Deposit;
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

watchEffect(() => {
    if (usePage().props.title !== null) {
        getResults();
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
                <th scope="col" class="pl-5 py-3">
                    Asset
                </th>
                <th scope="col" class="py-3">
                    Transaction ID
                </th>
                <th scope="col" class="py-3">
                    Date
                </th>
                <th scope="col" class="py-3">
                    Amount
                </th>
                <th scope="col" class="py-3 text-center">
                    Status
                </th>
            </tr>
            </thead>
            <tbody>
            <DepositTableBody
                :deposits="deposits"
            />
            </tbody>
        </table>
        <div class="flex justify-center mt-4" v-if="!depositLoading">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="deposits"
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
