<script setup>
defineProps({
    message: String
})
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-error-500">
            {{ message }}
        </p>
    </div>
</template>
