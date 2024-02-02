<script setup>
import Loading from "@/Components/Loading.vue";
import { onMounted, ref, watch } from "vue";
import Chart from 'chart.js/auto';

const props = defineProps({
    selectedMonth: [String, Number],
    getAmountPrefix: String
});

const chartData = ref({
    labels: [],
    datasets: [],
});
const isLoading = ref(false);
const month = ref(props.selectedMonth);
let chartInstance = null;

const fetchData = async () => {
    try {
        if (chartInstance) {
            chartInstance.destroy();
        }

        const ctx = document.getElementById('coinChart');

        isLoading.value = true;

        const response = await axios.get('/wallet/getCoinChart', { params: { month: month.value } });
        const { labels, datasets } = response.data;
        chartData.value.labels = labels;
        chartData.value.datasets = datasets;
        datasets[0].backgroundColor = (context) => {
            const bgColor = [
                'rgba(50, 213, 131, 0.2)',
                'rgba(50, 213, 131, 0.00)'
            ];

            const bgNegativeColor = [
                'rgba(240, 68, 56, 0.2)',
                'rgba(240, 68, 56, 0.00)'
            ];

            const bgNeutralColor = [
                'rgba(157,164,174, 0.2)',
                'rgba(157,164,174, 0.00)'
            ];

            if (!context.chart.chartArea) {
                return;
            }

            const { ctx, data, chartArea: { top, bottom } } = context.chart;
            const gradientBg = ctx.createLinearGradient(0, top, 0, bottom);

            if (props.getAmountPrefix === '+') {
                gradientBg.addColorStop(0, bgColor[0]);
                gradientBg.addColorStop(1, bgColor[1]);
            } else if (props.getAmountPrefix === '-') {
                gradientBg.addColorStop(0, bgNegativeColor[0]);
                gradientBg.addColorStop(1, bgNegativeColor[1]);
            } else {
                gradientBg.addColorStop(0, bgNeutralColor[0]);
                gradientBg.addColorStop(1, bgNeutralColor[1]);
            }

            return gradientBg;
        };

        isLoading.value = false;

        // Create the chart after updating chartData
        chartInstance = new Chart(ctx, {
            type: 'line',
            data: chartData.value,
            options: {
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                maintainAspectRatio: false,
                scales: {
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#9DA4AE',
                            font: {
                                family: 'Inter, sans-serif',
                                size: 14,
                                weight: 400,
                            },
                        },
                        grace: '50%',
                        beginAtZero: true
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#9DA4AE',
                            font: {
                                family: 'Inter, sans-serif',
                                size: 14,
                                weight: 400,
                            },
                        },
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        displayColors: false
                    }
                },
                onHover: (event, chartElements) => {
                if (chartElements.length > 0) {
                    const hoverX = chartElements[0].element.x;
                    const { top, bottom } = chartInstance.chartArea;
                    const ctx = chartInstance.ctx;

                    // Draw a vertical line
                    ctx.save();
                    ctx.beginPath();
                    ctx.strokeStyle = '#4D5761'; // White color
                    ctx.lineWidth = 1;
                    ctx.setLineDash([5, 5]);
                    ctx.moveTo(hoverX, top);
                    ctx.lineTo(hoverX, bottom);
                    ctx.stroke();
                    ctx.restore();
                }
            },

            }
        });
    } catch (error) {
        const ctx = document.getElementById('coinChart');

        isLoading.value = false;
        console.error('Error fetching chart data:', error);
    }
};

onMounted(async () => {
    await fetchData(); // Fetch data on mount

    // Watch for changes in the date and fetch data when it changes
    watch(
        () => props.selectedMonth,
        (newMonth) => {
            month.value = newMonth;
            fetchData();
        }
    );
});
</script>

<template>
    <div v-if="isLoading" class="flex justify-center mt-2">
        <Loading/>
    </div>
    <div>
        <canvas id="coinChart" height="350"></canvas>
    </div>
</template>
