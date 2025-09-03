import React from 'react';
import { clsx } from 'clsx';

const Input = React.forwardRef(({ className, type, error, ...props }, ref) => {
    return (
        <div className="w-full">
            <input
                type={type}
                className={clsx(
                    'input-primary',
                    error && 'border-red-500 focus:ring-red-500 focus:border-red-500',
                    className
                )}
                ref={ref}
                {...props}
            />
            {error && (
                <p className="mt-1 text-sm text-red-600 dark:text-red-400">{error}</p>
            )}
        </div>
    );
});

Input.displayName = 'Input';

export default Input;
