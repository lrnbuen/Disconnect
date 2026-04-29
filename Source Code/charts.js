//earthy colour palette 
const ACCENT  = '#4A7C59';  // forest green
const SUCCESS = '#3A6B48';  // medium green
const BORDER  = '#DDD8CC';  // warm beige border

//options shared by both charts
const baseOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#2C2416',
            titleColor: '#FDFCF9',
            bodyColor: '#C8BCA8',
            padding: 10,
            cornerRadius: 6
        }
    },
    scales: {
        x: {
            grid: { color: BORDER },
            ticks: { color: '#8C7E6A', font: { family: 'DM Sans', size: 11 } }
        },
        y: {
            grid: { color: BORDER },
            ticks: { color: '#8C7E6A', font: { family: 'DM Sans', size: 11 } }
        }
    }
};

//builds the mood line chart
function buildMoodChart(dates, values) {
    const ctx      = document.getElementById('moodChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(74,124,89,0.2)');
    gradient.addColorStop(1, 'rgba(74,124,89,0)');

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
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            ...baseOptions,
            scales: {
                ...baseOptions.scales,
                y: {
                    ...baseOptions.scales.y,
                    min: 1,
                    max: 5,
                    ticks: {
                        ...baseOptions.scales.y.ticks,
                        stepSize: 1,
                        //show mood word instead of number on y axis
                        callback: v => ['','rough','low','okay','good','great'][v] || v
                    }
                }
            }
        }
    });
}

//builds the weekly success bar chart
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
                borderSkipped: false
            }]
        },
        options: {
            ...baseOptions,
            scales: {
                ...baseOptions.scales,
                y: {
                    ...baseOptions.scales.y,
                    min: 0,
                    max: 100,
                    ticks: {
                        ...baseOptions.scales.y.ticks,
                        callback: v => v + '%'
                    }
                }
            }
        }
    });
}
