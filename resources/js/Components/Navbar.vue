<script setup>
import { onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useFullscreen } from '@vueuse/core'
import {
    SunIcon,
    MoonIcon,
    SearchIcon,
    MenuIcon,
    XIcon,
    ArrowsExpandIcon,
    ChevronDownIcon,
} from '@heroicons/vue/outline'
import {
    handleScroll,
    isDark,
    scrolling,
    toggleDarkMode,
    sidebarState,
} from '@/Composables'
import Button from '@/Components/Button.vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import { ArrowsInnerIcon } from '@/Components/Icons/outline'
import { BellIcon } from '@/Components/Icons/outline.jsx'

const { isFullscreen, toggle: toggleFullScreen } = useFullscreen()

onMounted(() => {
    document.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    document.removeEventListener('scroll', handleScroll)
})
</script>

<template>

    <!-- Mobile bottom bar -->
    <div
        :class="[
            'fixed inset-x-0 top-0 z-50 flex items-center justify-between px-4 py-4 sm:px-6 transition-transform duration-500 bg-white md:hidden dark:bg-gray-800']"
    >
        <div>
            <Button
                iconOnly
                variant="secondary"
                type="button"
                @click="sidebarState.isOpen = !sidebarState.isOpen"
                v-slot="{ iconSizeClasses }"
                class="md:hidden"
                srText="Search"
            >
                <MenuIcon
                    v-show="!sidebarState.isOpen"
                    aria-hidden="true"
                    :class="iconSizeClasses"
                />
                <XIcon
                    v-show="sidebarState.isOpen"
                    aria-hidden="true"
                    :class="iconSizeClasses"
                />
            </Button>
        </div>
        
        <div class="flex flex-row">
            <div>
                <Dropdown align="right">
                    <template #trigger>
                        <Button
                            iconOnly
                            variant="transparent"
                            type="button"
                            class="border-0 bg-transparent md:inline-flex p-0"
                            srText="Toggle dark mode"
                        >
                            <span class="dark:text-white">EN</span>
                            <ChevronDownIcon
                                aria-hidden="true"
                                class="w-4 h-4 dark:text-white"
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
            <div>
                <Button
                    iconOnly
                    variant="secondary"
                    type="button"
                    class="border-0 bg-transparent md:inline-flex p-0"
                    srText="Toggle dark mode"
                >
                    <BellIcon
                        aria-hidden="true"
                        class="w-6 h-6 dark:text-white"
                    />
                </Button>
            </div>
        </div>

        

        <!-- <Link :href="route('dashboard')">
            <ApplicationLogo class="w-10 h-10" />
            <span class="sr-only">K UI</span>
        </Link> -->

    </div>
</template>
