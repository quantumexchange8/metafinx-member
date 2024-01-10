<script setup>
import { ref } from 'vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import ReferralTree from "@/Pages/Affiliate/ReferralTree.vue";
import GenealogyTree from "@/Pages/Affiliate/GenealogyTree/GenealogyTree.vue";
import {LVL1Icon, LVL2Icon, LVL3Icon} from "@/Components/Icons/outline.jsx";

const categories = ref([
    {
        id: 1,
        name: 'Group Network',
        type: 'affiliate',
    },
    {
        id: 2,
        name: "XL Coin Binary Network",
        type: 'binary',
    },
])
</script>

<template>
    <div class="flex justify-between">
        <div class="w-full">
            <TabGroup>
                <TabList class="flex dark:bg-transparent w-full">
                    <Tab
                        v-for="category in categories"
                        as="template"
                        :key="category"
                        v-slot="{ selected }"
                    >
                        <button
                            class="px-4 py-2.5 text-sm font-semibold text-gray-900 border border-gray-200 focus:outline-none w-full sm:w-64"
                            :class="{
                                'rounded-l-xl': category.type === 'affiliate',
                                'rounded-r-xl': category.type === 'binary',
                                'hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600': true,
                                'bg-transparent dark:bg-[#38425080] dark:text-white': selected
                            }"
                        >
                            {{ category.name }}
                        </button>
                    </Tab>
                </TabList>
                <div class="flex py-8 gap-3 md:gap-10">
                    <div class="inline-flex items-center gap-3">
                        <LVL1Icon class="h-12" />
                        <div class="font-semibold text-sm">
                            {{$t('public.affiliate.lvl_1')}}
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-3">
                        <LVL2Icon class="h-12" />
                        <div class="font-semibold text-sm">
                            {{$t('public.affiliate.lvl_2')}}
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-3">
                        <LVL3Icon class="h-12" />
                        <div class="font-semibold text-sm">
                            {{$t('public.affiliate.lvl_3')}}
                        </div>
                    </div>
                </div>
                <TabPanels class="mt-2">
                    <TabPanel>
                        <ReferralTree />
                    </TabPanel>
                    <TabPanel>
                        <GenealogyTree />
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
    </div>

</template>
