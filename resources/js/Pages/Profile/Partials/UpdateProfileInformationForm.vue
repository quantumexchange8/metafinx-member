<script setup>
import InputError from '@/Components/InputError.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import { UserIcon, UsersIcon } from "@/Components/Icons/outline.jsx";
import { MailIcon, PhoneIcon, KeyIcon, HomeIcon } from '@heroicons/vue/outline'
import BaseListbox from "@/Components/BaseListbox.vue";
import {computed, ref, watch} from "vue";
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


const FilePond = vueFilePond(
    FilePondPluginFilePoster,
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType,
)

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    countries: Array,
    frontIdentityImg: String,
    backIdentityImg: String,
    profileImg: String,
})

const user = usePage().props.auth.user

const identityNumber = (user.identity_number || '').toString(); // Ensure it's a string, even if it's initially undefined or not a string
const lastFourDigits = identityNumber.slice(-4); // Get the last 4 digits
const formattedIdentityNumber = '********' + lastFourDigits;

// Now, formattedIdentityNumber contains '******7899'


const form = useForm({
    name: user.name,
    email: user.email,
    country: user.country,
    phone: user.phone,
    address_1: user.address_1,
    address_2: user.address_2,
    identity_number: formattedIdentityNumber,
    proof_front: props.frontIdentityImg,
    proof_back: props.backIdentityImg,
    profile_photo: props.profileImg,
})

// const formattedIdentityNumber = computed(() => {
  
//     const hashedPart = '******';
//     const lastPart = form.identity_number.slice(-4);
//     return hashedPart + lastPart;
  
// });


const selectedCountry = ref(form.country);

function onchangeDropdown() {
    const selectedCountryName = selectedCountry.value;
    const country = props.countries.find((country) => country.label === selectedCountryName);

    if (country) {
        form.phone = `${country.phone_code}`;
        form.country = selectedCountry;
    }
}

const submit = () => {
    form.post(route('profile.update'))
}

watch(selectedCountry, () => {
    onchangeDropdown();
});

const proofFront = ref([]);
const proofBack = ref([]);
const profilePic = ref([]);
const handleFrontLoad = (response) => {
    return form.proof_front = response
}

const handleBackLoad = (response) => {
    return form.proof_back = response
}

const handleProfilePhotoLoad = (response) => {
    return form.profile_photo = response
}

const handleFrontInit = () => {
    if (form.proof_front) {
        proofFront.value = [{
            source: form.proof_front,
            options: {
                type: 'local',
                metadata: {
                    poster: form.proof_front
                }
            }
        }]
    }
}

const handleBackInit = () => {
    if (form.proof_back) {
        proofBack.value = [{
            source: form.proof_back,
            options: {
                type: 'local',
                metadata: {
                    poster: form.proof_back
                }
            }
        }]
    }
}

const handleAvatarInit = () => {
    if (form.profile_photo) {
        profilePic.value = [{
            source: form.profile_photo,
            options: {
                type: 'local',
                metadata: {
                    poster: form.profile_photo
                }
            }
        }]
    }
}

const handleFrontRevert = (uniqueId, load, error) => {
    axios.post('/profile/upload/image-revert', {
        proof_front: form.proof_front,
    });
    load();
}

const handleBackRevert = (uniqueId, load, error) => {
    axios.post('/profile/upload/image-revert', {
        proof_back: form.proof_back,
    });
    load();
}

const handleProfilePhotoRevert = (uniqueId, load, error) => {
    axios.post('/profile/upload/image-revert', {
        profile_photo: form.profile_photo,
    });
    load();
}

</script>

<template>
    <section>
        <form
            @submit.prevent="form.patch(route('profile.update'))"

        >
            <header class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Personal Information
                </h2>
            </header>
            <hr class="h-px mt-3 bg-gray-200 border-0 dark:bg-gray-700">

            <section class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- personal details -->
                <div class="space-y-5">
                    <div>
                        <Label class="text-[14px] dark:text-white mb-2" for="name" value="Name" />

                        <Input
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            disabled
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <Label class="text-[14px] dark:text-white mb-2" for="country" value="Country" />

                        <BaseListbox
                            v-model="selectedCountry"
                            :options="props.countries"
                            :error="form.errors.country"
                            disabled
                        />

                        <InputError class="mt-2" :message="form.errors.country" />
                    </div>

                    <div>
                        <Label class="text-[14px] dark:text-white mb-2" for="phone" value="Phone Number" />

                        <InputIconWrapper>
                            <template #icon>
                                <PhoneIcon aria-hidden="true" class="w-5 h-5 text-gray-400" />
                            </template>
                            <Input
                                withIcon id="phone" type="text" placeholder="+6011-0000 0000" class="block w-full" v-model="form.phone" required disabled autocomplete="phone"
                                :class="form.errors.phone ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            />
                        </InputIconWrapper>

                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div>
                        <Label class="text-[14px] dark:text-white mb-2" for="email" value="Email" />

                        <InputIconWrapper>
                            <template #icon>
                                <MailIcon aria-hidden="true" class="w-5 h-5" />
                            </template>
                            <Input
                                withIcon id="email" type="email" class="block w-full" disabled placeholder="Email" v-model="form.email" required autocomplete="username"
                                :class="form.errors.email ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                            />
                        </InputIconWrapper>

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="space-y-3">
                        <div>
                            <Label class="text-[14px] dark:text-white mb-2" for="address_1" value="Address" />

                            <InputIconWrapper>
                                <template #icon>
                                    <HomeIcon aria-hidden="true" class="w-5 h-5" />
                                </template>
                                <Input
                                    withIcon id="address_1" type="text" class="block w-full" disabled placeholder="Line 1" v-model="form.address_1" required autocomplete="address_1"
                                    :class="form.errors.address_1 ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                                />
                            </InputIconWrapper>


                            <InputError class="mt-2" :message="form.errors.address_1" />
                        </div>

                        <div>
                            <InputIconWrapper>
                                <template #icon>
                                    <HomeIcon aria-hidden="true" class="w-5 h-5" />
                                </template>
                                <Input
                                    withIcon id="address2" type="text" class="block w-full" disabled placeholder="Line 2 (Optional)" v-model="form.address_2" autocomplete="address_2"
                                    :class="form.errors.address_2 ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"
                                />
                            </InputIconWrapper>

                            <InputError class="mt-2" :message="form.errors.address_2" />
                        </div>

                    </div>

                    <div
                        v-if="props.mustVerifyEmail && user.email_verified_at === null"
                    >
                        <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            >
                                Click here to re-send the verification email.
                            </Link>
                        </p>

                        <div
                            v-show="props.status === 'verification-link-sent'"
                            class="mt-2 font-medium text-sm text-green-600 dark:text-green-400"
                        >
                            A new verification link has been sent to your email address.
                        </div>
                    </div>
                </div>
                <!-- personal kyc -->
                <div class="space-y-5">
                    <div>
                        <Label class="text-[14px] dark:text-white mb-2" for="name" value="Identification Number" />
                        <Input type="text" class="block w-full" disabled v-model="form.identity_number" required autocomplete="username"
                                :class="form.errors.email ? 'border border-error-500 dark:border-error-500' : 'border border-gray-400 dark:border-gray-600'"/>
                    </div>

                    <div>
                        <Label class="text-[14px] dark:text-white mb-2" for="proof_front" value="Proof of Indentity (FRONT)" />
                        <file-pond
                            name="proof_front"
                            ref="pond"
                            :imagePreviewMaxHeight="150"
                            :filePosterMaxHeight="150"
                            v-bind:allow-multiple="false"
                            accepted-file-types="image/png, image/jpeg, image/jpg"
                            v-bind:server="{
                            url: '',
                            timeout: 7000,
                            process: {
                                url: '/profile/upload/tmp_img',
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
                            v-bind:files="proofFront"
                            v-on:init="handleFrontInit"
                        />
                        <InputError :message="form.errors.proof_front" class="mt-2" />
                    </div>
                    <div>
                        <Label class="text-[14px] dark:text-white mb-2" for="proof_back" value="Proof of Indentity (BACK)" />

                        <file-pond
                                name="proof_back"
                                ref="pond"
                                :imagePreviewMaxHeight="150"
                                :filePosterMaxHeight="150"
                                v-bind:allow-multiple="false"
                                accepted-file-types="image/png, image/jpeg, image/jpg"
                                v-bind:server="{
                                url: '',
                                timeout: 7000,
                                process: {
                                    url: '/profile/upload/tmp_img',
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
                                v-bind:files="proofBack"
                                v-on:init="handleBackInit"
                            />
                            <InputError :message="form.errors.proof_back" class="mt-2" />
                    </div>
                    <div>
                        <Label class="text-[14px] dark:text-white mb-2" for="profile_photo" value="Profile Photo" />

                        <file-pond
                                    name="profile_photo"
                                    ref="pond"
                                    :imagePreviewMaxHeight="150"
                                    :filePosterMaxHeight="150"
                                    v-bind:allow-multiple="false"
                                    accepted-file-types="image/png, image/jpeg, image/jpg"
                                    v-bind:server="{
                                    url: '',
                                    timeout: 7000,
                                    process: {
                                        url: '/profile/upload/tmp_img',
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': $page.props.csrf_token
                                        },
                                        withCredentials: false,
                                        onload: handleProfilePhotoLoad,
                                        onerror: () => {}
                                    },
                                    revert: handleProfilePhotoRevert
                                }"
                                    v-bind:files="profilePic"
                                    v-on:init="handleAvatarInit"
                                />
                                <InputError :message="form.errors.profile_photo" class="mt-2" />
                    </div>
                </div>

            </section>

            <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
            <div class="flex justify-end">
                <Button :disabled="form.processing">Save Changes</Button>

            </div>

        </form>
    </section>
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
</style>
