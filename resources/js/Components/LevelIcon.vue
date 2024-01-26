<script setup>
import {LVL1Icon, LVL2Icon, LVL3Icon, UserPlus01Icon} from "@/Components/Icons/outline.jsx";

const props = defineProps({
    level: Number
})
</script>

<template>
    <LVL1Icon v-if="level === 1" class="h-4" />
    <LVL2Icon v-if="level === 2" class="h-4" />
    <LVL2Icon v-if="level === 3" class="h-4" />
</template>
