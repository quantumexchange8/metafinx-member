<script setup>
import {ref, onMounted, watchEffect} from 'vue';
import panzoom from '@panzoom/panzoom';
import Tooltip from "@/Components/Tooltip.vue";
import {ZoomInIcon, ZoomOutIcon, Target02Icon, LVL3Icon} from "@/Components/Icons/outline.jsx";
import Button from "@/Components/Button.vue";
import GenealogyChild from "@/Pages/Affiliate/GenealogyTree/GenealogyChild.vue";
import {usePage} from "@inertiajs/vue3";
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronUpIcon } from '@heroicons/vue/outline'

const binaryTree = ref({});
// Use refs to store functions
const handleZoomIn = ref(() => {});
const handleZoomOut = ref(() => {});
const handleRecenter = ref(() => {});
const isExpand = ref(false)
const root = ref({});

const props = defineProps({
    downline: Array,
})

const getResults = async (search = '') => {
    try {
        let url = `/affiliate/getBinaryData`;

        if (search) {
            console.log(search)
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
</script>


<template>

    <div class="w-full py-5">
        <div class="w-full rounded-xl bg-gray-700 p-2">
            <Disclosure v-slot="{ open }">
                <DisclosureButton
                class="flex w-full justify-between rounded-lg bg-purple-100 px-4 py-2 text-left text-sm font-medium text-purple-900 hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500/75"
                >
                <span class="text-white text-base">Pending Placement</span>
                <ChevronUpIcon
                    :class="open ? 'rotate-180 transform' : ''"
                    class="h-5 w-5 text-purple-500"
                />
                </DisclosureButton>
                <DisclosurePanel class="px-4 pb-2 pt-4 text-sm text-gray-500">
                    <table class="w-full">
                        <thead class="w-[650px] md:w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
                            <tr>
                                <th scope="col" class="p-3">
                                    MEMBER
                                </th>
                                <th scope="col" class="p-3">
                                    STAKING (MXT)
                                </th>
                                <th scope="col" class="p-3">
                                    PLACEMENT TIME LEFT
                                </th>
                                <th scope="col" class="p-3">
                                    PLACEMENT
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- <tr>
                                <th colspan="5" class="py-4 text-lg text-center">
                                    no history
                                </th>
                            </tr> -->
                            <tr
                                v-for="donwline in props.downline"
                            >
                                <td class="p-3">
                                    {{ donwline.name }}
                                </td>
                                <td class="p-3">
                                
                                </td>
                                <td class="p-3">
                                
                                </td>
                                <td class="p-3">
                                
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </DisclosurePanel>
            </Disclosure>
        </div>
    </div>

    <div class="flex items-center self-stretch justify-between mb-3">
        <div class="text-base font-normal text-gray-600 dark:text-gray-400">
            Note: Only add distributor that currently have no associated legs.
        </div>
        <div class="flex gap-3">
            <Tooltip content="Zoom In" placement="bottom">
                <Button
                    type="button"
                    class="flex justify-center w-8 h-8 relative focus:outline-none"
                    variant="gray"
                    @click="handleZoomIn"
                    pill
                >
                    <ZoomInIcon aria-hidden="true" class="w-4 h-4 absolute" />
                    <span class="sr-only">Zoom In</span>
                </Button>
            </Tooltip>
            <Tooltip content="Zoom Out" placement="bottom">
                <Button
                    type="button"
                    class="flex justify-center w-8 h-8 relative focus:outline-none"
                    variant="gray"
                    @click="handleZoomOut"
                    pill
                >
                    <ZoomOutIcon aria-hidden="true" class="w-4 h-4 absolute" />
                    <span class="sr-only">Zoom Out</span>
                </Button>
            </Tooltip>
            <Tooltip content="Recenter" placement="bottom">
                <Button
                    type="button"
                    class="flex justify-center w-8 h-8 relative focus:outline-none"
                    variant="gray"
                    @click="handleRecenter"
                    pill
                >
                    <Target02Icon aria-hidden="true" class="w-4 h-4 absolute" />
                    <span class="sr-only">Recenter</span>
                </Button>
            </Tooltip>
        </div>
    </div>

    <div class="relative overflow-hidden bg-gray-900 bg-[radial-gradient(#333d4b_2px,transparent_1px)] [background-size:52px_52px] min-h-[400px] max-h-[600px] mb-12">
        <div ref="binaryTree" class="flex flex-col justify-center items-center">
            <div class="container mx-auto text-center pt-24">
                <div class="items-center justify-center flex">
                    <div class="text-center">
                        <div v-if="root.sponsor" class="flex flex-col justify-center items-center">
                            <div class="w-60 rounded-lg dark:bg-gray-700 flex gap-2 p-3 items-center z-20">
                                <img class="rounded-full w-8 h-8" alt="Duc Sun"
                                     src="https://randomuser.me/api/portraits/men/12.jpg" />
                                <div class="flex flex-col gap-1">
                                    <div class="flex gap-2 items-center text-gray-900 dark:text-white">
                                        <div class="text-sm font-semibold">
                                            {{ root.sponsor }}
                                        </div>
                                        <LVL3Icon class="h-4" />
                                        <div class="text-sm font-semibold">
                                            Sponsor
                                        </div>
                                    </div>
                                    <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                        {{ root.email }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <GenealogyChild
                            :binaryTree="root"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
