<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { PurseIcon, checkIcon } from '@/Components/Icons/outline'
import Wallet from "@/Pages/Wallet/Wallet.vue";
import Subscribe from "@/Pages/Earn/Partials/Subscribe.vue";

const props = defineProps({
    investmentPlans: Object,
    wallet_sel: Array,
})
</script>

<template>

    <AuthenticatedLayout title="Earn">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight">
                        Earn
                    </h2>
                    <p class="text-base dark:text-gray-400">
                        Simple and transparent investments that grow with you. Subscribe now.
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
                            <span>My Investment</span>
                        </div>
                    </Button>
                </div>

            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div v-for="plan in props.investmentPlans" class="p-5 bg-white rounded-xl shadow-md dark:bg-gray-700">
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

        <div class="flex flex-col gap-5 my-5 md:hidden">
            <h3 class="text-xl font-semibold leading-tight">
                Investment Performance
            </h3>
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    Incoming Monthly Return
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ 0.00
                </div>
            </div>
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    Incoming Dividend
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ 0.00
                </div>
            </div>
            <h3 class="text-xl font-semibold leading-tight pt-4">
                Affiliate Performance
            </h3>
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    Incoming Affiliate Earnings
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ 0.00
                </div>
            </div>
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    Incoming Dividend Earnings
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ 0.00
                </div>
            </div>
        </div>


        <template #asideRight>
            <div class="inset-y-0 p-6 flex flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 lg:w-96 fixed right-0">
                <h3 class="text-xl font-semibold leading-tight">
                    Investment Performance
                </h3>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        Incoming Monthly Return
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ 0.00
                    </div>
                </div>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        Incoming Dividend
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ 0.00
                    </div>
                </div>
                <h3 class="text-xl font-semibold leading-tight pt-4">
                    Affiliate Performance
                </h3>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        Incoming Affiliate Earnings
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ 0.00
                    </div>
                </div>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        Incoming Dividend Earnings
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ 0.00
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>

</template>
