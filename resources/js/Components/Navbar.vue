<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useFullscreen } from '@vueuse/core'
import {
    MenuIcon,
    XIcon,
    BellIcon,
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
import {
    ArrowsInnerIcon, DashboardIcon,
    DashboardIconInactive,
    EarnIcon,
    InactiveEarnIcon, InactiveWalletIcon,
    WalletIcon,
    AffiliateIcon, InactiveAffiliateIcon
} from '@/Components/Icons/outline'
import {transactionFormat} from "@/Composables/index.js";
import Modal from "@/Components/Modal.vue";

const { isFullscreen, toggle: toggleFullScreen } = useFullscreen()

onMounted(() => {
    document.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    document.removeEventListener('scroll', handleScroll)
})

const notificationModal = ref(false);
const notificationContent = ref(null);
const clickedNotificationIds = ref([]);
const { formatDateTime } = transactionFormat();
const openNotificationModal = async (notification) => {
    notificationModal.value = true;
    notificationContent.value = notification;

    clickedNotificationIds.value.push(notification.id);

    if (notification.read_at === null) {
        await axios.post('/markAsRead', {id: notification.id})
            .then(response => {
                console.log('marked')
            })
            .catch(error => {
                // Handle the error, if any
                console.error('Error marking notification as read:', error);
            });
    }
}

const closeModal = () => {
    notificationModal.value = false
}
</script>

<template>
    <nav
        aria-label="secondary"
        :class="[
            'md:hidden sticky top-0 z-10 p-4 md:py-8 md:px-4 bg-white flex items-center justify-between transition-transform duration-500 dark:bg-gray-800',
            // {
            //     '-translate-y-full': scrolling.down,
            //     'translate-y-0': scrolling.up,
            // },
        ]"
    >
        <div class="flex items-center gap-2">
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
        <div class="flex justify-end">
            <Dropdown width="80">
                <template #trigger>
                    <Button
                        iconOnly
                        variant="secondary"
                        type="button"
                        class="border-0 bg-transparent sm:hidden inline-flex p-0"
                        srText="Toggle dark mode"
                    >
                        <BellIcon
                            aria-hidden="true"
                            class="w-6 h-6 dark:text-gray-400"
                        />
                        <span v-show="$page.props.auth.user.unreadNotifications.length !== 0" class="top-2 left-5 absolute w-2 h-2 bg-pink-500 border border-transparent rounded-full"></span>

                    </Button>
                </template>
                <template #content>
                    <h3 class="font-semibold dark:text-white py-5">{{$t('public.navbar.notifications')}}</h3>
                    <DropdownLink v-for="notification in $page.props.auth.user.unreadNotifications" class="border-b-2 border-gray-600" @click="openNotificationModal(notification)">
                        <div class="inline-flex items-center gap-2 w-full">
                            <span v-if="clickedNotificationIds.includes(notification.id)" class="w-3 h-3 bg-gray-400 border border-transparent rounded-full shrink-0 grow-0"></span>
                            <span v-else class="w-3 h-3 bg-pink-500 border border-transparent rounded-full shrink-0 grow-0"></span>
                            <div class="flex flex-col">
                                <div class="my-auto dark:text-white">{{ notification.data['title'] }}</div>
                                <div class="my-auto dark:text-gray-400 break-words overflow-hidden"  style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;" v-html="notification.data['content']"></div>
                            </div>
                        </div>
                    </DropdownLink>
                    <DropdownLink v-for="readNotification in $page.props.auth.user.readNotifications" class="border-b-2 border-gray-600" @click="openNotificationModal(readNotification)">
                        <div class="inline-flex items-center gap-2">
                            <span v-show="readNotification && readNotification.read_at" class="w-3 h-3 bg-gray-400 border border-transparent rounded-full shrink-0 grow-0"></span>
                            <div class="flex flex-col">
                                <div class="my-auto dark:text-white">{{ readNotification.data['title'] }}</div>
                                <div class="my-auto dark:text-gray-400 break-words overflow-hidden"  style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;" v-html="readNotification.data['content']"></div>
                            </div>
                        </div>
                    </DropdownLink>
                    <DropdownLink v-if="$page.props.auth.user.notifications.length === 0" class="border-b-2 border-gray-600">
                        <div class="dark:text-dark-eval-5">{{$t('public.navbar.no_notifications')}}</div>
                    </DropdownLink>
                </template>
            </Dropdown>
        </div>
<!--        <div class="flex items-center gap-2">-->
<!--            <div class="flex flex-row">-->
<!--                <div>-->
<!--                    <Dropdown align="right">-->
<!--                        <template #trigger>-->
<!--                            <Button-->
<!--                                iconOnly-->
<!--                                variant="transparent"-->
<!--                                type="button"-->
<!--                                class="border-0 bg-transparent md:inline-flex p-0"-->
<!--                                srText="Toggle dark mode"-->
<!--                            >-->
<!--                                <span class="dark:text-white">EN</span>-->
<!--                                <ChevronDownIcon-->
<!--                                    aria-hidden="true"-->
<!--                                    class="w-4 h-4 ml-2 dark:text-white"-->
<!--                                />-->
<!--                            </Button>-->
<!--                        </template>-->
<!--                        <template #content>-->
<!--                            <DropdownLink>-->
<!--                                <div class="inline-flex items-center gap-2">-->
<!--                                    English-->
<!--                                </div>-->
<!--                            </DropdownLink>-->
<!--                            <DropdownLink>-->
<!--                                <div class="inline-flex items-center gap-2">-->
<!--                                    中文 (繁)-->
<!--                                </div>-->
<!--                            </DropdownLink>-->
<!--                        </template>-->
<!--                    </Dropdown>-->
<!--                </div>-->
<!--                <div>-->
<!--                    <Button-->
<!--                        iconOnly-->
<!--                        variant="secondary"-->
<!--                        type="button"-->
<!--                        class="border-0 bg-transparent md:inline-flex p-0"-->
<!--                        srText="Toggle dark mode"-->
<!--                    >-->
<!--                        <BellIcon-->
<!--                            aria-hidden="true"-->
<!--                            class="w-6 h-6 dark:text-white"-->
<!--                        />-->
<!--                    </Button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </nav>

    <!-- Mobile bottom bar -->
    <div
        :class="[
            'fixed inset-x-0 z-50 bottom-0 flex items-center justify-between px-4 py-4 sm:px-6 transition-transform duration-500 bg-white sm:hidden dark:bg-gray-900',
            // {
            //     'translate-y-full': scrolling.down,
            //     'translate-y-0': scrolling.up,
            // },
        ]"
    >
        <div>
            <Link :href="route('dashboard')">
                <div class="fixed bottom-4 dark:bg-gray-900 border-2 border-gray-800 rounded-full w-16 h-16 -translate-y-6">
                    <img src="/assets/icon.png" class="w-10 h-10 mx-auto mt-2" alt="logo" />
                </div>
                <div class="flex justify-center items-center mt-7 w-16">
                    <p :class="route().current('dashboard') ? 'text-pink-500' : 'text-white' " class="text-xs">
                        {{$t('public.navbar.dashboard')}}
                    </p>
                </div>
            </Link>
        </div>
        <div>
            <Link :href="route('earn.invest_subscription')">
                <div class="flex flex-col items-center w-16">
                    <EarnIcon
                        v-if="route().current('earn.*')"
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />
                    <InactiveEarnIcon
                        v-else
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />
                    <p :class="route().current('earn.*') ? 'text-pink-500' : 'text-white' " class="text-xs">
                        {{$t('public.navbar.earn')}}
                    </p>
                </div>

            </Link>
        </div>
        <div>
            <Link :href="route('wallet.details')">
                <div class="flex flex-col items-center w-16">
                    <WalletIcon
                        v-if="route().current('wallet.details')"
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />

                    <InactiveWalletIcon
                        v-else
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />
                    <p :class="route().current('wallet.details') ? 'text-pink-500' : 'text-white' " class="text-xs">
                        {{$t('public.navbar.wallet')}}
                    </p>
                </div>

            </Link>
        </div>
        <div>
            <Link :href="route('affiliate.referral_view')">
                <div class="flex flex-col items-center w-16">
                    <AffiliateIcon
                        v-if="route().current('affiliate.referral_view')"
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />

                    <InactiveAffiliateIcon
                        v-else
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />
                    <p :class="route().current('affiliate.referral_view') ? 'text-pink-500' : 'text-white' " class="text-xs">
                        {{$t('public.navbar.affiliate')}}
                    </p>
                </div>

            </Link>
        </div>
    </div>

    <!-- Notification Modal -->
    <Modal :show="notificationModal" :title="$t('public.navbar.details')" @close="closeModal" max-width="xl">
        <div class="text-xs dark:text-gray-400">{{ formatDateTime(notificationContent.data['post_date']) }}</div>
        <div class="my-5">
            <img class="rounded-lg w-full" :src="notificationContent.data['image']" alt="announcement image" />
        </div>
        <div class="my-5 dark:text-white">{{ notificationContent.data['title'] }}</div>
        <div class="dark:text-gray-300 text-sm prose leading-3" v-html="notificationContent.data['content']"></div>
    </Modal>
</template>
