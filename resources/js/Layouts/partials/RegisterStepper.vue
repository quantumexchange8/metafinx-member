<script setup>
import { UserIcon, UsersIcon } from "@/Components/Icons/outline.jsx";
import {KeyIcon, ShieldCheckIcon} from '@heroicons/vue/outline'

const props = defineProps({
    formStep: Number
})
</script>

<template>
    <div class="lg:py-6 lg:pr-16">
        <div class="flex">
            <div class="flex flex-col items-center mr-4">
                <div>
                    <div
                        :class="formStep === 1 ? 'border-gray-200 dark:border-gray-400' : 'border-gray-200 dark:border-gray-800'"
                        class="flex items-center justify-center w-10 h-10 border rounded-lg shadow"
                    >
                        <UserIcon
                            aria-hidden="true"
                            :class="formStep === 1 ? 'text-gray-800 dark:text-white' : 'text-gray-400 dark:text-gray-600'"
                            class="h-6 w-6"
                        />
                    </div>
                </div>
                <div class="w-[2px] h-full bg-gray-200 dark:bg-gray-800"></div>
            </div>
            <div class="pb-12">
                <p
                    :class="formStep === 1 ? 'text-gray-800 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                    class="font-semibold"
                >
                    {{$t('public.register_stepper.your_details')}}
                </p>
                <p
                    :class="formStep === 1 ? 'text-gray-800 dark:text-gray-300' : 'text-gray-500 dark:text-gray-400'"
                    class="text-sm"
                >
                    {{$t('public.register_stepper.name_contact_details')}}
                </p>
            </div>
        </div>
        <div class="flex">
            <div class="flex flex-col items-center mr-4">
                <div>
                    <div
                        :class="formStep === 2 ? 'border-gray-200 dark:border-gray-400' : 'border-gray-200 dark:border-gray-800'"
                        class="flex items-center justify-center w-10 h-10 border rounded-lg shadow"
                    >
                        <KeyIcon
                            aria-hidden="true"
                            :class="formStep === 2 ? 'text-gray-800 dark:text-white' : 'text-gray-400 dark:text-gray-600'"
                            class="h-6 w-6"
                        />
                    </div>
                </div>
                <div class="w-[2px] h-full bg-gray-200 dark:bg-gray-800"></div>
            </div>
            <div class="pb-12">
                <p
                    :class="formStep === 2 ? 'text-gray-800 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                    class="font-semibold"
                >
                    {{$t('public.register_stepper.password')}}
                </p>
                <p
                    :class="formStep === 2 ? 'text-gray-800 dark:text-gray-300' : 'text-gray-500 dark:text-gray-400'"
                    class="text-sm"
                >
                    {{$t('public.register_stepper.secure_password')}}
                </p>
            </div>
        </div>
        <div class="flex">
            <div class="flex flex-col items-center mr-4">
                <div>
                    <div
                        :class="formStep === 3 ? 'border-gray-200 dark:border-gray-400' : 'border-gray-200 dark:border-gray-800'"
                        class="flex items-center justify-center w-10 h-10 border rounded-lg shadow"
                    >
                        <ShieldCheckIcon
                            aria-hidden="true"
                            :class="formStep === 3 ? 'text-gray-800 dark:text-white' : 'text-gray-400 dark:text-gray-600'"
                            class="h-6 w-6"
                        />
                    </div>
                </div>
                <div class="w-[2px] h-full bg-gray-200 dark:bg-gray-800"></div>
            </div>
            <div class="pb-12">
                <p
                    :class="formStep === 3 ? 'text-gray-800 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                    class="font-semibold"
                >
                    {{$t('public.register_stepper.identity')}}
                </p>
                <p
                    :class="formStep === 3 ? 'text-gray-800 dark:text-gray-300' : 'text-gray-500 dark:text-gray-400'"
                    class="text-sm"
                >
                    {{$t('public.register_stepper.verify_identity')}}
                </p>
            </div>
        </div>
        <div class="flex">
            <div class="flex flex-col items-center mr-4">
                <div>
                    <div
                        :class="formStep === 4 ? 'border-gray-200 dark:border-gray-400' : 'border-gray-200 dark:border-gray-800'"
                        class="flex items-center justify-center w-10 h-10 border rounded-lg shadow"
                    >
                        <UsersIcon
                            aria-hidden="true"
                            :class="formStep === 4 ? 'text-gray-800 dark:text-white' : 'text-gray-400 dark:text-gray-600'"
                            class="h-6 w-6"
                        />
                    </div>
                </div>
            </div>
            <div class="pb-12">
                <p
                    :class="formStep === 4 ? 'text-gray-800 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                    class="font-semibold"
                >
                    {{$t('public.register_stepper.join_team')}}
                </p>
                <p
                    :class="formStep === 4 ? 'text-gray-800 dark:text-gray-300' : 'text-gray-500 dark:text-gray-400'"
                    class="text-sm"
                >
                    {{$t('public.register_stepper.join_with_friends')}}
                </p>
            </div>
        </div>
    </div>
</template>
