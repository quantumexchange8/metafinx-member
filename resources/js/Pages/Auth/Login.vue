<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { MailIcon, KeyIcon, LoginIcon } from '@heroicons/vue/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Button from '@/Components/Button.vue'
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/Guest.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import ValidationErrors from '@/Components/ValidationErrors.vue'
import InputError from "@/Components/InputError.vue";
import Caption from "@/Components/Auth/Caption.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const form = useForm({
    email: '',
    password: '',
    remember: false
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <GuestLayout title="Log in">

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <div class="text-center">
            <Caption
                title="Log in to your account"
                caption="Welcome back! Please enter your details."
            />
        </div>
        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="space-y-2">
                    <Label for="email" value="Email" />
                    <InputIconWrapper>
                        <template #icon>
                            <MailIcon aria-hidden="true" class="w-5 h-5" />
                        </template>
                        <Input
                            withIcon
                            id="email"
                            type="email"
                            :class="form.errors.email ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            class="block w-full"
                            placeholder="you@example.com"
                            v-model="form.email"
                            autofocus
                            autocomplete="username"
                        />
                    </InputIconWrapper>
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <Label for="password" value="Password" />
                    <InputIconWrapper>
                        <template #icon>
                            <KeyIcon aria-hidden="true" class="w-5 h-5" />
                        </template>
                        <Input
                            withIcon
                            id="password"
                            type="password"
                            :class="form.errors.password ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            class="block w-full"
                            placeholder="Enter password"
                            v-model="form.password"
                            autocomplete="current-password"
                        />
                    </InputIconWrapper>
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ml-2 text-sm text-white">Remember for 30 days</span>
                    </label>

                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-gray-400 hover:underline">
                        Forgot password
                    </Link>
                </div>

                <div>
                    <Button class="justify-center gap-2 w-full" :disabled="form.processing">
                        <span>Sign In</span>
                    </Button>
                </div>

                <p class="text-center text-gray-600 dark:text-gray-400">
                    Don't have an account?
                    <Link :href="route('register')" class="text-pink-500 hover:underline">
                        Sign Up
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
