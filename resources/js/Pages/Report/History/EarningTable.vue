<script setup>
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import {ArrowLeftIcon, ArrowRightIcon} from "@heroicons/vue/outline";
import {transactionFormat} from "@/Composables/index.js";
import {InternalUSDWalletIcon, InternalMUSDWalletIcon} from "@/Components/Icons/outline.jsx";

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
const earnings = ref({data: []});
const currentPage = ref(1);
const refreshEarning = ref(props.refresh);
const depositLoading = ref(props.isLoading);
const emit = defineEmits(['update:loading', 'update:refresh', 'update:export']);
const { formatDateTime, formatAmount, formatType } = transactionFormat();

watch(
    [() => props.search, () => props.type, () => props.date],
    debounce(([searchValue, typeValue, dateValue]) => {
        getResults(1, searchValue, typeValue, dateValue);
    }, 300)
);

const getResults = async (page = 1, search = '', type= '', date = '') => {
    depositLoading.value = true
    try {
        let url = `/report/getEarningRecord?page=${page}`;

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
        earnings.value = response.data;
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

        getResults(currentPage.value, props.search, props.type, props.date);
    }
};

watch(() => props.refresh, (newVal) => {
    refreshEarning.value = newVal;
    if (newVal) {
        // Call the getResults function when refresh is true
        getResults();
        emit('update:refresh', false);
    }
});

watch(() => props.exportStatus, (newVal) => {
    refreshEarning.value = newVal;
    if (newVal) {
        let url = `/report/getEarningRecord?exportStatus=yes`;

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
    'bg-transparent border-0 dark:text-gray-400 dark:enabled:hover:bg-gray-600'
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
        <div v-else class="overflow-x-auto">
            <table class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
                <thead class="text-xs font-medium text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-gray-400 border-b dark:border-gray-600">
                <tr>
                    <th scope="col" class="py-3">
                        {{$t('public.report.date')}}
                    </th>
                    <th scope="col" class="py-3">
                        {{$t('public.report.downline_name')}}
                    </th>
                    <th scope="col" class="py-3">
                        {{$t('public.report.category')}}
                    </th>
                    <th scope="col" class="py-3">
                        {{$t('public.wallet.wallet')}}
                    </th>
                    <th scope="col" class="py-3">
                        {{$t('public.report.amount')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="earning in earnings.data"
                    class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600 dark:hover:bg-gray-600"
                >
                    <td class="py-2">
                        {{ formatDateTime(earning.created_at) }}
                    </td>
                    <td class="py-2">
                        <div class="inline-flex items-center gap-2">
                            <img :src="earning.downline.profile_photo_url ? earning.downline.profile_photo_url : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-8 h-8 rounded-full" alt="">
                            <div class="grid">
                                <span>{{ earning.downline.name }}</span>
                                <span class="dark:text-gray-400">{{ earning.downline.email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-2">
                        {{ formatType(earning.type) }}
                    </td>
                    <td class="py-2">
                        <div class="inline-flex items-center gap-2">
                            <div v-if="earning.wallet.type === 'internal_wallet'" class="bg-gradient-to-b from-pink-400 to-pink-500 dark:shadow-pink-500 rounded-full w-4 h-4 shrink-0 grow-0">
                                <InternalUSDWalletIcon class="mt-0.5 ml-0.5"/>
                            </div>
                            <div v-else-if="earning.wallet.type === 'musd_wallet'" class="bg-gradient-to-t from-warning-300 to-warning-600 dark:shadow-warning-500 rounded-full w-4 h-4 shrink-0 grow-0">
                                <InternalMUSDWalletIcon class="mt-0.5 ml-0.5"/>
                            </div>
                            {{ earning.wallet.name }}
                        </div>
                    </td>
                    <td class="py-2">
                        $&nbsp;{{ formatAmount(earning.after_amount) }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-if="earnings.data.length === 0 && !depositLoading" class="flex flex-col dark:text-gray-400 mt-3 items-center">
            <img src="/assets/no_data.png" class="w-60" alt="">
            {{$t('public.no_data')}}
        </div>

        <div class="flex justify-center mt-4" v-if="!depositLoading">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="earnings"
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
