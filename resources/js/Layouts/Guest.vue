<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import PageFooter from '@/Components/PageFooter.vue'
import Alert from "@/Components/Alert.vue";
import {onUnmounted, ref} from "vue";
import {Inertia} from "@inertiajs/inertia";


const page = usePage();
defineProps({
    title: String
})

const showAlert = ref(false);
const intent = ref(null);
const alertTitle = ref('');
const alertMessage = ref(null);

let removeFinishEventListener = Inertia.on("finish", () => {
    if (page.props.success) {
        showAlert.value = true
        intent.value = 'success'
        alertTitle.value = page.props.title
        alertMessage.value = page.props.success
    } else if (page.props.warning) {
        showAlert.value = true
        intent.value = 'warning'
        alertTitle.value = page.props.title
        alertMessage.value = page.props.warning
    }
});

onUnmounted(() => removeFinishEventListener());
</script>

<template>
    <Head :title="title" />

    <div
        class="flex flex-col items-center justify-center min-h-screen gap-4 py-6 bg-white dark:bg-gray-800"
    >
        <div class="fixed left-0 top-0 flex-shrink-0 inline-flex items-center gap-3">
            <Link href="https://metafinx.com/" target="_blank">
                <ApplicationLogo class="w-48" />
            </Link>
        </div>

        <main class="flex items-center flex-1 w-full sm:max-w-lg">
            <div
                class="w-full px-6 py-4 overflow-hidden sm:rounded-lg"
            >
                <Alert
                    :show="showAlert"
                    :on-dismiss="() => showAlert = false"
                    :title="alertTitle"
                    :intent="intent"
                >
                    {{ alertMessage }}
                </Alert>
                <slot />
            </div>
        </main>

        <PageFooter />

    </div>
</template>
