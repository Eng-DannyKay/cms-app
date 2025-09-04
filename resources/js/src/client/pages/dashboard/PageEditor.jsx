import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import MainLayout from '../dashboard/components/MainLayout';
import TextEditor from '@/client/components/editor/TextEditor';
import ImageUpload from '@/client/components/editor/ImageUpload';
import PreviewModal from '@/client/components/editor/PreviewModal';
import { pageApi } from '@/services/pageApi';
import Button from '@/components/UI/Button';
import Input from '@/components/UI/Input';
import LoadingSpinner from '@/components/UI/LoadingSpinner';

const PageEditor = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [previewOpen, setPreviewOpen] = useState(false);
    const [page, setPage] = useState(null);
    const [formData, setFormData] = useState({
        title: '',
        slug: '',
        content: { sections: [] },
        is_published: false,
    });

    useEffect(() => {
        loadPage();
    }, [id]);

    const loadPage = async () => {
        try {
            setLoading(true);
            if (id === 'new') {
                setPage(null);
                setFormData({
                    title: '',
                    slug: '',
                    content: { sections: [] },
                    is_published: false,
                });
            } else {
                const response = await pageApi.getPage(id);
                setPage(response.data);
                setFormData(response.data);
            }
        } catch (error) {
            console.error('Failed to load page:', error);
            navigate('/dashboard/pages');
        } finally {
            setLoading(false);
        }
    };

    const handleSave = async (publish = false) => {
        setSaving(true);
        try {
            const data = { ...formData, is_published: publish };
            
            let response;
            if (page) {
                response = await pageApi.updatePage(page.id, data);
            } else {
                response = await pageApi.createPage(data);
            }

            // TODO: Add success toast
            if (!page) {
                navigate(`/dashboard/pages/edit/${response.data.id}`);
            }
        } catch (error) {
            console.error('Failed to save page:', error);
            // TODO: Add error toast
        } finally {
            setSaving(false);
        }
    };

    const handleContentChange = (content) => {
        setFormData(prev => ({
            ...prev,
            content: { sections: content ? [content] : [] },
        }));
    };

    const handleImageUpload = (imageUrl) => {
        // Add image to content
        setFormData(prev => ({
            ...prev,
            content: {
                sections: [
                    ...(prev.content?.sections || []),
                    { type: 'image', content: imageUrl }
                ]
            },
        }));
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
                            {page ? 'Edit Page' : 'Create New Page'}
                        </h1>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {page ? 'Update your page content' : 'Create a new page for your website'}
                        </p>
                    </div>

                    <div className="flex space-x-3">
                        <Button
                            variant="outline"
                            onClick={() => setPreviewOpen(true)}
                            disabled={saving}
                        >
                            Preview
                        </Button>
                        <Button
                            variant="secondary"
                            onClick={() => handleSave(false)}
                            loading={saving}
                            disabled={saving}
                        >
                            Save Draft
                        </Button>
                        <Button
                            variant="primary"
                            onClick={() => handleSave(true)}
                            loading={saving}
                            disabled={saving}
                        >
                            {page?.is_published ? 'Update' : 'Publish'}
                        </Button>
                    </div>
                </div>

                {/* Basic Info */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Page Title *
                        </label>
                        <Input
                            value={formData.title}
                            onChange={(e) => setFormData(prev => ({ ...prev, title: e.target.value }))}
                            placeholder="Enter page title"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            URL Slug *
                        </label>
                        <Input
                            value={formData.slug}
                            onChange={(e) => setFormData(prev => ({ ...prev, slug: e.target.value }))}
                            placeholder="page-slug"
                        />
                    </div>
                </div>

                {/* Content Editor */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Page Content
                    </label>
                    <TextEditor
                        content={formData.content?.sections?.[0]}
                        onChange={handleContentChange}
                        placeholder="Start writing your page content..."
                    />
                </div>

                {/* Image Upload */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Add Images
                    </label>
                    <ImageUpload onImageUpload={handleImageUpload} />
                </div>

                {/* Preview Modal */}
                <PreviewModal
                    isOpen={previewOpen}
                    onClose={() => setPreviewOpen(false)}
                    content={formData.content}
                    title={formData.title}
                />
            </div>
        </MainLayout>
    );
};

export default PageEditor;