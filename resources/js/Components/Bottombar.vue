<script setup>
import { onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useFullscreen } from '@vueuse/core'
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import { DashboardIcon, LogoutIcon, DashboardIconInactive, InactiveReportIcon, InactiveAffiliateIcon, WalletIcon, InactiveWalletIcon, InactiveEarnIcon } from '@/Components/Icons/outline'

</script>

<template>

    <!-- Mobile view only -->
    <div
        :class="[
            'fixed inset-x-0 bottom-0 flex items-center justify-between px-4 py-4 sm:px-6 transition-transform duration-500 bg-gray-900 md:hidden']"
    >    
        <div>
            <Link :href="route('dashboard')">
                <div class="flex flex-col items-center w-16">
                    <DashboardIcon
                        v-if="route().current('dashboard')"
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />
                    <DashboardIconInactive
                        v-else
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />
                    <p :class="route().current('dashboard') ? 'text-pink-500' : 'text-white' " class="text-xs">
                        Dashboard
                    </p>
                </div>
            </Link>
        </div>
        <div>
            <Link :href="route('profile.edit')">
                <div class="flex flex-col items-center w-16">
                    <InactiveEarnIcon
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />
                    <p :class="route().current('profile.edit') ? 'text-pink-500' : 'text-white' " class="text-xs">
                        Earn
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
                        Wallet
                    </p>
                </div>
                
            </Link>
        </div>
        <div>
            <Link :href="route('profile.edit')">
                <div class="flex flex-col items-center w-16">
                    <InactiveAffiliateIcon
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />
                    <p :class="route().current('profile.edit') ? 'text-pink-500' : 'text-white' " class="text-xs">
                        Affiliate
                    </p>
                </div>
                
            </Link>
        </div>
        <div>
            <Link :href="route('profile.edit')">
                <div class="flex flex-col items-center w-16">
                    <InactiveReportIcon
                        class="flex-shrink-0 w-6 h-6 mb-1"
                        aria-hidden="true"
                    />
                    <p :class="route().current('profile.edit') ? 'text-pink-500' : 'text-white' " class="text-xs">
                        Report
                    </p>
                </div>
                
            </Link>
        </div>
    </div>
</template>