<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: [String, Number],
    },
    keyIndex: String,
    valueIndex: String,
    labelIndex: String,
    data: Array,
    placeholder: {
        type: String,
        default: 'Please Select'
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);

const focus = () => input.value?.focus();

defineExpose({
    input,
    focus
});

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>

<template>
    <select
        :class="[
            'py-2 border-gray-400 rounded-lg placeholder:text-gray-400 text-gray-800',
            'focus:border-pink-700 focus:ring focus:ring-pink-500 focus:ring-offset-0 focus:ring-offset-white',
            'dark:border-gray-600 dark:bg-gray-600 dark:text-white',
            ]"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        ref="input"
    >
        <option class="text-sm" value="" disabled selected>{{ placeholder }}</option>
        <slot></slot>
    </select>
</template>
