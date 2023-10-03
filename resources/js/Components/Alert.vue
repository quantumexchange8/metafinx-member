<script setup>
import {checkCircle, alertTriangle} from '@/Components/Icons/outline'
import Button from "@/Components/Button.vue";
import {computed} from "vue";

const props = defineProps({
    intent: {
        type: String,
        validator(value) {
            return ['success', 'warning'].includes(value)
        },
        default: 'success'
    },
    title: String,
    show: {
        type: Boolean,
        default: false
    },
    onDismiss: Function
})

const iconComponent = computed(() => {
    const icons = {
        success: checkCircle,
        warning: alertTriangle
    }

    return icons[props.intent]
})

function dismiss() {
    if (props.onDismiss) {
        props.onDismiss();
    }
}

</script>

<template>
    <TransitionGroup
        tag="div"
        enter-from-class="opacity-0"
        enter-active-class="duration-300"
        leave-active-class="duration-200"
        leave-to-class="opacity-0"
        class="fixed top-1/3 right-1/3 z-50 w-full max-w-md space-y-4"
    >
        <div v-if="props.show" class="px-4 py-6 flex flex-col gap-5 dark:bg-gray-600 rounded-xl max-w-md">
            <component :is="iconComponent" />

            <div class="px-2 space-y-2">
                <h2 class="text-xl font-semibold dark:text-white">{{ props.title }}</h2>
                <div class="text-sm font-normal dark:text-gray-400">
                    <slot />
                </div>
            </div>
            <div class="px-2">
                <Button
                    type="button"
                    class="w-full flex justify-center"
                    @click="dismiss"
                >
                    Ok
                </Button>
            </div>
        </div>
    </TransitionGroup>
</template>
