<script setup>
import { computed } from 'vue'
import {Link, useForm, usePage} from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/Guest.vue'
import Button from '@/Components/Button.vue'
import {MailIcon} from "@heroicons/vue/outline";

const props = defineProps({
    status: String
})

const page = usePage();

const form = useForm({
    'user': page.props.auth.user
})

const submit = () => {
    form.post(route('verification.send'))
}

const verificationLinkSent = computed(() => props.status === 'verification-link-sent')
</script>

<template>
    <GuestLayout title="Email Verification">
        <div class="flex flex-col items-center mb-8">
            <div>
                <div class="flex items-center justify-center w-10 h-10 border rounded-lg dark:border-gray-400">
                    <MailIcon aria-hidden="true" class="h-6 w-6 text-white"/>
                </div>
            </div>
            <div class="my-3 text-[28px] font-semibold text-gray-600 dark:text-white">
                Verify Email
            </div>
            <div class="mb-4 text-[16px] text-gray-600 dark:text-gray-400 font-normal text-center">
                Thanks for signing up! Before getting started, could you verify your
                email address by clicking on the link we just emailed to you? If you
                didn't receive the email, we will gladly send you another.
            </div>
        </div>

        <div
            class="mb-4 text-sm font-medium text-green-600 text-center"
            v-if="verificationLinkSent"
        >
            A new verification link has been sent to the email address you provided
            during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="flex flex-col gap-3 mt-4">
                <Button
                    class="w-full flex justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </Button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-[16px] text-gray-600 hover:underline dark:text-gray-400"
                >
                    Log Out
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
