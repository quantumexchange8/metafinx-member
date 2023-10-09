<script setup>
const { title, caption, size } = defineProps({
    title: String,
    caption: String,
    size: {
        type: String,
        default: '3xl'
    }
});

const getFontSize = (size) => {
    const sizes = {
        '3xl': 'text-3xl',
        '4xl': 'text-4xl',
    };

    return sizes[size] || '3xl';
};
</script>

<template>
    <div class="text-center mb-6 md:text-4xl" :class="['text-white', 'font-semibold', getFontSize(size)]">
        <h2 class="mb-2">{{ title }}</h2>
        <p class="text-sm text-gray-400">{{ caption }}</p>
    </div>
</template>
