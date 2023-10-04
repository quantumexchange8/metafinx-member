<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { PurseIcon } from '@/Components/Icons/outline'
import Wallet from "@/Pages/Wallet/Wallet.vue";

const props = defineProps({
    investmentPlans: Object
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
                        type="button"
                        variant="warning"
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
                <div class="flex flex-col items-center justify-center">
                    <img class="w-10 h-10 rounded bg-white" src="/assets/icon.png" alt="Medium avatar">
                </div>
            </div>
        </div>


        <template #asideRight>
            <div class="inset-y-0 p-6 flex flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 w-96 fixed right-0">
                <h3 class="text-xl font-semibold leading-tight">
                    Investment Performance
                </h3>


            </div>
        </template>
    </AuthenticatedLayout>

</template>
