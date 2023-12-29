<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { EyeOffIcon, EyeIcon } from '@heroicons/vue/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import InputError from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'

const passwordInput = ref(null)
const currentPasswordInput = ref(null)

const showPassword = ref(false);
const showPassword2 = ref(false);
const showPassword3 = ref(false);

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

const togglePasswordVisibilityNew = () => {
    showPassword2.value = !showPassword2.value;
}

const togglePasswordVisibilityConfirm = () => {
    showPassword3.value = !showPassword3.value;
}

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation')
                passwordInput.value.focus()
            }
            if (form.errors.current_password) {
                form.reset('current_password')
                currentPasswordInput.value.focus()
            }
        },
    })
}
</script>

<template>
    <section>
        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <header class="flex justify-between items-center gap-2">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{$t('public.profile.update_password')}}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{$t('public.profile.password_warning_message')}}.
                    </p>
                </div>
            </header>
            <hr class="h-px mt-3 bg-gray-200 border-0 dark:bg-gray-700">

            <section class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-5">
                    <div>
                        <Label for="current_password" :value="$t('public.profile.current_password')" />
                        
                        <InputIconWrapper>
                            <Input
                                id="current_password"
                                ref="currentPasswordInput"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.current_password"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="current-password"
                            />
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                                @click="togglePasswordVisibility"
                            >
                                <template v-if="showPassword">
                                    <EyeIcon aria-hidden="true" class="w-5 h-5" />
                                </template>
                                <template v-else>
                                    <EyeOffIcon aria-hidden="true" class="w-5 h-5" />
                                </template>
                            </div>
                        </InputIconWrapper>

                        <InputError
                            :message="form.errors.current_password"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <Label for="password" :value="$t('public.profile.new_password')" />

                        <InputIconWrapper>
                            <Input
                                id="password"
                                ref="passwordInput"
                                :type="showPassword2 ? 'text' : 'password'"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                            />
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                                @click="togglePasswordVisibilityNew"
                            >
                                <template v-if="showPassword2">
                                    <EyeIcon aria-hidden="true" class="w-5 h-5" />
                                </template>
                                <template v-else>
                                    <EyeOffIcon aria-hidden="true" class="w-5 h-5" />
                                </template>
                            </div>
                        </InputIconWrapper>

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>

                    <div>
                        <Label for="password_confirmation" :value="$t('public.profile.confirm_password')" />
                        
                        <InputIconWrapper>
                            <Input
                                id="password_confirmation"
                                :type="showPassword3 ? 'text' : 'password'"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                            />
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                                @click="togglePasswordVisibilityConfirm"
                            >
                                <template v-if="showPassword3">
                                    <EyeIcon aria-hidden="true" class="w-5 h-5" />
                                </template>
                                <template v-else>
                                    <EyeOffIcon aria-hidden="true" class="w-5 h-5" />
                                </template>
                            </div>
                        </InputIconWrapper>

                        <InputError
                            :message="form.errors.password_confirmation"
                            class="mt-2"
                        />
                    </div>
                </div>

            </section>
            <div class="flex justify-end">
                <Button :disabled="form.processing">{{$t('public.profile.save_changes')}}</Button>

            </div>
        </form>



            <!-- <div class="flex items-center gap-4">
                <Button :disabled="form.processing">Save</Button>

                <Transition
                    enter-from-class="opacity-0"
                    leave-to-class="opacity-0"
                    class="transition ease-in-out"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Saved.
                    </p>
                </Transition>
            </div> -->
    </section>
</template>
