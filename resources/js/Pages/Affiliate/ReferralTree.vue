<script setup>
import ReferralChild from "@/Pages/Affiliate/ReferralChild.vue";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import {LVL1Icon, LVL2Icon, LVL3Icon, LVL4Icon} from "@/Components/Icons/outline.jsx"
import Input from "@/Components/Input.vue";
import {SearchIcon} from "@heroicons/vue/outline";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";

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

</script>

<template>
    <div class="flex pt-2 gap-3 md:gap-10">
        <div class="inline-flex items-center gap-3">
            <LVL1Icon class="h-12" />
            <div class="font-semibold text-sm">
                LVL 1
            </div>
        </div>
        <div class="inline-flex items-center gap-3">
            <LVL2Icon class="h-12" />
            <div class="font-semibold text-sm">
                LVL 2
            </div>
        </div>
        <div class="inline-flex items-center gap-3">
            <LVL3Icon class="h-12" />
            <div class="font-semibold text-sm">
                LVL 3
            </div>
        </div>
    </div>
    <div class="pt-8">
        <InputIconWrapper>
            <template #icon>
                <SearchIcon aria-hidden="true" class="w-5 h-5" />
            </template>
            <Input
                withIcon
                id="search"
                type="text"
                class="block border-transparent w-full md:w-1/3"
                placeholder="Search"
                v-model="search"
            />
        </InputIconWrapper>
    </div>
    <ReferralChild
        :node="root"
        :isLoading="isLoading"
        class="pt-8 overflow-x-auto"
    />
</template>
