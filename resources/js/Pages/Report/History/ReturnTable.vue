<script setup>
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import {computed, ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import {ArrowLeftIcon, ArrowRightIcon} from "@heroicons/vue/outline";
import {InternalUSDWalletIcon} from "@/Components/Icons/outline.jsx";
import {transactionFormat} from "@/Composables/index.js";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    search: String,
    type: String,
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
const refreshReturns = ref(props.refresh);
const returnsLoading = ref(props.isLoading);
const emit = defineEmits(['update:loading', 'update:refresh']);
const { formatDateTime, formatAmount, formatCategory } = transactionFormat();

watch(
    [() => props.search, () => props.type, () => props.date],
    debounce(([searchValue, typeValue, dateValue]) => {
        getResults(1, searchValue, typeValue, dateValue);
    }, 300)
);

const getResults = async (page = 1, search = '',  type= '', date = '') => {
    returnsLoading.value = true
    try {
        let url = `/report/getReturnRecord?page=${page}`;

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

        returns.value = response.data;

    } catch (error) {
        console.error(error);
    } finally {
        returnsLoading.value = false
        emit('update:loading', false);
    }
}

getResults()

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;

        getResults(currentPage.value, props.search, props.type, props.date);
    }
};

watch(() => props.refresh, (newVal) => {
    refreshReturns.value = newVal;
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

const currentLocale = ref(usePage().props.locale);

</script>

<template>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <div v-if="returnsLoading" class="w-full flex justify-center my-8">
            <Loading />
        </div>
        <table v-else class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
            <thead class="text-xs font-medium text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-gray-400 border-b dark:border-gray-600">
            <tr>
                <th scope="col" class="py-3 w-1/4">
                    {{$t('public.report.date')}}
                </th>
                <th scope="col" class="py-3 w-1/4 text-center">
                    {{$t('public.report.plan')}}
                </th>
                <th scope="col" class="py-3 w-1/4 text-center">
                    {{$t('public.report.category')}}
                </th>
                <th scope="col" class="py-3 w-1/4 text-center">
                    {{$t('public.report.amount')}}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="earn in returns.data"
                class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600 dark:hover:bg-gray-600"
            >
                <td class="py-3">
                    {{ formatDateTime(earn.roi_release_date) }}
                </td>
                <td class="py-3 text-center">
                    {{ earn.subscription_plan.investment_plan.name[currentLocale] }} - <span class="text-gray-600 dark:text-gray-400">{{ earn.subscription_plan.subscription_id }}</span>
                </td>
                <td class="py-3 text-center">
                    {{ formatCategory(earn.type) }}
                </td>
                <td class="py-3 text-center">
                    $ {{ formatAmount(earn.after_amount) }}
                </td>
                <td class="py-3 text-center">
                    <span v-if="earn.status === 'Success'" class="flex w-2 h-2 bg-green-500 dark:bg-success-500 mx-auto rounded-full"></span>
                    <span v-else-if="earn.status === 'Pending'" class="flex w-2 h-2 bg-red-500 dark:bg-warning-500 mx-auto rounded-full"></span>
                    <span v-else-if="earn.status === 'Processing'" class="flex w-2 h-2 bg-red-500 dark:bg-[#007AFF] mx-auto rounded-full"></span>
                    <span v-else-if="earn.status === 'Rejected'" class="flex w-2 h-2 bg-red-500 dark:bg-error-500 mx-auto rounded-full"></span>
                </td>
            </tr>
            </tbody>
        </table>
        <div v-if="returns.data.length === 0 && !returnsLoading" class="flex flex-col dark:text-gray-400 mt-3 items-center">
            <img src="/assets/no_data.png" class="w-60" alt="">
            {{$t('public.no_data')}}
        </div>
        <div class="flex justify-center mt-4 " v-if="!returnsLoading">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="returns"
                :limit=2
                @pagination-change-page="handlePageChange"
            >
            <template #prev-nav>
                <span class="flex gap-2"><ArrowLeftIcon class="w-5 h-5" /> {{$t('public.previous')}}</span>
            </template>
            <template #next-nav>
                <span class="flex gap-2">{{$t('public.next')}} <ArrowRightIcon class="w-5 h-5" /></span>
            </template>
            </TailwindPagination>
        </div>
    </div>

</template>
