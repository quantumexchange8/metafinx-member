<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import InputError from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'
import Modal from "@/Components/Modal.vue";
import Badge from "@/Components/Badge.vue"
import PaymentAccountDetail from "@/Pages/Profile/Partials/PaymentAccountDetail.vue"
import { DuplicateIcon } from "@heroicons/vue/outline";
import toast from "@/Composables/toast.js";
import { trans } from 'laravel-vue-i18n';
import Tooltip from "@/Components/Tooltip.vue";
import { CreditEditIcon } from "@/Components/Icons/outline.jsx";

const props = defineProps({
    paymentAccounts: Object,
})

const form = useForm({
    payment_platform_name: 'USDT (TRC20)',
    payment_account_name: '',
    account_no: '',
})
const createPaymentAccountModal = ref(false);
const paymentModal = ref(false);
const paymentDetails = ref(null);

const openCreatePaymentAccountModal = () => {
    createPaymentAccountModal.value = true;
}

const closeCreatePaymentAccountModal = () => {
    createPaymentAccountModal.value = false
}

const openModal = (paymentAccount) => {
    paymentModal.value = true;
    paymentDetails.value = paymentAccount;
}

const closeModal = () => {
    paymentModal.value = false
}

const handleCloseModal = () => {
    closeModal(); 
}

const submit = async () => {
    await form.post('/addPaymentAccount', {
        onSuccess: () => {
            form.reset()
            closeCreatePaymentAccountModal()
        },
    })
}

const statusVariant = (transactionStatus) => {
    if (transactionStatus === 'Active') return 'success';
    if (transactionStatus === 'Inactive') return 'danger';
}

const copyAccountNumber = (accountNumber) => {
    const tempInput = document.createElement('input');
    tempInput.value = accountNumber;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    toast.add({
        message: trans('public.copy_success'),
    });
}


</script>

<template>
    <section>
        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <header class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{$t('public.profile.payment_account')}}
                    </h2>
                </div>
            </header>
            <hr class="h-px mt-3 bg-gray-200 border-0 dark:bg-gray-700">

            <div class="space-y-5">
                <div v-if="paymentAccounts.length === 0" class="flex justify-center">
                    No Payment Accounts
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div v-for="paymentAccount in paymentAccounts" class="flex flex-col overflow-hidden rounded-[20px] w-96 border border-gray-00 dark:border-gray-800">
                        <div
                            class="flex justify-between h-32 bg-gradient-to-bl from-gray-300 to-gray-500"
                        >
                            <div class="py-5 px-4 flex flex-col gap-2">
                                <div class="flex flex-col">
                                    <div class="text-base font-semibold text-gray-100 dark:text-white">
                                        {{ paymentAccount.payment_account_name }}
                                    </div>
                                    <div class="text-xl font-semibold text-gray-100 dark:text-white flex items-center">
                                        <span>{{ paymentAccount.account_no }}</span>
                                        <div @click.prevent="copyAccountNumber(paymentAccount.account_no)" class="ml-2">
                                            <DuplicateIcon class="w-5 hover:cursor-pointer" />
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <Tooltip :content="$t('public.profile.payment_account_detail')" placement="right">
                                            <Button
                                                type="button"
                                                class="justify-center p-1 w-8 h-8 relative focus:outline-none dark:bg-[#ffffff32]"
                                                variant="transparent"
                                                @click="openModal(paymentAccount)"
                                                pill
                                            >
                                                <CreditEditIcon aria-hidden="true" class="w-5 h-5 absolute" />
                                                <span class="sr-only">{{ $t('public.profile.payment_account_detail') }}</span>
                                            </Button>
                                        </Tooltip>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <Button @click="openCreatePaymentAccountModal" :disabled="form.processing">{{$t('public.profile.add_payment_account')}}</Button>
            </div>
        </form>
    </section>

    <Modal :show="paymentModal" :title="$t('public.profile.payment_account_detail')" @close="closeModal" max-width="lg">
        <PaymentAccountDetail :paymentDetails="paymentDetails" @closeModal="handleCloseModal"/>
    </Modal>

    <Modal :show="createPaymentAccountModal" :title="$t('public.profile.payment_account')" @close="closeCreatePaymentAccountModal">
        <form class="space-y-4">
            <div class="space-y-2">
                <!-- <CryptoSetting/> -->
                <div class="space-y-2">
                    <Label
                        for="crypto_name"
                        :value="$t('public.profile.tether')"
                    />
                    <Input
                        id="crypto_name"
                        type="text"
                        class="block w-full"
                        v-model="form.payment_platform_name"
                        readonly
                        :invalid="form.errors.payment_platform_name"
                    />
                    <InputError :message="form.errors.payment_platform_name" />
                </div>
                <div class="space-y-2">
                    <Label
                        for="crypto_account_name"
                        :value="$t('public.profile.crypto_wallet_name')"
                    />
                    <Input
                        id="crypto_account_name"
                        type="text"
                        class="block w-full"
                        v-model="form.payment_account_name"
                        :invalid="form.errors.payment_account_name"
                    />
                    <InputError :message="form.errors.payment_account_name" />
                </div>
                <div class="space-y-2">
                    <Label
                        for="account_number"
                        :value="$t('public.profile.wallet_address')"
                    />
                    <Input
                        id="account_number"
                        type="text"
                        min="0"
                        class="block w-full"
                        v-model="form.account_no"
                        :invalid="form.errors.account_no"
                    />
                    <InputError :message="form.errors.account_no" />
                </div>
            </div>

            <div class="pt-5 flex justify-end">
                <Button
                    class="flex justify-center"
                    @click="submit"
                    :disabled="form.processing"
                >
                    {{ $t('public.profile.save_changes') }}
                </Button>
            </div>
        </form>
    </Modal>

</template>

