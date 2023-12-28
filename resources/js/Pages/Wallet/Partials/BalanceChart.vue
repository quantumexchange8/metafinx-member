<script setup>
import { onMounted, ref, watch } from 'vue';
import Chart from 'chart.js/auto';

const date = ref('');
let totalSum = 0;
const chartData = ref({
    labels: [],
    datasets: [
        {
            data: [],
            backgroundColor: ['#FF2D55', '#4d5761'],
            hoverOffset: 4,
            borderWidth: 0,
            label: '',
            borderColor: 'transparent',
            weight: 1,
        },
        {
            data: [],
            backgroundColor: ['#4d5761', '#4d5761'], // Transparent colors for spacing
            hoverOffset: 0,
            borderWidth: 0,
            label: '',
            borderColor: 'transparent',
            weight: 1,
        },
        {
            data: [],
            backgroundColor: ['#FDB022', '#4d5761'],
            hoverOffset: 4,
            borderWidth: 0,
            label: '',
            borderColor: 'transparent',
            weight: 1,
        },
    ],
});
let chartInstance = null;

const fetchData = async () => {
    try {
        const ctx = document.getElementById('planChart');

        const response = await axios.get('/wallet/getWalletBalance', { params: { date: date.value } });
        const { labels, datasetData } = response.data;

        chartData.value.labels = ['Wallet Balance'];

        // Calculate the total sum of datasetData
        totalSum = datasetData.reduce((acc, value) => acc + parseFloat(value), 0);

        // Calculate the remaining part for both charts
        const remainingPartChart1 = totalSum - datasetData[0]; // For the first chart
        const remainingPartChart2 = totalSum - datasetData[1]; // For the second chart

        chartData.value.datasets[0].data = [datasetData[0], remainingPartChart1];
        chartData.value.datasets[0].label = labels[0];
        chartData.value.datasets[2].data = [datasetData[1], remainingPartChart2];
        chartData.value.datasets[2].label = labels[1];

        // Hide legend for the transparent ring
        chartData.value.datasets[1].labels = [];

        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: chartData.value,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    tooltip: {
                        enabled: true, // Enable tooltips by default
                        callbacks: {
                            label: function (context) {
                                // Check if it's the first part of the second chart or the second part of the first chart and return an empty string to disable tooltip
                                if ((context.datasetIndex === 0 && context.dataIndex === 1) ||
                                    (context.datasetIndex === 2 && context.dataIndex === 1)) {
                                    return '';
                                }
                                // Otherwise, return the label for tooltip display
                                return chartData.value.label;
                            }
                        }
                    },
                    legend: {
                        position: 'right',
                        display: true,
                        labels: {
                            font: {
                                family: 'Inter, sans',
                                size: 14,
                                weight: 'bold',
                            },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxHeight: 8,
                            // Customizing legend colors
                            generateLabels: function (chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.datasets
                                        .filter((dataset) => dataset.label) // Filter out datasets without labels
                                        .map((dataset, i) => {
                                            const color = i === 0 ? '#FF2D55' : '#FDB022';
                                            return {
                                                text: dataset.label,
                                                fontColor: '#ffffff',
                                                fillStyle: color,
                                                strokeStyle: color,
                                                lineWidth: 2,
                                                hidden: false,
                                            };
                                        });
                                }
                                return [];
                            },
                        },
                    },
                },
            },
            plugins: [
                {
                    afterDraw: (chart) => {
                        const ctx = chart.ctx;
                        const centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                        const centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;

                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillStyle = '#ffffff';
                        ctx.font = '24px Inter';
                        ctx.fillText(`$${totalSum}`, centerX, centerY);
                        ctx.restore();
                    },
                },
            ],
        });
    } catch (error) {
        console.error('Error fetching data:', error);
    }
};

onMounted(() => {
    fetchData();
    watch(date, () => {
        fetchData();
    });
});
</script>

<template>
    <div class="w-full md:h-[200px]">
        <canvas id="planChart"></canvas>
    </div>
</template>
