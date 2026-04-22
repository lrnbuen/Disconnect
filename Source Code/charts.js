// shared chart colours
const ACCENT  = '#3D5AFE';
const SUCCESS = '#4CAF82';
const MUTED   = '#E4E4E0';
const TEXT    = '#1A1A1A';
const BORDER  = '#E8E8E4';

// shared chart options applied to every chart
const sharedOptions = {
    responsive: true,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1A1A1A',
            titleColor: '#fff',
            bodyColor: '#ccc',
            padding: 10,
            cornerRadius: 6,
        }
    },
    scales: {
        x: {
            grid: { color: BORDER },
            ticks: { color: '#888884', font: { family: 'DM Sans', size: 11 } }
        },
        y: {
            grid: { color: BORDER },
            ticks: { color: '#888884', font: { family: 'DM Sans', size: 11 } }
        }
    }
};

// build the mood line chart
function buildMoodChart(dates, values) {
    const ctx = document.getElementById('moodChart').getContext('2d');

    // gradient fill under the line
    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(61, 90, 254, 0.18)');
    gradient.addColorStop(1, 'rgba(61, 90, 254, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                data: values,
                borderColor: ACCENT,
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: ACCENT,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4, // smooth curve
                fill: true,
            }]
        },
        options: {
            ...sharedOptions,
            scales: {
                ...sharedOptions.scales,
                y: {
                    ...sharedOptions.scales.y,
                    min: 1,
                    max: 5,
                    ticks: {
                        ...sharedOptions.scales.y.ticks,
                        stepSize: 1,
                        // label each tick with the mood word
                        callback: v => ['', 'rough', 'low', 'okay', 'good', 'great'][v] || v
                    }
                }
            }
        }
    });
}

// build the weekly success bar chart
function buildSuccessChart(labels, values) {
    const ctx = document.getElementById('successChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: SUCCESS,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            ...sharedOptions,
            scales: {
                ...sharedOptions.scales,
                y: {
                    ...sharedOptions.scales.y,
                    min: 0,
                    max: 100,
                    ticks: {
                        ...sharedOptions.scales.y.ticks,
                        callback: v => v + '%' // add percent sign to y axis labels
                    }
                }
            }
        }
    });
}