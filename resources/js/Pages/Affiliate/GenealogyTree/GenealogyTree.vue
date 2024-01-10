<script setup>
import { ref, onMounted } from 'vue';
import panzoom from 'panzoom';

const binaryTree = ref(null);

onMounted(() => {
    const element = binaryTree.value;

    if (element) {
        panzoom(element);
    } else {
        console.error("Element with ID 'binary-tree' not found");
    }
});
</script>

<template>
    <div class="relative overflow-hidden bg-gray-900 bg-[radial-gradient(#333d4b_2px,transparent_1px)] [background-size:64px_64px]">
        <div ref="binaryTree" class="flex flex-col justify-center items-center">
            <div class="container mx-auto text-center pt-24">
                <div class="items-center justify-center flex">
                    <div class="text-center">
                        <div class="flex flex-col justify-center items-center">
                            <div class="w-16">
                                <img class="block rounded-full m-auto shadow-md" alt="Duc Sun"
                                     src="https://randomuser.me/api/portraits/men/12.jpg" />
                            </div>
                            <div class="text-gray-600 dark:text-gray-400">
                                <p>Duc Sun</p>
                                <p>ceo</p>
                            </div>
                        </div>
                        <ul class="flex flex-row mt-10 justify-center">
                            <div class="-mt-10 border-l-2 absolute h-10 border-gray-400"></div>
                            <li class="relative p-6">
                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                     style="left: 50%; right: 0px;"></div>
                                <div class="relative flex justify-center">
                                    <div class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0"></div>
                                    <div class="text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="w-16">
                                                <img class="block rounded-full m-auto shadow-md" alt="Leo Six"
                                                     src="https://randomuser.me/api/portraits/men/83.jpg" />
                                            </div>
                                            <div class="text-gray-600 dark:text-gray-400">
                                                <p>Leo Six</p>
                                                <p>Management</p>
                                            </div>
                                        </div>
                                        <ul class="flex flex-row mt-10 justify-center">
                                            <div class="-mt-10 border-l-2 absolute h-10 border-gray-400"></div>
                                            <li class="relative p-6">
                                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                                     style="left: 50%; right: 0px;"></div>
                                                <div class="relative flex justify-center">
                                                    <div class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0">
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="flex flex-col justify-center items-center">
                                                            <div class="w-16">
                                                                <img class="block rounded-full m-auto shadow-md"
                                                                     alt="Tab Han"
                                                                     src="https://randomuser.me/api/portraits/men/46.jpg" />
                                                            </div>
                                                            <div class="text-gray-600 dark:text-gray-400">
                                                                <p>Tab Han</p>
                                                                <p>Project Manager</p>
                                                            </div>
                                                        </div>
                                                        <ul class="flex flex-row mt-10 justify-center">
                                                            <div
                                                                class="-mt-10 border-l-2 absolute h-10 border-gray-400">
                                                            </div>
                                                            <li class="relative p-6">
                                                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                                                     style="left: 50%; right: 0px;"></div>
                                                                <div class="relative flex justify-center">
                                                                    <div
                                                                        class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0">
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div
                                                                            class="flex flex-col justify-center items-center">
                                                                            <div class="w-16">
                                                                                <img class="block rounded-full m-auto shadow-md"
                                                                                     alt="Daniel Zhou"
                                                                                     src="https://randomuser.me/api/portraits/men/76.jpg" />
                                                                            </div>
                                                                            <div class="text-gray-600 dark:text-gray-400">
                                                                                <p>Daniel Zhou</p>
                                                                                <p>Team Lead</p>
                                                                            </div>
                                                                        </div>
                                                                        <ul class="flex flex-row mt-10 justify-center">
                                                                            <div
                                                                                class="-mt-10 border-l-2 absolute h-10 border-gray-400">
                                                                            </div>
                                                                            <li class="relative p-6">
                                                                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                                                                     style="left: 50%; right: 0px;">
                                                                                </div>
                                                                                <div
                                                                                    class="relative flex justify-center">
                                                                                    <div
                                                                                        class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0">
                                                                                    </div>
                                                                                    <div class="text-center">
                                                                                        <div
                                                                                            class="flex flex-col justify-center items-center">
                                                                                            <div class="w-16">
                                                                                                <img class="block rounded-full m-auto shadow-md"
                                                                                                     alt="Patrick Wang"
                                                                                                     src="https://randomuser.me/api/portraits/men/55.jpg" />
                                                                                            </div>
                                                                                            <div class="text-gray-600 dark:text-gray-400">
                                                                                                <p>Patrick Wang</p>
                                                                                                <p>Developer</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                            <li class="relative p-6">
                                                                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                                                                     style="left: 0px; right: 50%;">
                                                                                </div>
                                                                                <div
                                                                                    class="relative flex justify-center">
                                                                                    <div
                                                                                        class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0">
                                                                                    </div>
                                                                                    <div class="text-center">
                                                                                        <div
                                                                                            class="flex flex-col justify-center items-center">
                                                                                            <div class="w-16">
                                                                                                <img class="block rounded-full m-auto shadow-md"
                                                                                                     alt="Jonsen Li"
                                                                                                     src="https://randomuser.me/api/portraits/men/41.jpg" />
                                                                                            </div>
                                                                                            <div class="text-gray-600 dark:text-gray-400">
                                                                                                <p>Jonsen Li</p>
                                                                                                <p>Developer</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li class="relative p-6">
                                                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                                                     style="left: 0px; right: 0px;"></div>
                                                                <div class="relative flex justify-center">
                                                                    <div
                                                                        class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0">
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div
                                                                            class="flex flex-col justify-center items-center">
                                                                            <div class="w-16">
                                                                                <img class="block rounded-full m-auto shadow-md"
                                                                                     alt="Derek Hu"
                                                                                     src="https://randomuser.me/api/portraits/men/44.jpg" />
                                                                            </div>
                                                                            <div class="text-gray-600 dark:text-gray-400">
                                                                                <p>Derek Hu</p>
                                                                                <p>Team Lead</p>
                                                                            </div>
                                                                        </div>
                                                                        <ul class="flex flex-row mt-10 justify-center">
                                                                            <div
                                                                                class="-mt-10 border-l-2 absolute h-10 border-gray-400">
                                                                            </div>
                                                                            <li class="relative p-6">
                                                                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                                                                     style="left: 50%; right: 50%;">
                                                                                </div>
                                                                                <div
                                                                                    class="relative flex justify-center">
                                                                                    <div
                                                                                        class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0">
                                                                                    </div>
                                                                                    <div class="text-center">
                                                                                        <div
                                                                                            class="flex flex-col justify-center items-center">
                                                                                            <div class="w-16">
                                                                                                <img class="block rounded-full m-auto shadow-md"
                                                                                                     alt="Alfie Zu"
                                                                                                     src="https://randomuser.me/api/portraits/men/25.jpg" />
                                                                                            </div>
                                                                                            <div class="text-gray-600 dark:text-gray-400">
                                                                                                <p>Alfie Zu</p>
                                                                                                <p>Developer</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li class="relative p-6">
                                                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                                                     style="left: 0px; right: 50%;"></div>
                                                                <div class="relative flex justify-center">
                                                                    <div
                                                                        class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0">
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div
                                                                            class="flex flex-col justify-center items-center">
                                                                            <div class="w-16">
                                                                                <img class="block rounded-full m-auto shadow-md"
                                                                                     alt="Lennis Chan"
                                                                                     src="https://randomuser.me/api/portraits/men/30.jpg" />
                                                                            </div>
                                                                            <div class="text-gray-600 dark:text-gray-400">
                                                                                <p>Lennis Chan</p>
                                                                                <p>Team Lead</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="relative p-6">
                                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                                     style="left: 0px; right: 50%;"></div>
                                                <div class="relative flex justify-center">
                                                    <div class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0">
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="flex flex-col justify-center items-center">
                                                            <div class="w-16">
                                                                <img class="block rounded-full m-auto shadow-md"
                                                                     alt="Peter Miao"
                                                                     src="https://randomuser.me/api/portraits/men/54.jpg" />
                                                            </div>
                                                            <div class="text-gray-600 dark:text-gray-400">
                                                                <p>Peter Miao</p>
                                                                <p>Project Manager</p>
                                                            </div>
                                                        </div>
                                                        <ul class="flex flex-row mt-10 justify-center">
                                                            <div
                                                                class="-mt-10 border-l-2 absolute h-10 border-gray-400">
                                                            </div>
                                                            <li class="relative p-6">
                                                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                                                     style="left: 50%; right: 50%;"></div>
                                                                <div class="relative flex justify-center">
                                                                    <div
                                                                        class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0">
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div
                                                                            class="flex flex-col justify-center items-center">
                                                                            <div class="w-16">
                                                                                <img class="block rounded-full m-auto shadow-md"
                                                                                     alt="Peter Ming"
                                                                                     src="https://randomuser.me/api/portraits/men/90.jpg" />
                                                                            </div>
                                                                            <div class="text-gray-600 dark:text-gray-400">
                                                                                <p>Peter Ming</p>
                                                                                <p>Team Lead</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li class="relative p-6">
                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                     style="left: 0px; right: 0px;"></div>
                                <div class="relative flex justify-center">
                                    <div class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0"></div>
                                    <div class="text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="w-16">
                                                <img class="block rounded-full m-auto shadow-md" alt="Shi Ten"
                                                     src="https://randomuser.me/api/portraits/men/43.jpg" />
                                            </div>
                                            <div class="text-gray-600 dark:text-gray-400">
                                                <p>Shi Ten</p>
                                                <p>HR</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="relative p-6">
                                <div class="border-t-2 absolute h-8 border-gray-400 top-0"
                                     style="left: 0px; right: 50%;"></div>
                                <div class="relative flex justify-center">
                                    <div class="-mt-6 border-l-2 absolute h-6 border-gray-400 top-0"></div>
                                    <div class="text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="w-16">
                                                <img class="block rounded-full m-auto shadow-md" alt="Shawn Mong"
                                                     src="https://randomuser.me/api/portraits/men/97.jpg" />
                                            </div>
                                            <div class="text-gray-600 dark:text-gray-400">
                                                <p>Shawn Mong</p>
                                                <p>admin</p>
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
