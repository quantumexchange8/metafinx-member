<script setup>
import ReferralChild from "@/Pages/Affiliate/ReferralChild.vue";
import {onMounted, ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import Input from "@/Components/Input.vue";
import {SearchIcon} from "@heroicons/vue/outline";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import panzoom from '@panzoom/panzoom';
import Tooltip from "@/Components/Tooltip.vue";
import {ZoomInIcon, ZoomOutIcon, Target02Icon, LVL3Icon} from "@/Components/Icons/outline.jsx";
import Button from "@/Components/Button.vue";
import {Rank1Icon, Rank2Icon, Rank3Icon, Rank4Icon} from "@/Components/Icons/outline.jsx";

const referralTree = ref(null);
// Use refs to store functions
const handleZoomIn = ref(() => {});
const handleZoomOut = ref(() => {});
const handleRecenter = ref(() => {});

let search = ref(null);
let root = ref({});
const isLoading = ref(false);

watch(search, debounce(function() {
    isLoading.value = true;
    getResults(search.value);
}, 300));

const getResults = async (search = '') => {
    isLoading.value = true;
    try {
        let url = `/affiliate/getTreeData`;

        if (search) {
            console.log(search)
            url += `?search=${search}`;
        }

        const response = await axios.get(url);
        root.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}

getResults();

onMounted(() => {
    const element = referralTree.value;

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

</script>

<template>
    <div class="flex py-8 gap-3 md:gap-10">
        <div class="inline-flex items-center gap-3">
            <Rank1Icon class="w-8 h-8" />
            <div class="font-semibold text-sm">
                {{$t('public.affiliate.lvl_1')}}
            </div>
        </div>
        <div class="inline-flex items-center gap-3">
            <Rank2Icon class="w-8 h-8" />
            <div class="font-semibold text-sm">
                {{$t('public.affiliate.lvl_2')}}
            </div>
        </div>
        <div class="inline-flex items-center gap-3">
            <Rank3Icon class="w-8 h-8" />
            <div class="font-semibold text-sm">
                {{$t('public.affiliate.lvl_3')}}
            </div>
        </div>
        <div class="inline-flex items-center gap-3">
            <Rank4Icon class="w-8 h-8" />
            <div class="font-semibold text-sm">
                {{$t('public.affiliate.lvl_4')}}
            </div>
        </div>
    </div>
    <div>
        <InputIconWrapper>
            <template #icon>
                <SearchIcon aria-hidden="true" class="w-5 h-5" />
            </template>
            <Input
                withIcon
                id="search"
                type="text"
                class="block border-transparent w-full md:w-1/3"
                :placeholder="$t('public.affiliate.search_placeholder')"
                v-model="search"
            />
        </InputIconWrapper>
    </div>

<!--    <div class="flex items-center self-stretch justify-end my-3">-->
<!--        <div class="flex gap-3">-->
<!--            <Tooltip content="Zoom In" placement="bottom">-->
<!--                <Button-->
<!--                    type="button"-->
<!--                    class="flex justify-center w-8 h-8 relative focus:outline-none"-->
<!--                    variant="gray"-->
<!--                    @click="handleZoomIn"-->
<!--                    pill-->
<!--                >-->
<!--                    <ZoomInIcon aria-hidden="true" class="w-4 h-4 absolute" />-->
<!--                    <span class="sr-only">Zoom In</span>-->
<!--                </Button>-->
<!--            </Tooltip>-->
<!--            <Tooltip content="Zoom Out" placement="bottom">-->
<!--                <Button-->
<!--                    type="button"-->
<!--                    class="flex justify-center w-8 h-8 relative focus:outline-none"-->
<!--                    variant="gray"-->
<!--                    @click="handleZoomOut"-->
<!--                    pill-->
<!--                >-->
<!--                    <ZoomOutIcon aria-hidden="true" class="w-4 h-4 absolute" />-->
<!--                    <span class="sr-only">Zoom Out</span>-->
<!--                </Button>-->
<!--            </Tooltip>-->
<!--            <Tooltip content="Recenter" placement="bottom">-->
<!--                <Button-->
<!--                    type="button"-->
<!--                    class="flex justify-center w-8 h-8 relative focus:outline-none"-->
<!--                    variant="gray"-->
<!--                    @click="handleRecenter"-->
<!--                    pill-->
<!--                >-->
<!--                    <Target02Icon aria-hidden="true" class="w-4 h-4 absolute" />-->
<!--                    <span class="sr-only">Recenter</span>-->
<!--                </Button>-->
<!--            </Tooltip>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="relative overflow-hidden bg-gray-900 bg-[radial-gradient(#333d4b_2px,transparent_1px)] [background-size:52px_52px] min-h-[400px] max-h-[600px] mb-12">-->
<!--        <div ref="referralTree" class="flex flex-col justify-center items-center">-->
<!--            <ReferralChild-->
<!--                :node="root"-->
<!--                :isLoading="isLoading"-->
<!--                class="pt-8 overflow-x-auto"-->
<!--            />-->
<!--        </div>-->
<!--    </div>-->

    <ReferralChild
        :node="root"
        :isLoading="isLoading"
        class="pt-8 overflow-x-auto"
    />
</template>
