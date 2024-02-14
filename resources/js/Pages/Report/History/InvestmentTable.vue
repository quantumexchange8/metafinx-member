<script setup>
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import {ArrowLeftIcon, ArrowRightIcon} from "@heroicons/vue/outline";
import {transactionFormat} from "@/Composables/index.js";

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
const investments = ref({data: []});
const currentPage = ref(1);
const refreshInvestment = ref(props.refresh);
const isLoading = ref(props.isLoading);
const emit = defineEmits(['update:loading', 'update:refresh', 'update:export']);
const { formatDateTime, formatAmount, formatType } = transactionFormat();

watch(
    [() => props.search, () => props.type, () => props.date],
    debounce(([searchValue, typeValue, dateValue]) => {
        getResults(1, searchValue, typeValue, dateValue);
    }, 300)
);

const getResults = async (page = 1, search = '', type= '', date = '') => {
    isLoading.value = true
    try {
        let url = `/report/getInvestmentRecord?page=${page}`;

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
        investments.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false
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
    refreshInvestment.value = newVal;
    if (newVal) {
        // Call the getResults function when refresh is true
        getResults();
        emit('update:refresh', false);
    }
});

watch(() => props.exportStatus, (newVal) => {
    refreshInvestment.value = newVal;
    if (newVal) {
        let url = `/report/getInvestmentRecord?exportStatus=yes`;

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
        <div v-if="isLoading" class="w-full flex justify-center my-8">
            <Loading />
        </div>
        <div v-else class="overflow-x-auto">
            <table class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
                <thead class="text-xs font-medium text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-gray-400 border-b dark:border-gray-600">
                <tr>
                    <th scope="col" class="py-3">
                        {{$t('public.report.transaction_date')}}
                    </th>
                    <th scope="col" class="py-3">
                        {{$t('public.report.valid_thru')}}
                    </th>
                    <th scope="col" class="py-3">
                        {{$t('public.report.id_number')}}
                    </th>
                    <th scope="col" class="py-3">
                        {{$t('public.report.plan')}}
                    </th>
                    <th scope="col" class="py-3">
                        {{$t('public.report.amount')}}
                    </th>
                    <th scope="col" class="py-3">
                        {{$t('public.report.status')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="investment in investments.data"
                    class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600  dark:hover:bg-gray-600"
                >
                    <td class="py-3">
                        {{ formatDateTime(investment.subscription_date) }}
                    </td>
                    <td class="py-3">
                        {{ investment.subscription_expired_date }}
                    </td>
                    <td class="py-3">
                        {{ investment.subscription_id }}
                    </td>
                    <td class="py-3">
                        {{ investment.plan_name }}
                    </td>
                    <td class="py-3">
                        <div v-if="investment.plan_type === 'standard'">
                            $ {{ formatAmount(investment.subscription_amount) }}
                        </div>
                        <div v-if="investment.plan_type === 'staking'">
                            {{ formatAmount(investment.subscription_unit, 4) }} MXT ($ {{ formatAmount(investment.subscription_amount) }})
                        </div>
                    </td>
                    <td class="py-3 uppercase">
                        <span class="uppercase dark:text-error-500 font-semibold" v-if="investment.subscription_status === 'Terminated'">{{ formatType(investment.subscription_status) }}</span>
                        <span class="uppercase dark:text-blue-500 font-semibold" v-if="investment.subscription_status === 'CoolingPeriod'">{{ formatType(investment.subscription_status) }}</span>
                        <span class="uppercase dark:text-warning-500 font-semibold" v-if="investment.subscription_status === 'OnGoingPeriod'">{{ formatType(investment.subscription_status) }}</span>
                        <span class="uppercase dark:text-success-500 font-semibold" v-if="investment.subscription_status === 'MaturityPeriod'">{{ formatType(investment.subscription_status) }}</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-if="investments.data.length === 0 && !isLoading" class="flex flex-col dark:text-gray-400 mt-3 items-center">
            <img src="/assets/no_data.png" class="w-60" alt="">
            {{$t('public.no_data')}}
        </div>

        <div class="flex justify-center mt-4" v-if="!isLoading">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="investments"
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
