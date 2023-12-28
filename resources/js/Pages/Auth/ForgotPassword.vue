<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { MailIcon, PaperAirplaneIcon, KeyIcon, ArrowNarrowLeftIcon } from '@heroicons/vue/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Button from '@/Components/Button.vue'
import GuestLayout from '@/Layouts/Guest.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import ValidationErrors from '@/Components/ValidationErrors.vue'

defineProps({
    status: String
})

const form = useForm({
    email: ''
})

const submit = () => {
    form.post(route('password.email'))
}
</script>

<template>
    <GuestLayout :title="$t('public.forgot_password.forgot_password')">
        <div class="flex flex-col items-center mb-8">
            <div>
                <div class="flex items-center justify-center w-10 h-10 border rounded-lg shadow dark:border-gray-400">
                    <KeyIcon aria-hidden="true" class="h-6 w-6 text-gray-800 dark:text-white"/>
                </div>
            </div>
            <div class="my-3 text-[28px] font-semibold text-gray-600 dark:text-white">
                {{$t('public.forgot_password.forgot_password_label')}}
            </div>
            <div class="text-[16px] text-gray-600 dark:text-gray-400 font-normal">
                {{$t('public.forgot_password.forgot_password_message')}}
            </div>
        </div>

        <ValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="space-y-2">
                    <Label for="email" :value="$t('public.forgot_password.email')" />
                    <InputIconWrapper>
                        <template #icon>
                            <MailIcon aria-hidden="true" class="w-5 h-5 text-gray-400" />
                        </template>
                        <Input
                            withIcon
                            id="email"
                            type="email"
                            class="block w-full"
                            :placeholder="$t('public.forgot_password.email_placeholder')"
                            v-model="form.email"
                            autofocus
                            :class="form.errors.email ? 'border border-error-500 dark:border-error-500' : 'border border-gray-300 dark:border-gray-600'"
                            autocomplete="email" />
                    </InputIconWrapper>
                </div>
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <div>
                    <Button class="justify-center gap-2 w-full" :disabled="form.processing" v-slot="{ iconSizeClasses }">
                        <span>{{$t('public.forgot_password.reset_password')}}</span>
                    </Button>


                </div>
                <div class="flex justify-center">
                    <div class="flex">
                        <Link :href="route('login')" class="text-base flex items-center text-gray-400 hover:underline">
                            <ArrowNarrowLeftIcon class="text-pink-500 dark:text-gray-400 w-5 mr-2" />
                            <span class="text-pink-500 dark:text-white font-semibold">{{$t('public.forgot_password.Back_to_login')}}</span>
                        </Link>
                    </div>

                </div>
            </div>
        </form>
    </GuestLayout>
</template>
