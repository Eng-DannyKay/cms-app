export const chartColors = {
    primary: '#4c1d95',
    secondary: '#8b5cf6',
    accent: '#ec4899',
    success: '#10b981',
    warning: '#f59e0b',
    error: '#ef4444',
    grid: '#e5e7eb',
    text: '#374151',
};

export const getChartOptions = (theme = 'light') => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
            labels: {
                color: theme === 'dark' ? '#d1d5db' : '#374151',
                font: {
                    family: 'Inter, sans-serif',
                },
            },
        },
        tooltip: {
            backgroundColor: theme === 'dark' ? '#1f2937' : '#ffffff',
            titleColor: theme === 'dark' ? '#ffffff' : '#374151',
            bodyColor: theme === 'dark' ? '#d1d5db' : '#6b7280',
            borderColor: theme === 'dark' ? '#374151' : '#e5e7eb',
            borderWidth: 1,
        },
    },
    scales: {
        x: {
            grid: {
                color: theme === 'dark' ? '#374151' : '#e5e7eb',
            },
            ticks: {
                color: theme === 'dark' ? '#9ca3af' : '#6b7280',
                font: {
                    family: 'Inter, sans-serif',
                },
            },
        },
        y: {
            grid: {
                color: theme === 'dark' ? '#374151' : '#e5e7eb',
            },
            ticks: {
                color: theme === 'dark' ? '#9ca3af' : '#6b7280',
                font: {
                    family: 'Inter, sans-serif',
                },
            },
        },
    },
});


export const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
    });
};

export const getDateRange = (period) => {
    const now = new Date();
    switch (period) {
        case '24h':
            return new Date(now.setDate(now.getDate() - 1));
        case '7d':
            return new Date(now.setDate(now.getDate() - 7));
        case '30d':
            return new Date(now.setDate(now.getDate() - 30));
        case '90d':
            return new Date(now.setDate(now.getDate() - 90));
        default:
            return new Date(now.setDate(now.getDate() - 30));
    }
};