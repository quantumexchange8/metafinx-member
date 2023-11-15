<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
    href: String
});
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        class="block w-full p-2 rounded-lg text-sm leading-5 text-left text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:focus:text-white dark:focus:bg-dark-eval-3 dark:text-white dark:hover:text-white dark:hover:bg-gray-500"
    >
        <slot />
    </Link>
    <span
        v-else
        class="block w-full p-2 rounded-lg text-sm leading-5 text-left text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:focus:text-white dark:focus:bg-dark-eval-3 dark:text-white dark:hover:text-white dark:hover:bg-gray-500 cursor-pointer"
    >
      <slot />
    </span>

</template>
