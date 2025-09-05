import React from 'react';
import { ArrowUp, ArrowDown } from 'lucide-react';

const MetricsCard = ({ title, value, change, changeType, icon, loading = false }) => {
    const isPositive = changeType === 'increase';
    const changeColor = isPositive ? 'text-green-600' : 'text-red-600';
    const ChangeIcon = isPositive ? ArrowUp : ArrowDown;

    if (loading) {
        return (
            <div className="card p-6">
                <div className="animate-pulse">
                    <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3 mb-4"></div>
                    <div className="h-8 bg-gray-200 dark:bg-gray-700 rounded w-2/3 mb-2"></div>
                    <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>
            </div>
        );
    }

    return (
        <div className="card p-6 hover:shadow-lg transition-shadow duration-200">
            <div className="flex items-center justify-between">
                <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">
                        {title}
                    </p>
                    <p className="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {typeof value === 'number' ? value.toLocaleString() : value}
                    </p>
                    <div className={`flex items-center mt-2 ${changeColor}`}>
                        <ChangeIcon size={16} className="mr-1" />
                        <span className="text-sm font-medium">
                            {change}% {isPositive ? 'increase' : 'decrease'}
                        </span>
                    </div>
                </div>
                {icon && (
                    <div className="flex-shrink-0">
                        <div className="p-3 bg-primary/10 rounded-lg">
                            {icon}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default MetricsCard;
