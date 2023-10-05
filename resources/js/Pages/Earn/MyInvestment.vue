<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Link} from "@inertiajs/vue3";
import {transactionFormat} from "@/Composables/index.js";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    investments: Object
})

const { formatDate, formatType } = transactionFormat();
const tncModal = ref(false);

const openTncModal = () => {
    tncModal.value = true;
}

const closeModal = () => {
    tncModal.value = false
}

function calculateWidthPercentage(created_at, period) {
    const startDate = new Date(created_at);
    const endDate = new Date(startDate);
    endDate.setMonth(startDate.getMonth() + period);

    const currentDate = new Date();

    // Calculate remaining days
    const remainingMilliseconds = currentDate - startDate;
    const remainingDays = Math.ceil(remainingMilliseconds / (1000 * 60 * 60 * 24));
    const widthResult = Math.max(0, Math.min(100, (remainingDays / 365) * 100));
    const remainingMonth = Math.floor(remainingDays / 30);

    return { widthResult, remainingMonth };
}
</script>

<template>
    <AuthenticatedLayout title="My Investment">
        <template #header>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <div class="flex items-center">
                            <Link :href="route('earn.invest_subscription')" class="text-2xl font-semibold leading-tight dark:text-gray-400 dark:hover:text-white">Earn</Link>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 dark:text-white mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="text-2xl font-semibold leading-tight md:ml-2">My Investment</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </template>

        <div class="border-b pb-3 dark:border-gray-700 mt-2 mb-8">
            <div class="font-semibold">
                Current Investment
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div v-for="investment in props.investments" class="p-5 bg-white rounded-[20px] border dark:border-gray-600 dark:bg-gray-700 shadow-[0_0_12px_0] dark:shadow-[#9da4ae33]">
                <div class="flex justify-between">
                    <div class="text-xs">
                        {{ investment.investment_plan.name }} &#x2022; $ {{ investment.amount }}
                    </div>
                    <div class="dark:text-gray-400 text-xs">
                        <span class="uppercase">Since {{ formatDate(investment.created_at) }}</span>
                    </div>
                </div>
                <div class="relative my-3">
                    <div class="mb-1 flex h-2.5 overflow-hidden rounded-full bg-gray-100 text-xs">
                        <div
                            :style="{ width: `${calculateWidthPercentage(investment.created_at, investment.investment_plan.investment_period).widthResult}%` }"
                            class="rounded-full bg-gradient-to-r from-warning-400 to-pink-500 transition-all duration-500 ease-out"
                        >
                        </div>
                    </div>
                    <div class="mb-2 flex items-center justify-between text-xs">
                        <div class="dark:text-gray-400">
                            1
                        </div>
                        <div class="dark:text-gray-400">
                            {{ investment.investment_plan.investment_period/4 }}
                        </div>
                        <div class="dark:text-gray-400">
                            {{ investment.investment_plan.investment_period/2 }}
                        </div>
                        <div class="dark:text-gray-400">{{ investment.investment_plan.investment_period }}</div>
                    </div>
                </div>
                <div class="flex justify-between mb-1">
                    <div class="dark:text-gray-400 text-xs">
                        Situation
                    </div>
                    <div class="dark:text-white text-xs">
                        <span class="uppercase">{{ formatType(investment.status) }}</span>
                    </div>
                </div>
                <div class="flex justify-between mb-1">
                    <div class="dark:text-gray-400 text-xs">
                        Next ROI release on
                    </div>
                    <div class="dark:text-white text-xs">
                        <span class="uppercase">{{ formatDate(investment.next_roi_date) }}</span>
                    </div>
                </div>
                <div class="flex justify-between mb-1">
                    <div class="dark:text-gray-400 text-xs">
                        Valid thru
                    </div>
                    <div class="dark:text-white text-xs">
                        <span class="uppercase">{{ formatDate(investment.expired_date) }}</span>
                    </div>
                </div>
                <div class="flex justify-between mb-1">
                    <div class="dark:text-gray-400 text-xs">
                        ID Number
                    </div>
                    <div class="dark:text-white text-xs">
                        <span class="uppercase">{{ investment.subscription_id }}</span>
                    </div>
                </div>
                <div class="mt-4 text-xs">
                    <span class="dark:text-gray-400">*T&Cs apply.</span><span class="dark:text-white underline cursor-pointer dark:hover:text-gray-300" @click="openTncModal">Learn more.</span>
                </div>
            </div>
        </div>

        <div v-if="investments.length === 0" class="flex flex-col items-center justify-center my-8">
            <img src="/assets/no_data.png" class="w-1/2" alt="">
            <div class="dark:text-gray-400 mt-3">
                No data to show
            </div>
        </div>

        <Modal :show="tncModal" title="Terms & Conditions" @close="closeModal">
            <div class="text-xs font-medium dark:text-gray-400 my-4">
                IMPORTANT: Please carefully read and understand the following terms and conditions before subscribing to our investment services. Your subscription is subject to your acceptance of these terms.
            </div>
            <div class="text-xs font-normal dark:text-gray-400 mb-4">
                By clicking "Confirm" below, you agree to the following terms and conditions:
            </div>

            <ol class="text-[10px] list-decimal list-inside dark:text-gray-400">
                <li>
                    Subscription Agreement
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>Eligibility: By subscribing to our investment services, you confirm that you are at least 21 years of age and have the legal capacity to enter into this agreement.</li>
                        <li>Subscription Fee: You agree to pay the subscription fee as displayed on our website at the time of subscription. This fee is non-refundable.</li>
                        <li>Subscription Term: The subscription term will be specified during the sign-up process and may vary depending on the plan you choose.</li>
                    </ul>
                </li>
                <li>
                    Investment Services
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>Investment Strategy: We will provide investment advisory and management services based on the strategy and risk tolerance you specify during the subscription process. We do not guarantee any specific investment results or returns.</li>
                        <li>Risk Disclosure: You acknowledge that all investments involve risk, and past performance is not indicative of future results. We are not liable for any losses incurred as a result of your investment decisions.</li>
                        <li>Information Accuracy: You are responsible for providing accurate and complete information to us for the provision of our services. We are not liable for any losses resulting from inaccurate or incomplete information provided by you.</li>
                    </ul>
                </li>
                <li>
                    Confidentiality and Privacy
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>Confidentiality: We will maintain the confidentiality of your personal and financial information in accordance with applicable privacy laws and regulations.</li>
                        <li>Data Usage: We may collect and use your data for the purpose of providing our services. By subscribing, you consent to the collection and use of your data in accordance with our Privacy Policy.</li>
                    </ul>
                </li>
                <li>
                    Termination
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>Termination by Us: We reserve the right to terminate your subscription and access to our services at our sole discretion, with or without cause, by providing notice to you.</li>
                    </ul>
                </li>
                <li>
                    Limitation of Liability
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>No Liability: We shall not be liable for any direct, indirect, incidental, special, or consequential damages arising from or related to our services, including but not limited to losses on investments.</li>
                    </ul>
                </li>
                <li>
                    Miscellaneous
                    <ul class="pl-2 space-y-1 list-disc list-inside">
                        <li>Amendment: We reserve the right to amend these Terms and Conditions at any time. Notice of such amendments will be provided to you, and your continued use of our services constitutes acceptance of the amended terms.</li>
                        <li>Entire Agreement: This Agreement constitutes the entire agreement between you and us with respect to our services and supersedes all prior agreements and understandings.</li>
                    </ul>
                </li>
            </ol>

        </Modal>
    </AuthenticatedLayout>

</template>