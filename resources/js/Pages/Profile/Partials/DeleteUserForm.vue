<script setup>
import { nextTick, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import InputError from '@/Components/InputError.vue'
import Modal from '@/Components/Modal.vue'
import Button from '@/Components/Button.vue'

const confirmingUserDeletion = ref(false)
const passwordInput = ref(null)

const form = useForm({
    password: '',
})

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true

    nextTick(() => passwordInput.value.focus())
}

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    })
}

const closeModal = () => {
    confirmingUserDeletion.value = false

    form.reset()
}
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{$t('public.profile.delete_account')}}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{$t('public.profile.warning_message')}}
            </p>
        </header>

        <Button variant="danger" @click="confirmUserDeletion">
            {{$t('public.profile.delete_account')}}
        </Button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2
                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    {{$t('public.profile.confirm_delete_message')}}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{$t('public.profile.warning_message')}}
                </p>

                <div class="mt-6">
                    <Label for="password" :value="$t('public.profile.password')" class="sr-only" />

                    <Input
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        :placeholder="$t('public.profile.password_placeholder')"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <Button variant="secondary" @click="closeModal">
                        {{$t('public.cancel')}}
                    </Button>

                    <Button
                        variant="danger"
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                    {{$t('public.profile.delete_account')}}
                    </Button>
                </div>
            </div>
        </Modal>
    </section>
</template>
