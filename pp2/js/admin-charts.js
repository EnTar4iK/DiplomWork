// Renders dashboard charts. Expects window.adminDashboard payload from admin.php.
(function () {
    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); }
        else { document.addEventListener('DOMContentLoaded', fn); }
    }

    ready(function () {
        if (typeof Chart === 'undefined' || !window.adminDashboard) return;

        var palette = {
            blue:        '#98BCDD',
            blueStrong:  '#6B98C4',
            purple:      '#382C44',
            copper:      '#A46150',
            copperSoft:  '#C58472',
            muted:       '#585D63',
            line:        'rgba(7, 10, 13, 0.06)',
            text:        '#070A0D'
        };

        Chart.defaults.font.family = "'Inter', 'Manrope', 'Segoe UI', system-ui, -apple-system, Arial, sans-serif";
        Chart.defaults.color = palette.muted;
        Chart.defaults.plugins.legend.labels.boxWidth = 12;
        Chart.defaults.plugins.legend.labels.boxHeight = 12;

        var data = window.adminDashboard;

        var revenueEl = document.getElementById('revenueChart');
        if (revenueEl) {
            var ctx = revenueEl.getContext('2d');
            var grad = ctx.createLinearGradient(0, 0, 0, 280);
            grad.addColorStop(0, 'rgba(152, 188, 221, 0.45)');
            grad.addColorStop(1, 'rgba(152, 188, 221, 0.02)');
            new Chart(revenueEl, {
                type: 'line',
                data: {
                    labels: data.revenue.labels,
                    datasets: [{
                        label: 'Выручка, ₽',
                        data: data.revenue.data,
                        borderColor: palette.blueStrong,
                        backgroundColor: grad,
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: palette.blueStrong,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ctx.parsed.y.toLocaleString('ru-RU') + ' ₽';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: palette.line, drawBorder: false },
                            ticks: {
                                callback: function (v) {
                                    if (v >= 1000) return (v / 1000).toFixed(0) + 'к';
                                    return v;
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { autoSkip: true, maxRotation: 0 }
                        }
                    }
                }
            });
        }

        var statusEl = document.getElementById('statusChart');
        if (statusEl) {
            new Chart(statusEl, {
                type: 'doughnut',
                data: {
                    labels: data.status.labels,
                    datasets: [{
                        data: data.status.data,
                        backgroundColor: [
                            palette.blue,
                            palette.copper,
                            palette.purple,
                            palette.blueStrong,
                            palette.copperSoft,
                            palette.muted
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 14, usePointStyle: true, pointStyle: 'circle' }
                        }
                    }
                }
            });
        }

        var topProductsEl = document.getElementById('topProductsChart');
        if (topProductsEl) {
            new Chart(topProductsEl, {
                type: 'bar',
                data: {
                    labels: data.topProducts.labels,
                    datasets: [{
                        label: 'Продано, шт.',
                        data: data.topProducts.data,
                        backgroundColor: palette.copper,
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 24
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: palette.line, drawBorder: false },
                            ticks: { precision: 0 }
                        },
                        y: { grid: { display: false } }
                    }
                }
            });
        }

        var ordersEl = document.getElementById('ordersChart');
        if (ordersEl) {
            new Chart(ordersEl, {
                type: 'bar',
                data: {
                    labels: data.ordersByDay.labels,
                    datasets: [{
                        label: 'Заказов',
                        data: data.ordersByDay.data,
                        backgroundColor: palette.blue,
                        borderRadius: 4,
                        borderSkipped: false,
                        maxBarThickness: 18
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: palette.line, drawBorder: false },
                            ticks: { precision: 0 }
                        },
                        x: { grid: { display: false }, ticks: { autoSkip: true, maxRotation: 0 } }
                    }
                }
            });
        }
    });
})();
