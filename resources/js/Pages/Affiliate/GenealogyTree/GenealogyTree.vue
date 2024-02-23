<script setup>
import {onMounted, onUnmounted, ref, watch, watchEffect} from 'vue';
import panzoom from '@panzoom/panzoom';
import Tooltip from "@/Components/Tooltip.vue";
import {LVL3Icon, Target02Icon, ZoomInIcon, ZoomOutIcon} from "@/Components/Icons/outline.jsx";
import Button from "@/Components/Button.vue";
import GenealogyChild from "@/Pages/Affiliate/GenealogyTree/GenealogyChild.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    RadioGroup,
    RadioGroupDescription,
    RadioGroupLabel,
    RadioGroupOption
} from '@headlessui/vue'
import {ArrowLeftIcon, ArrowRightIcon, ChevronDownIcon} from '@heroicons/vue/outline'
import axios from "axios";
import {TailwindPagination} from "laravel-vue-pagination";
import {transactionFormat} from "@/Composables/index.js";
import Modal from "@/Components/Modal.vue";
import Loading from "@/Components/Loading.vue";
import debounce from "lodash/debounce.js";

const binaryTree = ref({});
// Use refs to store functions
const handleZoomIn = ref(() => {});
const handleZoomOut = ref(() => {});
const handleRecenter = ref(() => {});
const isExpand = ref(false)
const root = ref({});
const { formatAmount, formatDateTime } = transactionFormat();

const props = defineProps({
    search: String,
    downline: Array,
})

watch(
    () => props.search,
    debounce((searchValue) => {
        getResults(searchValue);
    }, 300)
);

const getResults = async (search = '') => {
    try {
        let url = `/affiliate/getBinaryData`;

        if (search) {
            url += `?search=${search}`;
        }

        const response = await axios.get(url);
        binaryTree.value = response.data;
        root.value = response.data;
    } catch (error) {
        console.error(error);
    }
}

onMounted(() => {
    getResults();

    const element = binaryTree.value;

    if (element) {
        const pzInstance = panzoom(element, { canvas: true });
        const parent = element.parentElement;

        parent.addEventListener('wheel', pzInstance.zoomWithWheel);

        parent.addEventListener('wheel', function (event) {
            if (!event.shiftKey) return;
            pzInstance.zoomWithWheel(event);
        });

        // Define click functions here inside onMounted
        handleZoomIn.value = () => {
            pzInstance.zoomIn();
        };

        handleZoomOut.value = () => {
            pzInstance.zoomOut();
        };

        handleRecenter.value = () => {
            pzInstance.reset();
        };

    } else {
        console.error("Element with ID 'binary-tree' not found");
    }
});

watchEffect(() => {
    if (usePage().props.title !== null) {
        getResults();
    }
});

const handleExpand = () => {
    isExpand.value = !isExpand.value
}
const referralTableData = ref([]);
const isLoading = ref(false);
const currentPage = ref(1);
const search = ref('');
const getAffiliateResults = async (page = 1, search = '') => {
    isLoading.value = true;
    try {
        let url = `/affiliate/getAvailableBinaryAffiliate?page=${page}`;

        if (search) {
            url += `&search=${search}`;
        }

        const response = await axios.get(url);
        referralTableData.value = response.data;
    } catch (error) {
        console.error(error);
        console.error("Error fetching data:", error.message);
    } finally {
        isLoading.value = false;
    }
};

getAffiliateResults();

const paginationClass = [
    'bg-transparent border-0 dark:text-gray-400 dark:enabled:hover:text-white'
];

const paginationActiveClass = [
    'border dark:border-gray-600 dark:bg-gray-600 rounded-full text-[#FF9E23] dark:text-white'
];

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;
        getAffiliateResults(currentPage.value, search.value);
    }
};

const timeLeft = ref('');
const calculateTimeLeft = (datetime) => {
    const currentTime = new Date(); // Get the current time

    // Create a Date object for today's date with the target time (12 AM)
    const targetTime = new Date(datetime);
    targetTime.setHours(0, 0, 0, 0); // Set target time to 12 AM

    // If the target time is earlier than the current time, set it to tomorrow
    if (targetTime.getTime() <= currentTime.getTime()) {
        targetTime.setDate(targetTime.getDate() + 1);
    }

    // Calculate the time difference
    const timeDiff = targetTime.getTime() - currentTime.getTime();

    const hours = Math.floor(timeDiff / (1000 * 60 * 60));
    const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

    timeLeft.value = `${hours}h ${minutes}m ${seconds}s`;
    return timeLeft.value; // Return the formatted time left
};

// Update the countdown every second
onMounted(() => {
    const interval = setInterval(() => {
        if (referralTableData.value.data.length > 0) {
            referralTableData.value.data.forEach(dataItem => {
                calculateTimeLeft(dataItem.auto_assign_at);
            });
        }
    }, 1000);

    setInterval(fetchPendingPlacementCount, 5000);

    // Clear the interval when the component is unmounted
    onUnmounted(() => {
        clearInterval(interval);
    });
});

const positions = [
    {
        name: 'left',
        value: 'left'
    },
    {
        name: 'right',
        value: 'right'
    }
]

const placementModal = ref(false);
const selectedDistributor = ref();
const selectPosition = ref(positions[0]);
const lastChild = ref();
const getPendingPlacementCount = ref();

const openPlacementModal = (distributor) => {
    placementModal.value = true;
    selectedDistributor.value = distributor;
    getLastChild();
}

const closeModal = () => {
    placementModal.value = false
}

watch(selectPosition, () => {
    getLastChild();
})

const getLastChild = async () => {
    try {
        let url = `/affiliate/getLastChild?position=${selectPosition.value.value}`;

        const response = await axios.get(url);
        lastChild.value = response.data;
    } catch (error) {
        console.error(error);
        console.error("Error fetching data:", error.message);
    }
}

const form = useForm({
    user_id: '',
    upline_id: '',
    position: ''
})

const submit = () => {
    form.position = selectPosition.value.value;
    form.user_id = selectedDistributor.value.id;
    form.upline_id = lastChild.value.id;
    form.post(route('affiliate.addDistributor'), {
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    });
};

watchEffect(() => {
    if (usePage().props.title !== null) {
        getAffiliateResults();
    }
});

const fetchPendingPlacementCount = async () => {
    try {
        const response = await axios.get('/affiliate/getPendingPlacementCount');
        getPendingPlacementCount.value = response.data;
    } catch (error) {
        console.error('Error fetching data:', error.message);
    }
};
const coinStackingExists = ref(false);

const checkCoinStackingExistence = async () => {
    try {
        const response = await axios.get('/affiliate/checkCoinStackingExistence');
        coinStackingExists.value = response.data; // Update coin stacking existence based on response
    } catch (error) {
        console.error('Error checking coin stacking existence:', error);
        coinStackingExists.value = false; // Set coin stacking existence to false in case of an error
    }
};

checkCoinStackingExistence();
</script>

<template>

    <div class="w-full py-5">
        <div class="w-full rounded-xl bg-gray-700 p-2">
            <Disclosure v-slot="{ open }">
                <DisclosureButton
                class="flex w-full justify-between rounded-lg bg-purple-100 px-4 py-2 text-left text-sm font-medium text-purple-900 hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500/75"
                >
                    <div class="flex items-center gap-2">
                        <div class="text-white text-base">{{ $t('public.pending_placement') }}</div>
                        <div v-if="getPendingPlacementCount > 0" class="bg-pink-500 w-5 h-5 rounded-full flex justify-center">
                            {{ getPendingPlacementCount }}
                        </div>
                    </div>
                <ChevronDownIcon
                    :class="open ? 'rotate-180 transform' : ''"
                    class="h-5 w-5 text-purple-500"
                />
                </DisclosureButton>
                <DisclosurePanel class="px-4 pb-2 text-sm text-gray-500">
                    <div v-if="isLoading" class="w-full flex justify-center my-8">
                        <Loading />
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
                            <thead
                                class="text-xs font-medium text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-gray-400 border-b dark:border-gray-600">
                                <tr>
                                    <th scope="col" class="p-3 uppercase">
                                        {{ $t('public.affiliate.members') }}
                                    </th>
                                    <th scope="col" class="p-3 uppercase">
                                        {{ $t('public.register_date') }}
                                    </th>
                                    <th scope="col" class="p-3 uppercase">
                                        {{ $t('public.placement_time_left') }}
                                    </th>
                                    <th scope="col" class="p-3 uppercase text-center">
                                        {{ $t('public.placement') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr v-if="referralTableData.data.length === 0">
                                <th colspan="4" class="py-4 text-lg text-center">
                                    {{$t('public.no_data')}}
                                </th>
                            </tr>
                                <tr
                                    class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600"
                                    v-for="referee in referralTableData.data"
                                >
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <img :src="referee.profile_photo ? referee.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-6 h-6 rounded-full" alt="">
                                            <div class="flex flex-col">
                                                <div class="text-xs font-medium">
                                                    {{ referee.name }}
                                                </div>
                                                <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                                    {{ referee.email }}
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                    <td class="p-3">
                                       <div>
                                           {{ formatDateTime(referee.created_at) }}
                                       </div>
                                    </td>
                                    <td class="p-3">
                                       <div>
                                           {{ calculateTimeLeft(referee.auto_assign_at) }}
                                       </div>
                                    </td>
                                    <td class="p-3 flex justify-center">
                                        <Button
                                            type="button"
                                            variant="gray"
                                            size="sm"
                                            @click="openPlacementModal(referee)"
                                        >
                                            {{ $t('public.place') }}
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-center mt-4" v-if="!isLoading">
                        <TailwindPagination
                            :item-classes=paginationClass
                            :active-classes=paginationActiveClass
                            :data="referralTableData"
                            :limit=2 @pagination-change-page="handlePageChange"
                        >
                            <template #prev-nav>
                                <span class="flex gap-2"><ArrowLeftIcon class="w-5 h-5" /> <span class="hidden sm:flex">{{$t('public.previous')}}</span></span>
                            </template>
                            <template #next-nav>
                                <span class="flex gap-2"><span class="hidden sm:flex">{{$t('public.next')}}</span> <ArrowRightIcon class="w-5 h-5" /></span>
                            </template>
                        </TailwindPagination>
                    </div>
                </DisclosurePanel>
            </Disclosure>
        </div>
    </div>

    <div class="flex items-center self-stretch justify-between mb-3">
        <div class="text-base font-normal text-gray-600 dark:text-gray-400">
            {{ $t('public.affiliate.note') }}
        </div>
        <div class="flex gap-3">
            <Tooltip :content="$t('public.zoom_in')" placement="bottom">
                <Button
                    type="button"
                    class="flex justify-center w-8 h-8 relative focus:outline-none"
                    variant="gray"
                    @click="handleZoomIn"
                    pill
                >
                    <ZoomInIcon aria-hidden="true" class="w-4 h-4 absolute" />
                    <span class="sr-only">{{ $t('public.zoom_in') }}</span>
                </Button>
            </Tooltip>
            <Tooltip :content="$t('public.zoom_out')" placement="bottom">
                <Button
                    type="button"
                    class="flex justify-center w-8 h-8 relative focus:outline-none"
                    variant="gray"
                    @click="handleZoomOut"
                    pill
                >
                    <ZoomOutIcon aria-hidden="true" class="w-4 h-4 absolute" />
                    <span class="sr-only">{{ $t('public.zoom_out') }}</span>
                </Button>
            </Tooltip>
            <Tooltip :content="$t('public.recenter')" placement="bottom">
                <Button
                    type="button"
                    class="flex justify-center w-8 h-8 relative focus:outline-none"
                    variant="gray"
                    @click="handleRecenter"
                    pill
                >
                    <Target02Icon aria-hidden="true" class="w-4 h-4 absolute" />
                    <span class="sr-only">{{ $t('public.recenter') }}</span>
                </Button>
            </Tooltip>
        </div>
    </div>

    <div class="relative overflow-hidden bg-gray-900 bg-[radial-gradient(#333d4b_2px,transparent_1px)] [background-size:52px_52px] min-h-[400px] max-h-[600px] mb-12">
        <div ref="binaryTree" class="flex flex-col justify-center items-center">
            <div class="container mx-auto text-center pt-24">
                <div class="items-center justify-center flex">
                    <div class="text-center">
                        <!-- <div v-if="root.sponsor_name" class="flex flex-col justify-center items-center">
                            <div class="w-60 rounded-lg dark:bg-gray-700 flex gap-2 p-3 items-center z-20">
                                <img :src="root.sponsor_profile_photo ? root.sponsor_profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-8 h-8 rounded-full" alt="">
                                <div class="flex flex-col gap-1">
                                    <div class="flex gap-2 items-center text-gray-900 dark:text-white">
                                        <div class="text-sm font-semibold">
                                            {{ root.sponsor_name }}
                                        </div>
                                        <div class="rounded-xl bg-warning-400 text-gray-800 font-normal text-xs px-2 text-center w-16">
                                           Sponsor
                                        </div>
                                    </div>
                                    <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                        {{ root.sponsor_email }}
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <GenealogyChild
                            :binaryTree="root"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Placement Modal-->
    <Modal :show="placementModal" :title="$t('public.new_placement')" @close="closeModal">
        <div class="flex flex-col gap-8">
            <div class="flex flex-col sm:flex-row gap-1 space-y-2 sm:space-y-0 items-start self-stretch">
                <div class="text-base text-gray-800 dark:text-white w-52">
                    {{ $t('public.distributor') }}
                </div>
                <div class="p-5 bg-gray-400 dark:bg-gray-700 rounded-xl w-full">
                    <div class="flex items-center gap-2">
                        <img :src="selectedDistributor.profile_photo ? selectedDistributor.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-8 h-8 rounded-full" alt="">
                        <div class="flex flex-col gap-1">
                            <div class="flex gap-2 items-center text-left text-gray-900 dark:text-white">
                                <div class="text-sm font-semibold">
                                    {{ selectedDistributor.name }}
                                </div>
                            </div>
                            <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                {{ selectedDistributor.email }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-1 space-y-2 sm:space-y-0 items-start self-stretch">
                <div class="text-base text-gray-800 dark:text-white w-52">
                    {{ $t('public.placement') }}
                </div>
                <div class="w-full">
                    <RadioGroup v-model="selectPosition" class="flex gap-1 md:gap-4 flex-col md:flex-row">
                        <div class="flex flex-row w-full gap-4">
                            <RadioGroupOption
                                as="template"
                                v-for="plan in positions"
                                :key="plan.name"
                                :value="plan"
                                v-slot="{ active, checked }"
                                class="w-full"
                                :disabled="plan.value === 'right' && !coinStackingExists"
                            >
                                <div
                                    :class="[
                                        checked ? 'bg-gray-600 dark:text-white border-2 border-white' : 'bg-gray-700',
                                        plan.value === 'right' && !coinStackingExists ? 'hover:cursor-not-allowed dark:bg-gray-700' : ''
                                    ]"
                                    class="relative flex cursor-pointer rounded-lg px-5 py-4 shadow-md focus:outline-none"
                                >
                                    <div class="flex w-full items-center justify-center">
                                        <div class="flex items-center">
                                            <div class="text-sm">
                                                <RadioGroupLabel
                                                    as="p"
                                                    :class="[
                                                        checked ? 'text-white' : '',
                                                        plan.value === 'right' && !coinStackingExists ? 'dark:text-gray-600' : 'text-white'
                                                    ]"
                                                    class="font-medium"
                                                >
                                                {{ $t('public.' + plan.name ) }}
                                                </RadioGroupLabel>
                                                <RadioGroupDescription
                                                    as="span"
                                                    :class="checked ? 'dark:text-white' : 'dark:text-white'"
                                                    class="inline"
                                                >
                                                </RadioGroupDescription>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </RadioGroupOption>
                        </div>
                    </RadioGroup>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-1 space-y-2 sm:space-y-0 items-start self-stretch">
                <div class="text-base text-gray-800 dark:text-white w-52">
                    {{ $t('public.place_under') }}
                </div>
                <div class="p-5 bg-gray-400 dark:bg-gray-700 rounded-xl w-full">
                    <div v-if="lastChild" class="flex items-center gap-2">
                        <img :src="lastChild.profile_photo ? lastChild.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-8 h-8 rounded-full" alt="">
                        <div class="flex flex-col gap-1">
                            <div class="flex gap-2 items-center text-left text-gray-900 dark:text-white">
                                <div class="text-sm font-semibold">
                                    {{ lastChild.user.name }}
                                </div>
                            </div>
                            <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                {{ lastChild.user.email }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

        <div class="pb-5 grid grid-cols-2 gap-4 w-full md:w-1/3 md:float-right">
            <Button variant="secondary" type="button" class="justify-center" @click.prevent="closeModal">
                {{ $t('public.cancel') }}
            </Button>
            <Button class="justify-center" @click="submit" :disabled="form.processing">{{ $t('public.confirm') }}</Button>
        </div>
    </Modal>

</template>
