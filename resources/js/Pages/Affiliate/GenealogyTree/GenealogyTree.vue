<script setup>
import { ref, onMounted } from 'vue';
import panzoom from '@panzoom/panzoom';
import Tooltip from "@/Components/Tooltip.vue";
import {ZoomInIcon, ZoomOutIcon, Target02Icon, LVL3Icon} from "@/Components/Icons/outline.jsx";
import Button from "@/Components/Button.vue";
import {PlusIcon} from "@heroicons/vue/outline";

const binaryTree = ref(null);
// Use refs to store functions
const handleZoomIn = ref(() => {});
const handleZoomOut = ref(() => {});
const handleRecenter = ref(() => {});
const isExpand = ref(false)

onMounted(() => {
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

const handleExpand = () => {
    isExpand.value = !isExpand.value
}
</script>


<template>
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

    <div class="relative overflow-hidden bg-gray-900 bg-[radial-gradient(#333d4b_2px,transparent_1px)] [background-size:52px_52px] min-h-[400px] mb-12">
        <div ref="binaryTree" class="flex flex-col justify-center items-center">
            <div class="container mx-auto text-center pt-24">
                <div class="items-center justify-center flex">
                    <div class="text-center">
                        <div class="flex flex-col justify-center items-center">
                            <div class="w-60 rounded-lg dark:bg-gray-700 flex gap-2 p-3 items-center">
                                <img class="rounded-full w-8 h-8" alt="Duc Sun"
                                     src="https://randomuser.me/api/portraits/men/12.jpg" />
                                <div class="flex flex-col gap-1">
                                    <div class="flex gap-2 items-center text-gray-900 dark:text-white">
                                        <div class="text-sm font-semibold">
                                            Duc Sun
                                        </div>
                                        <LVL3Icon class="h-4" />
                                        <div class="text-sm font-semibold">
                                            Sponsor
                                        </div>
                                    </div>
                                    <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                        email@email.com
                                    </div>
                                </div>
                            </div>
                            <div class="-mt-[102px] border-l-2 absolute h-10 border-gray-600"></div>
                            <div class="w-60 mt-10 rounded-lg dark:bg-gray-700 p-3">
                                <div class="flex gap-2 items-center border-b border-gray-600 pb-3">
                                    <img class="rounded-full w-8 h-8" alt="Duc Sun"
                                         src="https://randomuser.me/api/portraits/men/12.jpg" />
                                    <div class="flex flex-col gap-1">
                                        <div class="flex gap-2 items-center text-gray-900 dark:text-white">
                                            <div class="text-sm font-semibold">
                                                John Doe
                                            </div>
                                            <LVL3Icon class="h-4" />
                                            <div class="text-sm font-semibold">
                                                Sponsor
                                            </div>
                                        </div>
                                        <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                            email@email.com
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                            Personal
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            $ 100
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                            Left
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            $ 100
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                            Right
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            $ 100
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center mt-1">
                            <div
                                class="rounded-full bg-pink-500 grow-0 shrink-0 w-4 h-4 cursor-pointer"
                                @click="handleExpand"
                            >
                                <PlusIcon />
                            </div>
                        </div>
                        <ul v-if="isExpand" class="flex flex-row mt-4 justify-center">
                            <div class="-mt-4 border-l-2 absolute h-4 border-gray-600"></div>
                            <li class="relative p-6">
                                <div class="border-t-2 absolute h-8 border-gray-600 top-0"
                                     style="left: 50%; right: 0px;">
                                </div>
                                <div
                                    class="relative flex justify-center">
                                    <div
                                        class="-mt-6 border-l-2 absolute h-6 border-gray-600 top-0">
                                    </div>
                                    <div class="text-center">
                                        <div
                                            class="flex flex-col justify-center items-center">
                                            <div class="w-60 rounded-lg dark:bg-gray-700 p-3">
                                                <div class="flex gap-2 items-center border-b border-gray-600 pb-3">
                                                    <img class="rounded-full w-8 h-8" alt="Duc Sun"
                                                         src="https://randomuser.me/api/portraits/men/55.jpg" />
                                                    <div class="flex flex-col gap-1">
                                                        <div class="flex gap-2 items-center text-gray-900 dark:text-white">
                                                            <div class="text-sm font-semibold">
                                                                Patrick
                                                            </div>
                                                            <LVL3Icon class="h-4" />
                                                            <div class="text-sm font-semibold">
                                                                Sponsor
                                                            </div>
                                                        </div>
                                                        <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                                            email@email.com
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3 space-y-2">
                                                    <div class="flex justify-between items-center">
                                                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                            Personal
                                                        </div>
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                            $ 100
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                            Left
                                                        </div>
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                            $ 100
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                            Right
                                                        </div>
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                            $ 100
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-center mt-1">
                                    <div
                                        class="rounded-full bg-pink-500 grow-0 shrink-0 w-4 h-4 cursor-pointer"
                                        @click="handleExpand"
                                    >
                                        <PlusIcon />
                                    </div>
                                </div>
                                <ul v-if="isExpand" class="flex flex-row mt-4 justify-center">
                                    <div class="-mt-4 border-l-2 absolute h-4 border-gray-600"></div>
                                    <li class="relative p-6">
                                        <div class="border-t-2 absolute h-8 border-gray-600 top-0"
                                             style="left: 50%; right: 0px;">
                                        </div>
                                        <div
                                            class="relative flex justify-center">
                                            <div
                                                class="-mt-6 border-l-2 absolute h-6 border-gray-600 top-0">
                                            </div>
                                            <div class="text-center">
                                                <div
                                                    class="flex flex-col justify-center items-center">
                                                    <div class="w-60 rounded-lg dark:bg-gray-700 p-3">
                                                        <div class="flex gap-2 items-center border-b border-gray-600 pb-3">
                                                            <img class="rounded-full w-8 h-8" alt="Duc Sun"
                                                                 src="https://randomuser.me/api/portraits/men/55.jpg" />
                                                            <div class="flex flex-col gap-1">
                                                                <div class="flex gap-2 items-center text-gray-900 dark:text-white">
                                                                    <div class="text-sm font-semibold">
                                                                        Patrick
                                                                    </div>
                                                                    <LVL3Icon class="h-4" />
                                                                    <div class="text-sm font-semibold">
                                                                        Sponsor
                                                                    </div>
                                                                </div>
                                                                <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                                                    email@email.com
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 space-y-2">
                                                            <div class="flex justify-between items-center">
                                                                <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                                    Personal
                                                                </div>
                                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                    $ 100
                                                                </div>
                                                            </div>
                                                            <div class="flex justify-between items-center">
                                                                <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                                    Left
                                                                </div>
                                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                    $ 100
                                                                </div>
                                                            </div>
                                                            <div class="flex justify-between items-center">
                                                                <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                                    Right
                                                                </div>
                                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                    $ 100
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li v-if="isExpand" class="relative p-6">
                                        <div class="border-t-2 absolute h-8 border-gray-600 top-0"
                                             style="left: 0px; right: 50%;">
                                        </div>
                                        <div
                                            class="relative flex justify-center">
                                            <div
                                                class="-mt-6 border-l-2 absolute h-6 border-gray-600 top-0">
                                            </div>
                                            <div class="text-center">
                                                <div
                                                    class="flex flex-col justify-center items-center">
                                                    <div class="w-60 rounded-lg dark:bg-gray-700 p-3">
                                                        <div class="flex gap-2 items-center border-b border-gray-600 pb-3">
                                                            <img class="rounded-full w-8 h-8" alt="Duc Sun"
                                                                 src="https://randomuser.me/api/portraits/men/41.jpg" />
                                                            <div class="flex flex-col gap-1">
                                                                <div class="flex gap-2 items-center text-gray-900 dark:text-white">
                                                                    <div class="text-sm font-semibold">
                                                                        Jonsen Li
                                                                    </div>
                                                                    <LVL3Icon class="h-4" />
                                                                    <div class="text-sm font-semibold">
                                                                        Sponsor
                                                                    </div>
                                                                </div>
                                                                <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                                                    email@email.com
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 space-y-2">
                                                            <div class="flex justify-between items-center">
                                                                <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                                    Personal
                                                                </div>
                                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                    $ 100
                                                                </div>
                                                            </div>
                                                            <div class="flex justify-between items-center">
                                                                <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                                    Left
                                                                </div>
                                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                    $ 100
                                                                </div>
                                                            </div>
                                                            <div class="flex justify-between items-center">
                                                                <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                                    Right
                                                                </div>
                                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                    $ 100
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li v-if="isExpand" class="relative p-6">
                                <div class="border-t-2 absolute h-8 border-gray-600 top-0"
                                     style="left: 0px; right: 50%;">
                                </div>
                                <div
                                    class="relative flex justify-center">
                                    <div
                                        class="-mt-6 border-l-2 absolute h-6 border-gray-600 top-0">
                                    </div>
                                    <div class="text-center">
                                        <div
                                            class="flex flex-col justify-center items-center">
                                            <div class="w-60 rounded-lg dark:bg-gray-700 p-3">
                                                <div class="flex gap-2 items-center border-b border-gray-600 pb-3">
                                                    <img class="rounded-full w-8 h-8" alt="Duc Sun"
                                                         src="https://randomuser.me/api/portraits/men/41.jpg" />
                                                    <div class="flex flex-col gap-1">
                                                        <div class="flex gap-2 items-center text-gray-900 dark:text-white">
                                                            <div class="text-sm font-semibold">
                                                                Jonsen Li
                                                            </div>
                                                            <LVL3Icon class="h-4" />
                                                            <div class="text-sm font-semibold">
                                                                Sponsor
                                                            </div>
                                                        </div>
                                                        <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                                                            email@email.com
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3 space-y-2">
                                                    <div class="flex justify-between items-center">
                                                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                            Personal
                                                        </div>
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                            $ 100
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                            Left
                                                        </div>
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                            $ 100
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                                                            Right
                                                        </div>
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                            $ 100
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
