<script setup>
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
import { sidebarState } from '@/Composables'
import Alert from "@/Components/Alert.vue";
import {onUnmounted, ref} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {usePage} from "@inertiajs/vue3";
import ToastList from "@/Components/ToastList.vue";

const page = usePage();
defineProps({
    title: String
})

const showAlert = ref(false);
const intent = ref(null);
const alertTitle = ref('');
const alertMessage = ref(null);
const alertButton = ref(null);

let removeFinishEventListener = Inertia.on("finish", () => {
    if (page.props.success) {
        showAlert.value = true
        intent.value = 'success'
        alertTitle.value = page.props.title
        alertMessage.value = page.props.success
        alertButton.value = page.props.alertButton
    } else if (page.props.warning) {
        showAlert.value = true
        intent.value = 'warning'
        alertTitle.value = page.props.title
        alertMessage.value = page.props.warning
        alertButton.value = page.props.alertButton
    }
});

onUnmounted(() => removeFinishEventListener());
</script>

<template>
    <Head :title="title"></Head>

    <div
        class="min-h-screen text-gray-900 bg-white dark:bg-gray-800 dark:text-white"
    >
        <!-- Sidebar -->
        <Sidebar />

        <div
            style="transition-property: margin; transition-duration: 150ms"
            :class="[
                'min-h-screen flex flex-col',
                {
                    'lg:ml-64': sidebarState.isOpen,
                    'md:ml-16': !sidebarState.isOpen,
                },
            ]"
        >
            <!-- Navbar -->
            <Navbar />

            <main class="flex-1 px-4 sm:px-6 md:pt-0" :class="{ 'md:mr-80': $slots.asideRight, 'lg:mr-96': $slots.asideRight}">
                <!-- Page Heading -->
                <header v-if="$slots.header">
                    <div class="pb-4 sm:py-6 px-0">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <Alert
                    :show="showAlert"
                    :on-dismiss="() => showAlert = false"
                    :title="alertTitle"
                    :intent="intent"
                    :alertButton="alertButton"
                >
                    {{ alertMessage }}
                </Alert>
                <ToastList />
                <slot />

            </main>

            <!-- <PageFooter class="hidden md:block"/> -->
        </div>

        <aside v-if="$slots.asideRight" class="hidden md:block">
            <slot name="asideRight" />
        </aside>
    </div>
</template>
