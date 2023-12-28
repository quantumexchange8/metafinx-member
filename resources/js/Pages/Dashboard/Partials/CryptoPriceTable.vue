<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";

const liveCryptoPrices = [
    { value: 'btcusdt', name: 'Bitcoin', coin: 'BTC', img: '/assets/crypto/bitcoin.png' },
    { value: 'ethusdt', name: 'Ethereum', coin: 'ETH', img: '/assets/crypto/ethereum.png' },
    { value: 'fdusdusdt', name: 'Tether', coin: 'USDT', img: '/assets/crypto/tether.png' },
    { value: 'bnbusdt', name: 'Binance Coin', coin: 'BNB', img: '/assets/crypto/binance_coin.png' },
    { value: 'xrpusdt', name: 'XRP', coin: 'XRP', img: '/assets/crypto/xrp.png' },
    { value: 'usdcusdt', name: 'USD Coin', coin: 'USDC', img: '/assets/crypto/usd_coin.png' },
    { value: 'solusdt', name: 'Solana', coin: 'SOL', img: '/assets/crypto/solana.png' },
    { value: 'adausdt', name: 'Cardano', coin: 'ADA', img: '/assets/crypto/cardano.png' },
    { value: 'dogeusdt', name: 'Dogecoin', coin: 'DOGE', img: '/assets/crypto/dogecoin.png' },
    { value: 'trxusdt', name: 'TRON', coin: 'TRX', img: '/assets/crypto/tron.png' },
];

const cryptoPrices = ref({});
const cryptoPricesChanges = ref({});
const isLoading = ref(true);

const websocketConnections = {}; // Store WebSocket connections
let connectedCount = 0; // Counter for connected WebSockets
let dataReceivedCount = 0; // Counter for WebSockets that received data

onMounted(() => {
    liveCryptoPrices.forEach((crypto) => {
        const ws = new WebSocket(`wss://stream.binance.com:9443/ws/${crypto.value}@ticker`);

        ws.onopen = () => {
            // WebSocket connection is established
            connectedCount++;

            if (connectedCount === liveCryptoPrices.length) {
                // All WebSockets are connected
                isLoading.value = false;
            }
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            cryptoPrices.value[crypto.name] = parseFloat(data.c);
            cryptoPricesChanges.value[crypto.name] = parseFloat(data.P).toFixed(2);
            dataReceivedCount++;

            if (dataReceivedCount === liveCryptoPrices.length) {
                // All WebSockets have received data
                isLoading.value = false;
            }
        };

        websocketConnections[crypto.name] = ws;
    });

    onBeforeUnmount(() => {
        // Close WebSocket connections on component unmount
        liveCryptoPrices.forEach((crypto) => {
            websocketConnections[crypto.name].close();
        });
    });
});
</script>

<template>
    <h4 class="font-semibold dark:text-white">{{$t('public.crypto_price_table.cryptocurrency_market_price')}}</h4>
    <div class="relative overflow-x-auto sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-5">
            <thead class="text-xs font-medium text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-gray-400 border-b dark:border-gray-600">
            <tr>
                <th scope="col" class="py-3">
                    {{$t('public.crypto_price_table.name')}}
                </th>
                <th scope="col" class="py-3">
                    {{$t('public.crypto_price_table.price')}}
                </th>
                <th scope="col" class="py-3 text-end">
                    {{$t('public.crypto_price_table.change')}}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="(crypto, index) in liveCryptoPrices"
                :key="index"
                class="bg-white dark:bg-transparent text-xs text-gray-900 dark:text-white border-b dark:border-gray-600"
            >
                <td class="py-3 inline-flex items-center gap-3">
                    <div class="rounded-full w-6 h-6 shrink-0 grow-0">
                        <img :src="crypto.img" alt="icon">
                    </div>
                    <div>
                        <span class="dark:text-white text-sm">{{ crypto.coin }}</span> <span class="dark:text-gray-400">{{ crypto.name }}</span>
                    </div>
                </td>
                <td class="p-2 w-1/4">
                    <div v-if="isLoading" class="animate-pulse h-2.5 bg-gray-200 rounded-full dark:bg-gray-500 w-32 mb-2.5"></div>
                    <div v-else>
                        ${{ cryptoPrices[crypto.name] }}
                    </div>
                </td>
                <td class="p-2 w-1/4 text-end">
                    <div v-if="isLoading" class="animate-pulse h-2.5 bg-gray-200 rounded-full dark:bg-gray-500 w-32 mb-2.5"></div>
                    <div v-else>
                         <div :class="{ 'text-success-500': parseFloat(cryptoPricesChanges[crypto.name]) > 0, 'text-error-500': parseFloat(cryptoPricesChanges[crypto.name]) < 0 }">
                             <template v-if="parseFloat(cryptoPricesChanges[crypto.name]) > 0">
                                +{{ cryptoPricesChanges[crypto.name] }} %
                             </template>
                             <template v-else>
                                 {{ cryptoPricesChanges[crypto.name] }} %
                             </template>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
