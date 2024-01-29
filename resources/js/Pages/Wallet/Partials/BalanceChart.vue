<script setup>
import {onMounted, ref, watch, watchEffect} from 'vue';
import Chart from 'chart.js/auto';
import {usePage} from "@inertiajs/vue3";

const date = ref('');
const chartData = ref({
    labels: [],
    datasets: [],
});
let chartInstance = null;

const fetchData = async () => {
    try {
        const ctx = document.getElementById('planChart');

        const response = await axios.get('/wallet/getWalletBalance', { params: { date: date.value } });
        const { labels, datasets } = response.data;

        chartData.value.labels = labels;
        chartData.value.datasets = datasets;

        if (chartInstance) {
            // Update chart data instead of destroying and recreating
            chartInstance.data = chartData.value;
            chartInstance.update();
        } else {
            chartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: chartData.value,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            display: true,
                            labels: {
                                font: {
                                    family: 'Inter, sans',
                                    size: 12,
                                    weight: 'normal',
                                },
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxHeight: 6,
                                color: '#ffffff'
                            },
                        },
                    },
                },
            });
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
};

onMounted(() => {
    fetchData();
});

watchEffect(() => {
    if (usePage().props.title !== null) {
        fetchData();
    }
});
</script>

<template>
    <div class="w-[300px] h-[200px]">
        <canvas id="planChart"></canvas>
    </div>
</template>
