<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import History from "@/Pages/Report/History/History.vue";
import {computed, ref, watch} from "vue";
import Input from "@/Components/Input.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import {SearchIcon, FilterIcon} from "@heroicons/vue/outline";
import {CloudDownloadIcon} from "@/Components/Icons/outline.jsx";
import BaseListbox from "@/Components/BaseListbox.vue";
import {transactionFormat} from "@/Composables/index.js";
import { trans } from 'laravel-vue-i18n';
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    standardRewards: [String, Number],
    standardReferralEarnings: [String, Number],
    affiliateEarnings: [String, Number],
    dividendEarnings: [String, Number],
    affiliateDividendEarnings: [String, Number],
    stakingRewards: [String, Number],
    stakingReferralEarnings: [String, Number],
    pairingEarnings: [String, Number],
    investmentPlans: Object,
})
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const search = ref('');
const date = ref('');
const type = ref();
const reportType = ref('Return (Personal)');
const exportStatus = ref(false);
const { formatAmount } = transactionFormat();

const returnFilter = [
    {value: '', label: trans('public.all')},
    {value: 'StandardRewards', label: trans('public.standard_rewards')},
    {value: 'StakingRewards', label: trans('public.staking_rewards')},
    {value: 'DividendEarnings', label: trans('public.dividend_earnings')},
];

const earnFilter = [
    {value: '', label: trans('public.all')},
    {value: 'ReferralEarnings', label: trans('public.referral_earnings')},
    {value: 'AffiliateEarnings', label: trans('public.affiliate_earnings')},
    {value: 'DividendEarnings', label: trans('public.dividend_earnings')},
    {value: 'PairingEarnings', label: trans('public.pairing_earnings')},
];

const investFilter = [
    {value: '', label: trans('public.all')},
    {value: 'CoolingPeriod', label: trans('public.cooling_period')},
    {value: 'OngoingPeriod', label: trans('public.ongoing_period')},
    {value: 'MaturityPeriod', label: trans('public.maturity_period')},
    {value: 'Terminated', label: trans('public.terminated')},
];

const historyType = (type) => {
    reportType.value = type;
};

const exportReport = () => {
    exportStatus.value = true;
}

const clearFilter = () => {
    search.value = ''
    type.value = null
    date.value = ''
}

</script>

<template>
    <AuthenticatedLayout :title="$t('public.report.report')">
        <template #header>
            <div class="md:flex md:flex-row md:justify-between">
                <div class="flex flex-col gap-1 md:w-1/2">
                    <div class="md:flex-row md:items-center md:justify-between">
                        <h2 class="text-3xl font-semibold leading-tight">
                            {{$t('public.report.report')}}
                        </h2>
                    </div>
                    <p class="text-base font-normal dark:text-gray-400">
                        {{$t('public.report.track_finance_flow')}}
                    </p>
                </div>
                <div class="flex justify-end md:w-1/3 md:h-1/2 md:self-end pt-5 md:pt-0">
                    <Button
                        type="button"
                        class="justify-center w-full md:w-2/3 gap-2 border border-gray-600 text-white text-sm dark:hover:bg-gray-600"
                        variant="transparent"
                        @click="exportReport"
                    >
                        <CloudDownloadIcon aria-hidden="true" class="w-5 h-5" />
                        <span>{{$t('public.export_excel')}}</span>
                    </Button>
                </div>
            </div>
        </template>

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
                    v-if="reportType === 'Return (Personal)'"
                    v-model="type"
                    :options="returnFilter"
                    :placeholder="$t('public.report.filters_placeholder')"
                />
                <BaseListbox
                    v-if="reportType === 'Earning'"
                    v-model="type"
                    :options="earnFilter"
                    :placeholder="$t('public.report.filters_placeholder')"
                />
                <BaseListbox
                    v-if="reportType === 'Investment'"
                    v-model="type"
                    :options="investFilter"
                    :placeholder="$t('public.report.filters_placeholder')"
                />
            </div>
            <div class="w-full sm:w-auto">
                <Button
                    type="button"
                    variant="secondary"
                    @click="clearFilter"
                    class="w-full justify-center"
                >
                    {{ $t('public.clear') }}
                </Button>
            </div>
        </div>

        <div class="p-5 my-5 bg-white overflow-hidden md:overflow-visible rounded-xl shadow-md dark:bg-gray-700">
            <History
                @clicked="historyType"
                :search="search"
                :type="type"
                :date="date"
                :exportStatus="exportStatus"
                @update:export="exportStatus = $event"
            />
        </div>

        <div
            class="sm:block md:hidden mb-28 inset-y-0 p-2 flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 w-full">
            <h3 class="text-xl font-semibold leading-tight">
                {{ $t('public.report.standard_plan_overview') }}
            </h3>

            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-xl dark:bg-gray-700">
                <div class="space-y-1">
                    <div class="text-xs font-medium dark:text-gray-400">{{ $t('public.standard_rewards') }}</div>
                    <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.standardRewards) }}</div>
                </div>
            </div>
            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-xl dark:bg-gray-700">
                <div class="space-y-1">
                    <div class="text-xs font-medium dark:text-gray-400">{{ $t('public.referral_earnings') }}</div>
                    <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.standardReferralEarnings) }}
                    </div>
                </div>
            </div>
            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-xl dark:bg-gray-700">
                <div class="space-y-1">
                    <div class="text-xs font-medium dark:text-gray-400">{{ $t('public.affiliate_earnings') }}</div>
                    <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.affiliateEarnings) }}</div>
                </div>
            </div>
            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-xl dark:bg-gray-700">
                <div class="space-y-1">
                    <div class="text-xs font-medium dark:text-gray-400">{{ $t('public.dividend_earnings') }}</div>
                    <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.dividendEarnings) }}</div>
                </div>
            </div>
            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-xl dark:bg-gray-700">
                <div class="space-y-1">
                    <div class="text-xs font-medium dark:text-gray-400">{{ $t('public.affiliate_dividend_earnings') }}</div>
                    <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.affiliateDividendEarnings) }}</div>
                </div>
            </div>

            <h3 class="text-xl font-semibold leading-tight">
                {{ $t('public.report.coin_staking_overview') }}
            </h3>

            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-xl dark:bg-gray-700">
                <div class="space-y-1">
                    <div class="text-xs font-medium dark:text-gray-400">{{ $t('public.staking_rewards') }}</div>
                    <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.stakingRewards) }}</div>
                </div>
            </div>
            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-xl dark:bg-gray-700">
                <div class="space-y-1">
                    <div class="text-xs font-medium dark:text-gray-400">{{ $t('public.referral_earnings') }}</div>
                    <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.stakingReferralEarnings) }}
                    </div>
                </div>
            </div>
            <div class="p-5 flex justify-between items-center overflow-hidden bg-white rounded-xl dark:bg-gray-700">
                <div class="space-y-1">
                    <div class="text-xs font-medium dark:text-gray-400">{{ $t('public.pairing_earnings') }}</div>
                    <div class="text-2xl font-semibold dark:text-white">{{ formatAmount(props.pairingEarnings) }}</div>
                </div>
            </div>
        </div>

        <template #asideRight>
            <div class="hidden overflow-y-scroll sm:flex inset-y-0 p-6 flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 w-96 fixed right-0">
                <h3 class="text-xl font-semibold leading-tight">
                    {{$t('public.report.standard_plan_overview')}}
                </h3>

                <div class="p-5 flex justify-between items-center bg-white rounded-xl dark:bg-gray-700">
                    <div class="space-y-1">
                        <div class="text-xs font-medium dark:text-gray-400">{{$t('public.standard_rewards')}}</div>
                        <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.standardRewards) }}</div>
                    </div>
                </div>
                <div class="p-5 flex justify-between items-center bg-white rounded-xl dark:bg-gray-700">
                    <div class="space-y-1">
                        <div class="text-xs font-medium dark:text-gray-400">{{$t('public.referral_earnings')}}</div>
                        <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.standardReferralEarnings) }}</div>
                    </div>
                </div>
                <div class="p-5 flex justify-between items-center bg-white rounded-xl dark:bg-gray-700">
                    <div class="space-y-1">
                        <div class="text-xs font-medium dark:text-gray-400">{{$t('public.affiliate_earnings')}}</div>
                        <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.affiliateEarnings) }}</div>
                    </div>
                </div>
                <div class="p-5 flex justify-between items-center bg-white rounded-xl dark:bg-gray-700">
                    <div class="space-y-1">
                        <div class="text-xs font-medium dark:text-gray-400">{{$t('public.dividend_earnings')}}</div>
                        <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.dividendEarnings) }}</div>
                    </div>
                </div>
                <div class="p-5 flex justify-between items-center bg-white rounded-xl dark:bg-gray-700">
                    <div class="space-y-1">
                        <div class="text-xs font-medium dark:text-gray-400">{{$t('public.affiliate_dividend_earnings')}}</div>
                        <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.affiliateDividendEarnings) }}</div>
                    </div>
                </div>

                <h3 class="text-xl font-semibold leading-tight">
                    {{$t('public.report.coin_staking_overview')}}
                </h3>

                <div class="p-5 flex justify-between items-center bg-white rounded-xl dark:bg-gray-700">
                    <div class="space-y-1">
                        <div class="text-xs font-medium dark:text-gray-400">{{$t('public.staking_rewards')}}</div>
                        <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.stakingRewards) }}</div>
                    </div>
                </div>
                <div class="p-5 flex justify-between items-center bg-white rounded-xl dark:bg-gray-700">
                    <div class="space-y-1">
                        <div class="text-xs font-medium dark:text-gray-400">{{$t('public.referral_earnings')}}</div>
                        <div class="text-2xl font-semibold dark:text-white">$&nbsp;{{ formatAmount(props.stakingReferralEarnings) }}</div>
                    </div>
                </div>
                <div class="p-5 flex justify-between items-center bg-white rounded-xl dark:bg-gray-700">
                    <div class="space-y-1">
                        <div class="text-xs font-medium dark:text-gray-400">{{$t('public.pairing_earnings')}}</div>
                        <div class="text-2xl font-semibold dark:text-white">$ {{ formatAmount(props.pairingEarnings) }}</div>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
