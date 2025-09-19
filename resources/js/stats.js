import Chart from 'chart.js/auto';

const palette = [
    { bg: 'rgba(37, 99, 235, 0.78)', border: 'rgba(37, 99, 235, 1)' },
    { bg: 'rgba(124, 58, 237, 0.78)', border: 'rgba(124, 58, 237, 1)' },
    { bg: 'rgba(34, 197, 94, 0.78)', border: 'rgba(34, 197, 94, 1)' },
    { bg: 'rgba(249, 115, 22, 0.78)', border: 'rgba(249, 115, 22, 1)' },
    { bg: 'rgba(219, 39, 119, 0.78)', border: 'rgba(219, 39, 119, 1)' },
    { bg: 'rgba(14, 165, 233, 0.78)', border: 'rgba(14, 165, 233, 1)' },
    { bg: 'rgba(217, 119, 6, 0.78)', border: 'rgba(217, 119, 6, 1)' },
    { bg: 'rgba(99, 102, 241, 0.78)', border: 'rgba(99, 102, 241, 1)' },
];

const modeStyles = {
    light: {
        text: '#111827',
        grid: 'rgba(17, 24, 39, 0.08)',
        tooltipBg: 'rgba(17, 24, 39, 0.92)',
        tooltipText: '#f9fafb',
    },
    dark: {
        text: '#e5e7eb',
        grid: 'rgba(148, 163, 184, 0.35)',
        tooltipBg: 'rgba(15, 23, 42, 0.94)',
        tooltipText: '#f8fafc',
    },
};

const getMode = () => {
    if (document.documentElement.classList.contains('dark')) {
        return 'dark';
    }

    if (typeof window !== 'undefined' && typeof window.matchMedia === 'function') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    return 'light';
};

const charts = [];

const buildColors = (count) => {
    const background = [];
    const border = [];

    for (let index = 0; index < count; index += 1) {
        const swatch = palette[index % palette.length];
        background.push(swatch.bg);
        border.push(swatch.border);
    }

    return { background, border };
};

const syncFallback = (canvas, fallback, visible) => {
    if (visible) {
        canvas.classList.remove('hidden');
        fallback?.classList.add('hidden');
    } else {
        canvas.classList.add('hidden');
        fallback?.classList.remove('hidden');
    }
};

const createChart = (canvasId, config, styles) => {
    const canvas = document.getElementById(canvasId);

    if (!canvas) {
        return null;
    }

    const fallback = document.querySelector(`[data-chart-fallback="${canvasId}"]`);
    const datasets = config?.data?.datasets ?? [];
    const values = datasets
        .flatMap((dataset) => dataset?.data ?? [])
        .map((value) => Number(value));
    const hasData = values.some((value) => Number.isFinite(value) && value > 0);

    if (!hasData) {
        syncFallback(canvas, fallback, false);
        return null;
    }

    syncFallback(canvas, fallback, true);

    const preparedDatasets = datasets.map((dataset) => {
        const length = dataset?.data?.length ?? 0;
        const colors = buildColors(length);

        return {
            borderRadius: config.type === 'bar' ? 6 : 0,
            maxBarThickness: config.type === 'bar' ? 48 : undefined,
            ...dataset,
            backgroundColor: dataset.backgroundColor ?? colors.background,
            borderColor: dataset.borderColor ?? colors.border,
            borderWidth: config.type === 'doughnut' ? 1 : 2,
        };
    });

    return new Chart(canvas, {
        type: config.type,
        data: {
            ...config.data,
            datasets: preparedDatasets,
        },
        options: {
            indexAxis: config.indexAxis ?? 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: config.type !== 'bar' || preparedDatasets.length > 1,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 16,
                        color: styles.text,
                    },
                },
                title: config.title
                    ? {
                          display: true,
                          text: config.title,
                          color: styles.text,
                          font: { weight: '600' },
                      }
                    : undefined,
                tooltip: {
                    backgroundColor: styles.tooltipBg,
                    titleColor: styles.tooltipText,
                    bodyColor: styles.tooltipText,
                    footerColor: styles.tooltipText,
                    cornerRadius: 8,
                    padding: 12,
                },
            },
            scales:
                config.type === 'bar'
                    ? {
                          x: {
                              ticks: { color: styles.text },
                              grid: {
                                  color: styles.grid,
                                  drawBorder: false,
                              },
                          },
                          y: {
                              beginAtZero: true,
                              ticks: { color: styles.text },
                              grid: {
                                  color: styles.grid,
                                  drawBorder: false,
                              },
                          },
                      }
                    : {},
        },
    });
};

const renderCharts = () => {
    const configs = window.statsChartConfigs ?? {};
    const entries = Object.entries(configs);

    if (!entries.length) {
        return;
    }

    const mode = getMode();
    const styles = modeStyles[mode];

    entries.forEach(([canvasId, config]) => {
        const chart = createChart(canvasId, config, styles);
        if (chart) {
            charts.push(chart);
        }
    });
};

const destroyCharts = () => {
    while (charts.length) {
        const chart = charts.pop();
        chart?.destroy();
    }
};

const bootstrap = () => {
    destroyCharts();
    renderCharts();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrap, { once: true });
} else {
    bootstrap();
}

const observer = new MutationObserver((mutations) => {
    if (mutations.some((mutation) => mutation.attributeName === 'class')) {
        bootstrap();
    }
});

observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
