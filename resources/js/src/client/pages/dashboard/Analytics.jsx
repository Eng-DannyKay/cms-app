import React, { useState, useEffect } from 'react';
import MainLayout from '../dashboard/components/MainLayout';
import MetricsCard from '../analytics/components/MetricsCard';
import VisitorChart from '@/client/components/analytics/VisitorChart';
import GeographyChart from '@/client/components/analytics/GeographyChart';
import DeviceChart from '@/client/components/analytics/DeviceChart';
import { analyticsApi } from '../../../services/analyticsApi';
import Button from '../../../Components/UI/Button';
import LoadingSpinner from '../../../Components/UI/LoadingSpinner';

const Analytics = () => {
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [period, setPeriod] = useState('30d');
    const [summary, setSummary] = useState(null);
    const [charts, setCharts] = useState({});

    useEffect(() => {
        loadAnalyticsData();
    }, [period]);

    const loadAnalyticsData = async () => {
        try {
            setLoading(true);
            setError(null);

            const [summaryResponse] = await Promise.all([
                analyticsApi.getClientAnalytics(period),

            ]);

            setSummary(summaryResponse.data);
            // Set chart data
        } catch (err) {
            setError(err.message);
            console.error('Failed to load analytics:', err);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <MainLayout>
                <div className="flex items-center justify-center h-64">
                    <LoadingSpinner className="w-8 h-8" />
                </div>
            </MainLayout>
        );
    }

    return (
        <MainLayout>
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                            Analytics Dashboard
                        </h1>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Track your website performance and visitor insights
                        </p>
                    </div>

                    <div className="flex space-x-2">
                        <select
                            value={period}
                            onChange={(e) => setPeriod(e.target.value)}
                            className="input-primary"
                        >
                            <option value="24h">Last 24 hours</option>
                            <option value="7d">Last 7 days</option>
                            <option value="30d">Last 30 days</option>
                            <option value="90d">Last 90 days</option>
                        </select>
                        <Button variant="outline">
                            Export Report
                        </Button>
                    </div>
                </div>

                {error && (
                    <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl">
                        {error}
                    </div>
                )}

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <MetricsCard
                        title="Total Visitors"
                        value={summary?.total_views || 0}
                        change={12.5}
                        changeType="increase"
                        icon="👥"
                    />
                    <MetricsCard
                        title="Unique Visitors"
                        value={summary?.unique_visitors || 0}
                        change={8.3}
                        changeType="increase"
                        icon="👤"
                    />
                    <MetricsCard
                        title="Bounce Rate"
                        value={`${summary?.bounce_rate || 0}%`}
                        change={-4.2}
                        changeType="decrease"
                        icon="📉"
                    />
                    <MetricsCard
                        title="Avg. Session"
                        value={`${summary?.avg_session_duration || 0}m`}
                        change={15.7}
                        changeType="increase"
                        icon="⏱️"
                    />
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <VisitorChart data={charts.visitors} period={period} />
                    <GeographyChart data={charts.geography} />
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <DeviceChart data={charts.devices} />
                </div>
            </div>
        </MainLayout>
    );
};

export default Analytics;
