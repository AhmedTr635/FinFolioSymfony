// trading.js

// Function to create and update the chart
 src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"
function createAndUpdateChart() {
    const chart = LightweightCharts.createChart(document.getElementById('chart'), {
        width: 800,
        height: 400,
        priceScale: {
            scaleMargins: {
                top: 0.3,
                bottom: 0.2
            }
        },
        timeScale: {
            visible: false,
            timeVisible: true,
            secondsVisible: false,
            tickMarkType: 'timestamp'
        }
    });

    const candleSeries = chart.addCandlestickSeries();

    let lastOpenPrice = null;

    function parseCandlestickData(data) {
        const candlestickData = [];
        for (let i = 0; i < data.length; i++) {
            const time = new Date(data[i][0]).getTime();
            const open = parseFloat(data[i][1]);
            const high = parseFloat(data[i][2]);
            const low = parseFloat(data[i][3]);
            const close = parseFloat(data[i][4]);

            if (!isNaN(open) && !isNaN(high) && !isNaN(low) && !isNaN(close)) {
                candlestickData.push({ time, open, high, low, close });
            } else {
                console.warn("Skipping data point due to missing or invalid values:", { time, open, high, low, close });
            }
        }
        return candlestickData;
    }

    function filterData(data) {
        for (let i = 1; i < data.length; i++) {
            if (data[i].open === null || isNaN(data[i].open)) {
                data[i].open = data[i - 1].open;
            }
            if (data[i].high === null || isNaN(data[i].high)) {
                data[i].high = data[i - 1].high;
            }
            if (data[i].low === null || isNaN(data[i].low)) {
                data[i].low = data[i - 1].low;
            }
            if (data[i].close === null || isNaN(data[i].close)) {
                data[i].close = data[i - 1].close;
            }
        }
        return data;
    }

    function displayFilteredData(symbol, interval) {
        fetch(`https://api.binance.com/api/v3/klines?symbol=${symbol}USDT&interval=${interval}`)
            .then(response => response.json())
            .then(newData => {
                const parsedData = parseCandlestickData(newData);
                const filteredData = filterData(parsedData);
                candleSeries.setData(filteredData);

                if (filteredData.length > 0) {
                    lastOpenPrice = filteredData[filteredData.length - 1].open;
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    // Call the function initially with default values
    displayFilteredData('BTC', '1m');

    // Refresh data and chart every minute
    setInterval(function() {
        displayFilteredData('BTC', '1m');
    }, 60000); // Refresh every 1 minute (60 seconds)
}
