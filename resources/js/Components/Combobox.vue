<script setup>
import {computed, inject, ref, watch} from "vue";
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    TransitionRoot,
} from "@headlessui/vue";
import {ChevronDownIcon, XCircleIcon, XIcon} from "@heroicons/vue/solid";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    modelValue: [Object, Array],
    options: {
        type: Array,
        default: () => [],
    },
    loadOptions: Function,
    createOption: Function,
    nullable: Boolean,
    multiple: Boolean,
    placeholder: String,
    id: String,
    error: String,
    ariaDescribedBy: String,
    image: {
        type: Boolean,
        default: false,
    },
});

const field = inject("field", props);
const inputRef = ref();
const options = ref(props.options);
const isLoadingOptions = ref(false);
const isCreatingOption = ref(false);
const query = ref("");

const queryOption = computed(() => {
    if (!props.createOption) {
        return null;
    }

    if (query.value === "") {
        return null;
    }

    if (filteredOptions.value.length) {
        return null;
    }

    if (
        props.multiple &&
        props.modelValue?.some(
            option => option?.label.toLowerCase() === query.value?.toLowerCase()
        )
    ) {
        return null;
    }

    return {
        missing: true,
        label: query.value,
    };
});

if (props.loadOptions) {
    watch(
        query,
        q => {
            isLoadingOptions.value = true;

            props.loadOptions(q, results => {
                options.value = results;
                isLoadingOptions.value = false;
            });

            inputRef.value?.$el?.setAttribute("size", q.length || 1);
        },
        {immediate: true}
    );
}

const filteredOptions = computed(() => {
    let selectedOptions = [];

    if (props.multiple) {
        selectedOptions = props.modelValue ?? [];
    } else if (props.modelValue) {
        selectedOptions = [props.modelValue];
    }

    const result = [
        ...selectedOptions.filter(
            option => !options.value.some(opt => opt?.value === option.value)
        ),
        ...options.value,
    ];

    if (!query.value) {
        return result;
    }

    return result.filter(option => {
        return option?.label
            .toLowerCase()
            .replace(/\s+/g, "")
            .includes(query.value.toLowerCase().replace(/\s+/g, ""));
    });
});

function handleUpdateModelValue(selected) {
    emit("update:modelValue", selected);
    query.value = "";
    inputRef.value?.$el?.focus();

    if (!props.createOption) return;

    if (props.multiple) {
        let newOption = selected.length ? selected[selected.length - 1] : undefined;

        if (newOption?.missing) {
            isCreatingOption.value = true;
            props.createOption(newOption, option => {
                selected[selected.length - 1] = option;
                emit("update:modelValue", [...selected]);
                isCreatingOption.value = false;
            });
        }
    } else {
        if (selected?.missing) {
            isCreatingOption.value = true;
            props.createOption(selected, option => {
                emit("update:modelValue", option);
                isCreatingOption.value = false;
            });
        }
    }
}

function remove(option) {
    emit(
        "update:modelValue",
        props.modelValue?.filter(opt => opt.value !== option.value)
    );
}

function handleQueryDelete() {
    if (!query.value) {
        emit("update:modelValue", props.modelValue?.slice(0, -1));
    }
}

function clearSelection() {
    emit("update:modelValue", props.multiple ? [] : null);
    inputRef.value?.$el?.focus();
}
</script>

<template>
    <Combobox
        v-slot="{open}"
        :aria-describedby="field.ariaDescribedBy"
        :model-value="props.modelValue"
        :multiple="props.multiple"
        :nullable="props.nullable"
        as="div"
        by="value"
        class="relative"
        @update:model-value="handleUpdateModelValue"
    >
        <ComboboxButton
            :class="{
                'border-pink-500 bg-white dark:bg-gray-600': open && !field.error,
                'border-error-500': !open && field.error,
                'border-gray-600': !open && !field.error,
              }"
            class="min-h-[2.5rem] relative flex w-full flex-wrap items-center gap-1 overflow-hidden rounded-lg border bg-white dark:bg-gray-600 p-1 pr-14 focus-within:border-pink-500 hover:bg-gray-100 focus:ring-0 dark:disabled:bg-gray-700 dark:disabled:border-transparent"
        >
            <template v-if="props.multiple">
                <div
                    v-for="(option, idx) in props.modelValue"
                    :key="idx"
                    class="inline-flex items-center overflow-hidden rounded-lg bg-gray-200 p-1.5 text-xs leading-none text-black"
                >
                    <span>{{ option.label }}</span>
                    <button
                        class="-my-1.5 ml-1 -mr-1.5 self-stretch px-1 outline-0 hover:text-error-500"
                        @click.prevent="remove(option)"
                    >
                        <XIcon class="h-3.5 w-3.5" />
                    </button>
                </div>
            </template>

            <ComboboxInput
                :id="field.id"
                ref="inputRef"
                :displayValue="option => (props.multiple ? query : option?.label)"
                :placeholder="
                    (props.multiple ? (!props.modelValue || !props.modelValue.length) : !props.modelValue)
                      ? props.placeholder
                      : 'Please Select'
                  "
                class="-mx-1 min-w-0 flex-grow cursor-pointer disabled:cursor-not-allowed border-none bg-transparent py-1 px-2 dark:text-white placeholder-gray-400 focus:ring-0 dark:disabled:placeholder-gray-600"
                size="1"
                @change="query = $event.target.value"
                @keydown.delete="props.multiple ? handleQueryDelete : undefined"
            />

            <div class="absolute inset-y-0 right-1 flex items-center">
                <button
                    v-if="
            Array.isArray(props.modelValue)
              ? props.modelValue.length
              : props.modelValue
          "
                    class="inline-flex h-full w-6 items-center justify-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:text-gray-700 focus:outline-none"
                    @click.prevent="clearSelection"
                >
                    <XCircleIcon
                        aria-hidden="true"
                        class="h-4 w-4"
                    />
                </button>

                <ChevronDownIcon
                    aria-hidden="true"
                    class="h-5 w-5 text-gray-500"
                />
            </div>
        </ComboboxButton>

        <TransitionRoot
            class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-lg bg-white dark:bg-gray-600 py-1 text-base shadow-md ring-1 ring-black ring-opacity-5 focus:outline-none"
            leave="transition ease-in duration-100"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
            @after-leave="query = ''"
        >
            <ComboboxOptions class="min-w-fit">
                <li
                    v-if="!filteredOptions.length && !isLoadingOptions && !queryOption"
                    class="relative cursor-default select-none py-2 px-3 text-sm text-gray-700 dark:text-white"
                >
                    No options...
                </li>

                <li
                    v-if="isLoadingOptions || isCreatingOption"
                    class="relative cursor-default select-none py-2 px-3 text-sm text-gray-700 dark:text-white"
                >
                    Loading...
                </li>

                <template v-if="!isLoadingOptions && !isCreatingOption">
                    <ComboboxOption
                        v-if="queryOption"
                        v-slot="{active}"
                        :value="queryOption"
                        as="template"
                    >
                        <li
                            :class="{
                                'bg-gray-200 shadow-[inset_2px_0px_0px] shadow-pink-500':
                                  active,
                              }"
                            class="relative cursor-default select-none whitespace-pre py-2 px-3 text-sm dark:text-white active:bg-gray-300"
                        >
                            Create "{{ queryOption.label }}"
                        </li>
                    </ComboboxOption>

                    <ComboboxOption
                        v-for="option in filteredOptions"
                        :key="option.value"
                        v-slot="{selected, active}"
                        :value="option"
                        as="template"
                    >
                        <li
                            :class="{
                                'shadow-[inset_2px_0px_0px] shadow-pink-500':
                                  active || selected,
                                'bg-gray-100': selected,
                                'bg-gray-200': active,
                              }"
                            class="relative inline-flex items-center gap-2 w-full cursor-default select-none whitespace-pre py-2 px-3 text-sm dark:text-white active:bg-gray-300 dark:bg-gray-600"
                        >
                            <img v-if="image" :src="option.img ? option.img : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-8 h-8 rounded-full" alt="">
                            <span
                                class="block truncate"
                                :class="{ 'font-medium': selected, 'font-normal': !selected }"
                            >
                                    {{ option.label }}
                                </span>
                        </li>
                    </ComboboxOption>
                </template>
            </ComboboxOptions>
        </TransitionRoot>
    </Combobox>
</template>
