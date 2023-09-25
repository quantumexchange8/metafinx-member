<script setup>
import { MailIcon } from '@heroicons/vue/outline'
</script>

<template>
    <footer class="px-8 pb-2 flex items-center w-full justify-between gap-1 text-sm text-gray-600 dark:text-gray-400">
        <div>
            &copy; METABASE FinX 2023
        </div>
        <div class="flex items-center justify-center gap-1 text-sm text-gray-600 dark:text-gray-400">
            <span>
                <MailIcon class="w-6 h-6" />

                <span className="sr-only">Love</span>
            </span>
                <a
                    href="mailto:metabasefinx.admin@gmail.com"
                    target="_blank"
                    class="hover:underline"
                >
                    metabasefinx.admin@gmail.com
                </a>
        </div>
    </footer>
</template>
