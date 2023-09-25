<script setup>
const { title, caption, size } = defineProps({
    title: String,
    caption: String,
    size: {
        type: String,
        default: '4xl'
    }
});

const getFontSize = (size) => {
    const sizes = {
        '3xl': 'text-3xl',
        '4xl': 'text-4xl',
    };

    return sizes[size] || '4xl';
};
</script>

<template>
    <div class="text-center mb-6" :class="['text-white', 'font-semibold', getFontSize(size)]">
        <h2 class="mb-2">{{ title }}</h2>
        <p class="text-sm text-gray-400">{{ caption }}</p>
    </div>
</template>
