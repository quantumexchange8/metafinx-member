<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import PaymentAccount from './Partials/PaymentAccount.vue'

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    countries: Object,
    frontIdentityImg: String,
    backIdentityImg: String,
    profileImg: String,
    paymentAccounts: Object,
})
</script>

<template>
    <AuthenticatedLayout :title="$t('public.profile.profile')">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                {{$t('public.profile.my_profile')}}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{$t('public.profile.update_personal_details')}}
            </p>
        </template>

        <div>
            <div
                class="bg-white dark:bg-gray-800 sm:rounded-lg mt-6"
            >
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    :countries="countries"
                    :frontIdentityImg="frontIdentityImg"
                    :backIdentityImg="backIdentityImg"
                    :profileImg="profileImg"
                />
            </div>

            <div
                class="bg-white dark:bg-gray-800 sm:rounded-lg mt-6"
            >
                <PaymentAccount 
                    :paymentAccounts="paymentAccounts"
                />
            </div>

            <div
                class="bg-white dark:bg-gray-800 sm:rounded-lg my-[60px] mb-24"
            >
                <UpdatePasswordForm />
            </div>

            <!-- <div
                class="bg-white dark:bg-gray-800 shadow sm:rounded-lg"
            >
                <DeleteUserForm class="max-w-xl" />
            </div> -->
        </div>
    </AuthenticatedLayout>
</template>
