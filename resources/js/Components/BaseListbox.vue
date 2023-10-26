<script setup>
import {computed, ref} from 'vue'
import {
    Listbox,
    ListboxLabel,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
} from '@headlessui/vue'
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/solid'

const props = defineProps({
    options: Array,
    modelValue: [String, Number, Array],
    placeholder: {
        type: String,
        default: 'Please Select'
    },
    multiple: Boolean,
    error: String
})

const emit = defineEmits(['update:modelValue'])

const label = computed(() => {
    return props.options.filter(option => {
        if (Array.isArray(props.modelValue)) {
            return props.modelValue.includes(option.value);
        }

        return props.modelValue === option.value;
    }).map(option => option.label).join(', ')
})
</script>

<template>
    <Listbox
        :multiple="props.multiple"
        :model-value="props.modelValue"
        @update:modelValue="value => emit('update:modelValue', value)"
    >
        <div class="relative mt-1">
            <ListboxButton
                class="relative w-full cursor-default rounded-lg bg-white dark:bg-gray-600 py-2.5 pl-3 pr-10 text-left shadow-md focus:outline-none focus-visible:border-pink-500 focus-visible:ring-2 focus-visible:ring-pink-500 focus-visible:ring-opacity-100 focus-visible:ring-offset-2 focus-visible:ring-offset-pink-500"
                :class="[
                    { 'border border-pink-500': error }
                ]"
            >
                <span class="block truncate dark:text-white" v-if="label">{{ label }}</span>
                <span v-else class="dark:text-gray-400">{{ props.placeholder }}</span>
                <span
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
                >
            <ChevronDownIcon
                class="h-5 w-5 text-gray-400"
                aria-hidden="true"
            />
          </span>
            </ListboxButton>

            <transition
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions
                    class="z-10 absolute mt-2 max-h-60 w-full overflow-auto rounded-md bg-white dark:bg-gray-600 py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                >
                    <ListboxOption
                        v-slot="{ active, selected }"
                        v-for="option in props.options"
                        :key="option.label"
                        :value="option.value"
                        as="template"
                    >
                        <li
                            :class="[
                  active ? 'bg-gray-500 dark:text-white' : 'text-gray-900 dark:text-white',
                  'relative cursor-default select-none py-2 px-4',
                ]"
                        >
                <span
                    :class="[
                    selected ? 'font-medium' : 'font-normal',
                    'block truncate',
                  ]"
                >{{ option.label }}</span
                >
                            <span
                                v-if="selected"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 dark:text-white"
                            >
                  <CheckIcon class="h-5 w-5" aria-hidden="true" />
                </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
            <div class="text-sm text-error-500 mt-2" v-if="props.error">{{ props.error }}</div>
        </div>
    </Listbox>
</template>
