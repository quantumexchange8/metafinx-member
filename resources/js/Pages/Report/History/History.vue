<script setup>
import {SearchIcon, RefreshIcon} from "@heroicons/vue/outline";
import {ref} from "vue";
import Button from "@/Components/Button.vue";
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from "@headlessui/vue";
import ReturnTable from "@/Pages/Report/History/ReturnTable.vue";
import EarningTable from "@/Pages/Report/History/EarningTable.vue";

const emit = defineEmits(['clicked'])
const props = defineProps({
    search: String,
    date: String,
})

const isLoading = ref(false);
const refresh = ref(false);
const type = ref('Deposit');

function refreshTable() {
    isLoading.value = !isLoading.value;
    refresh.value = true;
}

const updateTransactionType = (transaction_type) => {
    type.value = transaction_type
    emit('clicked', transaction_type)
};
</script>

<template>
    <div class="">
        <TabGroup>
            <TabList class="max-w-xs md:max-w-full flex py-1 ">
                <Tab
                    as="template"
                    v-slot="{ selected }"
                >
                    <button
                        @click="updateTransactionType('Deposit')"
                        :class="[
                              'w-full md:w-1/6 py-2.5 text-sm font-semibold dark:text-gray-400',
                              'ring-white ring-offset-0 focus:outline-none focus:ring-0',
                              selected
                                ? 'dark:text-white border-b-2'
                                : 'border-b border-gray-400',
                           ]"
                    >
                        Deposit
                    </button>
                </Tab>
                <Tab
                    as="template"
                    v-slot="{ selected }"
                >
                    <button
                        @click="updateTransactionType('Withdrawal')"
                        :class="[
                              'w-full md:w-1/6 py-2.5 text-sm font-semibold dark:text-gray-400',
                              'ring-white ring-offset-0 focus:outline-none focus:ring-0',
                              selected
                                ? 'dark:text-white border-b-2'
                                : 'border-b border-gray-400',
                           ]"
                    >
                        Withdrawal
                    </button>
                </Tab>
                <Tab
                    as="template"
                    v-slot="{ selected }"
                >
                    <button
                    @click="updateTransactionType('Investment')"
                        :class="[
                              'w-full md:w-1/6 py-2.5 text-sm font-semibold dark:text-gray-400',
                              'ring-white ring-offset-0 focus:outline-none focus:ring-0',
                              selected
                                ? 'dark:text-white border-b-2'
                                : 'border-b border-gray-400',
                           ]"
                    >
                        Investment
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
                        @update:loading="isLoading = $event"
                        @update:refresh="refresh = $event"
                    />
                </TabPanel>
                <TabPanel>
                    <EarningTable
                        :refresh="refresh"
                        :isLoading="isLoading"
                        :search="search"
                        :date="date"
                        @update:loading="isLoading = $event"
                        @update:refresh="refresh = $event"
                    />
                </TabPanel>
            </TabPanels>
        </TabGroup>
    </div>

</template>
