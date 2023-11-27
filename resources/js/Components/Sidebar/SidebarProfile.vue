<script setup>
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Button from "@/Components/Button.vue";
import { ChevronDownIcon } from '@heroicons/vue/outline'
import { BellIcon, Rank1Icon, Rank2Icon, Rank3Icon, Rank4Icon, PendingIcon, ApproveIcon } from '@/Components/Icons/outline.jsx'
import {
    MailIcon,
    MailOpenIcon
} from '@heroicons/vue/solid'
import { Link } from "@inertiajs/vue3";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {transactionFormat} from "@/Composables/index.js";

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
    <div class="flex justify-end pr-3 gap-3">
        <Dropdown width="80" align="right-side">
            <template #trigger>
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
                    <span v-show="$page.props.auth.user.unreadNotifications.length !== 0" class="top-2 left-5 absolute w-2 h-2 bg-pink-500 border border-transparent rounded-full"></span>

                </Button>
            </template>
            <template #content>
                <h3 class="font-semibold dark:text-white py-5">Notifications</h3>
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
                    <div class="dark:text-dark-eval-5">No notifications</div>
                </DropdownLink>
            </template>
        </Dropdown>

<!--        </div>-->
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
            <div class="p-4 rounded-[10px] hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer" :class=" route().current('profile.edit') ? 'dark:bg-gray-700' : '' ">
                <div class="relative">
                    <img
                        class="h-20 w-20 rounded-full mx-auto"
                        :src="$page.props.auth.user.picture ? $page.props.auth.user.picture : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                        alt="ProfilePic"
                    >
                    <PendingIcon aria-hidden="true" class="absolute bottom-0 right-14 flex-shrink-0 w-5 h-5 mr-1"
                    v-if="$page.props.auth.user.kyc_approval === 'pending' || $page.props.auth.user.kyc_approval === 'unverified'"
                    />

                    <ApproveIcon
                    aria-hidden="true"
                    class="absolute bottom-0 right-14 flex-shrink-0 w-5 h-5 mr-1"
                    v-if="$page.props.auth.user.kyc_approval === 'verified'"
                    />
                </div>

                <div class="flex flex-col text-center mt-5">
                    <div class="flex flex-row items-center justify-center mb-1">
                        <Rank1Icon
                        aria-hidden="true"
                        class="flex-shrink-0 w-5 h-5 mr-1"
                        v-if="$page.props.auth.user.setting_rank_id === 2"
                        />
                        <Rank2Icon
                        aria-hidden="true"
                        class="flex-shrink-0 w-5 h-5 mr-1"
                        v-if="$page.props.auth.user.setting_rank_id === 3"
                        />
                        <Rank3Icon
                        aria-hidden="true"
                        class="flex-shrink-0 w-5 h-5 mr-1"
                        v-if="$page.props.auth.user.setting_rank_id === 4"
                        />
                        <Rank4Icon
                        aria-hidden="true"
                        class="flex-shrink-0 w-5 h-5 mr-1"
                        v-if="$page.props.auth.user.setting_rank_id === 5"
                        />
                        <span>{{ $page.props.auth.user.name }}</span>
                    </div>

                    <span class="text-sm text-gray-400">{{ $page.props.auth.user.email }}</span>
                </div>
            </div>
        </Link>

    </div>

    <!-- Notification Modal -->
    <Modal :show="notificationModal" title="Details" @close="closeModal" max-width="xl">
        <div class="text-xs dark:text-gray-400">{{ formatDateTime(notificationContent.data['post_date']) }}</div>
        <div class="my-5">
            <img class="rounded-lg w-full" :src="notificationContent.data['image']" alt="announcement image" />
        </div>
        <div class="my-5 dark:text-white">{{ notificationContent.data['title'] }}</div>
        <div class="dark:text-gray-300 text-sm prose leading-3" v-html="notificationContent.data['content']"></div>
    </Modal>

</template>
