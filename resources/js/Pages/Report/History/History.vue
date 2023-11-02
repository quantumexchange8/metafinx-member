<script setup>
import {SearchIcon, RefreshIcon} from "@heroicons/vue/outline";
import {ref} from "vue";
import Button from "@/Components/Button.vue";
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from "@headlessui/vue";
import ReturnTable from "@/Pages/Report/History/ReturnTable.vue";
import EarningTable from "@/Pages/Report/History/EarningTable.vue";
import InvestmentTable from "@/Pages/Report/History/InvestmentTable.vue";

const emit = defineEmits(['clicked', 'update:export'])
const props = defineProps({
    search: String,
    type: String,
    date: String,
    exportStatus: Boolean,
})

const categories = ref({
    'Return (Personal)': [
        {
            name: 'Return'
        }
    ],
    Earning: [
        {
            name: 'Earning'
        }
    ],
    Investment: [
        {
            name: 'Investment'
        }
    ],
})
const isLoading = ref(false);
const refresh = ref(false);
const reportType = ref('Return');

function refreshTable() {
    isLoading.value = !isLoading.value;
    refresh.value = true;
}

const updateTransactionType = (transaction_type) => {
    reportType.value = transaction_type
    emit('clicked', transaction_type)
};
</script>

<template>
    <div class="">
        <TabGroup>
            <TabList class="max-w-xs md:max-w-full flex py-1 ">
                <Tab
                    v-for="category in Object.keys(categories)"
                    as="template"
                    :key="category"
                    v-slot="{ selected }"
                >
                    <button
                        @click="updateTransactionType(category)"
                        :class="[
                              'w-full md:w-1/6 py-2.5 text-sm font-semibold dark:text-gray-400',
                              'ring-white ring-offset-0 focus:outline-none focus:ring-0',
                              selected
                                ? 'dark:text-white border-b-2'
                                : 'border-b border-gray-400',
                           ]"
                    >
                        {{ category }}
                    </button>
                </Tab>
                <RefreshIcon
                    :class="{ 'animate-spin': isLoading }"
                    class="flex-shrink-0 w-5 h-5 cursor-pointer dark:text-white ml-auto self-center"
                    aria-hidden="true"
                    @click="refreshTable"
                />

            </TabList>

            <TabPanels>
                <TabPanel>
                    <ReturnTable
                        :refresh="refresh"
                        :isLoading="isLoading"
                        :search="search"
                        :date="date"
                        :exportStatus="exportStatus"
                        @update:loading="isLoading = $event"
                        @update:refresh="refresh = $event"
                        @update:export="$emit('update:export', $event)"
                    />
                </TabPanel>
                <TabPanel>
                    <EarningTable
                        :refresh="refresh"
                        :isLoading="isLoading"
                        :search="search"
                        :type="type"
                        :date="date"
                        :exportStatus="exportStatus"
                        @update:loading="isLoading = $event"
                        @update:refresh="refresh = $event"
                        @update:export="$emit('update:export', $event)"
                    />
                </TabPanel>
                <TabPanel>
                    <InvestmentTable
                        :refresh="refresh"
                        :isLoading="isLoading"
                        :search="search"
                        :type="type"
                        :date="date"
                        :exportStatus="exportStatus"
                        @update:loading="isLoading = $event"
                        @update:refresh="refresh = $event"
                        @update:export="$emit('update:export', $event)"
                    />
                </TabPanel>
            </TabPanels>
        </TabGroup>
    </div>

</template>
