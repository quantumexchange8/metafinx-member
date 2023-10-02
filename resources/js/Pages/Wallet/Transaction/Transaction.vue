<script setup>
import Input from "@/Components/Input.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import {SearchIcon, RefreshIcon} from "@heroicons/vue/outline";
import {ref, watch} from "vue";
import {CloudDownloadIcon, InternalWalletIcon} from "@/Components/Icons/outline.jsx";
import { TailwindPagination } from 'laravel-vue-pagination';
import Loading from "@/Components/Loading.vue";
import debounce from "lodash/debounce.js";
import Button from "@/Components/Button.vue";
import DepositTableBody from "@/Pages/Wallet/Transaction/DepositTableBody.vue";

const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const search = ref('');
const date = ref('');
const deposits = ref({data: []});
const isLoading = ref(false);
const currentPage = ref(1);

watch(
    [search, date],
    debounce(function ([searchValue, dateValue]) {
        getResults(1, searchValue, dateValue);
    }, 300)
);

const getResults = async (page = 1, search = '', date = '') => {
    isLoading.value = true;
    try {
        let url = `/wallet/getTransaction?page=${page}`;

        if (search) {
            url += `&search=${search}`;
        }

        if (date) {
            url += `&date=${date}`;
        }

        const response = await axios.get(url);
        deposits.value = response.data.deposits;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}

getResults()

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;

        getResults(currentPage.value, search.value, date.value);
    }
};

const exportDeposit = () => {

    let url = `/wallet/getTransaction?export=yes`;

    if (date) {
        url += `&date=${date.value}`;
    }

    if (search) {
        url += `&search=${search.value}`;
    }

    window.location.href = url;
}

function refreshTable() {
    isLoading.value = !isLoading.value;
    getResults();
}

const paginationClass = [
    'bg-transparent border-0 dark:text-gray-400'
];

const paginationActiveClass = [
    'border dark:border-gray-600 dark:bg-gray-600 rounded-full text-[#FF9E23] dark:text-white'
];
</script>

<template>
    <div class="flex justify-between">
        <h4 class="font-semibold dark:text-white">All Transactions</h4>
        <RefreshIcon
            :class="{ 'animate-spin': isLoading }"
            class="flex-shrink-0 w-5 h-5 cursor-pointer dark:text-white"
            aria-hidden="true"
            @click="refreshTable"
        />
    </div>

    <div class="mt-5 flex justify-between">
        <div class="grid grid-cols-2 md:grid-cols-3 w-full md:w-2/3 gap-3">
            <InputIconWrapper class="md:col-span-2">
                <template #icon>
                    <SearchIcon aria-hidden="true" class="w-5 h-5" />
                </template>
                <Input withIcon id="search" type="text" class="block w-full" placeholder="Search" v-model="search" />
            </InputIconWrapper>
            <vue-tailwind-datepicker
                placeholder="Select dates"
                :formatter="formatter"
                separator=" - "
                v-model="date"
                input-classes="py-2.5 border-gray-400 w-full rounded-lg text-sm placeholder:text-base dark:placeholder:text-gray-400 focus:border-gray-400 focus:border-pink-700 focus:ring focus:ring-pink-500 focus:ring-offset-0 focus:ring-offset-white dark:border-gray-600 dark:bg-gray-600 dark:text-white"
            />
        </div>
        <Button
            type="button"
            class="justify-center gap-2 border border-gray-600 text-white text-sm dark:hover:bg-gray-600"
            variant="transparent"
            @click="exportDeposit"
        >
            <CloudDownloadIcon aria-hidden="true" class="w-5 h-5" />
            <span>Export as Excel</span>
        </Button>
    </div>
    <div class="flex gap-5 mt-5">
        <span class="flex items-center text-xs font-normal text-gray-900 dark:text-white"><span class="flex w-2 h-2 bg-green-500 dark:bg-error-500 rounded-full mr-2 flex-shrink-0"></span>Rejected</span>
        <span class="flex items-center text-xs font-normal text-gray-900 dark:text-white"><span class="flex w-2 h-2 bg-red-500 dark:bg-warning-500 rounded-full mr-2 flex-shrink-0"></span>Pending</span>
        <span class="flex items-center text-xs font-normal text-gray-900 dark:text-white"><span class="flex w-2 h-2 bg-red-500 dark:bg-[#007AFF] rounded-full mr-2 flex-shrink-0"></span>Processing</span>
        <span class="flex items-center text-xs font-normal text-gray-900 dark:text-white"><span class="flex w-2 h-2 bg-red-500 dark:bg-success-500 rounded-full mr-2 flex-shrink-0"></span>Success</span>
    </div>

    <div class="relative overflow-x-auto sm:rounded-lg">
        <div v-if="isLoading" class="w-full flex justify-center my-8">
            <Loading />
        </div>
        <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
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
                <th scope="col" class="py-3">
                    Price
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
        <div class="flex justify-center mt-4" v-if="!isLoading">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="deposits"
                :limit=2
                @pagination-change-page="handlePageChange"
            />
        </div>
    </div>

</template>
