import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { pageSchema } from '@/shared/validations/page';
import Button from '@/components/UI/Button';
import Input from '@/components/UI/Input';

const PageModal = ({ isOpen, onClose, onSubmit, initialData, loading }) => {
    const {
        register,
        handleSubmit,
        formState: { errors },
        reset,
    } = useForm({
        resolver: zodResolver(pageSchema),
        defaultValues: initialData || {
            title: '',
            slug: '',
            content: { sections: [] },
            is_published: false,
        },
    });

    React.useEffect(() => {
        if (isOpen) {
            reset(initialData || {
                title: '',
                slug: '',
                content: { sections: [] },
                is_published: false,
            });
        }
    }, [isOpen, initialData, reset]);

    if (!isOpen) return null;

    const handleFormSubmit = (data) => {
        onSubmit(data);
    };

    return (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div className="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md mx-auto">
                <div className="p-6">
                    <h2 className="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        {initialData ? 'Edit Page' : 'Create New Page'}
                    </h2>

                    <form onSubmit={handleSubmit(handleFormSubmit)} className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Page Title *
                            </label>
                            <Input
                                {...register('title')}
                                placeholder="Enter page title"
                                error={errors.title?.message}
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                URL Slug *
                            </label>
                            <Input
                                {...register('slug')}
                                placeholder="page-slug"
                                error={errors.slug?.message}
                            />
                            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                This will be used in the page URL: /page/your-slug
                            </p>
                        </div>

                        <div className="flex items-center">
                            <input
                                type="checkbox"
                                {...register('is_published')}
                                className="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                            />
                            <label className="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                Publish immediately
                            </label>
                        </div>

                        <div className="flex space-x-3 pt-4">
                            <Button
                                type="submit"
                                variant="primary"
                                loading={loading}
                                className="flex-1"
                            >
                                {initialData ? 'Update Page' : 'Create Page'}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={onClose}
                                disabled={loading}
                            >
                                Cancel
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default PageModal;