<script setup>
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Button from "@/Components/Button.vue";
import { ChevronDownIcon } from '@heroicons/vue/outline'
import { BellIcon, Rank1Icon, Rank2Icon, Rank3Icon, PendingIcon, ApproveIcon } from '@/Components/Icons/outline.jsx'
import { Link } from "@inertiajs/vue3";

</script>

<template>
    <div class="flex justify-end pr-3 gap-3">
        <div>
            <Button
                iconOnly
                variant="secondary"
                type="button"
                class="border-0 bg-transparent hidden md:inline-flex p-0"
                srText="Toggle dark mode"
            >
                <BellIcon
                    aria-hidden="true"
                    class="w-6 h-6 dark:text-gray-400"
                />
            </Button>

        </div>
<!--        <Dropdown align="right">-->
<!--            <template #trigger>-->
<!--                <Button-->
<!--                    iconOnly-->
<!--                    variant="transparent"-->
<!--                    type="button"-->
<!--                    class="border-0 bg-transparent hidden md:inline-flex p-0"-->
<!--                    srText="Toggle dark mode"-->
<!--                >-->
<!--                    <span class="dark:text-gray-400">EN</span>-->
<!--                    <ChevronDownIcon-->
<!--                        aria-hidden="true"-->
<!--                        class="w-4 h-4 dark:text-gray-400"-->
<!--                    />-->
<!--                </Button>-->
<!--            </template>-->
<!--            <template #content>-->
<!--                <DropdownLink>-->
<!--                    <div class="inline-flex items-center gap-2">-->
<!--                        English-->
<!--                    </div>-->
<!--                </DropdownLink>-->
<!--                <DropdownLink>-->
<!--                    <div class="inline-flex items-center gap-2">-->
<!--                        中文 (繁)-->
<!--                    </div>-->
<!--                </DropdownLink>-->
<!--            </template>-->
<!--        </Dropdown>-->
    </div>

    <div class="px-3 py-2 flex flex-col justify-center">

        <Link :href="route('profile.edit')">
            <div class="p-4 rounded-[10px] hover:bg-gray-700 cursor-pointer" :class=" route().current('profile.edit') ? 'dark:bg-gray-700' : '' ">
                <div class="relative">
                    <img
                        class="h-20 w-20 rounded-full mx-auto"
                        :src="$page.props.auth.user.picture ? $page.props.auth.user.picture : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                        alt="ProfilePic"
                    >
                    <PendingIcon aria-hidden="true" class="absolute bottom-0 right-14 flex-shrink-0 w-5 h-5 mr-1" 
                    v-if="$page.props.auth.user.kyc_approval === 'pending' || $page.props.auth.user.kyc_approval === 'rejected'"
                    />

                    <ApproveIcon
                    aria-hidden="true"
                    class="absolute bottom-0 right-14 flex-shrink-0 w-5 h-5 mr-1"
                    v-if="$page.props.auth.user.kyc_approval === 'approved'"
                    /> 
                </div>

                <div class="flex flex-col text-center mt-5">
                    <div class="flex flex-row items-center justify-center mb-1">
                        <Rank1Icon 
                        aria-hidden="true" 
                        class="flex-shrink-0 w-5 h-5 mr-1"
                        v-if="$page.props.auth.user.setting_rank_id === 1"
                        />
                        <Rank2Icon 
                        aria-hidden="true" 
                        class="flex-shrink-0 w-5 h-5 mr-1"
                        v-if="$page.props.auth.user.setting_rank_id === 2"
                        />
                        <Rank3Icon 
                        aria-hidden="true" 
                        class="flex-shrink-0 w-5 h-5 mr-1"
                        v-if="$page.props.auth.user.setting_rank_id === 3"
                        />
                        <span>{{ $page.props.auth.user.name }}</span>
                    </div>
                    
                    <span class="text-sm text-gray-400">{{ $page.props.auth.user.email }}</span>
                </div>
            </div>
        </Link>


    </div>

</template>
