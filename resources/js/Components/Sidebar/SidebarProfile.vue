<script setup>
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Button from "@/Components/Button.vue";
import { ChevronDownIcon } from '@heroicons/vue/outline'
import { BellIcon } from '@/Components/Icons/outline.jsx'
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
        <Dropdown align="right">
            <template #trigger>
                <Button
                    iconOnly
                    variant="transparent"
                    type="button"
                    class="border-0 bg-transparent hidden md:inline-flex p-0"
                    srText="Toggle dark mode"
                >
                    <span class="dark:text-gray-400">EN</span>
                    <ChevronDownIcon
                        aria-hidden="true"
                        class="w-4 h-4 dark:text-gray-400"
                    />
                </Button>
            </template>
            <template #content>
                <DropdownLink>
                    <div class="inline-flex items-center gap-2">
                        English
                    </div>
                </DropdownLink>
                <DropdownLink>
                    <div class="inline-flex items-center gap-2">
                        中文 (繁)
                    </div>
                </DropdownLink>
            </template>
        </Dropdown>
    </div>

    <div class="px-3 py-2 flex flex-col justify-center">
        <div class="p-4 rounded-[10px] hover:bg-gray-700">
            <img
                class="h-20 w-20 rounded-full mx-auto"
                :src="$page.props.auth.user.picture ? $page.props.auth.user.picture : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                alt="ProfilePic"
            >
            <div class="flex justify-center my-2">
                <span class="bg-gray-100 text-gray-800 text-xs px-3 py-0.5 font-thin rounded-full dark:bg-gray-600 dark:text-white">Platinum</span>
            </div>
            <div class="flex flex-col text-center">
                <span>{{ $page.props.auth.user.name }}</span>
                <span class="text-sm text-gray-400">{{ $page.props.auth.user.email }}</span>
            </div>
        </div>
    </div>

</template>
