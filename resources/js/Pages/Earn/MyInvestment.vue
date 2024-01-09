<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Link} from "@inertiajs/vue3";
import {transactionFormat} from "@/Composables/index.js";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    investments: Object
})

const { formatDate, formatType } = transactionFormat();
const tncModal = ref(false);

const openTncModal = () => {
    tncModal.value = true;
}

const closeModal = () => {
    tncModal.value = false
}

const calculateWidthPercentage = (created_at, period) => {
    const startDate = new Date(created_at);
    const endDate = new Date(startDate);
    endDate.setMonth(startDate.getMonth() + period);

    const currentDate = new Date();

    // Calculate elapsed days from startDate to currentDate
    const elapsedMilliseconds = currentDate - startDate;
    const elapsedDays = Math.ceil(elapsedMilliseconds / (1000 * 60 * 60 * 24));

    // Calculate total days from startDate to endDate
    const totalMilliseconds = endDate - startDate;
    const totalDays = Math.ceil(totalMilliseconds / (1000 * 60 * 60 * 24));

    // Calculate widthResult based on the progress
    const widthResult = Math.max(0, Math.min(100, (elapsedDays / totalDays) * 100));
    const remainingMonth = Math.floor((totalDays - elapsedDays) / 30);

    return { widthResult, remainingMonth };
};
</script>

<template>
    <AuthenticatedLayout :title="$t('public.earn.my_investment')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-2xl font-semibold leading-tight">
                    {{$t('public.earn.my_investment')}}
                </h2>
            </div>
            <p class="text-base font-normal dark:text-gray-400">
                    {{$t('public.earn.track_investment')}}
            </p>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div v-for="investment in props.investments" class="p-5 bg-white rounded-[20px] border dark:border-gray-600 dark:bg-gray-700 shadow-[0_0_12px_0] dark:shadow-[#9da4ae33]">
                <div class="flex justify-between">
                    <div class="text-xs">
                        <span v-if="investment.type === 'standard'">{{ investment.plan_name.name }} &#x2022; $ {{ investment.amount }}</span>
                        <span v-else>{{ investment.plan_name.name }} {{ (investment.amount/10000).toFixed(2) }} &#x2022; $ {{ investment.amount }}</span>
                    </div>
                    <div class="dark:text-gray-400 text-xs">
                        <span class="uppercase">{{$t('public.earn.since')}} {{ formatDate(investment.created_at) }}</span>
                    </div>
                </div>
                <div class="relative my-3">
                    <div class="mb-1 flex h-2.5 overflow-hidden rounded-full bg-gray-100 text-xs">
                        <div
                            :style="{ width: `${calculateWidthPercentage(investment.created_at, investment.investment_period).widthResult}%` }"
                            class="rounded-full bg-gradient-to-r from-warning-400 to-pink-500 transition-all duration-500 ease-out"
                        >
                        </div>
                    </div>
                    <div class="mb-2 flex items-center justify-between text-xs">
                        <div class="dark:text-gray-400">
                            1
                        </div>
                        <div class="dark:text-gray-400">
                            {{ investment.investment_period/4 }}
                        </div>
                        <div class="dark:text-gray-400">
                            {{ investment.investment_period/2 }}
                        </div>
                        <div class="dark:text-gray-400">{{ investment.investment_period }}</div>
                    </div>
                </div>
                <div class="flex justify-between mb-1">
                    <div class="dark:text-gray-400 text-xs">
                        {{$t('public.earn.situation')}}
                    </div>
                    <div class="dark:text-white text-xs">
                        <span class="uppercase dark:text-error-500 font-semibold" v-if="investment.status === 'Terminated'">{{ formatType(investment.status) }}</span>
                        <span class="uppercase dark:text-blue-500 font-semibold" v-if="investment.status === 'CoolingPeriod'">{{ formatType(investment.status) }}</span>
                        <span class="uppercase dark:text-warning-500 font-semibold" v-if="investment.status === 'OnGoingPeriod'">{{ formatType(investment.status) }}</span>
                        <span class="uppercase dark:text-success-500 font-semibold" v-if="investment.status === 'MaturityPeriod'">{{ formatType(investment.status) }}</span>
                    </div>
                </div>
                <div class="flex justify-between mb-1">
                    <div class="dark:text-gray-400 text-xs">
                        {{$t('public.earn.next_roi')}}
                    </div>
                    <div class="dark:text-white text-xs">
                        <span class="uppercase">{{ formatDate(investment.next_roi_date) }}</span>
                    </div>
                </div>
                <div class="flex justify-between mb-1">
                    <div class="dark:text-gray-400 text-xs">
                        {{$t('public.earn.valid')}}
                    </div>
                    <div class="dark:text-white text-xs">
                        <span class="uppercase">{{ formatDate(investment.expired_date) }}</span>
                    </div>
                </div>
                <div class="flex justify-between mb-1">
                    <div class="dark:text-gray-400 text-xs">
                        {{$t('public.earn.id_number')}}
                    </div>
                    <div class="dark:text-white text-xs">
                        <span class="uppercase">{{ investment.subscription_id }}</span>
                    </div>
                </div>
                <div class="flex justify-between mb-1">
                    <div class="dark:text-gray-400 text-xs">
                        {{$t('public.earn.total_earning')}}
                    </div>
                    <div class="dark:text-white text-xs">
                        <span class="uppercase">$ {{ investment.total_earning }}</span>
                    </div>
                </div>
                <div class="mt-4 text-xs">
                    <span class="dark:text-gray-400">{{$t('public.earn.t&c_apply')}}</span><span class="dark:text-white underline cursor-pointer dark:hover:text-gray-300" @click="openTncModal">Learn more.</span>
                </div>
            </div>
        </div>

        <div v-if="investments.length === 0" class="flex flex-col items-center justify-center my-8">
            <img src="/assets/no_data.png" class="w-1/2" alt="">
            <div class="dark:text-gray-400 mt-3">
                {{$t('public.no_data')}}
            </div>
        </div>

        <Modal :show="tncModal" :title="$t('public.earn.t&c_apply')" @close="closeModal">
            <div class="text-xs font-medium dark:text-gray-400 my-4">
                {{$t('public.earn.important')}}
            </div>
            <div class="text-xs font-normal dark:text-gray-400 mb-4">
                {{$t('public.earn.click_confirm')}}
            </div>

            <ol class="text-[10px] list-decimal list-inside dark:text-gray-400">
                <li>
                    {{$t('public.earn.subscription_agreement')}}
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>{{$t('public.earn.eligibility')}}</li>
                        <li>{{$t('public.earn.subscription_fee')}}</li>
                        <li>{{$t('public.earn.subscription_term')}}</li>
                    </ul>
                </li>
                <li>
                    {{$t('public.earn.investment_services')}}
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>{{$t('public.earn.investment_strategy')}}</li>
                        <li>{{$t('public.earn.risk_disclosure')}}</li>
                        <li>{{$t('public.earn.information_accuracy')}}</li>
                    </ul>
                </li>
                <li>
                    {{$t('public.earn.confidentiality_privacy')}}
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>{{$t('public.earn.confidentiality')}}</li>
                        <li>{{$t('public.earn.data_usage')}}</li>
                    </ul>
                </li>
                <li>
                    {{$t('public.earn.termination')}}
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>{{$t('public.earn.termination_by_us')}}</li>
                    </ul>
                </li>
                <li>
                    {{$t('public.earn.limitation')}}
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>{{$t('public.earn.no_liability')}}</li>
                    </ul>
                </li>
                <li>
                    {{$t('public.earn.miscellaneous')}}
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>{{$t('public.earn.amendment')}}</li>
                        <li>{{$t('public.earn.entire_agreement')}}</li>
                    </ul>
                </li>
            </ol>

        </Modal>
    </AuthenticatedLayout>

</template>
