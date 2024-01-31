<script setup>
import Input from "@/Components/Input.vue";
import { ref, watch, defineProps } from 'vue';
import { SearchIcon, RefreshIcon, ArrowLeftIcon, ArrowRightIcon } from '@heroicons/vue/outline';
import Button from '@/Components/Button.vue';
import { TailwindPagination } from "laravel-vue-pagination";
import Loading from "@/Components/Loading.vue";
import debounce from "lodash/debounce.js";
import { transactionFormat } from "@/Composables/index.js";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Modal from "@/Components/Modal.vue";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
  coin_payment: Object,
  gasFee: Object,
  stackingFee: Object,
  setting_coin: Object,
});

const transactions = ref({ data: [] });
const search = ref('');
const date = ref('');
const isLoading = ref(false);
const refresh = ref(false);
const currentPage = ref(1);
const transactionModal = ref(false);
const selectedTransaction = ref();

const formatter = ref({
  date: 'YYYY-MM-DD',
  month: 'MM'
});

const openTransactionModal = (transaction) => {
    selectedTransaction.value = transaction;
    transactionModal.value = true;
};

const closeModal = () => {
  transactionModal.value = false
}

function refreshTable() {
  isLoading.value = true;
  getResults(refresh.value, search.value, date.value);
}

const { formatDateTime, formatAmount, formatType } = transactionFormat();

watch(
  [() => search.value, () => date.value, () => refresh.value],
  debounce(([searchValue, dateValue]) => {
    getResults(1, searchValue, dateValue);
  }, 300)
);

const getResults = async (page = 1, search = '', date = '') => {
  isLoading.value = true;
  try {
    let url = `/wallet/getCoinPaymentHistory?page=${page}`;

    if (search) {
      url += `&search=${search}`;
    }

    if (date) {
      url += `&date=${date}`;
    }

    const response = await axios.get(url);
    transactions.value = response.data;
  } catch (error) {
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

getResults();

const handlePageChange = (newPage) => {
  if (newPage >= 1) {
    currentPage.value = newPage;
    getResults(currentPage.value, search.value, date.value);
  }
};

const paginationClass = [
  'bg-transparent border-0 dark:text-gray-400 dark:enabled:hover:text-white'
];

const paginationActiveClass = [
  'border dark:border-gray-600 dark:bg-gray-600 rounded-full text-[#FF9E23] dark:text-white'
];

const page = usePage();
const currentLocale = ref(page.props.locale);
const walletName = ref(page.props.auth.user.wallets[1]);
</script>

<template>
    <div class="p-5 rounded-[20px] bg-gray-700">
        <div class="flex gap-2 items-center ">
            <div class="w-full">
                <InputIconWrapper>
                    <template #icon>
                        <SearchIcon aria-hidden="true" class="w-5 h-5"/>
                    </template>
                    <Input withIcon id="search" type="text"
                           class="w-full block dark:border-transparent"
                           :placeholder="$t('public.report.search_placeholder')"
                           v-model="search"
                    />
                </InputIconWrapper>
            </div>
            <div class="w-full">
                <vue-tailwind-datepicker
                    :placeholder="$t('public.report.date_picker_placeholder')"
                    :formatter="formatter"
                    separator=" - " as-single use-range
                    :shortcuts="false"
                    v-model="date"
                    input-classes="py-2.5 border-gray-400 w-full rounded-lg text-sm placeholder:text-base dark:placeholder:text-gray-400 focus:border-gray-400 focus:border-pink-700 focus:ring focus:ring-pink-500 focus:ring-offset-0 focus:ring-offset-white dark:border-gray-600 dark:bg-gray-600 dark:text-white"/>
            </div>
            <div class="ml-auto">
                <RefreshIcon
                    :class="{ 'animate-spin': isLoading }"
                    class="flex-shrink-0 w-5 h-5 cursor-pointer dark:text-white"
                    aria-hidden="true"
                    @click="refreshTable"
                />
            </div>
        </div>

        <div v-if="isLoading" class="w-full flex justify-center my-8">
            <Loading/>
        </div>
        <div v-else class="overflow-x-auto">
            <table class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
                <thead
                    class="text-xs font-medium text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-gray-400 border-b dark:border-gray-600">
                <tr>
                    <th scope="col" class="py-3">
                        {{ $t('public.wallet.date') }}
                    </th>
                    <th scope="col" class="py-3">
                        {{ $t('public.wallet.transaction_type') }}
                    </th>
                    <th scope="col" class="py-3">
                        {{ $t('public.wallet.transaction_id') }}
                    </th>
                    <th scope="col" class="py-3">
                        {{ $t('public.wallet.price') }}
                    </th>
                    <th scope="col" class="py-3">
                        {{ $t('public.wallet.unit') }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="transactions.data && !transactions.data.length">
                    <th colspan="5" class="py-4 text-lg text-center">
                        {{ $t('public.wallet.no_history') }}
                    </th>
                </tr>
                <tr v-for="data in transactions.data"
                    class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600 dark:hover:bg-gray-600 hover:cursor-pointer"
                    @click="openTransactionModal(data)"
                >
                    <td class="py-3">
                        {{ formatDateTime(data.created_at,false) }}
                    </td>
                    <td class="py-3">
                        {{ data.transaction_type }}
                    </td>
                    <td class="py-3">
                        {{ data.transaction_number }}
                    </td>
                    <td class="py-3">
                        $&nbsp;{{ formatAmount(data.transaction_amount) }}
                    </td>
                    <td 
                        class="py-3"
                        :class="{
                                    'text-success-500': data.transaction_type === 'BuyCoin',
                                    'text-error-500': data.transaction_type === 'SwapCoin' || data.transaction_type === 'Staking',
                                }"
                    >
                        {{ formatAmount(data.unit) }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="flex justify-center mt-4" v-if="!isLoading">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="transactions"
                :limit=2
                @pagination-change-page="handlePageChange">

                <template #prev-nav>
                    <span class="flex gap-2"><ArrowLeftIcon class="w-5 h-5" /> <span class="hidden sm:flex">{{$t('public.previous')}}</span></span>
                </template>
                <template #next-nav>
                    <span class="flex gap-2"><span class="hidden sm:flex">{{$t('public.next')}}</span> <ArrowRightIcon class="w-5 h-5" /></span>
                </template>
            </TailwindPagination>
        </div>
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
                <span class="text-black col-span-3 dark:text-white py-2" v-else>{{ selectedTransaction.transaction_number }}</span>
            </div>
            <div class="grid grid-cols-5 items-center">
                <span class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.date_time')}}</span>
                <span class="text-black col-span-3 dark:text-white py-2">{{ formatDateTime(selectedTransaction.created_at) }}</span>
            </div>
            <div class="grid grid-cols-5 items-center">
                <span v-if="selectedTransaction.transaction_type === 'BuyCoin'" class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.from')}}</span>
                <span v-if="selectedTransaction.transaction_type === 'BuyCoin'" class="text-black col-span-3 dark:text-white py-2">{{ (selectedTransaction.from_wallet.name) }}</span>
                
                <span v-if="selectedTransaction.transaction_type === 'SwapCoin'" class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.to')}}</span>
                <span v-if="selectedTransaction.transaction_type === 'SwapCoin'" class="text-black col-span-3 dark:text-white py-2">{{ (selectedTransaction.to_wallet.name) }}</span>
                
                <span v-if="selectedTransaction.transaction_type === 'Staking'" class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.earn.plan')}}</span>
                <span v-if="selectedTransaction.transaction_type === 'Staking'" class="text-black col-span-3 dark:text-white py-2">{{ (selectedTransaction.coin_stacking.investment_plan.name[currentLocale]) }}</span>
            </div>
            <div class="grid grid-cols-5 items-center">
                <span class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.unit')}}</span>
                <span class="text-black col-span-3 dark:text-white py-2">{{ formatAmount(selectedTransaction.unit) }} {{setting_coin.name}}</span>
            </div>
            <div class="grid grid-cols-5 items-center">
                <span class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.linked_price')}}</span>
                <span class="text-black col-span-3 dark:text-white py-2">$&nbsp;{{ selectedTransaction.amount }}</span>

            </div>
            <div class="grid grid-cols-5 items-center">
                <span v-if="selectedTransaction.transaction_type === 'BuyCoin' || selectedTransaction.transaction_type === 'SwapCoin'" class="col-span-2 text-sm font-semibold dark:text-gray-400">{{ $t('public.gas_fee') }} ({{ gasFee.value }}%)</span>
                <span v-if="selectedTransaction.transaction_type === 'Staking'" class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.staking_fee')}} ({{ stackingFee.value }}%)</span>
                <span v-if="selectedTransaction.transaction_type === 'BuyCoin' || selectedTransaction.transaction_type === 'SwapCoin'" class="text-black col-span-3 dark:text-white py-2">$&nbsp;{{ selectedTransaction.transaction_charges }}</span>
                <span v-if="selectedTransaction.transaction_type === 'Staking'" class="text-black col-span-3 dark:text-white py-2">$&nbsp;{{ (selectedTransaction.transaction_charges) }} ({{ walletName.name }})</span>
            </div>
            <div class="grid grid-cols-5 items-center">
                <span v-if="selectedTransaction.transaction_type === 'BuyCoin'" class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.paid')}}</span>
                <span v-if="selectedTransaction.transaction_type === 'SwapCoin'" class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.received')}}</span>
                <span v-if="selectedTransaction.transaction_type === 'BuyCoin' || selectedTransaction.transaction_type === 'SwapCoin'" class="text-black col-span-3 dark:text-white py-2">$&nbsp;{{ selectedTransaction.transaction_amount }}</span>
            </div>
            <div class="grid grid-cols-5 items-center">
                <span class="col-span-2 text-sm font-semibold dark:text-gray-400">{{$t('public.wallet.transaction_status')}}</span>
                <span class="text-black col-span-3 dark:text-white py-2">{{ selectedTransaction.status }}</span>
            </div>
        </div>
    </Modal>

</template>
