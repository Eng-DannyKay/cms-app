import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { pageApi } from '@/services/pageApi';
import Button from '@/components/UI/Button';
import LoadingSpinner from '@/components/UI/LoadingSpinner';

const PageCard = ({ page, onUpdate, onDelete }) => {
    const [loading, setLoading] = useState(false);
    const [action, setAction] = useState('');

    const handleAction = async (actionType, actionFn) => {
        setLoading(true);
        setAction(actionType);
        
        try {
            const result = await actionFn();
            if (onUpdate) onUpdate(result);
        } catch (error) {
            console.error(`${actionType} error:`, error);
            // TODO: Add toast notification
        } finally {
            setLoading(false);
            setAction('');
        }
    };

    const handlePublish = () => 
        handleAction('publishing', () => pageApi.publishPage(page.id));

    const handleDuplicate = () => 
        handleAction('duplicating', () => pageApi.duplicatePage(page.id));

    const handleDelete = () => 
        handleAction('deleting', () => pageApi.deletePage(page.id).then(() => page));

    return (
        <div className="card group hover:shadow-xl transition-all duration-300">
            <div className="p-6">
                {/* Header */}
                <div className="flex items-start justify-between mb-4">
                    <div className="flex-1 min-w-0">
                        <h3 className="text-lg font-semibold text-gray-900 dark:text-white truncate">
                            {page.title}
                        </h3>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            /{page.slug}
                        </p>
                    </div>
                    
                    {/* Status badge */}
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                        page.is_published 
                            ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' 
                            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
                    }`}>
                        {page.is_published ? 'Published' : 'Draft'}
                    </span>
                </div>

                {/* Content preview */}
                <div className="mb-4">
                    <p className="text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                        {page.content?.sections?.[0]?.content || 'No content yet...'}
                    </p>
                </div>

                {/* Stats */}
                <div className="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-4">
                    <span>v{page.version}</span>
                    <span>
                        Updated {new Date(page.updated_at).toLocaleDateString()}
                    </span>
                </div>

                {/* Actions */}
                <div className="flex items-center justify-between space-x-2">
                    <div className="flex space-x-2">
                        <Link
                            to={`/dashboard/pages/edit/${page.id}`}
                            className="flex items-center px-3 py-1.5 text-sm text-primary hover:bg-primary/10 rounded-lg transition-colors"
                        >
                            Edit
                        </Link>
                        
                        <Button
                            variant="ghost"
                            size="sm"
                            loading={loading && action === 'duplicating'}
                            onClick={handleDuplicate}
                            disabled={loading}
                        >
                            Duplicate
                        </Button>
                    </div>

                    <div className="flex space-x-2">
                        <Button
                            variant={page.is_published ? "secondary" : "primary"}
                            size="sm"
                            loading={loading && action === 'publishing'}
                            onClick={handlePublish}
                            disabled={loading}
                        >
                            {page.is_published ? 'Unpublish' : 'Publish'}
                        </Button>

                        <Button
                            variant="outline"
                            size="sm"
                            loading={loading && action === 'deleting'}
                            onClick={handleDelete}
                            disabled={loading}
                            className="text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20"
                        >
                            Delete
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PageCard;