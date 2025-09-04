import React, { useState, useEffect } from 'react';
import { pageApi } from '@/services/pageApi';
import PageCard from './PageCard';
import Button from '@/components/UI/Button';
import LoadingSpinner from '@/components/UI/LoadingSpinner';
import Input from '@/components/UI/Input';

const PageList = ({ onEditPage, onCreatePage }) => {
    const [pages, setPages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [searchTerm, setSearchTerm] = useState('');
    const [filter, setFilter] = useState('all'); 

    useEffect(() => {
        loadPages();
    }, [filter, searchTerm]);

    const loadPages = async () => {
        try {
            setLoading(true);
            setError(null);
            
            const params = {};
            if (searchTerm) params.search = searchTerm;
            if (filter !== 'all') params.published = filter === 'published';
            
            const response = await pageApi.getPages(params);
            setPages(response.data || []);
        } catch (err) {
            setError(err.message);
            console.error('Failed to load pages:', err);
        } finally {
            setLoading(false);
        }
    };

    const handlePageUpdate = (updatedPage) => {
        setPages(prev => prev.map(page => 
            page.id === updatedPage.id ? updatedPage : page
        ));
    };

    const handlePageDelete = (deletedPage) => {
        setPages(prev => prev.filter(page => page.id !== deletedPage.id));
    };

    const filteredPages = pages.filter(page => {
        const matchesSearch = page.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                            page.slug.toLowerCase().includes(searchTerm.toLowerCase());
        const matchesFilter = filter === 'all' || 
                            (filter === 'published' && page.is_published) ||
                            (filter === 'draft' && !page.is_published);
        
        return matchesSearch && matchesFilter;
    });

    if (loading) {
        return (
            <div className="flex items-center justify-center h-64">
                <LoadingSpinner className="w-8 h-8" />
            </div>
        );
    }

    return (
        <div className="space-y-6">
          
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-white">
                        Pages
                    </h2>
                    <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Manage your website pages
                    </p>
                </div>

                <Button
                    variant="primary"
                    onClick={onCreatePage}
                >
                    Create New Page
                </Button>
            </div>
            <div className="flex flex-col sm:flex-row gap-4">
                <div className="flex-1">
                    <Input
                        type="text"
                        placeholder="Search pages..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className="w-full"
                    />
                </div>

                <div className="flex space-x-2">
                    <select
                        value={filter}
                        onChange={(e) => setFilter(e.target.value)}
                        className="input-primary"
                    >
                        <option value="all">All Pages</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

            {error && (
                <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl">
                    {error}
                </div>
            )}
            {filteredPages.length === 0 && !loading && (
                <div className="text-center py-12">
                    <div className="text-gray-400 dark:text-gray-500 text-6xl mb-4">📄</div>
                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        No pages found
                    </h3>
                    <p className="text-gray-500 dark:text-gray-400">
                        {searchTerm || filter !== 'all' 
                            ? 'Try adjusting your search or filters'
                            : 'Get started by creating your first page'
                        }
                    </p>
                </div>
            )}

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {filteredPages.map((page) => (
                    <PageCard
                        key={page.id}
                        page={page}
                        onUpdate={handlePageUpdate}
                        onDelete={handlePageDelete}
                    />
                ))}
            </div>

            {pages.length > 0 && filteredPages.length === 0 && (
                <div className="text-center py-8">
                    <p className="text-gray-500 dark:text-gray-400">
                        No pages match your current filters
                    </p>
                    <Button
                        variant="outline"
                        onClick={() => {
                            setSearchTerm('');
                            setFilter('all');
                        }}
                        className="mt-4"
                    >
                        Clear Filters
                    </Button>
                </div>
            )}
        </div>
    );
};

export default PageList;