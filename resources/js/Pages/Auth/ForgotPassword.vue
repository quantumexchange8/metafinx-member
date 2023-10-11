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
    <GuestLayout title="Forgot Password">
        <div class="flex flex-col items-center mb-8">
            <div>
                <div class="flex items-center justify-center w-10 h-10 border rounded-lg dark:border-gray-400">
                    <KeyIcon aria-hidden="true" class="h-6 w-6 text-white"/>
                </div>
            </div>
            <div class="my-3 text-[28px] font-semibold text-gray-600 dark:text-white">
                Forgot password?
            </div>
            <div class="text-[16px] text-gray-400 font-normal">
                No worries, we'll send you reset instructions.
            </div>
        </div>

        <ValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="space-y-2">
                    <Label for="email" value="Email" />
                    <InputIconWrapper>
                        <template #icon>
                            <MailIcon aria-hidden="true" class="w-5 h-5" />
                        </template>
                        <Input withIcon id="email" type="email" class="block w-full" placeholder="you@example.com" v-model="form.email" required autofocus autocomplete="username" />
                    </InputIconWrapper>
                </div>
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <div>
                    <Button class="justify-center gap-2 w-full" :disabled="form.processing" v-slot="{ iconSizeClasses }">
                        <span>Reset Password</span>
                    </Button>


                </div>
                <div class="flex justify-center">
                    <div class="flex">
                        <Link :href="route('login')" class="text-base flex items-center text-gray-400 hover:underline">
                            <ArrowNarrowLeftIcon class="text-gray-400 w-5 mr-2" />
                            Back to log in
                        </Link>
                    </div>

                </div>
            </div>
        </form>
    </GuestLayout>
</template>
