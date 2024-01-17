<script setup>
import {onMounted, ref, watch, watchEffect} from 'vue';
import Chart from 'chart.js/auto';
import {usePage} from "@inertiajs/vue3";

const date = ref('');
let totalSum = 0;
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
        let newTotalSum = 0; // Initialize a new totalSum

        datasets.forEach(dataset => {
            if (dataset.data && dataset.data.length > 0) {
                newTotalSum += parseFloat(dataset.data[0]);
            }
        });
        totalSum = newTotalSum.toLocaleString();

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
                    cutout: '70%',
                    borderRadius: 9999,
                    plugins: {
                        tooltip: {
                            enabled: true, // Enable tooltips by default
                            callbacks: {
                                label: function (context) {
                                    // Check if it's the first part of the second chart or the second part of the first chart and return an empty string to disable tooltip
                                    if (context.dataIndex === 1) {
                                        return chartData.value.labels;
                                    }
                                }
                            }
                        },
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
                        beforeDraw: (chart) => {
                            const ctx = chart.ctx;
                            ctx.save();

                            const datasets = chart.data.datasets;

                            datasets.forEach((dataset, datasetIndex) => {
                                const meta = chart.getDatasetMeta(datasetIndex);
                                const xCoor = meta.data[0].x;
                                const yCoor = meta.data[0].y;
                                const innerRadius = meta.data[0].innerRadius;
                                const outerRadius = meta.data[0].outerRadius;
                                const width = outerRadius - innerRadius - 4
                                const spacing = 4; // Adjust the spacing value as needed
                                const angle = Math.PI / 180;

                                ctx.beginPath();
                                ctx.lineWidth = width;
                                ctx.strokeStyle = '#4D5761';

                                // Adjust the radius to add spacing between strokes
                                const adjustedOuterRadius = outerRadius - width / 2 - datasetIndex * spacing;
                                ctx.arc(xCoor, yCoor, adjustedOuterRadius, 0, angle * 360, false);
                                ctx.stroke();
                            });

                            ctx.restore();
                        },
                        afterDraw: (chart) => {
                            const ctx = chart.ctx;
                            const centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                            const centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;

                            ctx.save();
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillStyle = '#ffffff';
                            ctx.font = 'semibold 16px Inter';
                            ctx.fillText(`$ ${totalSum}`, centerX, centerY);
                            ctx.restore();
                        },
                    },
                ],
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
    <div class="w-[300px] md:h-[200px]">
        <canvas id="planChart"></canvas>
    </div>
</template>
