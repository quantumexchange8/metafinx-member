<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import History from "@/Pages/Report/History/History.vue";
import {ref, watch} from "vue";
import Input from "@/Components/Input.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import {SearchIcon, FilterIcon} from "@heroicons/vue/outline";
import {CloudDownloadIcon} from "@/Components/Icons/outline.jsx";
import BaseListbox from "@/Components/BaseListbox.vue";

const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const search = ref('');
const date = ref('');
const filter = ref();
const test = ref('Deposit');

const returnFilter = [
    {value:1, label:"Monthly Return"}, 
    {value:2, label:"Ticket Bonus"},
    {value:3, label:"Dividend"},
];
const earnFilter = [
    {value:1, label:"Referral Earning"},
    {value:2, label:"Affiliate Earning"},
    {value:3, label:"Dividend Earning"},

];
const investFilter = [
    {value:1, label:"Basic"},
    {value:2, label:"Advance"},
    {value:3, label:"Pro"},
    {value:4, label:"Maturity Period"},
];

// const exportDeposit = () => {

//     let url = `/wallet/getTransaction/${type.value}?export=yes`;

//     if (date) {
//         url += `&date=${date.value}`;
//     }

//     if (search) {
//         url += `&search=${search.value}`;
//     }

//     window.location.href = url;
// }

const historyType = (childData) => {
    test.value = childData;
};

</script>

<template>
    <AuthenticatedLayout title="Report">
        <template #header>
            <div class="md:flex md:flex-row md:justify-between">
                <div class="flex flex-col gap-1 md:w-1/2">
                    <div class="md:flex-row md:items-center md:justify-between">
                        <h2 class="text-3xl font-semibold leading-tight">
                            Report
                        </h2>
                    </div>
                    <p class="text-base font-normal dark:text-gray-400">
                        Track your finance flow of this account.
                    </p>
                </div>
                <div class="flex justify-end md:w-1/3 md:h-1/2 md:self-end">
                    <Button
                        type="button"
                        class="justify-center w-full md:w-2/3 gap-2 border border-gray-600 text-white text-sm dark:hover:bg-gray-600"
                        variant="transparent"
                        @click=""
                    >
                        <CloudDownloadIcon aria-hidden="true" class="w-5 h-5" />
                        <span>Export as Excel</span>
                    </Button>
                </div>
            </div>
        </template>

        <h3 class="md:hidden text-xl font-semibold leading-tight my-5">
            Finance Overview
        </h3>
        <div class="flex flex-col md:hidden gap-3 overflow-x-auto md:overflow-visible">
            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-[20px] dark:bg-gray-700 w-full">
                <div class="space-y-2">
                    <div class="text-xs font-semibold dark:text-white">
                        Total Growth since 01 Jan 2023
                    </div>
                    <div class="text-2xl font-semibold dark:text-white">
                        $ 10,000.00
                    </div>
                </div>
            </div>
            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-[20px] dark:bg-gray-700 w-full">
                <div class="space-y-2">
                    <div class="text-xs font-semibold dark:text-white">
                        Total Withdrawal since 01 Jan 2023
                    </div>
                    <div class="text-2xl font-semibold dark:text-white">
                        $ 10,000.00
                    </div>
                </div>
            </div>
            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-[20px] dark:bg-gray-700 w-full">
                <div class="space-y-2">
                    <div class="text-xs font-semibold dark:text-white">
                        Total Investment since 01 Jan 2023
                    </div>
                    <div class="text-2xl font-semibold dark:text-white">
                        $ 10,000.00
                    </div>
                </div>
            </div>
            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-[20px] dark:bg-gray-700 w-full">
                <div class="space-y-2">
                    <div class="text-xs font-semibold dark:text-white">
                        Total Balance since 01 Jan 2023
                    </div>
                    <div class="text-2xl font-semibold dark:text-white">
                        $ 10,000.00
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-11 gap-3">
            <div class="md:col-span-4 mt-1">
                <InputIconWrapper>
                    <template #icon>
                        <SearchIcon aria-hidden="true" class="w-5 h-5" />
                    </template>
                    <Input withIcon id="search" type="text" class="w-full block" placeholder="Search" v-model="search" />
                </InputIconWrapper>
            </div>
            <div class="md:col-span-2">
                <BaseListbox v-if="test === 'Deposit'"
                    v-model="filter"
                    :options="returnFilter"
                    placeholder = "Filters"
                />
                <BaseListbox v-if="test === 'Withdrawal'"
                    v-model="filter"
                    :options="earnFilter"
                    placeholder = "Filters"
                />
                <BaseListbox v-if="test === 'Investment'"
                    v-model="filter"
                    :options="investFilter"
                    placeholder = "Filters"
                />
            </div>
            <div class="md:col-span-2 mt-1">
                <vue-tailwind-datepicker
                    placeholder="Select dates"
                    :formatter="formatter"
                    separator=" - "
                    v-model="date"
                    input-classes="py-2.5 border-gray-400 w-full rounded-lg text-sm placeholder:text-base dark:placeholder:text-gray-400 focus:border-gray-400 focus:border-pink-700 focus:ring focus:ring-pink-500 focus:ring-offset-0 focus:ring-offset-white dark:border-gray-600 dark:bg-gray-600 dark:text-white"
                />
            </div>
        </div>

        <div class="p-5 my-5 bg-white overflow-hidden md:overflow-visible rounded-xl shadow-md dark:bg-gray-700">
            <History 
            @clicked="historyType"
            :search="search"
            :date="date"
            />
        </div>

        <template #asideRight>
            <div class="inset-y-0 p-6 flex flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 w-96 fixed right-0">
                <h3 class="text-xl font-semibold leading-tight">
                    Finance Overview
                </h3>

                <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-[20px] dark:bg-gray-700">
                    <div class="space-y-2">
                        <div class="text-xs font-medium dark:text-gray-400">
                            Total Growth since 01 Jan 2023
                        </div>
                        <div class="text-2xl font-semibold dark:text-white">
                            $ 10,000.00
                        </div>
                    </div>
                </div>
                <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-[20px] dark:bg-gray-700">
                    <div class="space-y-2">
                        <div class="text-xs font-medium dark:text-gray-400">
                            Total Withdrawal since 01 Jan 2023
                        </div>
                        <div class="text-2xl font-semibold dark:text-white">
                            $ 10,000.00
                        </div>
                    </div>
                </div>
                <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-[20px] dark:bg-gray-700">
                    <div class="space-y-2">
                        <div class="text-xs font-medium dark:text-gray-400">
                            Total Investment since 01 Jan 2023
                        </div>
                        <div class="text-2xl font-semibold dark:text-white">
                            $ 10,000.00
                        </div>
                    </div>
                </div>
                <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-[20px] dark:bg-gray-700">
                    <div class="space-y-2">
                        <div class="text-xs font-medium dark:text-gray-400">
                            Total Balance since 01 Jan 2023
                        </div>
                        <div class="text-2xl font-semibold dark:text-white">
                            $ 10,000.00
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
