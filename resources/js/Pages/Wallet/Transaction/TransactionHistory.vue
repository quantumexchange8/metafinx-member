<script setup>
import Input from "@/Components/Input.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import {SearchIcon, RefreshIcon} from "@heroicons/vue/outline";
import {ref} from "vue";
import Button from "@/Components/Button.vue";
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from "@headlessui/vue";
import {usePage} from "@inertiajs/vue3";
import TransactionTable from "@/Pages/Wallet/Transaction/TransactionTable.vue";
import BaseListbox from "@/Components/BaseListbox.vue";

const props = defineProps({
    conversion_rate: Object,
})

const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});

const wallets = ref(usePage().props.auth.user.wallets)
const search = ref('');
const date = ref('');
const isLoading = ref(false);
const refresh = ref(false);
const type = ref('');

const exportDeposit = () => {

    let url = `/wallet/getTransaction/${type.value}?export=yes`;

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
    refresh.value = true;
}

const updateTransactionType = (transaction_type) => {
    type.value = transaction_type
};

const categories = ref({});

wallets.value.forEach(wallet => {
    if (!categories.value[wallet.name]) {
        categories.value[wallet.name] = [];
    }

    categories.value[wallet.name].push({
        id: wallet.id,
        name: wallet.name,
        type: wallet.type,
        balance: wallet.balance,
    });
});

const typeFilter = [
    {value: '', label:"All"},
    {value: 'BuyCoin', label:"Buy Coin"},
    {value: 'Deposit', label:"Deposit"},
    {value: 'Withdrawal', label:"Withdrawal"},
    {value: 'Transfer', label:"Transfer"},
    {value: 'Investment', label:"Investment"},
    {value: 'StandardRewards', label:"Standard Rewards"},
    {value: 'ReferralEarnings', label:"Referral Earnings"},
    {value: 'AffiliateEarnings', label:"Affiliate Earnings"},
    {value: 'DividendEarnings', label:"Dividend Earnings"},
    {value: 'AffiliateDividendEarnings', label:"Affiliate Dividend Earnings"},
];

const clearFilter = () => {
    search.value = ''
    type.value = null
    date.value = ''
}
</script>

<template>
    <div class="flex justify-between mb-3">
        <h4 class="font-semibold dark:text-white">{{$t('public.wallet.all_transactions')}}</h4>
        <RefreshIcon
            :class="{ 'animate-spin': isLoading }"
            class="flex-shrink-0 w-5 h-5 cursor-pointer dark:text-white"
            aria-hidden="true"
            @click="refreshTable"
        />
    </div>

    <div class="flex flex-wrap gap-3 items-center sm:flex-nowrap">
      <div class="w-full">
            <InputIconWrapper>
                <template #icon>
                    <SearchIcon aria-hidden="true" class="w-5 h-5" />
                </template>
                <Input withIcon id="search" type="text" class="w-full block dark:border-transparent" :placeholder="$t('public.report.search_placeholder')" v-model="search" />
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
        <div class="w-full">
            <BaseListbox
                v-model="type"
                :options="typeFilter"
                :placeholder="$t('public.report.filters_placeholder')"
                class="w-full"
            />
        </div>
        <div class="w-full sm:w-auto">
            <Button
                type="button"
                variant="secondary-700"
                @click="clearFilter"
                class="w-full justify-center"
            >
                {{$t('public.clear')}}
            </Button>
        </div>
    </div>
    <div class="flex gap-4 mt-5">
        <span class="flex items-center text-xs font-normal text-gray-900 dark:text-white"><span class="flex w-2 h-2 bg-green-500 dark:bg-error-500 rounded-full mr-2 flex-shrink-0"></span>{{$t('public.wallet.rejected')}}</span>
        <span class="flex items-center text-xs font-normal text-gray-900 dark:text-white"><span class="flex w-2 h-2 bg-red-500 dark:bg-warning-500 rounded-full mr-2 flex-shrink-0"></span>{{$t('public.wallet.pending')}}</span>
        <span class="flex items-center text-xs font-normal text-gray-900 dark:text-white"><span class="flex w-2 h-2 bg-red-500 dark:bg-[#007AFF] rounded-full mr-2 flex-shrink-0"></span>{{$t('public.wallet.processing')}}</span>
        <span class="flex items-center text-xs font-normal text-gray-900 dark:text-white"><span class="flex w-2 h-2 bg-red-500 dark:bg-success-500 rounded-full mr-2 flex-shrink-0"></span>{{$t('public.wallet.success')}}</span>
    </div>

    <div class="w-full pt-5">
        <TabGroup>
            <TabList class="max-w-xs flex py-1">
                <Tab
                    v-for="walletName in Object.keys(categories)"
                    as="template"
                    :key="walletName"
                    v-slot="{ selected }"
                >
                    <button
                        :class="[
                              'w-full py-2.5 text-sm font-semibold dark:text-gray-400',
                              'ring-white ring-offset-0 focus:outline-none focus:ring-0',
                              selected
                                ? 'dark:text-white border-b-2'
                                : 'border-b border-gray-400',
                           ]"
                    >
                        {{ walletName }}
                    </button>
                </Tab>
            </TabList>

            <TabPanels class="mt-2">
                <TabPanel
                    v-for="(wallets, idx) in Object.values(categories)"
                    :key="idx"
                >
                    <div
                        v-for="wallet in wallets"
                        class="relative overflow-x-auto sm:rounded-lg"
                    >
                        <TransactionTable
                            :walletId="wallet.id"
                            :refresh="refresh"
                            :isLoading="isLoading"
                            :search="search"
                            :type="type"
                            :date="date"
                            @update:loading="isLoading = $event"
                            @update:refresh="refresh = $event"
                        />
                    </div>
                </TabPanel>
            </TabPanels>
        </TabGroup>
    </div>

</template>
