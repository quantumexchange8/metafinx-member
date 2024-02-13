<script setup>
import { ref } from 'vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import ReferralTree from "@/Pages/Affiliate/ReferralTree.vue";
import GenealogyTree from "@/Pages/Affiliate/GenealogyTree/GenealogyTree.vue";
import {Rank1Icon, Rank2Icon, Rank3Icon, Rank4Icon} from "@/Components/Icons/outline.jsx";
import Input from "@/Components/Input.vue";
import {SearchIcon} from "@heroicons/vue/outline";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";

const categories = ref([
    {
        id: 1,
        name: 'UniLevel Network',
        type: 'affiliate',
    },
    {
        id: 2,
        name: "Binary Network",
        type: 'binary',
    },
]);

const props = defineProps({
    downline: Array,
    uplineStaking: Boolean,
})

const search = ref('');
</script>

<template>
    <div class="flex justify-between">
        <div class="w-full">
            <TabGroup>
                <TabList class="flex dark:bg-transparent w-full flex-col gap-3 sm:flex-row sm:justify-between">
                    <div class="w-full">
                        <Tab
                            v-for="category in categories"
                            as="template"
                            :key="category"
                            v-slot="{ selected }"
                        >
                            <button
                                v-show="category.type !== 'binary' || uplineStaking"
                                class="px-4 py-2.5 text-sm font-semibold text-gray-900 border border-gray-200 focus:outline-none max-w-md"
                                :class="{
                                'rounded-lg': !uplineStaking && !$page.props.auth.user.binary && category.type === 'affiliate',
                                'rounded-l-xl': category.type === 'affiliate' && uplineStaking,
                                'rounded-r-xl': category.type === 'binary' && uplineStaking,
                                'hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600': true,
                                'bg-transparent dark:bg-[#38425080] dark:text-white': selected
                            }"
                            >
                                {{ category.name }}
                            </button>
                        </Tab>
                    </div>

                    <div>
                        <InputIconWrapper>
                            <template #icon>
                                <SearchIcon aria-hidden="true" class="w-5 h-5" />
                            </template>
                            <Input
                                withIcon
                                id="search"
                                type="text"
                                class="block border-transparent w-full"
                                :placeholder="$t('public.affiliate.search_placeholder')"
                                v-model="search"
                            />
                        </InputIconWrapper>
                    </div>
                </TabList>
                <TabPanels class="mt-2">
                    <TabPanel>
                         <ReferralTree
                             :search="search"
                         />
                    </TabPanel>
                    <TabPanel>
                        <GenealogyTree
                            :downline="downline"
                            :search="search"
                        />
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
    </div>

</template>
