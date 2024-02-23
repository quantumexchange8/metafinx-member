<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import { ShareIcon, DuplicateIcon, CloudDownloadIcon } from "@heroicons/vue/outline";
import Tooltip from "@/Components/Tooltip.vue";
import { ref } from "vue";
import toast from "@/Composables/toast.js";
import ReferralTree from "@/Pages/Affiliate/ReferralTree.vue";
import { transactionFormat } from "@/Composables/index.js";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { LVL1Icon, LVL2Icon, LVL3Icon } from "@/Components/Icons/outline.jsx";
import ReferralTable from "@/Pages/Affiliate/ReferralTable.vue";


const props = defineProps({
    referredCounts: Number,
    validAffiliateDeposit: [String, Number],
    totalAffiliate: Number,
    totalGeneration: Number,
    exportStatus: Boolean,
})

const { formatDateTime, formatAmount } = transactionFormat();
const exportStatus = ref(false);

const networkType = ref(null);

const networkCategory = [
    {id: 1, name: 'group_network', type: 'affiliate'},
    {id: 2, name: "coin_binary_network", type: 'binary',},
]

const categories = ref(networkCategory)

const exportAffiliateTable = () => {
  exportStatus.value = true;

  setTimeout(() => {
    exportStatus.value = false;
  }, 100);
};

</script>

<template>
    <AuthenticatedLayout :title="$t('public.affiliate.my_group')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight">
                        {{ $t('public.affiliate.my_group') }}
                    </h2>
                    <p class="text-base font-normal dark:text-gray-400">
                        {{ $t('public.affiliate.group_tracking') }}
                    </p>
                </div>

                <div class="flex justify-end md:w-1/5 md:h-1/2 md:self-end pt-5 md:pt-0">
                    <Button
                        type="button"
                        class="justify-center w-full gap-2 border border-gray-600 text-white text-sm dark:hover:bg-gray-600"
                        variant="transparent"
                        :class="{ 'bg-transparent dark:bg-[#38425080] dark:text-white': exportStatus }"
                        @click="exportAffiliateTable"
                    >
                        <CloudDownloadIcon aria-hidden="true" class="w-5 h-5" />
                        <span>{{ $t('public.export_excel') }}</span>
                    </Button>
                </div>
            </div>
        </template>

        <div class="flex justify-between">
            <div class="w-full">
                <TabGroup>
                    <TabList class="flex dark:bg-transparent w-full">
                        <Tab v-for="category in categories" as="template" :key="category" v-slot="{ selected }">
                            <button
                                class="px-4 py-2.5 text-sm font-semibold text-gray-900 border border-gray-200 focus:outline-none w-full sm:w-64"
                                :class="{
                                    'rounded-l-xl': category.type === 'affiliate',
                                    'rounded-r-xl': category.type === 'binary',
                                    'hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600': true,
                                    'bg-transparent dark:bg-[#38425080] dark:text-white': selected
                                }">
                                {{ $t('public.' + category.name ) }}
                            </button>
                        </Tab>
                    </TabList>
                    <TabPanel>
                        <ReferralTable
                            :referredCounts="referredCounts"
                            :validAffiliateDeposit="validAffiliateDeposit"
                            :totalAffiliate="totalAffiliate"
                            :totalGeneration="totalGeneration"
                            :exportStatus="exportStatus"
                        />
                    </TabPanel>
                </TabGroup>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
