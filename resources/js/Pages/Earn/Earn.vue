<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { PurseIcon, checkIcon } from '@/Components/Icons/outline'
import Subscribe from "@/Pages/Earn/Partials/Subscribe.vue";
import { ref } from 'vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import {transactionFormat} from "@/Composables/index.js";

const props = defineProps({
    investmentPlans: Object,
    coin_price: Object,
    internal_wallet: Object,
    musd_wallet: Object,
    stackingFee: Object,
    averageProfit: Object,
})

const { formatAmount } = transactionFormat();

const selectedCategory = ref('standard');

const selectCategory = (category) => {
  selectedCategory.value = category;
};
</script>

<template>

    <AuthenticatedLayout :title="$t('public.earn.earn')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight">
                        {{$t('public.earn.earn')}}
                    </h2>
                    <p class="text-base dark:text-gray-400">
                        {{$t('public.earn.simple_subscribe')}}
                    </p>
                </div>

                <div>
                    <Button
                        external
                        type="button"
                        variant="warning"
                        class="items-center gap-2 max-w-xs"
                        v-slot="{ iconSizeClasses }"
                        :href="route('earn.investment')"
                    >
                        <div class="inline-flex items-center">
                            <PurseIcon
                                aria-hidden="true"
                                class="mr-2"
                            />
                            <span>{{$t('public.earn.my_investment')}}</span>
                        </div>
                    </Button>
                </div>

            </div>
        </template>

        <div class="w-full mt-2">
            <TabGroup v-model="selectedCategory"  as="div">
                <TabList class="flex dark:bg-transparent w-full">
                    <Tab
                        v-for="(category, index) in props.investmentPlans"
                        as="template"
                        :key="category.id"
                        v-slot="{ selected }"
                    >
                        <button
                            class="px-4 py-2.5 text-sm font-semibold text-gray-900 border border-gray-200 focus:outline-none w-full sm:w-44"
                            :class="{
                                'rounded-l-xl': category.type === 'standard',
                                'rounded-r-xl': category.type === 'staking',
                                'hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600': true,
                                'bg-transparent dark:bg-[#38425080] dark:text-white': selected
                            }"
                            @click="selectCategory(category.type)"
                        >
                            <span class="uppercase">{{ category.type }} {{$t('public.earn.plan')}}</span>
                        </button>
                    </Tab>
                </TabList>

                <TabPanels class="mt-8">
                    <TabPanel
                        v-for="(investmentTypes, idx) in props.investmentPlans"
                        :key="idx"
                        :class="[
                        'rounded-xl dark:bg-transparent',
                      ]"
                    >
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div v-for="plan in investmentTypes.plans" class="p-5 bg-white rounded-xl shadow-md dark:bg-gray-700">
                                <div class="flex flex-col items-center justify-center gap-2 border-b dark:border-gray-600 pb-5">
                                    <img v-if="plan.type === 'standard'" class="w-14 h-14 rounded-xl" :src="plan.media.standard ?? '/assets/icon.png'" alt="Medium avatar">
                                    <img v-if="plan.type === 'staking'" class="w-14 h-14 rounded-xl" :src="plan.media.stacking ?? '/assets/stacking_default.png'" alt="Medium avatar">
                                    <div class="font-semibold">
                                        {{ plan.name }}
                                    </div>
                                    <div v-if="plan.type === 'staking'" class="font-semibold text-[32px] text-center">
                                        {{ plan.commision_multiplier * 100 }}% <span class="flex text-base">{{$t('public.staking_rewards')}}</span>
                                    </div>
                                    <div v-else class="font-semibold text-[32px]">
                                        {{ plan.roi_per_annum }} p.a.
                                    </div>
                                </div>
                                <div class="pt-5">
                                    <div v-for="item in plan.descriptions" class="mb-3">
                                        <div class="inline-flex items-center gap-2 text-xs">
                                            <checkIcon />
                                            {{ item.description }}
                                        </div>
                                    </div>
                                    <Subscribe
                                        :plan="plan"
                                        :coin_price="coin_price"
                                        :internal_wallet="internal_wallet"
                                        :musd_wallet="musd_wallet"
                                        :stackingFee="stackingFee"
                                    />
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>

        <div class="flex flex-col gap-5 my-5 mb-28 md:hidden">
            <h3 class="text-xl font-semibold leading-tight">
                {{$t('public.earn.investment_performance')}}
            </h3>
            <div v-if="selectedCategory === 'staking'" >
                <div class="p-5 mb-3 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        {{$t('public.average_profit_monthly')}}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        {{formatAmount(averageProfit.percent)}}&nbsp;%
                    </div>
                </div>
            </div>
            <div v-if="selectedCategory === 'standard'" >
                <div class="p-5 mb-3 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        {{$t('public.earn.incoming_standard_reward')}}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $&nbsp;0.00
                    </div>
                </div>
                <div class="p-5 mb-3 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        {{$t('public.earn.incoming_dividend')}}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $&nbsp;0.00
                    </div>
                </div>
                <h3 class="my-5 text-xl font-semibold leading-tight pt-4">
                    {{$t('public.earn.affiliate_performance')}}
                </h3>
                <div class="p-5 mb-3 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        {{$t('public.earn.incoming_affiliate_earnings')}}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $&nbsp;0.00
                    </div>
                </div>
                <div class="p-5 mb-3 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        {{$t('public.earn.incoming_dividend_earnings')}}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $&nbsp;0.00
                    </div>
                </div>
            </div>
        </div>

        <template #asideRight>
            <div class="inset-y-0 p-6 flex flex-col bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 lg:w-96 fixed right-0">
                <h3 class="my-5 text-xl font-semibold leading-tight">
                    {{$t('public.earn.investment_performance')}}
                </h3>
                <div v-if="selectedCategory === 'staking'" >
                    <div class="p-5 mb-3 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                        <div class="font-medium text-xs dark:text-gray-400">
                            {{$t('public.average_profit_monthly')}}
                        </div>
                        <div class="font-semibold text-2xl dark:text-white">
                            {{formatAmount(averageProfit.percent)}}&nbsp;%
                        </div>
                    </div>
                </div>
                <div v-if="selectedCategory === 'standard'" >
                    <div class="p-5 mb-3 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                        <div class="font-medium text-xs dark:text-gray-400">
                            {{$t('public.earn.incoming_standard_reward')}}
                        </div>
                        <div class="font-semibold text-2xl dark:text-white">
                            $&nbsp;0.00
                        </div>
                    </div>
                    <div class="p-5 mb-3 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                        <div class="font-medium text-xs dark:text-gray-400">
                            {{$t('public.earn.incoming_dividend')}}
                        </div>
                        <div class="font-semibold text-2xl dark:text-white">
                            $&nbsp;0.00
                        </div>
                    </div>
                    <h3 class="my-5 text-xl font-semibold leading-tight pt-4">
                        {{$t('public.earn.affiliate_performance')}}
                    </h3>
                    <div class="p-5 mb-3 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                        <div class="font-medium text-xs dark:text-gray-400">
                            {{$t('public.earn.incoming_affiliate_earnings')}}
                        </div>
                        <div class="font-semibold text-2xl dark:text-white">
                            $&nbsp;0.00
                        </div>
                    </div>
                    <div class="p-5 mb-3 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                        <div class="font-medium text-xs dark:text-gray-400">
                            {{$t('public.earn.incoming_dividend_earnings')}}
                        </div>
                        <div class="font-semibold text-2xl dark:text-white">
                            $&nbsp;0.00
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>

</template>
