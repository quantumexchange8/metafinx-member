<script setup>
import {checkCircle, alertTriangle} from '@/Components/Icons/outline'
import Button from "@/Components/Button.vue";
import {computed} from "vue";
import {XIcon} from "@heroicons/vue/outline";

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
    <teleport to="body">
        <transition leave-active-class="duration-200">
            <div
                v-show="show"
                class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
                scroll-region
            >
                <transition
                    enter-active-class="ease-out duration-200"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-show="show"
                        class="fixed inset-0 transform transition-all"
                        @click="dismiss"
                    >
                        <div
                            class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"
                        />
                    </div>
                </transition>

                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 w-80 md:w-full space-y-4"
                >
                    <div v-if="props.show" class="px-4 py-6 flex flex-col gap-5 dark:bg-gray-700 rounded-xl max-w-md">
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
                                OK
                            </Button>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>
