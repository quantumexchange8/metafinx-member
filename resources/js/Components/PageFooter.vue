<script setup>
import { MailIcon } from '@heroicons/vue/outline'
</script>

<template>
    <footer class="px-6 pb-2 flex items-center w-full justify-between gap-1 text-xs md:text-sm text-gray-600 dark:text-gray-400">
        <div>
            &copy; METAFINX 2023
        </div>
        <div class="flex items-center justify-center gap-1 text-sm text-gray-600 dark:text-gray-400">
            <span>
                <MailIcon class="w-4 h-4 md:w-6 md:h-6" />

                <span className="sr-only">{{$t('public.email')}}</span>
            </span>
                <a
                    href="mailto:support@metafinx.com"
                    target="_blank"
                    class="hover:underline text-xs md:text-sm"
                >
                {{$t('public.metafinx_support')}}
                </a>
        </div>
    </footer>
</template>
