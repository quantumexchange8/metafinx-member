<script setup>
import {Head, Link, useForm} from '@inertiajs/vue3'
import { MailIcon, KeyIcon, ShieldCheckIcon, PhoneIcon, HomeIcon, EyeOffIcon, EyeIcon } from '@heroicons/vue/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import ValidationErrors from '@/Components/ValidationErrors.vue'
import Button from '@/Components/Button.vue'
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import PageFooter from "@/Components/PageFooter.vue";
import RegisterStepper from "@/Layouts/partials/RegisterStepper.vue";
import Caption from "@/Components/Auth/Caption.vue";
import { UserIcon, UsersIcon } from "@/Components/Icons/outline.jsx";
import {computed, ref, watch} from "vue";
import BaseListbox from "@/Components/BaseListbox.vue";
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";

// Import FilePond plugins
// Please note that you need to install these plugins separately

// Import image preview plugin styles
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";

// Import the plugin code
import FilePondPluginFilePoster from 'filepond-plugin-file-poster';

// Import the plugin styles
import 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css';

// Import image preview and file type validation plugins
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Terms from "@/Components/Terms.vue";

const signUpTerm = 'sign_up';

const FilePond = vueFilePond(
    FilePondPluginFilePoster,
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType,
)

const props = defineProps({
    countries: Array,
    referral: {
        type: String,
        default: null
    },
    position: {
        type: String,
    },
})
const formStep = ref(1);
const steps = [1, 2, 3, 4]; // Define the steps based on your form

const getCircleClass = (step) => ({
    'w-3 h-3': true,
    'bg-gray-100 dark:bg-gray-700': step !== formStep.value,
    'bg-pink-500': step === formStep.value,
    'rounded-full': true,
});

const stepDetails = {
    1: {
        title: 'Your details',
        caption: 'Make sure the details provided are accurate.'
    },
    2: {
        title: 'Choose a password',
        caption: 'Must be at least 8 characters'
    },
    3: {
        title: 'Your identity',
        caption: 'Verify your identity and secure your account.'
    },
    4: {
        title: 'Join your team',
        caption: 'Join with your friends to enjoy more benefits.'
    },
};

const computedTitle = computed(() => {
    return stepDetails[formStep.value]?.title || 'Register';
});

const computedCaption = computed(() => {
    return stepDetails[formStep.value]?.caption || 'Fill all fields';
});

function nextStep() {
    form.post(route('register.first.step'), {
        onSuccess: () => {
            formStep.value++;
            form.form_step++;
        },
    });
}

const form = useForm({
    form_step: 1,
    name: '',
    country: null,
    phone: '',
    email: '',
    address_1: '',
    address_2: '',
    password: '',
    password_confirmation: '',
    verification_type: 'nric',
    proof_front: '',
    proof_back: '',
    referral_code: props.referral,
    terms: false,
    identity_number: '',
    passport_number: '',
    terms: false,
    position: props.position,
})

const showPassword = ref(false);
const showPassword2 = ref(false);

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

const togglePasswordVisibilityConfirm = () => {
    showPassword2.value = !showPassword2.value;
}

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}

const selectedCountry = ref(form.country);

function onchangeDropdown() {
    const selectedCountryName = selectedCountry.value;
    const country = props.countries.find((country) => country.label === selectedCountryName);

    if (country) {
        form.phone = `${country.phone_code}`;
        form.country = selectedCountry;
    }
}

watch(selectedCountry, () => {
    onchangeDropdown();
});

const myFiles = ref([]);
const handleFrontLoad = (response) => {
    return form.proof_front = response
}

const handleBackLoad = (response) => {
    return form.proof_back = response
}

const handleFrontRevert = (uniqueId, load, error) => {
    axios.post('/upload/image-revert', {
        image: form.proof_front,
    });
    load();
}

const handleBackRevert = (uniqueId, load, error) => {
    axios.post('/upload/image-revert', {
        image: form.proof_back,
    });
    load();
}

</script>

<template>
    <Head :title="$t('public.register.register')" />

    <div class="flex justify-center min-h-screen">
        <!-- Row -->
        <div class="w-full flex">
            <!-- Col -->
            <div
                class="flex-col justify-between w-full h-auto hidden lg:flex lg:w-5/12 bg-gray-50 dark:bg-gray-900"
            >
                <Link href="https://metafinx.com/">
                    <ApplicationLogo class="w-48" />
                </Link>

                <div class="mt-5 ml-5">
                    <div class="mt-12">
                        <RegisterStepper
                            :formStep="formStep"
                        />
                    </div>
                </div>

                <PageFooter class="pb-6" />
            </div>
            <!-- Col -->
            <div class="w-full flex flex-col items-center lg:w-7/12 bg-white p-5 dark:bg-gray-800">
                <div class="mt-12 flex items-center justify-center w-10 h-10 border dark:border-gray-400 rounded-lg shadow mb-2">
                    <UserIcon v-if="formStep === 1" aria-hidden="true" class="h-6 w-6 text-gray-800 dark:text-white" />
                    <KeyIcon v-if="formStep === 2" aria-hidden="true" class="h-6 w-6 text-gray-800 dark:text-white" />
                    <ShieldCheckIcon v-if="formStep === 3" aria-hidden="true" class="h-6 w-6 text-gray-800 dark:text-white" />
                    <UsersIcon v-if="formStep === 4" aria-hidden="true" class="h-6 w-6 text-gray-800 dark:text-white" />
                </div>
                <Caption
                    :title="computedTitle"
                    :caption="computedCaption"
                    size="3xl"
                />
                <form class="w-full md:w-1/2">

                    <!-- Step 1 -->
                    <div v-if="formStep === 1" class="grid gap-6">
                        <div class="space-y-2">
                            <Label class="dark:text-white" for="name" :value="$t('public.register.name')" />
                            <InputIconWrapper>
                                <template #icon>
                                    <UserIcon aria-hidden="true" class="w-5 h-5 text-gray-400" />
                                </template>
                                <Input
                                    withIcon id="name"
                                    type="text"
                                    :placeholder="$t('public.register.name_placeholder')"
                                    class="block w-full" v-model="form.name"
                                    autofocus
                                    autocomplete="name"
                                    :class="form.errors.name ? 'border border-error-500 dark:border-error-500' : 'border border-gray-300 dark:border-gray-600'"
                                />
                            </InputIconWrapper>
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <Label class="dark:text-white" for="country" :value="$t('public.register.country')" />

                            <BaseListbox
                                v-model="selectedCountry"
                                :options="props.countries"
                                :error="form.errors.country"
                                :placeholder="$t('public.register.country_placeholder')"
                            />

                        </div>

                        <div class="space-y-2">
                            <Label class="dark:text-white" for="phone" :value="$t('public.register.phone')" />
                            <InputIconWrapper>
                                <template #icon>
                                    <PhoneIcon aria-hidden="true" class="w-5 h-5 text-gray-400" />
                                </template>
                                <Input
                                    withIcon
                                    id="phone"
                                    type="text"
                                    :placeholder="$t('public.register.phone_placeholder')"
                                    class="block w-full"
                                    v-model="form.phone"
                                    autocomplete="phone"
                                    :class="form.errors.phone ? 'border border-error-500 dark:border-error-500' : 'border border-gray-300 dark:border-gray-600'"
                                />
                            </InputIconWrapper>
                            <InputError :message="form.errors.phone" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <Label class="dark:text-white" for="email" :value="$t('public.register.email')" />
                            <InputIconWrapper>
                                <template #icon>
                                    <MailIcon aria-hidden="true" class="w-5 h-5 text-gray-400" />
                                </template>
                                <Input
                                    withIcon
                                    id="email"
                                    type="email"
                                    class="block w-full"
                                    :placeholder="$t('public.register.email_placeholder')"
                                    v-model="form.email"
                                    autocomplete="username"
                                    :class="form.errors.email ? 'border border-error-500 dark:border-error-500' : 'border border-gray-300 dark:border-gray-600'"
                                />
                            </InputIconWrapper>
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <Label class="dark:text-white" for="address_1" :value="$t('public.register.address')" />
                            <InputIconWrapper>
                                <template #icon>
                                    <HomeIcon aria-hidden="true" class="w-5 h-5 text-gray-400" />
                                </template>
                                <Input
                                    withIcon
                                    id="address_1"
                                    type="text"
                                    class="block w-full"
                                    :placeholder="$t('public.register.address_placeholder_line_1')"
                                    v-model="form.address_1"
                                    autocomplete="address_1"
                                    :class="form.errors.address_1 ? 'border border-error-500 dark:border-error-500' : 'border border-gray-300 dark:border-gray-600'"
                                />
                            </InputIconWrapper>
                            <InputError :message="form.errors.address_1" class="mt-2" />

                            <InputIconWrapper>
                                <template #icon>
                                    <HomeIcon aria-hidden="true" class="w-5 h-5 text-gray-400" />
                                </template>
                                <Input
                                    withIcon
                                    id="address_2"
                                    type="text"
                                    class="block w-full border border-gray-300 dark:border-gray-600"
                                    :placeholder="$t('public.register.address_placeholder_line_2')"
                                    v-model="form.address_2"
                                    autocomplete="address_2"
                                />
                            </InputIconWrapper>
                        </div>

                    </div>

                    <!-- Step 2 -->
                    <div v-if="formStep === 2" class="grid gap-6">
                        <div class="space-y-2">
                            <Label class="dark:text-white" for="password" :value="$t('public.register.password')" />
                            <InputIconWrapper>
                                <template #icon>
                                    <KeyIcon aria-hidden="true" class="w-5 h-5 text-gray-400" />
                                </template>
                                <Input
                                    withIcon
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="block w-full"
                                    :placeholder="$t('public.register.password_placeholder')"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                    :class="form.errors.password ? 'border border-error-500 dark:border-error-500' : 'border border-gray-300 dark:border-gray-600'"
                                />
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                                    @click="togglePasswordVisibility"
                                >
                                    <template v-if="showPassword">
                                        <EyeIcon aria-hidden="true" class="w-5 h-5 text-gray-400" />
                                    </template>
                                    <template v-else>
                                        <EyeOffIcon aria-hidden="true" class="w-5 h-5" />
                                    </template>
                                </div>
                            </InputIconWrapper>
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <Label class="dark:text-white" for="password_confirmation" :value="$t('public.register.confirm_password')" />
                            <InputIconWrapper>
                                <template #icon>
                                    <KeyIcon aria-hidden="true" class="w-5 h-5 text-gray-400" />
                                </template>
                                <Input
                                    withIcon
                                    id="password_confirmation"
                                    :type="showPassword2 ? 'text' : 'password'"
                                    class="block w-full"
                                    :placeholder="$t('public.register.confirm_password_placeholder')"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    :class="form.errors.password_confirmation ? 'border border-error-500 dark:border-error-500' : 'border border-gray-300 dark:border-gray-600'"
                                />
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                                    @click="togglePasswordVisibilityConfirm"
                                >
                                    <template v-if="showPassword2">
                                        <EyeIcon aria-hidden="true" class="w-5 h-5" />
                                    </template>
                                    <template v-else>
                                        <EyeOffIcon aria-hidden="true" class="w-5 h-5" />
                                    </template>
                                </div>
                            </InputIconWrapper>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div v-if="formStep === 3" class="grid gap-6">

                        <div class="space-y-2">
                            <Label class="dark:text-white" for="name" :value="$t('public.register.identification_type')" />
                            <div class="flex gap-x-12">
                                <div class="flex">
                                    <input type="radio" name="verification_type" v-model="form.verification_type" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-pink-500 focus:ring-pink-500 dark:bg-gray-800 dark:border-gray-400 dark:checked:bg-pink-500 dark:checked:border-pink-500 dark:focus:ring-offset-gray-800" id="hs-radio-group-1" value="nric">
                                    <label for="hs-radio-group-1" class="text-sm text-gray-600 ml-2 dark:text-white">NRIC</label>
                                </div>

                                <div class="flex">
                                    <input type="radio" name="verification_type" v-model="form.verification_type" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-pink-500 focus:ring-pink-500 dark:bg-gray-800 dark:border-gray-400 dark:checked:bg-pink-500 dark:checked:border-pink-500 dark:focus:ring-offset-gray-800" id="hs-radio-group-2" value="passport">
                                    <label for="hs-radio-group-2" class="text-sm text-gray-600 ml-2 dark:text-white">Passport</label>
                                </div>
                            </div>
                            <InputError :message="form.errors.verification_type" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <Label class="dark:text-white" for="identification_number" :value="$t('public.register.nric_passport_number')" />
                            <Input
                            id="identification_number"
                            type="text"
                            class="block w-full"
                            v-model="form.identity_number"
                            :placeholder="$t('public.register.identification_placeholder')"
                            :class="form.errors.identity_number ? 'border border-error-500 dark:border-error-500' : 'border border-gray-300 dark:border-gray-600'"
                            />

                            <InputError :message="form.errors.identity_number" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <Label class="dark:text-white" for="password" :value="$t('public.register.proof_of_identity(f)')" />
                            <file-pond
                                name="proof_front"
                                ref="pond"
                                v-bind:allow-multiple="false"
                                accepted-file-types="image/png, image/jpeg, image/jpg"
                                v-bind:server="{
                                url: '',
                                timeout: 7000,
                                process: {
                                    url: '/upload/tmp_img',
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $page.props.csrf_token
                                    },
                                    withCredentials: false,
                                    onload: handleFrontLoad,
                                    onerror: () => {}
                                },
                                revert: handleFrontRevert
                            }"
                                v-bind:files="myFiles"
                            />
                            <InputError :message="form.errors.proof_front" class="mt-2" />
                        </div>

                        <div class="space-y-2">
                            <Label class="dark:text-white" for="password" :value="$t('public.register.proof_of_identity(b)')" />
                            <file-pond
                                name="proof_back"
                                ref="pond"
                                v-bind:allow-multiple="false"
                                accepted-file-types="image/png, image/jpeg, image/jpg"
                                v-bind:server="{
                                url: '',
                                timeout: 7000,
                                process: {
                                    url: '/upload/tmp_img',
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $page.props.csrf_token
                                    },
                                    withCredentials: false,
                                    onload: handleBackLoad,
                                    onerror: () => {}
                                },
                                revert: handleBackRevert
                            }"
                                v-bind:files="myFiles"
                            />
                            <InputError :message="form.errors.proof_back" class="mt-2" />
                        </div>

                    </div>

                    <!-- Step 4 -->
                    <div v-if="formStep === 4" class="grid gap-6">

                        <div class="space-y-2">
                            <Label class="dark:text-white" for="referral_code" :value="$t('public.register.referral_code')" />
                            <Input
                                id="referral_code"
                                type="text"
                                :placeholder="$t('public.register.referral_code_placeholder')"
                                class="block w-full"
                                :class="form.errors.referral_code ? 'border border-error-500 dark:border-error-500' : 'border border-gray-300 dark:border-gray-600'"
                                v-model="form.referral_code"
                                autocomplete="referral_code" />
                            <InputError :message="form.errors.referral_code" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <label>
                                <div class="flex">
                                    <Checkbox name="remember" v-model:checked="form.terms" />
                                    <span class="ml-2 text-xs dark:text-gray-400">{{ $t('public.agreement') }}
                                        <Terms 
                                        :type=signUpTerm 
                                        />
                                    </span>
                                </div>
                                <InputError v-if="form.errors.terms" :message="form.errors.terms" class="mt-2" />
                            </label>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Button
                            class="justify-center gap-2 w-full"
                            v-if="formStep !== 4"
                            @click.prevent="nextStep"
                        >
                            <span>{{$t('public.register.continue')}}</span>
                        </Button>
                        <Button
                            class="justify-center gap-2 w-full"
                            v-if="formStep === 4"
                            @click="submit"
                            :disabled="form.processing"
                        >
                            <span>{{$t('public.register.sign_up')}}</span>
                        </Button>
                    </div>

                    <div class="mt-6 flex justify-center gap-4">
                        <span v-for="step in steps" :key="step" :class="getCircleClass(step)"></span>
                    </div>

                </form>
            </div>
        </div>
    </div>

</template>

<style>
.filepond--panel-root {
    background-color: #4D5761;
}

.filepond--drop-label {
    color: #9DA4AE;
}

.filepond--label-action {
    color: white;
    text-decoration-color: white;
}

[data-filepond-item-state*='error'] .filepond--item-panel,
[data-filepond-item-state*='invalid'] .filepond--item-panel {
    background-color: #F04438;
}

[data-filepond-item-state='processing-complete'] .filepond--item-panel {
    background-color: #039855;
}

@media (max-width: 30em) {
    .filepond--item {
        width: calc(50% - 0.5em);
    }
}
</style>
