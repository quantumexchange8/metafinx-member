<script setup>
import ReferralChild from "@/Pages/Affiliate/ReferralChild.vue";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import {StaterIcon, SilverIcon, GoldIcon} from "@/Components/Icons/outline.jsx"
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
    <div class="flex pt-2 gap-10">
        <div class="inline-flex items-center gap-3">
            <StaterIcon class="h-12" />
            <div class="font-semibold">
                Starter Tier
            </div>
        </div>
        <div class="inline-flex items-center gap-3">
            <SilverIcon class="h-12" />
            <div class="font-semibold">
                Silver Tier
            </div>
        </div>
        <div class="inline-flex items-center gap-3">
            <GoldIcon class="h-12" />
            <div class="font-semibold">
                Gold Tier
            </div>
        </div>
    </div>
<!--    <div class="pt-8">-->
<!--        <InputIconWrapper>-->
<!--            <template #icon>-->
<!--                <SearchIcon aria-hidden="true" class="w-5 h-5" />-->
<!--            </template>-->
<!--            <Input-->
<!--                withIcon-->
<!--                id="search"-->
<!--                type="text"-->
<!--                class="block"-->
<!--                placeholder="Search"-->
<!--                v-model="search"-->
<!--            />-->
<!--        </InputIconWrapper>-->
<!--    </div>-->
    <ReferralChild
        :node="root"
        :isLoading="isLoading"
        class="pt-8"
    />
</template>