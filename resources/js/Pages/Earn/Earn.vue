<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { PurseIcon, checkIcon } from '@/Components/Icons/outline'
import Subscribe from "@/Pages/Earn/Partials/Subscribe.vue";
import { ref } from 'vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const props = defineProps({
    investmentPlans: Object,
    wallet_sel: Array,
})
const categories = ref({
    Recent: [
        {
            id: 1,
            title: 'Does drinking coffee make you smarter?',
            date: '5h ago',
            commentCount: 5,
            shareCount: 2,
        },
        {
            id: 2,
            title: "So you've bought coffee... now what?",
            date: '2h ago',
            commentCount: 3,
            shareCount: 2,
        },
    ],
    Popular: [
        {
            id: 1,
            title: 'Is tech making coffee better or worse?',
            date: 'Jan 7',
            commentCount: 29,
            shareCount: 16,
        },
        {
            id: 2,
            title: 'The most innovative things happening in coffee',
            date: 'Mar 19',
            commentCount: 24,
            shareCount: 12,
        },
    ],
    Trending: [
        {
            id: 1,
            title: 'Ask Me Anything: 10 answers to your questions about coffee',
            date: '2d ago',
            commentCount: 9,
            shareCount: 5,
        },
        {
            id: 2,
            title: "The worst advice we've ever heard about coffee",
            date: '4d ago',
            commentCount: 1,
            shareCount: 2,
        },
    ],
})
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

        <div class="w-full mt-2]">
            <TabGroup>
                <TabList class="flex dark:bg-transparent w-full">
                    <Tab
                        v-for="category in props.investmentPlans"
                        as="template"
                        :key="category.id"
                        v-slot="{ selected }"
                    >
                        <button
                            class="px-4 py-2.5 text-sm font-semibold text-gray-900 border border-gray-200 focus:outline-none w-full sm:w-44"
                            :class="{
                                'rounded-l-xl rounded-r-xl': category.type === 'standard' || category.type === 'ebmi',
                                'hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600': true,
                                'bg-transparent dark:bg-[#38425080] dark:text-white': selected
                            }"
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
                                    <img class="w-10 h-10 rounded bg-white" src="/assets/icon.png" alt="Medium avatar">
                                    <div class="font-semibold">
                                        {{ plan.name }}
                                    </div>
                                    <div class="font-semibold text-[32px]">
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
                                        :wallet_sel="wallet_sel"
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
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.earn.incoming_monthly_return')}}
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ 0.00
                </div>
            </div>
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.earn.incoming_dividend')}}
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ 0.00
                </div>
            </div>
            <h3 class="text-xl font-semibold leading-tight pt-4">
                {{$t('public.earn.affiliate_performance')}}
            </h3>
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.earn.incoming_affiliate_earnings')}}
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ 0.00
                </div>
            </div>
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.earn.incoming_dividend_earnings')}}
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ 0.00
                </div>
            </div>
        </div>


        <template #asideRight>
            <div class="inset-y-0 p-6 flex flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 lg:w-96 fixed right-0">
                <h3 class="text-xl font-semibold leading-tight">
                    {{$t('public.earn.investment_performance')}}
                </h3>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.earn.incoming_monthly_return')}}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ 0.00
                    </div>
                </div>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.earn.incoming_dividend')}}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ 0.00
                    </div>
                </div>
                <h3 class="text-xl font-semibold leading-tight pt-4">
                {{$t('public.earn.affiliate_performance')}}
                </h3>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.earn.incoming_affiliate_earnings')}}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ 0.00
                    </div>
                </div>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.earn.incoming_dividend_earnings')}}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ 0.00
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>

</template>
