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

const props = defineProps({
  coin_payment: Object,
});

const coin_payment = ref({ data: [] });
const search = ref('');
const date = ref('');
const isLoading = ref(false);
const refresh = ref(false);
const currentPage = ref(1);

const formatter = ref({
  date: 'YYYY-MM-DD',
  month: 'MM'
});

function refreshTable() {
  isLoading.value = true;
  getResults(refresh.value, search.value, date.value);
}

const { formatDateTime, formatAmount } = transactionFormat();

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
    coin_payment.value = response.data;
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
</script>

<template>
  <div class="p-5 rounded-[20px] bg-gray-700">
    <div class="flex gap-2 items-center ">
      <div class="w-1/3">
        <InputIconWrapper>
          <template #icon>
            <SearchIcon aria-hidden="true" class="w-5 h-5" />
          </template>
          <Input withIcon id="search" type="text" 
            class="w-full block dark:border-transparent"
            :placeholder="$t('public.report.search_placeholder')" 
            v-model="search" 
          />
        </InputIconWrapper>
      </div>
      <div class="w-1/2">
        <vue-tailwind-datepicker 
          :placeholder="$t('public.report.date_picker_placeholder')" 
          :formatter="formatter"
          separator=" - " as-single use-range 
          :shortcuts="false" 
          v-model="date"
          input-classes="py-2.5 border-gray-400 w-full rounded-lg text-sm placeholder:text-base dark:placeholder:text-gray-400 focus:border-gray-400 focus:border-pink-700 focus:ring focus:ring-pink-500 focus:ring-offset-0 focus:ring-offset-white dark:border-gray-600 dark:bg-gray-600 dark:text-white" />
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
      <Loading />
    </div>
    <table v-else class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
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
            {{ $t('public.wallet.amount') }}
          </th>
          <th scope="col" class="py-3">
            {{ $t('public.wallet.unit') }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="coin_payment.data && !coin_payment.data.length">
          <th colspan="5" class="py-4 text-lg text-center">
            {{ $t('public.wallet.no_history') }}
          </th>
        </tr>
        <tr v-for="coin_payment in coin_payment.data"
          class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600 hover:cursor-pointer dark:hover:bg-gray-600">
          <td class="py-3">
            {{ formatDateTime(coin_payment.created_at) }}
          </td>
          <td class="py-3">
            {{ coin_payment.type }}
          </td>
          <td class="py-3">
            {{ coin_payment.transaction_id }}
          </td>
          <td class="py-3">
            $ {{ formatAmount(coin_payment.amount) }}
          </td>
          <td class="py-3">
            {{ formatAmount(coin_payment.unit) }}
          </td>
        </tr>
      </tbody>
    </table>

    <div class="flex justify-center mt-4" v-if="!isLoading">
      <TailwindPagination 
        :item-classes=paginationClass 
        :active-classes=paginationActiveClass 
        :data="coin_payment"
        :limit=2 
        @pagination-change-page="handlePageChange">

        <template #prev-nav>
          <span class="flex gap-2">
            <ArrowLeftIcon class="w-5 h-5" /> {{ $t('public.previous') }}
          </span>
        </template>
        <template #next-nav>
          <span class="flex gap-2">{{ $t('public.next') }}
            <ArrowRightIcon class="w-5 h-5" />
          </span>
        </template>
      </TailwindPagination>
    </div>
  </div>
</template>