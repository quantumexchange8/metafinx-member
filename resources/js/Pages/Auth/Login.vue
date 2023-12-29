<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { MailIcon, KeyIcon, LoginIcon, EyeOffIcon, EyeIcon } from '@heroicons/vue/outline'
import { ref } from 'vue'
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

const showPassword = ref(false);

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <GuestLayout :title="$t('public.login.login')">

        <div class="text-center">
            <h2 class="text-4xl	font-sans font-bold mb-2  text-gray-800 dark:text-white">{{ $t('public.login.login_account') }}</h2>
            <p class="font-sans text-gray-600 dark:text-gray-400">{{ $t('public.login.welcome_message') }}</p>
        </div>
        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="space-y-2">
                    <Label for="email" :value="$t('public.login.email')" />
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
                            :placeholder="$t('public.login.email_placeholder')"
                            v-model="form.email"
                            autofocus
                            autocomplete="username"
                        />
                    </InputIconWrapper>
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <Label for="password" :value="$t('public.login.password')" />
                    <InputIconWrapper>
                        <template #icon>
                            <KeyIcon aria-hidden="true" class="w-5 h-5" />
                        </template>
                        <Input
                            withIcon
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            :class="form.errors.password ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            class="block w-full"
                            :placeholder="$t('public.login.password_placeholder')"
                            v-model="form.password"
                            autocomplete="current-password"
                        />
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                            @click="togglePasswordVisibility"
                        >
                            <template v-if="showPassword">
                                <EyeIcon aria-hidden="true" class="w-5 h-5" />
                            </template>
                            <template v-else>
                                <EyeOffIcon aria-hidden="true" class="w-5 h-5" />
                            </template>
                        </div>
                        <!-- <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                            <EyeOffIcon aria-hidden="true" class="w-5 h-5" />
                        </div> -->

                    </InputIconWrapper>
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ml-2 text-sm text-gray-600 dark:text-white">{{$t('public.login.remember_token')}}</span>
                    </label>

                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-pink-500 font-semibold dark:text-gray-400 hover:underline">
                        {{$t('public.login.forgot_password')}}
                    </Link>
                </div>

                <div>
                    <Button class="justify-center gap-2 w-full" :disabled="form.processing">
                        <span>{{$t('public.login.sign_in')}}</span>
                    </Button>
                </div>

                <p class="text-center text-gray-600 dark:text-gray-400">
                    {{$t('public.login.register_message')}}
                    <Link :href="route('register')" class="text-pink-500 hover:underline font-semibold">
                        {{$t('public.login.sign_up')}}
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
