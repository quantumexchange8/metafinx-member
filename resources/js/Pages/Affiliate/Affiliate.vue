<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import {ShareIcon, DuplicateIcon} from "@heroicons/vue/outline";
import Tooltip from "@/Components/Tooltip.vue";
import {ref} from "vue";
import toast from "@/Composables/toast.js";
import ReferralTree from "@/Pages/Affiliate/ReferralTree.vue";

const props = defineProps({
    referredCounts: Number
})

const tooltipContent = ref('Copy');
function copyReferralCode () {
    let referralCodeCopy = document.querySelector('#referralCode')
    referralCodeCopy.setAttribute('type', 'text');
    referralCodeCopy.select();

    try {
        var successful = document.execCommand('copy');
        if (successful) {
            tooltipContent.value = 'Copied!';
            setTimeout(() => {
                tooltipContent.value = 'Copy'; // Reset tooltip content to 'Copy' after 2 seconds
            }, 1000);
        } else {
            tooltipContent.value = 'Try Again Later!';
        }

    } catch (err) {
        alert('Oops, unable to copy');
    }
}

function copyReferralCodeLink() {
    const referralCode = document.querySelector('#referralCode').value;
    const url = window.location.origin + '/register/' + referralCode;

    const tempInput = document.createElement('input');
    tempInput.value = url;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    toast.add({
        message: 'Link copied successful!'
    });
}
</script>

<template>
    <AuthenticatedLayout title="Affiliate">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-2xl font-semibold leading-tight">
                    Affiliate
                </h2>
            </div>
            <p class="text-base font-normal dark:text-gray-400">
                You can get rewarded when you invite your friends to join MetafinX.
            </p>
        </template>

        <ReferralTree />

        <div class="flex flex-col space-y-6 pt-8 pb-12 md:hidden">
            <h3 class="text-xl font-semibold leading-tight">
                Referral Program
            </h3>
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    Total Referral Earning since 01 Jan 2023
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ 0.00
                </div>
            </div>
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                <div class="font-medium text-xs dark:text-gray-400">
                    Total Affiliate Earning since 01 Jan 2023
                </div>
                <div class="font-semibold text-2xl dark:text-white">
                    $ 0.00
                </div>
            </div>
            <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col items-center justify-center gap-8 pb-12">
                <div class="grid gap-2 font-medium text-base dark:text-gray-400 text-center">
                    You’ve successfully referred
                    <div>
                        <span class="dark:text-white font-semibold">{{ props.referredCounts }}</span> <span class="dark:text-gray-400">members</span>
                    </div>
                </div>
                <div class="flex rounded-md shadow-sm">
                    <input type="text" id="referralCode" class="py-2 px-4 block w-full border-transparent shadow-sm rounded-l-lg text-sm focus:z-10 focus:border-pink-700 focus:ring focus:ring-pink-500 focus:ring-offset-0 focus:ring-offset-white dark:bg-gray-600 dark:text-white text-center" readonly :value="$page.props.auth.user.referral_code">
                    <Tooltip :content="tooltipContent" placement="top">
                        <button
                            type="button"
                            class="py-2 px-4 inline-flex flex-shrink-0 justify-center items-center gap-2 rounded-r-lg border border-transparent font-semibold dark:bg-gray-500 text-white dark:hover:bg-gray-400 focus:z-10 focus:outline-none transition-all text-sm uppercase"
                            @click="copyReferralCode"
                        >
                            <DuplicateIcon
                                aria-hidden="true"
                                class="h-5"
                            />
                            Copy
                        </button>
                    </Tooltip>
                </div>
                <Button
                    type="button"
                    class="items-center gap-2 w-full"
                    v-slot="{ iconSizeClasses }"
                    @click="copyReferralCodeLink"
                >
                    <div class="inline-flex justify-center items-center w-full">
                        <span class="uppercase font-semibold">Invite a friend</span>
                        <ShareIcon
                            aria-hidden="true"
                            class="h-5 ml-2"
                        />
                    </div>
                </Button>
            </div>
        </div>

        <template #asideRight>
            <div class="inset-y-0 p-6 flex flex-col space-y-6 bg-white shadow-lg dark:bg-gray-800 border-l dark:border-gray-700 w-96 fixed right-0">
                <h3 class="text-xl font-semibold leading-tight">
                    Referral Program
                </h3>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        Total Referral Earning since 01 Jan 2023
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ 0.00
                    </div>
                </div>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col gap-2">
                    <div class="font-medium text-xs dark:text-gray-400">
                        Total Affiliate Earning since 01 Jan 2023
                    </div>
                    <div class="font-semibold text-2xl dark:text-white">
                        $ 0.00
                    </div>
                </div>
                <div class="p-5 dark:bg-gray-700 rounded-[10px] flex flex-col items-center justify-center gap-8">
                    <div class="grid gap-2 font-medium text-base dark:text-gray-400 text-center">
                        You’ve successfully referred
                        <div>
                            <span class="dark:text-white font-semibold">{{ props.referredCounts }}</span> <span class="dark:text-gray-400">members</span>
                        </div>
                    </div>
                    <div class="flex rounded-md shadow-sm">
                        <input type="text" id="referralCode" class="py-2 px-4 block w-full border-transparent shadow-sm rounded-l-lg text-sm focus:z-10 focus:border-pink-700 focus:ring focus:ring-pink-500 focus:ring-offset-0 focus:ring-offset-white dark:bg-gray-600 dark:text-white text-center" readonly :value="$page.props.auth.user.referral_code">
                        <Tooltip :content="tooltipContent" placement="top">
                            <button
                                type="button"
                                class="py-2 px-4 inline-flex flex-shrink-0 justify-center items-center gap-2 rounded-r-lg border border-transparent font-semibold dark:bg-gray-500 text-white dark:hover:bg-gray-400 focus:z-10 focus:outline-none transition-all text-sm uppercase"
                                @click="copyReferralCode"
                            >
                                <DuplicateIcon
                                    aria-hidden="true"
                                    class="h-5"
                                />
                                Copy
                            </button>
                        </Tooltip>
                    </div>
                    <Button
                        type="button"
                        class="items-center gap-2 w-full"
                        v-slot="{ iconSizeClasses }"
                        @click="copyReferralCodeLink"
                    >
                        <div class="inline-flex justify-center items-center w-full">
                            <span class="uppercase font-semibold">Invite a friend</span>
                            <ShareIcon
                                aria-hidden="true"
                                class="h-5 ml-2"
                            />
                        </div>
                    </Button>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>
