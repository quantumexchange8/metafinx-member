<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { PurseIcon } from '@/Components/Icons/outline'

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
                    <Button class="bg-warning-400">
                        <div class="flex justify-between gap-2">
                            <PurseIcon
                                aria-hidden="true"
                            />
                        <span>
                            My Invesment
                        </span>
                        </div>
                        
                    </Button>
                </div>
                
            </div>
        </template>

    </AuthenticatedLayout>

</template>