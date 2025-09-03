import React from 'react';
import { clsx } from 'clsx';
import LoadingSpinner from './LoadingSpinner';

const Button = ({
    children,
    variant = 'primary',
    size = 'md',
    loading = false,
    disabled,
    className,
    ...props
}) => {
    const baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    const variants = {
        primary: 'btn-primary',
        secondary: 'btn-secondary',
        accent: 'btn-accent',
        outline: 'border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-primary',
        ghost: 'text-gray-700 hover:bg-gray-100 focus:ring-primary',
    };

    const sizes = {
        sm: 'px-3 py-1.5 text-sm',
        md: 'px-4 py-2 text-sm',
        lg: 'px-6 py-3 text-base',
        xl: 'px-8 py-4 text-lg',
    };

    return (
        <button
            className={clsx(
                baseClasses,
                variants[variant],
                sizes[size],
                className
            )}
            disabled={disabled || loading}
            {...props}
        >
            {loading && <LoadingSpinner className="mr-2" />}
            {children}
        </button>
    );
};

export default Button;
