<script setup>
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import {ArrowLeftIcon, ArrowRightIcon} from "@heroicons/vue/outline";
import {transactionFormat} from "@/Composables/index.js";
import {usePage} from "@inertiajs/vue3";
import {InternalMUSDWalletIcon, InternalUSDWalletIcon} from "@/Components/Icons/outline.jsx";
import {MXTIcon} from "@/Components/Icons/brands.jsx";

const props = defineProps({
    search: String,
    type: String,
    date: String,
    refresh: Boolean,
    isLoading: Boolean,
    exportStatus: Boolean,
})
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const returns = ref({data: []});
const currentPage = ref(1);
const refreshReturns = ref(props.refresh);
const returnsLoading = ref(props.isLoading);
const emit = defineEmits(['update:loading', 'update:refresh', 'update:export']);
const { formatDateTime, formatAmount, formatType } = transactionFormat();

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

watch(() => props.exportStatus, (newVal) => {
    refreshReturns.value = newVal;
    if (newVal) {
        let url = `/report/getReturnRecord?exportStatus=yes`;

        if (props.date) {
            url += `&date=${props.date}`;
        }

        if (props.type) {
            url += `&type=${props.type}`;
        }

        if (props.search) {
            url += `&search=${props.search}`;
        }

        window.location.href = url;
        emit('update:export', false);
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
        <div v-else class="overflow-x-auto">
            <table class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
                <thead class="text-xs font-medium text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-gray-400 border-b dark:border-gray-600">
                <tr>
                    <th scope="col" class="py-3 w-1/5">
                        {{$t('public.report.date')}}
                    </th>
                    <th scope="col" class="py-3 w-1/5">
                        {{$t('public.report.plan')}}
                    </th>
                    <th scope="col" class="py-3 w-1/5">
                        {{$t('public.report.category')}}
                    </th>
                    <th scope="col" class="py-3 w-1/5">
                        {{$t('public.wallet.wallet')}}
                    </th>
                    <th scope="col" class="py-3 w-1/5">
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
                    <td class="py-3">
                        {{ earn.subscription_plan.investment_plan.name[currentLocale] }} - <span class="text-gray-600 dark:text-gray-400">{{ earn.subscription_plan.subscription_id }}</span>
                    </td>
                    <td class="py-3">
                        {{ formatType(earn.type) }}
                    </td>
                    <td class="py-3 flex items-center gap-2">
                        <div v-if="earn.category === 'standard' && earn.wallet.type === 'internal_wallet'" class="flex items-center justify-center bg-gradient-to-b from-pink-400 to-pink-500 dark:shadow-pink-500 rounded-full w-5 h-5 shrink-0 grow-0">
                            <InternalUSDWalletIcon class="w-4 h-4" />
                        </div>
                        <div v-if="earn.category === 'standard' && earn.wallet.type === 'musd_wallet'" class="flex items-center justify-center bg-gradient-to-b from-warning-300 to-warning-500 dark:shadow-pink-500 rounded-full w-5 h-5 shrink-0 grow-0">
                            <InternalMUSDWalletIcon class="w-4 h-4" />
                        </div>
                        <div v-if="earn.category === 'staking'" class="rounded-full w-5 h-5 flex justify-center items-center grow-0 shrink-0" style="background: linear-gradient(146deg, #E85B7A 14.85%, #DC5277 16.26%, #D14F79 18.38%, #C84C7B 21.92%, #D24C7B 44.54%, #E34D7A 54.43%, #EF5572 66.45%, #F05B6C 85.53%)">
                            <MXTIcon class="w-4 h-4 text-white" />
                        </div>
                        <div v-if="earn.category === 'standard'">
                            {{ earn.wallet.name }}
                        </div>
                        <div v-if="earn.category === 'staking'">
                            {{ earn.coin.setting_coin.name }}
                        </div>
                    </td>
                    <td class="py-3">
                        $&nbsp;{{ formatAmount(earn.after_amount) }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-if="returns.data.length === 0 && !returnsLoading" class="flex flex-col dark:text-gray-400 mt-3 items-center">
            <img src="/assets/no_data.png" class="w-60" alt="">
            {{$t('public.no_data')}}
        </div>
        <div class="flex justify-center mt-4" v-if="!returnsLoading">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="returns"
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
    </div>

</template>
