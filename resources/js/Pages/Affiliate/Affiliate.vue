<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import {ShareIcon, DuplicateIcon, ArrowLeftIcon, ArrowRightIcon} from "@heroicons/vue/outline";
import { TableIcon } from '@/Components/Icons/outline'
import Tooltip from "@/Components/Tooltip.vue";
import {ref} from "vue";
import toast from "@/Composables/toast.js";
import ReferralTree from "@/Pages/Affiliate/ReferralTree.vue";
import {transactionFormat} from "@/Composables/index.js";
import AffiliateNetwork from "@/Pages/Affiliate/AffiliateNetwork.vue";
import {usePage} from "@inertiajs/vue3";
import InvestmentTable from "@/Pages/Report/History/InvestmentTable.vue";
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    referredCounts: Number,
    totalReferralEarning: Number,
    totalAffiliateEarning: Number,
    totalBinaryReferralEarning: Number,
    totalPairingEarning: Number,
    downline: Array,
    uplineStaking: Boolean,
    checkCoinStaking: Boolean,
})
const { formatAmount,formatDate } = transactionFormat();
const createdDate = usePage().props.auth.user.created_at;
const affiliateType = ref('affiliate');

const tooltipContent = ref('copy');
function copyReferralCode () {
    const referralCodeCopy = document.querySelector('#referralCode').value;
    const tempInput = document.createElement('input');
    tempInput.value = referralCodeCopy;
    document.body.appendChild(tempInput);
    tempInput.select();

    try {
        var successful = document.execCommand('copy');
        if (successful) {
            tooltipContent.value = 'copied';
            setTimeout(() => {
                tooltipContent.value = 'copy'; // Reset tooltip content to 'Copy' after 2 seconds
            }, 1000);
        } else {
            tooltipContent.value = 'try_again_later';
        }

    } catch (err) {
        alert('copy_error');
    }
    document.body.removeChild(tempInput);
    window.getSelection().removeAllRanges()
}

const copyReferralCodeLink = (position) => {
    const referralCode = document.querySelector('#referralCode').value;
    let url = window.location.origin + '/register/' + referralCode;

    if (position) {
        url += `?position=${position}`;
    }

    const tempInput = document.createElement('input');
    tempInput.value = url;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    toast.add({
        message: trans('public.link_copied')
    });
};

</script>

<template>
    <AuthenticatedLayout :title="$t('public.affiliate.affiliate')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight">
                        {{$t('public.affiliate.affiliate')}}
                    </h2>
                    <p class="text-base font-normal dark:text-gray-400">
                        {{$t('public.affiliate.proproganda')}}
                    </p>
                </div>

                <div>
                    <Button
                        external
                        type="button"
                        variant="warning"
                        class="items-center gap-2 max-w-xs"
                        v-slot="{ iconSizeClasses }"
                        :href="route('affiliate.group')"
                        :referredCounts="referredCounts"
                        :totalReferralEarning="totalReferralEarning"
                    >
                        <div class="inline-flex items-center">
                            <TableIcon
                                aria-hidden="true"
                                class="mr-2"
                            />
                            <span>{{$t('public.affiliate.my_group')}}</span>
                        </div>
                    </Button>
                </div>
            </div>
        </template>

        <AffiliateNetwork
            :downline="downline"
            :uplineStaking="uplineStaking"
            :checkCoinStaking="checkCoinStaking"
            @update:affiliateType="affiliateType = $event"
         />

        <div class="flex flex-col space-y-6 pt-8 pb-12 mb-16 md:hidden">
            <h3 class="text-xl font-semibold leading-tight">
                {{$t('public.affiliate.affiliate_program')}}
            </h3>
            <div v-if="affiliateType === 'affiliate'" class="p-5 dark:bg-gray-700 rounded-xl flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.affiliate.total_referral_earning')}} {{ formatDate(createdDate) }}
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $&nbsp;{{ formatAmount(totalReferralEarning) }}
                </div>
            </div>
            <div v-if="affiliateType === 'affiliate'" class="p-5 dark:bg-gray-700 rounded-xl flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.affiliate.total_affiliate_earning')}} {{ formatDate(createdDate) }}
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ {{ totalAffiliateEarning ? formatAmount(totalAffiliateEarning, 2) : '0.00' }}
                </div>
            </div>
            <div v-if="affiliateType === 'binary'" class="p-5 dark:bg-gray-700 rounded-xl flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.affiliate.total_referral_earning')}} {{ formatDate(createdDate) }}
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ {{ totalBinaryReferralEarning ? formatAmount(totalBinaryReferralEarning, 2) : '0.00' }}
                </div>
            </div>
            <div v-if="affiliateType === 'binary'" class="p-5 dark:bg-gray-700 rounded-xl flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    {{$t('public.affiliate.total_pairing_earning')}} {{ formatDate(createdDate) }}
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ {{ totalPairingEarning ? formatAmount(totalPairingEarning, 2) : '0.00' }}
                </div>
            </div>
            <div class="p-5 dark:bg-gray-700 rounded-xl flex flex-col items-center justify-center gap-8 pb-12">
                <div class="grid gap-2 font-medium text-base dark:text-gray-400 text-center">
                    {{$t('public.affiliate.referred')}}
                    <div>
                        <span class="dark:text-white font-semibold">{{ props.referredCounts }}</span> <span class="dark:text-gray-400">{{$t('public.affiliate.members')}}</span>
                    </div>
                </div>
                <div class="flex rounded-md shadow-sm">
                    <input type="text" id="referralCode" readonly :value="$page.props.auth.user.referral_code" class="py-2 px-4 block w-full border-transparent shadow-sm rounded-l-lg text-sm focus:z-10 focus:border-pink-700 focus:ring focus:ring-pink-500 focus:ring-offset-0 focus:ring-offset-white dark:bg-gray-600 dark:text-white text-center">
                    <Tooltip :content="$t('public.' + tooltipContent)" placement="top">
                        <button
                            type="button"
                            class="py-2 px-4 inline-flex flex-shrink-0 justify-center items-center gap-2 rounded-r-lg border border-transparent font-semibold dark:bg-gray-500 text-white dark:hover:bg-gray-400 focus:z-10 focus:outline-none transition-all text-sm uppercase"
                            @click="copyReferralCode"
                        >
                            <DuplicateIcon
                                aria-hidden="true"
                                class="h-5"
                            />
                            {{$t('public.copy')}}
                        </button>
                    </Tooltip>
                </div>
                <div class="flex flex-col gap-2 w-full">
                    <div class="flex flex-col items-center">
                        <div class="inline-flex justify-center items-center w-full">
                            <div class="uppercase font-semibold">{{$t('public.affiliate.invite_friend')}}</div>
                            <ShareIcon
                                aria-hidden="true"
                                class="h-5 ml-2"
                            />
                        </div>
                        <div>({{ $t('public.binary_placement_position') }})</div>
                    </div>

                    <div class="inline-flex justify-center items-center w-full">

                    </div>

                    <div class="flex gap-4 w-full">
                        <Button
                            type="button"
                            class="flex justify-center items-center gap-2 w-full"
                            @click="copyReferralCodeLink('left')"
                            v-slot="{ iconSizeClasses }"
                        >
                            <ArrowLeftIcon class="w-5 h-5" />
                            <div>{{$t('public.left')}}</div>
                        </Button>
                        <Button
                            type="button"
                            class="flex justify-center items-center gap-2 w-full"
                            :disabled="!checkCoinStaking"
                            @click="copyReferralCodeLink('right')"
                        >
                            <div>{{$t('public.right')}}</div>
                            <ArrowRightIcon class="w-5 h-5" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <template #asideRight>
            <div class="inset-y-0 p-6 flex flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 w-96 fixed right-0">
                <h3 class="text-xl font-semibold leading-tight">
                    {{$t('public.affiliate.affiliate_program')}}
                </h3>
                <div v-if="affiliateType === 'affiliate'" class="p-5 dark:bg-gray-700 rounded-xl flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        {{$t('public.affiliate.total_referral_earning')}} {{ formatDate(createdDate) }}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $&nbsp;{{ formatAmount(totalReferralEarning) }}
                    </div>
                </div>
                <div v-if="affiliateType === 'affiliate'" class="p-5 dark:bg-gray-700 rounded-xl flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        {{$t('public.affiliate.total_affiliate_earning')}} {{ formatDate(createdDate) }}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ {{ totalAffiliateEarning ? formatAmount(totalAffiliateEarning, 2) : '0.00' }}
                    </div>
                </div>
                <div v-if="affiliateType === 'binary'" class="p-5 dark:bg-gray-700 rounded-xl flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        {{$t('public.affiliate.total_referral_earning')}} {{ formatDate(createdDate) }}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ {{ totalBinaryReferralEarning ? formatAmount(totalBinaryReferralEarning, 2) : '0.00' }}
                    </div>
                </div>
                <div v-if="affiliateType === 'binary'" class="p-5 dark:bg-gray-700 rounded-xl flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        {{$t('public.affiliate.total_pairing_earning')}} {{ formatDate(createdDate) }}
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ {{ totalPairingEarning ? formatAmount(totalPairingEarning, 2) : '0.00' }}
                    </div>
                </div>
                <div class="p-5 dark:bg-gray-700 rounded-xl flex flex-col items-center justify-center gap-8">
                    <div class="grid gap-2 font-medium text-base dark:text-gray-400 text-center">
                        {{$t('public.affiliate.referred')}}
                        <div>
                            <span class="dark:text-white font-semibold">{{ props.referredCounts }}</span> <span class="dark:text-gray-400">{{$t('public.affiliate.members')}}</span>
                        </div>
                    </div>
                    <div class="flex rounded-md shadow-sm">
                        <input type="text" id="referralCodeSide" readonly :value="$page.props.auth.user.referral_code" class="py-2 px-4 block w-full border-transparent shadow-sm rounded-l-lg text-sm focus:z-10 focus:border-pink-700 focus:ring focus:ring-pink-500 focus:ring-offset-0 focus:ring-offset-white dark:bg-gray-600 dark:text-white text-center">
                        <Tooltip :content="$t('public.' + tooltipContent)" placement="top">
                            <button
                                type="button"
                                class="py-2 px-4 inline-flex flex-shrink-0 justify-center items-center gap-2 rounded-r-lg border border-transparent font-semibold dark:bg-gray-500 text-white dark:hover:bg-gray-400 focus:z-10 focus:outline-none transition-all text-sm uppercase"
                                @click="copyReferralCode"
                            >
                                <DuplicateIcon
                                    aria-hidden="true"
                                    class="h-5"
                                />
                                {{$t('public.copy')}}
                            </button>
                        </Tooltip>
                    </div>
                    <div class="flex flex-col gap-2 w-full">
                        <div class="flex flex-col items-center">
                            <div class="inline-flex justify-center items-center w-full">
                                <div class="uppercase font-semibold">{{$t('public.affiliate.invite_friend')}}</div>
                                <ShareIcon
                                    aria-hidden="true"
                                    class="h-5 ml-2"
                                />
                            </div>
                            <div>({{ $t('public.binary_placement_position') }})</div>
                        </div>

                        <div class="inline-flex justify-center items-center w-full">

                        </div>

                        <div class="flex gap-4 w-full">
                            <Button
                                type="button"
                                class="flex justify-center items-center gap-2 w-full"
                                @click="copyReferralCodeLink('left')"
                                v-slot="{ iconSizeClasses }"
                            >
                                <ArrowLeftIcon class="w-5 h-5" />
                               <div>{{$t('public.left')}}</div>
                            </Button>
                            <Button
                                type="button"
                                class="flex justify-center items-center gap-2 w-full"
                                :disabled="!checkCoinStaking"
                                @click="copyReferralCodeLink('right')"
                            >
                                <div>{{$t('public.right')}}</div>
                                <ArrowRightIcon class="w-5 h-5" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
