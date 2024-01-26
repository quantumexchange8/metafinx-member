<script setup>
import Input from "@/Components/Input.vue";
import { defineProps, ref, watch, watchEffect } from 'vue';
import axios from 'axios';
import { SearchIcon, RefreshIcon, ArrowLeftIcon, ArrowRightIcon } from '@heroicons/vue/outline';
import { TailwindPagination } from "laravel-vue-pagination";
import Loading from "@/Components/Loading.vue";
import debounce from "lodash/debounce.js";
import { transactionFormat } from "@/Composables/index.js";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import BaseListbox from "@/Components/BaseListbox.vue";
import Button from "@/Components/Button.vue";
import { Rank1Icon, Rank2Icon, Rank3Icon, Rank4Icon } from "@/Components/Icons/outline.jsx";

const props = defineProps({
  referredCounts: Number,
  validAffiliateDeposit: String,
  totalAffiliate: Number,
  totalGeneration: Number,
  exportStatus: Boolean,
})

const exportStatus = ref(false);
const referralTableData = ref([]);
const search = ref('');
const date = ref('');
const rank = ref('');
const level = ref('');
const isLoading = ref(false);
const refresh = ref(false);
const currentPage = ref(1);
const levelFilter = ref([{ value: '', label: 'All' }]);

const formatter = ref({
  date: 'YYYY-MM-DD',
  month: 'MM'
});

function refreshTable() {
  isLoading.value = true;
  getResults(search.value, date.value, rank.value, level.value, refresh.value);
}

const { formatDateTime, formatAmount } = transactionFormat();

watch(
  [() => search.value, () => date.value, () => rank.value, () => level.value, () => refresh.value],
  debounce(([searchValue, dateValue, rankValue, levelValue]) => {
    getResults(1, searchValue, dateValue, rankValue, levelValue);
  }, 300)
);

const getResults = async (page = 1, search = '', date = '', rank = '', level = '') => {
  isLoading.value = true;
  try {
    let url = `/affiliate/getReferralTableData?page=${page}`;

    if (search) {
      url += `&search=${search}`;
    }

    if (date) {
      url += `&date=${date}`;
    }

    if (rank) {
      url += `&rank=${rank}`;
    }

    if (level) {
      url += `&level=${level}`;
    }

    const response = await axios.get(url);
    referralTableData.value = response.data;
  } catch (error) {
    console.error(error);
    console.error("Error fetching data:", error.message);
  } finally {
    isLoading.value = false;
  }
};

getResults();

const handlePageChange = (newPage) => {
  if (newPage >= 1) {
    currentPage.value = newPage;
    getResults(currentPage.value, search.value, date.value, rank.value, level.value);
  }
};

const paginationClass = [
  'bg-transparent border-0 dark:text-gray-400 dark:enabled:hover:text-white'
];

const paginationActiveClass = [
  'border dark:border-gray-600 dark:bg-gray-600 rounded-full text-[#FF9E23] dark:text-white'
];

const rankFilter = [
  { value: '', label: "All" },
  { value: '1', label: "Member" },
  { value: '2', label: "Rank 1" },
  { value: '3', label: "Rank 2" },
  { value: '4', label: "Rank 3" },
  { value: '5', label: "Rank 4" },
];

for (let i = 1; i <= props.totalGeneration; i++) {
  levelFilter.value.push({ value: i.toString(), label: i.toString() });
}

const clearFilter = () => {
  search.value = ''
  date.value = ''
  rank.value = ''
  level.value = ''
}
watchEffect(() => {
  if (props.exportStatus) {
    let url = `/affiliate/getReferralTableData?exportStatus=yes`;
    if (search.value) {
      url += `&search=${search.value}`;
    }

    if (date.value) {
      url += `&date=${date.value}`;
    }

    if (rank.value) {
      url += `&rank=${rank.value}`;
    }

    if (level.value) {
      url += `&level=${level.value}`;
    }

    window.location.href = url;
    exportStatus.value = false;
  }
});

</script>

<template>
  <div class="flex flex-nowrap md:grid md:grid-cols-4 gap-3 overflow-x-auto md:overflow-visible my-8">
    <div class="flex-1 rounded-[10px] border border-gray-200 shadow dark:border-transparent dark:bg-gray-700">
      <div class="px-5 py-2.5 flex flex-col justify-between h-full">
        <p class="flex-grow text-gray-400 text-xs md:text-sm w-32 md:w-full h-full">{{ $t('public.affiliate.total_VAD') }}</p>
        <p class="text-gray-800 dark:text-white text-xl font-semibold">$ {{ formatAmount(props.validAffiliateDeposit) }}
        </p>
      </div>
    </div>
    <div class="flex-1 rounded-[10px] border border-gray-200 shadow dark:border-transparent dark:bg-gray-700">
      <div class="px-5 py-2.5 flex flex-col justify-between h-full">
        <p class="flex-grow text-gray-400 text-xs md:text-sm w-32 md:w-full h-full">{{ $t('public.affiliate.total_direct_referrals') }}</p>
        <p class="text-gray-800 dark:text-white text-xl font-semibold">{{ props.referredCounts }}</p>
      </div>
    </div>
    <div class="flex-1 rounded-[10px] border border-gray-200 shadow dark:border-transparent dark:bg-gray-700">
      <div class="px-5 py-2.5 flex flex-col justify-between h-full">
        <p class="flex-grow text-gray-400 text-xs md:text-sm w-32 md:w-full h-full">{{ $t('public.affiliate.total_affiliates') }}</p>
        <p class="text-gray-800 dark:text-white text-xl font-semibold">{{ props.totalAffiliate }}</p>
      </div>
    </div>
    <div class="flex-1 rounded-[10px] border border-gray-200 shadow dark:border-transparent dark:bg-gray-700">
      <div class="px-5 py-2.5 flex flex-col justify-between h-full">
        <p class="flex-grow text-gray-400 text-xs md:text-sm w-32 md:w-full h-full">{{ $t('public.affiliate.total_generation') }}</p>
        <p class="text-gray-800 dark:text-white text-xl font-semibold">{{ props.totalGeneration }}</p>
      </div>
    </div>
  </div>

  <div class="p-3 rounded-[20px] bg-gray-700 mb-28 overflow-hidden md:overflow-visible">
    <div class="flex justify-between mb-5">
      <h4 class="font-semibold dark:text-white">{{ $t('public.affiliate.all_affiliate') }}</h4>
      <RefreshIcon 
        :class="{ 'animate-spin': isLoading }" 
        class="flex-shrink-0 w-5 h-5 cursor-pointer dark:text-white" 
        aria-hidden="true" 
        @click="refreshTable" 
      />
    </div>
    <div class="flex flex-wrap gap-2 items-center sm:flex-nowrap sm:items-start">
      <div class="w-full">
        <InputIconWrapper>
          <template #icon>
            <SearchIcon aria-hidden="true" class="w-5 h-5" />
          </template>
          <Input withIcon id="search" type="text" class="w-full block dark:border-transparent"
            :placeholder="$t('public.report.search_placeholder')" v-model="search" />
        </InputIconWrapper>
      </div>
      <div class="w-full">
        <vue-tailwind-datepicker 
          :placeholder="$t('public.report.date_picker_placeholder')" 
          :formatter="formatter"
          separator=" - "
          v-model="date"
          input-classes="py-2.5 border-gray-400 w-full rounded-lg text-sm placeholder:text-base dark:placeholder:text-gray-400 focus:border-gray-400 focus:border-pink-700 focus:ring focus:ring-pink-500 focus:ring-offset-0 focus:ring-offset-white dark:border-gray-600 dark:bg-gray-600 dark:text-white"
        />
      </div>
      <div class="w-full flex gap-2">
        <BaseListbox v-model="rank" :options="rankFilter" class="w-full" />

        <BaseListbox v-model="level" :options="levelFilter" class="w-full" />
      </div>
      <div class="w-full sm:w-auto">
        <Button type="button" variant="secondary" @click="clearFilter" class="w-full justify-center">
          Clear
        </Button>
      </div>
    </div>

    <div v-if="isLoading" class="w-full flex justify-center my-8">
      <Loading />
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-2">
        <thead
          class="text-xs font-medium text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-gray-400 border-b dark:border-gray-600">
          <tr>
            <th scope="col" class="px-3 py-2.5">
              {{ $t('public.affiliate.name') }}
            </th>
            <th scope="col" class="px-3 py-2.5">
              {{ $t('public.affiliate.refferer') }}
            </th>
            <th scope="col" class="px-3 py-2.5">
              {{ $t('public.affiliate.joining_date') }}
            </th>
            <th scope="col" class="px-3 py-2.5 text-center">
              {{ $t('public.affiliate.rank') }}
            </th>
            <th scope="col" class="px-3 py-2.5 text-center">
              {{ $t('public.affiliate.generation') }}
            </th>
            <th scope="col" class="px-3 py-2.5 text-center">
              {{ $t('public.affiliate.affiliate') }}
            </th>
            <th scope="col" class="px-3 py-2.5">
              {{ $t('public.affiliate.vsd') }}
            </th>
            <th scope="col" class="px-3 py-2.5">
              {{ $t('public.affiliate.vad') }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="child in referralTableData.data"
            class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600 dark:hover:bg-gray-600">
            <td class="px-3 py-2.5">
              <div class="flex items-center gap-2">
                <img class="object-cover w-6 h-6 rounded-full"
                  :src="child.profile_photo ? child.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                  alt="userPic" />
                <div class="flex flex-col">
                  <div>
                    {{ child.name }}
                  </div>
                  <div class="dark:text-gray-400">
                    {{ child.email }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-3 py-2.5">
              <div class="flex items-center gap-2">
                <img class="object-cover w-6 h-6 rounded-full"
                  :src="child.upline_profile_photo ? child.upline_profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                  alt="userPic" />
                <div class="flex flex-col">
                  <div>
                    {{ child.upline_name }}
                  </div>
                  <div class="dark:text-gray-400">
                    {{ child.upline_email }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-3 py-2.5">{{ formatDateTime(child.created_at, false) }}</td>
            <td class="px-3 py-2.5">
              <div class="flex justify-center items-center">
                <div class="uppercase" v-if="child.setting_rank_id === 1">
                  {{ child.setting_rank_name }}
                </div>
                <div v-if="child.setting_rank_id === 2">
                  <Rank1Icon class="h-6" />
                </div>
                <div v-if="child.setting_rank_id === 3">
                  <Rank2Icon class="h-6" />
                </div>
                <div v-if="child.setting_rank_id === 4">
                  <Rank3Icon class="h-6" />
                </div>
                <div v-if="child.setting_rank_id === 5">
                  <Rank4Icon class="h-6" />
                </div>
              </div>
            </td>
            <td class="px-3 py-2.5">
              <div class="flex justify-center items-center">
                {{ child.level }}
              </div>
            </td>
            <td class="px-3 py-2.5">
              <div class="flex justify-center items-center">
                {{ child.total_affiliate }}
              </div>
            </td>
            <td class="px-3 py-2.5">$&nbsp;{{ formatAmount(child.valid_self_deposit) }}</td>
            <td class="px-3 py-2.5">$&nbsp;{{ formatAmount(child.valid_affiliate_deposit) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center mt-4" v-if="!isLoading">
      <TailwindPagination 
        :item-classes=paginationClass 
        :active-classes=paginationActiveClass 
        :data="referralTableData"
        :limit=2 @pagination-change-page="handlePageChange"
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
