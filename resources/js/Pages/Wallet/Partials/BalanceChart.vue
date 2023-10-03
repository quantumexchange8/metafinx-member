<script setup>
import VueApexCharts from "vue3-apexcharts";
import {ref, watchEffect} from "vue";

const props = defineProps({
    wallets: Object
})

const chartSeries = ref(null)
const labels = Object.values(props.wallets).map(wallet => wallet.name);

watchEffect(() => {
    chartSeries.value = Object.values(props.wallets).map(wallet => wallet.balance);
})

const chartOptions = {
    chart: {
        height: 390,
        type: 'radialBar',
    },
    plotOptions: {
        radialBar: {
            offsetY: 0,
            offsetX: -25,
            startAngle: 0,
            endAngle: 360,
            hollow: {
                margin: 5,
                size: '70%',
                background: 'transparent',
                image: undefined,
            },
            dataLabels: {
                name: {
                    show: true,
                    offsetY: 6
                },
                total: {
                    show: true,
                    fontSize: '16px',
                    fontFamily: 'Inter',
                    color: '#ffffff',
                    label: '$ ' + chartSeries.value,
                }
            },
        }
    },
    colors: ['#FF2D55', '#F79009', '#5856D6', '#00C7BE'],
    labels: labels,
    stroke: {
        lineCap: 'round'
    },
    legend: {
        show: true,
        floating: true,
        fontSize: '12px',
        position: 'right',
        offsetX: -20,
        offsetY: 80,
        fontFamily: 'Inter',
        // width: 100,
        labels: {
            colors: ['#ffffff'],
            useSeriesColors: false
        },
        markers: {
            size: 0
        },
        itemMargin: {
            vertical: 3
        }
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                height: 200,
            },
            plotOptions: {
                radialBar: {
                    offsetY: 0,
                    offsetX: 10,
                    startAngle: 0,
                    endAngle: 360,
                    hollow: {
                        margin: 5,
                        size: '60%',
                        background: 'transparent',
                        image: undefined,
                    },
                }
            },
            legend: {
                show: true,
                showForSingleSeries: false,
                showForNullSeries: true,
                showForZeroSeries: true,
                position: 'bottom',
                horizontalAlign: 'center',
                floating: false,
                fontSize: '12px',
                fontFamily: 'Inter',
                fontWeight: 400,
                formatter: undefined,
                inverseOrder: false,
                width: undefined,
                height: undefined,
                tooltipHoverFormatter: undefined,
                customLegendItems: [],
                offsetX: 0,
                offsetY: -10,
                labels: {
                    colors: '#fff',
                    useSeriesColors: false
                },
                markers: {
                    width: 10,
                    height: 10,
                    strokeWidth: 0,
                    strokeColor: '#fff',
                    fillColors: undefined,
                    radius: 12,
                    customHTML: undefined,
                    onClick: undefined,
                    offsetX: 0,
                    offsetY: 1
                },
                itemMargin: {
                    horizontal: 5,
                    vertical: 0
                },
                onItemClick: {
                    toggleDataSeries: true
                },
                onItemHover: {
                    highlightDataSeries: true
                },
            },
        }
    }]
}
</script>

<template>
    <VueApexCharts type="radialBar" height="250" :options="chartOptions" :series="chartSeries"></VueApexCharts>
</template>
