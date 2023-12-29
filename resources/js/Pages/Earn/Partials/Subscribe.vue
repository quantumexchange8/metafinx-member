<script setup>
import Button from "@/Components/Button.vue";
import {ref, watch} from "vue";
import Modal from "@/Components/Modal.vue";
import {checkIcon} from '@/Components/Icons/outline'
import BaseListbox from "@/Components/BaseListbox.vue";
import Input from "@/Components/Input.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    plan: Object,
    wallet_sel: Array
})

const subscribeModal = ref(false);
const housingPrice = ref(null);
const amountCalculation = ref(0);

const openSubscribeModal = () => {
    subscribeModal.value = true
}

const closeModal = () => {
    subscribeModal.value = false
}

const form = useForm({
    investment_plan_id: props.plan.id,
    wallet_id: '',
    unit_number: '',
    amount: '',
    housing_price: '',
    terms: false
})
watch(housingPrice, (newValue) => {
    let number = newValue / 100;
    // Divide by 100 and round down to the nearest 100
    amountCalculation.value = Math.floor(number / 100) * 100;
});

const submit = () => {
    if (props.plan.type === 'ebmi') {
        form.housing_price = housingPrice.value
        form.amount = amountCalculation.value.toFixed()
    } else {
        form.unit_number = undefined
        form.housing_price = undefined
    }
    form.post(route('earn.subscribe'), {
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    });
};
</script>

<template>
    <Button
        type="button"
        class="w-full flex justify-center uppercase font-semibold text-sm"
        @click="openSubscribeModal"
    >
    {{$t('public.earn.subscribe')}}
    </Button>

    <Modal :show="subscribeModal" :title="$t('public.earn.subscribe')" @close="closeModal">
        <div class="p-5 bg-white rounded-xl shadow-md dark:bg-gray-700">
            <div class="grid grid-cols-2 gap gap-5">
                <div class="flex flex-col gap-2 items-center justify-center">
                    <img class="w-10 h-10 rounded bg-white" src="/assets/icon.png" alt="Medium avatar">
                    <div class="font-semibold dark:text-white">
                        {{ plan.name }}
                    </div>
                    <div class="font-semibold dark:text-white text-2xl md:text-[32px]">
                        {{ plan.roi_per_annum }} p.a.
                    </div>
                </div>
                <div class="flex flex-col gap-2 dark:text-gray-300">
                    <div v-for="item in plan.descriptions" class="mb-3">
                        <div class="inline-flex items-center gap-2 text-xs">
                            <checkIcon />
                            {{ item.description }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form class="pt-8">
            <div class="flex flex-col sm:flex-row gap-4">
                <Label class="text-sm dark:text-white w-full md:w-1/4 pt-0.5" for="amount" :value="$t('public.earn.wallet_selection')" />
                <div class="flex flex-col w-full">
                    <BaseListbox
                        v-model="form.wallet_id"
                        :options="wallet_sel"
                        :error="form.errors.wallet_id"
                        :placeholder="$t('public.earn.wallet_selection_placeholder')"
                    />
                </div>
            </div>

            <div v-if="plan.type === 'ebmi'" class="grid sm:flex gap-4 mt-5">
                <Label class="text-sm dark:text-white w-1/4" for="unit_number" :value="$t('public.earn.unit_number')" />
                <div class="flex flex-col w-full">
                    <Input
                        id="unit_number"
                        type="text"
                        :placeholder="$t('public.earn.unit_number_placeholder')"
                        class="block w-full"
                        :class="form.errors.unit_number ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                        v-model="form.unit_number"
                        />
                    <InputError :message="form.errors.unit_number" class="mt-2" />
                </div>
            </div>

            <div v-if="plan.type === 'ebmi'" class="grid sm:flex gap-4 mt-5">
                <Label class="text-sm dark:text-white w-full sm:w-1/4" for="amount" :value="$t('public.earn.housing_price')" />
                <div class="flex flex-col w-full">
                    <Input
                        id="amount"
                        type="number"
                        min="0"
                        step="100"
                        placeholder="$ 0.00"
                        class="block w-full"
                        :class="form.errors.housing_price ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                        v-model="housingPrice"
                    />
                    <InputError :message="form.errors.housing_price" class="mt-2" />
                    <span class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{$t('public.earn.investment_amount')}} $ {{ amountCalculation }}</span>
                    <span class="text-xs text-gray-500 mt-2">{{$t('public.earn.investment_amount_rule')}}</span>
                </div>
            </div>

            <div v-else class="grid sm:flex gap-4 mt-5">
                <Label class="text-sm dark:text-white w-full md:w-1/4" for="amount" :value="$t('public.earn.input_amount')" />
                <div class="flex flex-col w-full">
                    <Input
                        id="amount"
                        type="number"
                        min="0"
                        step="100"
                        placeholder="$ 0.00"
                        class="block w-full"
                        :class="form.errors.amount ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                        v-model="form.amount"
                        />
                    <InputError :message="form.errors.amount" class="mt-2" />
                    <span class="text-xs text-gray-500 mt-2">{{$t('public.earn.investment_amount_rule')}}</span>
                </div>
            </div>

            <div class="mt-6 pb-6 border-b dark:border-gray-700">
                <label>
                    <div class="flex">
                        <Checkbox name="remember" v-model:checked="form.terms" />
                        <span class="ml-2 text-xs dark:text-gray-400">{{$t('public.agreement')}}</span>
                    </div>
                    <InputError v-if="form.errors.terms" :message="form.errors.terms" class="mt-2" />
                </label>

            </div>

            <div class="py-5 grid grid-cols-2 gap-4 w-full md:w-1/3 md:float-right">
                <Button variant="secondary" type="button" class="justify-center" @click.prevent="closeModal">
                    {{$t('public.cancel')}}
                </Button>
                <Button class="justify-center" @click="submit" :disabled="form.processing">{{$t('public.confirm')}}</Button>
            </div>
        </form>
    </Modal>
</template>
