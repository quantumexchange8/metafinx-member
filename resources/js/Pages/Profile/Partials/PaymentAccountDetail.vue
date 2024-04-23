<script setup>
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import { useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue';
import BaseListbox from "@/Components/BaseListbox.vue";
import {
  RadioGroup,
  RadioGroupLabel,
  RadioGroupDescription,
  RadioGroupOption,
} from '@headlessui/vue'
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    paymentDetails: Object,
    countries: Array,
    currencies: Array,
})

const currency = ref('MYR');
const emit = defineEmits(['update:closeModal']);

const status = [
  {
    name: 'Active',
    value: 'Active'
  },
  {
    name: 'Inactive',
    value: 'Inactive'
  },
]

const selected = ref(status.find(plan => plan.value === props.paymentDetails.status) || status[0]);

const form = useForm({
    id: props.paymentDetails.id,
    payment_platform: props.paymentDetails.payment_platform,
    payment_platform_name: props.paymentDetails.payment_platform_name,
    payment_account_name: props.paymentDetails.payment_account_name,
    account_no: props.paymentDetails.account_no,
    status: ''
})

const closeModal = () => {
    emit('closeModal'); // Emitting the 'closeModal' event
}

const submit = async () => {
    form.status = selected.value.value;
    await form.post(route('updatePaymentAccount', { id: props.paymentDetails.id }), {
        onSuccess: () => {
            form.reset();
            closeModal();
        },
    });
};
</script>

<template>
    
    <form>
        <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
            <div class="space-y2">
                <Label
                    for="bank_name"
                    :value="$t('public.profile.tether')"
                />
                <Input
                    id="bank_name"
                    type="text"
                    class="block w-full"
                    v-model="form.payment_platform_name"
                    readonly
                    autofocus
                    autocomplete="bank name"
                />
                <InputError :message="form.errors.payment_platform_name" />
            </div>
            <div class="space-y2">
                <Label
                    for="bank_name"
                    :value="$t('public.profile.crypto_wallet_name')"
                />
                <Input
                    id="bank_account_name"
                    type="text"
                    class="block w-full"
                    v-model="form.payment_account_name"
                    autofocus
                    autocomplete="bank account name"
                />
                <InputError :message="form.errors.payment_account_name" />
            </div>
            <div class="space-y2">
                <Label
                    for="account_no"
                    :value="$t('public.profile.wallet_address')"
                />
                <Input
                    id="account_no"
                    type="text"
                    class="block w-full"
                    v-model="form.account_no"
                    autofocus
                    autocomplete="account number"
                />
                <InputError :message="form.errors.account_no" />
            </div>

            <div class="w-full space-y2">
                <Label
                    for="status"
                    :value="$t('public.profile.status')"
                />
                <div class="w-full">
                    <RadioGroup v-model="selected" class="flex gap-1 md:gap-4 flex-col md:flex-row">
                        <div class="flex flex-row w-full gap-4">
                            <RadioGroupOption
                                as="template"
                                v-for="plans in status"
                                :key="plans.name"
                                :value="plans"
                                v-slot="{ active, checked }"
                            >
                                <div
                                    :class="[
                                        checked ? 'bg-primary-500 text-white dark:text-white w-full' : 'bg-white w-full dark:bg-gray-700 dark:text-white',
                                    ]"
                                    class="relative flex cursor-pointer rounded-lg px-5 py-4 shadow-md focus:outline-none"
                                >
                                    <div class="flex w-full items-center justify-between">
                                        <div class="flex items-center">
                                        <div class="text-sm">
                                            <RadioGroupLabel
                                            as="p"
                                            :class="checked ? 'text-white dark:text-white' : 'text-gray-900 dark:text-white'"
                                            class="font-medium"
                                            >
                                            {{ plans.name }}
                                            </RadioGroupLabel>
                                            
                                        </div>
                                        </div>
                                        <div v-show="checked" class="shrink-0 text-white">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                                <circle
                                                cx="12"
                                                cy="12"
                                                r="12"
                                                fill="#fff"
                                                fill-opacity="0.2"
                                                />
                                                <path
                                                d="M7 13l3 3 7-7"
                                                stroke="#fff"
                                                stroke-width="1.5"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </RadioGroupOption>
                        </div>
                    </RadioGroup>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button @click="submit" :disabled="form.processing">
                    <span>Save</span>
                </Button>
            </div>
        </div>
    </form>
    
</template>