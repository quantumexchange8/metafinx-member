<script setup>
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import CoinPaymentTableBody from "@/Pages/Wallet/Transaction/CoinPaymentTableBody.vue";
import {ArrowLeftIcon, ArrowRightIcon} from "@heroicons/vue/outline";

const props = defineProps({
    search: String,
    date: String,
    refresh: Boolean,
    isLoading: Boolean,
    conversion_rate: Object,
})
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const coin_payment = ref({data: []});
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
        let url = `/wallet/getTransaction/CoinPayment?page=${page}`;

        if (search) {
            url += `&search=${search}`;
        }

        if (date) {
            url += `&date=${date}`;
        }

        const response = await axios.get(url);
        coin_payment.value = response.data.CoinPayment;
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
                <th scope="col" class="pl-5 py-3">
                    {{$t('public.wallet.from')}}
                </th>
                <th scope="col" class="py-3">
                    {{$t('public.wallet.transaction_id')}}
                </th>
                <th scope="col" class="py-3">
                    {{$t('public.wallet.date')}}
                </th>
                <th scope="col" class="py-3">
                    {{$t('public.wallet.paid')}}
                </th>
                <th scope="col" class="py-3">
                    {{$t('public.wallet.unit')}}
                </th>
                <th scope="col" class="py-3 text-center">
                    {{$t('public.wallet.status')}}
                </th>
            </tr>
            </thead>
            <tbody>
            <CoinPaymentTableBody
                :coin_payment="coin_payment"
                :conversion_rate="conversion_rate"
            />
            </tbody>
        </table>
        <div class="flex justify-center mt-4" v-if="!depositLoading">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="coin_payment"
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
