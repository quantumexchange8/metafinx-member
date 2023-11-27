<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue'

const props = defineProps({
    align: {
        default: 'right',
    },
    width: {
        default: '48',
    },
    contentClasses: {
        default: () => ['pb-1 px-5', 'bg-white dark:bg-gray-800'],
    },
})

let open = ref(false)

const closeOnEscape = (e) => {
    if (open.value && e.keyCode === 27) {
        open.value = false
    }
}

onMounted(() => document.addEventListener('keydown', closeOnEscape))

onUnmounted(() => document.removeEventListener('keydown', closeOnEscape))

const widthClass = computed(() => {
    return {
        48: 'w-48',
        56: 'w-56',
        64: 'w-64',
        80: 'w-80',
    }[props.width.toString()]
})

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'origin-top-left left-0'
    } else if (props.align === 'right') {
        return 'origin-top-right right-0'
    } else if (props.align === 'right-side') {
        return 'origin-top-right -right-[290px]'
    } else {
        return 'origin-top'
    }
})
</script>

<template>
    <div class="relative">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div
            v-show="open"
            class="fixed inset-0 z-40"
            @click="open = false"
        ></div>

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-50 mt-2 rounded-xl shadow-[0_4px_20px_0px_rgba(0,0,0,0.5)]"
                :class="[widthClass, alignmentClasses]"
                style="display: none"
                @click="open = false"
            >
                <div
                    class="rounded-xl ring-1 ring-black ring-opacity-5"
                    :class="contentClasses"
                >
                    <slot name="content" />
                </div>
            </div>
        </transition>
    </div>
</template>
