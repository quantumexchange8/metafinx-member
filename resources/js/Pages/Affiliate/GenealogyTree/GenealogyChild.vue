<script setup>
import {PlusIcon, MinusIcon, SearchIcon} from "@heroicons/vue/outline";
import {ref, watch} from 'vue';
import {LVL1Icon, LVL2Icon, LVL3Icon, UserPlus01Icon} from "@/Components/Icons/outline.jsx";
import Modal from "@/Components/Modal.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Input from "@/Components/Input.vue";
import LevelIcon from "@/Components/LevelIcon.vue";
import InputError from "@/Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";
import Combobox from "@/Components/Combobox.vue";
import Button from "@/Components/Button.vue";
import {transactionFormat} from "@/Composables/index.js";
import Loading from "@/Components/Loading.vue";

const props = defineProps({
    binaryTree: Object
});

const { formatAmount } = transactionFormat();
const node = ref(props.binaryTree);
const isExpand = ref(false);
const distributorModal = ref(false);
const uplineData = ref();
const user = ref();
const position = ref('');

watch(() => props.binaryTree, (newValue) => {
    node.value = newValue
})
const openDistributorModal = (child, treePosition) => {
    distributorModal.value = true
    uplineData.value = child
    position.value = treePosition
};

const closeModal = () => {
    distributorModal.value = false
}

const handleExpand = () => {
    isExpand.value = !isExpand.value;
};

// function loadUsers(query, setOptions) {
//     fetch('/affiliate/getAvailableDistributor?query=' + query + '&upline_id=' + props.binaryTree.id)
//         .then(response => response.json())
//         .then(results => {
//             setOptions(
//                 results.map(user => {
//                     return {
//                         value: user.id,
//                         label: user.name,
//                         img: user.profile_photo
//                     }
//                 })
//             )
//         });
// }

// const form = useForm({
//     user_id: '',
//     upline_id: '',
//     position: ''
// })
//
// const submitForm = () => {
//     form.user_id = user.value.value
//     form.upline_id = props.binaryTree.id
//     form.position = position.value
//
//     form.post(route('affiliate.addDistributor'), {
//         onSuccess: () => {
//             closeModal();
//             form.reset();
//         },
//     });
// }

const distributorDetailModal = ref(false);
const distributorDetail = ref();
const isLoading = ref(false);

const openDistributorDetailModal = (distributor) => {
    distributorDetailModal.value = true;
    getDistributorDetail(distributor.id, distributor.level)
}

const closeDetailModal = () => {
    distributorDetailModal.value = false;
}

const getDistributorDetail = async (id, level) => {
    isLoading.value = true;
    try {
        let url = `/affiliate/getDistributorDetail?id=` + id + `&level=${level}`;

        const response = await axios.get(url);
        distributorDetail.value = response.data;

    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <div v-if="node" class="text-center">
        <div class="flex flex-col justify-center items-center">
            <div v-if="node.id > 1 && node.level > 0" class="-mt-[194px] border-l-2 border-dashed absolute h-10 border-gray-600"></div>
            <div class="w-60 mt-5 rounded-lg dark:bg-gray-700 dark:hover:bg-gray-800 hover:cursor-pointer p-3 z-20" @click="openDistributorDetailModal(node)">
                <div class="flex gap-2 items-center border-b border-gray-600 pb-3">
                    <img :src="node.profile_photo ? node.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-8 h-8 rounded-full" alt="">
                    <div class="flex flex-col gap-1">
                        <div class="flex gap-2 items-center text-left text-gray-900 dark:text-white">
                            <div class="text-sm font-semibold">
                                {{ node.name }}
                            </div>
                            <div class="rounded-xl bg-warning-400 text-gray-800 font-normal text-xs px-2 text-center w-16">
                                {{ $t('public.affiliate.gen') }} {{ node.level }}
                            </div>
                        </div>
                        <div class="text-left text-xs text-gray-600 dark:text-gray-400">
                            {{ node.email }}
                        </div>
                    </div>
                </div>
                <div class="mt-3 space-y-2">
                    <div class="flex justify-between items-center">
                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                            {{ $t('public.personal') }}
                        </div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ node.personal_amount >= 0 ? '$ ' + formatAmount(node.personal_amount) : 'Loading..' }}
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                            {{ $t('public.left') }}
                        </div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                            <div v-if="node.left_amount >= 0">
                                {{ node.left_amount - node.right_amount >= 0 ? '$ ' + formatAmount(node.left_amount - node.right_amount) : '$ 0.00' }}
                            </div>
                            <div v-else>
                                Loading..
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="text-xs font-normal text-gray-600 dark:text-gray-400">
                            {{ $t('public.right') }}
                        </div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                            <div v-if="node.right_amount >= 0">
                                {{ node.right_amount && node.right_amount - node.left_amount >= 0 ? '$ ' + formatAmount(node.right_amount - node.left_amount) : '$ 0.00' }}
                            </div>
                            <div v-else>
                                Loading..
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="flex justify-center mt-1"
        >
            <div
                v-if="!isExpand"
                class="flex items-center justify-center rounded-full bg-pink-500 grow-0 shrink-0 w-4 h-4 cursor-pointer"
                @click="handleExpand"
            >
                <PlusIcon class="w-3 h-3"/>
            </div>
            <div
                v-else
                class="flex items-center justify-center rounded-full bg-gray-500 grow-0 shrink-0 w-4 h-4 cursor-pointer"
                @click="handleExpand"
            >
                <MinusIcon class="w-3 h-3"/>
            </div>
        </div>

        <!-- CHECK URER CHILDREN LENGTH HAVE ERROR-->
        <ul v-if="isExpand" class="flex flex-row mt-4 justify-center">
            <div class="-mt-4 border-l-2 border-dashed absolute h-4 border-gray-600"></div>
            <li v-if="node.children && node.children.length > 0" v-for="child in node.children" :key="child.name" class="relative p-6">
                <div v-if="child.position === 'left'" class="border-t-2 border-dashed absolute h-8 border-gray-600 top-0 left-1/2 right-0"></div>
                <div v-if="child.position === 'right'" class="border-t-2 border-dashed absolute h-8 border-gray-600 top-0 left-0 right-1/2"></div>
                <GenealogyChild v-if="Object.keys(child).length > 0" :binaryTree="child" />

                <!-- Add Distributor-->
                <div v-else>
                    <div v-if="binaryTree.children[0].position !== 'left'" class="border-t-2 border-dashed absolute h-8 border-gray-600 top-0 left-1/2 right-0"></div>
                    <div v-if="binaryTree.children[1].position !== 'right'" class="border-t-2 border-dashed absolute h-8 border-gray-600 top-0 left-1 right-1/2"></div>
                    <div v-if="binaryTree.children[0].position !== 'left'" class="relative flex justify-center">
                        <div class="-mt-6 border-l-2 border-dashed absolute h-6 border-gray-600 top-0"></div>
                        <div class="flex items-center justify-center rounded-full bg-success-500 grow-0 shrink-0 w-8 h-8" >
                            <UserPlus01Icon class="w-6 h-6"/>
                        </div>
                    </div>
                    <div v-if="binaryTree.children[1].position !== 'right'" class="relative flex justify-center">
                        <div class="-mt-6 border-l-2 border-dashed absolute h-6 border-gray-600 top-0"></div>
                        <div class="flex items-center justify-center rounded-full bg-success-500 grow-0 shrink-0 w-8 h-8" >
                            <UserPlus01Icon class="w-6 h-6"/>
                        </div>
                    </div>
                </div>
            </li>

            <!-- Add Distributor-->
            <li v-else>
                <ul class="flex flex-row justify-center">
                    <li class="relative p-6">
                        <div class="border-t-2 border-dashed absolute h-8 border-gray-600 top-0 left-1/2 right-0"></div>
                        <div class="relative flex justify-center">
                            <div class="-mt-6 border-l-2 border-dashed absolute h-6 border-gray-600 top-0"></div>
                            <div class="flex items-center justify-center rounded-full bg-success-500 grow-0 shrink-0 w-8 h-8" >
                                <UserPlus01Icon class="w-6 h-6"/>
                            </div>
                        </div>
                    </li>
                    <li class="relative p-6">
                        <div class="border-t-2 border-dashed absolute h-8 border-gray-600 top-0 left-0 right-1/2"></div>
                        <div class="relative flex justify-center">
                            <div class="-mt-6 border-l-2 border-dashed absolute h-6 border-gray-600 top-0"></div>
                            <div class="flex items-center justify-center rounded-full bg-success-500 grow-0 shrink-0 w-8 h-8" >
                                <UserPlus01Icon class="w-6 h-6"/>
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

<!--    <Modal :show="distributorModal" title="Add Distributor" @close="closeModal">-->
<!--        <form>-->
<!--            <div class="mt-3 flex flex-col items-end gap-8">-->
<!--                <div class="flex items-start gap-1 self-stretch">-->
<!--                    <div class="text-sm font-medium text-gray-800 dark:text-white w-1/4">-->
<!--                        Place Under-->
<!--                    </div>-->
<!--                    <div class="flex items-center p-5 rounded-xl bg-gray-300 dark:bg-gray-700 gap-2 self-stretch w-3/4">-->
<!--                        <img :src="node.profile_photo ? node.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-8 h-8 rounded-full" alt="">-->
<!--                        <div class="flex flex-col items-start gap-1">-->
<!--                            <div class="flex items-center gap-2">-->
<!--                                <div class="text-gray-700 dark:text-white text-sm font-semibold">-->
<!--                                    {{ uplineData.name }}-->
<!--                                </div>-->
<!--                                <div v-if="uplineData && uplineData.rank > 1" class="h-4">-->
<!--                                    <LevelIcon :level="uplineData.rank - 1"  />-->
<!--                                </div>-->
<!--                                <div class="rounded-xl bg-warning-400 text-gray-800 font-normal text-xs px-2">-->
<!--                                    Gen {{ uplineData.level }}-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="text-xs text-gray-600 dark:text-gray-400">-->
<!--                                {{ uplineData.email }}-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="flex items-start gap-1 self-stretch">-->
<!--                    <div class="text-sm font-medium text-gray-800 dark:text-white w-1/4">-->
<!--                        Distributor-->
<!--                    </div>-->
<!--                    <div class="w-3/4">-->
<!--                        <Combobox-->
<!--                            :load-options="loadUsers"-->
<!--                            v-model="user"-->
<!--                            :error="form.errors.user_id"-->
<!--                            image-->
<!--                        />-->
<!--                        <InputError :message="form.errors.user_id" class="mt-1 col-span-4" />-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="flex pt-8 justify-end items-center gap-3 self-stretch border-t border-gray-400 dark:border-gray-600">-->
<!--                    <Button-->
<!--                        type="button"-->
<!--                        variant="secondary"-->
<!--                        class="flex justify-center"-->
<!--                        @click.prevent="closeModal"-->
<!--                    >-->
<!--                        Cancel-->
<!--                    </Button>-->
<!--                    <Button-->
<!--                        class="flex justify-center"-->
<!--                        @click="submitForm"-->
<!--                        :disabled="form.processing"-->
<!--                    >-->
<!--                        Confirm-->
<!--                    </Button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </form>-->
<!--    </Modal>-->

    <Modal :show="distributorDetailModal" :title="$t('public.affiliate.distributor_detail')" @close="closeDetailModal">
        <div v-if="isLoading" class="flex justify-center">
            <Loading />
        </div>
        <div v-if="distributorDetail && !isLoading">
            <div class="flex items-center p-5 rounded-xl bg-gray-300 dark:bg-gray-700 gap-2 self-stretch w-full mt-3 mb-8">
                <img :src="distributorDetail.profile_photo ? distributorDetail.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-8 h-8 rounded-full" alt="">
                <div class="flex flex-col items-start gap-1">
                    <div class="flex items-center gap-2">
                        <div class="text-gray-700 dark:text-white text-sm font-semibold">
                            {{ distributorDetail.name }}
                        </div>
                        <div class="rounded-xl bg-warning-400 text-gray-800 font-normal text-xs px-2">
                            {{ $t('public.affiliate.gen') }} {{ distributorDetail.level }}
                        </div>
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        {{ distributorDetail.email }}
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-start gap-3 self-stretch">
                <!-- <div class="flex items-center gap-2 self-stretch">
                    <div class="font-semibold text-sm text-gray-600 dark:text-gray-400 w-1/4">
                        Sponsor
                    </div>
                    <div class="flex items-center gap-2">
                        <img :src="distributorDetail.sponsor_profile_photo ? distributorDetail.sponsor_profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-8 h-8 rounded-full" alt="">
                        <div class="text-base text-gray-800 dark:text-white">
                            {{ distributorDetail.sponsor_name ?? '-'}}
                        </div>
                    </div>
                </div> -->
                <div class="flex items-center gap-2 self-stretch">
                    <div class="font-semibold text-sm text-gray-600 dark:text-gray-400 w-1/4">
                        {{ $t('public.place_under') }}
                    </div>
                    <div class="flex items-center gap-2">
                        <img :src="distributorDetail.upline_profile_photo ? distributorDetail.upline_profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" class="w-8 h-8 rounded-full" alt="">
                        <div class="text-base text-gray-800 dark:text-white">
                            {{ distributorDetail.upline_name ?? '-'}}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-stretch">
                    <div class="font-semibold text-sm text-gray-600 dark:text-gray-400 w-1/4">
                        {{ $t('public.current_staking') }}
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="text-base text-gray-800 dark:text-white">
                            {{ distributorDetail.current_staking }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-stretch">
                    <div class="font-semibold text-sm text-gray-600 dark:text-gray-400 w-1/4">
                        {{ $t('public.accrued_staking') }}
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="text-base text-gray-800 dark:text-white">
                            {{ distributorDetail.accrued_staking }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Modal>

</template>
