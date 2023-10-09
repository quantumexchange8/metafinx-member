<script setup>
import {arrow, computePosition, flip, offset, shift} from "@floating-ui/dom";
import {ref} from "vue";

const props = defineProps({
    content: String,
    placement: {
        type: String,
        default: 'top'
    }
})
const referenceRef = ref();
const floatingRef = ref();
const arrowRef = ref();
const isHidden = ref(true)

async function calculatePosition() {
    const {x, y, middlewareData, placement} = await computePosition(referenceRef.value, floatingRef.value, {
        placement: props.placement,
        middleware: [offset(8), flip(), shift({padding: 5}), arrow({element: arrowRef.value})]
    });

    Object.assign(floatingRef.value.style, {
        left: `${x}px`,
        top: `${y}px`,
    });

    const {x: arrowX, y: arrowY} = middlewareData.arrow;

    const opposedSide = {
        left: "right",
        right: "left",
        bottom: "top",
        top: "bottom",
    }[placement.split("-")[0]]

    Object.assign(arrowRef.value.style, {
        left: arrowX ? `${arrowX}px` : "",
        top: arrowY ? `${arrowY}px` : "",
        bottom: "",
        right: "",
        [opposedSide]: "-4px"
    })
}

function hide() {
    isHidden.value = true;
}

function show() {
    isHidden.value = false;
    calculatePosition();
}
</script>

<template>
    <div class="inline-block">
        <div
            @mouseenter="show"
            @mouseleave="hide"
            @focus="show"
            @blur="hide"
            class="inline-block"
            ref="referenceRef"
        >
            <slot />
        </div>
        <div
            ref="floatingRef"
            :class="[
                'absolute top-0 left-0 z-50 bg-gray-200 text-black dark:text-white dark:bg-gray-600 text-sm px-3 py-1.5 rounded-md cursor-default',
                isHidden && 'hidden'
            ]"
        >
            {{ props.content }}
            <div class="absolute bg-gray-200 text-black dark:dark:bg-gray-600 h-[8px] w-[8px] rotate-45" ref="arrowRef"></div>
        </div>
    </div>
</template>
