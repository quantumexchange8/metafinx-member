<script setup>
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import Loading from "@/Components/Loading.vue";
import {onMounted, ref, watch} from "vue";
import Chart from 'chart.js/auto'
import ChartDataLabels from 'chartjs-plugin-datalabels';
const date = ref('');

const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});

const chartData = ref({
    labels: [],
    datasets: [{
        data: [],
        backgroundColor: [
            '#FF2D55',
            '#FDB022',
            '#6C737F'
        ],
        borderColor: 'transparent',
        hoverOffset: 4,
        borderRadius: 20,
        borderJoinStyle: 'round',
        weight: 1,
    }]
});
const isLoading = ref(false)

let chartInstance = null; // Variable to store the chart instance

const DoughnutLabel = {
    id: 'doughnutLabel',
    beforeDatasetsDraw(chart, args, options) {
        const { ctx, data } = chart;

        ctx.save();
        const xCoor = chart.getDatasetMeta(0).data[0].x
        const yCoor = chart.getDatasetMeta(0).data[0].y
        ctx.fillStyle = '#ffffff'
        ctx.textAlign = 'center'
        ctx.textBaseline = 'middle'
        ctx.fillText(`$ ${data.datasets[0].data[0]}`, xCoor, yCoor)
    }
}

const fetchData = async () => {
    try {
        const ctx = document.getElementById('planChart');

        const response = await axios.get('/wallet/getWalletBalance', { params: { date: date.value } });
        const { labels, datasetData } = response.data;
        // Update chartData
        chartData.value.labels = labels;
        chartData.value.datasets[0].data = datasetData;

        // Destroy previous chart instance if exists
        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: chartData.value,
            plugins: [DoughnutLabel],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                family: 'Inter, sans-serif',
                                size: 14,
                                weight: 400,
                            },
                            padding: 20,
                            color: '#fff',
                            usePointStyle: true,
                            boxHeight: 8
                        },
                        align: 'center',
                        position: 'bottom',
                    },
                },
                cutout: 90,
            },
        });
    } catch (error) {
        console.error('Error fetching data:', error);
    }
};

onMounted(() => {
    fetchData(); // Fetch data on mount

    // Watch for changes in the date and fetch data when it changes
    watch(date, () => {
        fetchData();
    });
});

</script>

<template>
    <div class="h-64">
        <canvas id="planChart"></canvas>
    </div>
</template>
