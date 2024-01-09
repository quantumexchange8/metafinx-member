<template>
    <div
        @wheel.prevent="handleMouseWheel"
        @mousedown="startDrag"
        @mousemove="handleDrag"
        @mouseup="stopDrag"
        class="zoom-container bg-white"
    >
        <!-- Your content goes here -->
        <div
            class="content"
            :style="{ transform: `scale(${scale}) translate(${translateX}px, ${translateY}px)` }"
        >
            <p class="text-blue-600">This is zoomable content!</p>
            <p class="text-blue-600">Feel free to add more content here.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';

const scale = ref(1);
const drag = reactive({
    isDragging: false,
    startX: 0,
    startY: 0,
});

const translateX = ref(0);
const translateY = ref(0);

const handleMouseWheel = (event) => {
    if (event.deltaY > 0) {
        zoomOut();
    } else {
        zoomIn();
    }
};

const zoomIn = () => {
    scale.value += 0.1;
};

const zoomOut = () => {
    if (scale.value > 0.1) {
        scale.value -= 0.1;
    }
};

const startDrag = (event) => {
    drag.isDragging = true;
    drag.startX = event.clientX;
    drag.startY = event.clientY;
};

const handleDrag = (event) => {
    if (drag.isDragging) {
        const offsetX = event.clientX - drag.startX;
        const offsetY = event.clientY - drag.startY;
        translateX.value += offsetX;
        translateY.value += offsetY;
        drag.startX = event.clientX;
        drag.startY = event.clientY;
    }
};

const stopDrag = () => {
    drag.isDragging = false;
};
</script>

<style scoped>
.zoom-container {
    width: 300px; /* Adjust the width of the zoomable area as needed */
    height: 200px; /* Adjust the height of the zoomable area as needed */
    overflow: hidden;
    border: 1px solid #ccc; /* Optional: Add a border for visualization */
    cursor: grab; /* Set the cursor to indicate draggable area */
}

.content {
    width: 100%; /* Set the width of the content inside the zoomable area */
    height: 100%; /* Set the height of the content inside the zoomable area */
    background-color: #ffffff; /* Optional: Set a background color for the blank space */
    padding: 10px;
    transition: transform 0.3s ease; /* Add transition for smoother zoom effect */
}

.content:hover {
    cursor: grab; /* Change cursor on hover to indicate draggable area */
}

.content:active {
    cursor: grabbing; /* Change cursor when dragging */
}
</style>
