<script setup>
import { useForm } from '@inertiajs/vue3'
import {MailIcon, LockClosedIcon, KeyIcon, EyeIcon, EyeOffIcon} from '@heroicons/vue/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Button from '@/Components/Button.vue'
import GuestLayout from '@/Layouts/Guest.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import ValidationErrors from '@/Components/ValidationErrors.vue'
import InputError from "@/Components/InputError.vue";
import {ref} from "vue";

const props = defineProps({
    email: String,
    token: String,
})

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}

const showPassword = ref(false);
const showPassword2 = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordVisibilityConfirm = () => {
    showPassword2.value = !showPassword2.value;
}

</script>

<template>
    <GuestLayout :title="$t('public.reset_password.reset_password')">
        <div class="flex flex-col items-center mb-8">
            <div>
                <div class="flex items-center justify-center w-10 h-10 border rounded-lg dark:border-gray-400">
                    <KeyIcon aria-hidden="true" class="h-6 w-6 text-white"/>
                </div>
            </div>
            <div class="my-3 text-[28px] font-semibold text-gray-600 dark:text-white">
                {{$t('public.reset_password.choose_password')}}
            </div>
            <div class="text-[16px] text-gray-400 font-normal">
                {{$t('public.reset_password.password_restriction')}}
            </div>
        </div>

        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="space-y-2">
                    <Label for="email" :value="$t('public.reset_password.email')" />
                    <InputIconWrapper>
                        <template #icon>
                            <MailIcon aria-hidden="true" class="w-5 h-5" />
                        </template>
                        <Input withIcon id="email" type="email" :placeholder="$t('public.reset_password.email_placeholder')" class="block w-full" v-model="form.email" readonly required autofocus autocomplete="username" />
                    </InputIconWrapper>
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <Label for="password" :value="$t('public.reset_password.password')" />
                    <InputIconWrapper>
                        <template #icon>
                            <LockClosedIcon aria-hidden="true" class="w-5 h-5" />
                        </template>
                        <Input
                            withIcon
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            :placeholder="$t('public.reset_password.password_placeholder')"
                            class="block w-full"
                            v-model="form.password"
                            :class="form.errors.password ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            autocomplete="new-password"
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
                    </InputIconWrapper>
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <Label for="password_confirmation" :value="$t('public.reset_password.confirm_password')" />
                    <InputIconWrapper>
                        <template #icon>
                            <LockClosedIcon aria-hidden="true" class="w-5 h-5" />
                        </template>
                        <Input
                            withIcon
                            id="password_confirmation"
                            :type="showPassword2 ? 'text' : 'password'"
                            :placeholder="$t('public.reset_password.confirm_password_placeholder')"
                            class="block w-full"
                            v-model="form.password_confirmation"
                            autocomplete="new-password"
                            :class="form.errors.password_confirmation ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                        />
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                            @click="togglePasswordVisibilityConfirm"
                        >
                            <template v-if="showPassword2">
                                <EyeIcon aria-hidden="true" class="w-5 h-5" />
                            </template>
                            <template v-else>
                                <EyeOffIcon aria-hidden="true" class="w-5 h-5" />
                            </template>
                        </div>
                    </InputIconWrapper>
                </div>

                <div>
                    <Button class="w-full justify-center" :disabled="form.processing">
                        {{$t('public.reset_password.reset_password')}}
                    </Button>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
