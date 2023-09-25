<script setup>
import { computed } from 'vue'

const emit = defineEmits(['update:checked'])

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        default: null,
    },
})

const proxyChecked = computed({
    get() {
        return props.checked
    },

    set(val) {
        emit('update:checked', val)
    },
})
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        :class="proxyChecked ? 'w-4 h-4 rounded-full text-pink-500 bg-transparent border-transparent focus:ring-0 focus:ring-offset-0' : 'w-4 h-4 rounded-full text-pink-500 bg-transparent focus:ring-0 focus:ring-offset-0 border-gray-300 dark:border-gray-400'"
    />

</template>
