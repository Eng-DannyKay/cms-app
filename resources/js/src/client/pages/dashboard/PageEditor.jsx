import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import MainLayout from '../dashboard/components/MainLayout';
import TextEditor from '../../components/editor/TextEditor';
import ImageUpload from '../../components/editor/ImageUpload';
import PreviewModal from '../../components/editor/PreviewModal';
import SectionManager from '../../components/editor/SectionManager';
import SEOMetadata from '../../components/editor/SEOMetadata';
import { pageApi } from '../../../services/pageApi';
import { themeApi } from '@/services/themeApi';
import Button from '../../../Components/UI/Button';
import Input from '../../../Components/UI//Input';
import LoadingSpinner from '../../../Components/UI//LoadingSpinner';
import { pageSchema } from '../../../shared/validations/page';

const PageEditor = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [autoSaving, setAutoSaving] = useState(false);
    const [previewOpen, setPreviewOpen] = useState(false);
    const [themes, setThemes] = useState([]);
    const [page, setPage] = useState(null);
    const [activeSection, setActiveSection] = useState(0);

    const {
        register,
        handleSubmit,
        setValue,
        watch,
        formState: { errors },
    } = useForm({
        resolver: zodResolver(pageSchema),
        defaultValues: {
            title: '',
            slug: '',
            content: { sections: [] },
            seo: {
                title: '',
                description: '',
                keywords: '',
                og_image: '',
                no_index: false,
            },
            is_published: false,
            theme_id: null,
        },
    });

    const formData = watch();

    useEffect(() => {
        loadPageAndThemes();
    }, [id]);

    const loadPageAndThemes = async () => {
        try {
            setLoading(true);
            const [themesResponse] = await Promise.all([
                themeApi.getAvailableThemes(),
                id !== 'new' && loadPageData()
            ]);
            setThemes(themesResponse.data);
        } catch (error) {
            console.error('Failed to load data:', error);
            navigate('/dashboard/pages');
        } finally {
            setLoading(false);
        }
    };

    const loadPageData = async () => {
        try {
            const response = await pageApi.getPage(id);
            setPage(response.data);

            // Set form values
            setValue('title', response.data.title);
            setValue('slug', response.data.slug);
            setValue('content', response.data.content || { sections: [] });
            setValue('seo', response.data.content?.seo || {
                title: '',
                description: '',
                keywords: '',
                og_image: '',
                no_index: false,
            });
            setValue('is_published', response.data.is_published);
            setValue('theme_id', response.data.content?.theme_id || null);
        } catch (error) {
            throw error;
        }
    };

    const handleSave = async (publish = false) => {
        setSaving(true);
        try {
            const data = {
                title: formData.title,
                slug: formData.slug,
                content: {
                    sections: formData.content?.sections || [],
                    seo: formData.seo,
                    theme_id: formData.theme_id,
                },
                is_published: publish,
            };

            let response;
            if (page) {
                response = await pageApi.updatePage(page.id, data);
            } else {
                response = await pageApi.createPage(data);
            }

            if (!page) {
                navigate(`/dashboard/pages/edit/${response.data.id}`);
            } else {
                // TODO: Add success toast
            }
        } catch (error) {
            console.error('Failed to save page:', error);
            // TODO: Add error toast
        } finally {
            setSaving(false);
        }
    };

    const handleContentChange = (content, sectionIndex = activeSection) => {
        const newSections = [...formData.content.sections];
        newSections[sectionIndex] = content;
        setValue('content.sections', newSections);
    };

    const handleImageUpload = (imageUrl) => {
        const newSections = [...formData.content.sections];
        if (!newSections[activeSection]) {
            newSections[activeSection] = { type: 'image', content: imageUrl };
        } else {
            newSections[activeSection].content = imageUrl;
        }
        setValue('content.sections', newSections);
    };

    const handleAddSection = (sectionType) => {
        const newSection = {
            type: sectionType,
            content: '',
            ...(sectionType === 'hero' && {
                title: 'Welcome to Your Website',
                subtitle: 'This is a hero section',
                button_text: 'Get Started',
                button_link: '#',
            }),
            ...(sectionType === 'features' && {
                items: [
                    { title: 'Feature 1', description: 'Description here', icon: '🚀' },
                    { title: 'Feature 2', description: 'Description here', icon: '💡' },
                    { title: 'Feature 3', description: 'Description here', icon: '🔧' },
                ]
            }),
        };

        const newSections = [...formData.content.sections, newSection];
        setValue('content.sections', newSections);
        setActiveSection(newSections.length - 1);
    };

    const handleRemoveSection = (index) => {
        const newSections = formData.content.sections.filter((_, i) => i !== index);
        setValue('content.sections', newSections);
        if (activeSection >= newSections.length) {
            setActiveSection(Math.max(0, newSections.length - 1));
        }
    };

    const handleReorderSection = (fromIndex, toIndex) => {
        if (toIndex < 0 || toIndex >= formData.content.sections.length) return;

        const newSections = [...formData.content.sections];
        const [moved] = newSections.splice(fromIndex, 1);
        newSections.splice(toIndex, 0, moved);
        setValue('content.sections', newSections);
        setActiveSection(toIndex);
    };

    const handleSEOChange = (seoData) => {
        setValue('seo', seoData);
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
                    </div>
                </div>

                {/* Theme Selection */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Theme
                    </label>
                    <select
                        {...register('theme_id')}
                        className="input-primary"
                    >
                        <option value="">Default Theme</option>
                        {themes.map((theme) => (
                            <option key={theme.id} value={theme.id}>
                                {theme.name}
                            </option>
                        ))}
                    </select>
                </div>

                {/* Section Manager */}
                <SectionManager
                    sections={formData.content?.sections || []}
                    onAddSection={handleAddSection}
                    onRemoveSection={handleRemoveSection}
                    onReorderSection={handleReorderSection}
                />

                {/* Content Editor for Active Section */}
                {formData.content?.sections?.length > 0 && (
                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Edit Section: {formData.content.sections[activeSection]?.type}
                        </label>
                        <TextEditor
                            content={formData.content.sections[activeSection]}
                            onChange={(content) => handleContentChange(content, activeSection)}
                            placeholder="Start editing your section content..."
                        />
                    </div>
                )}

                {/* Image Upload */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Add Images
                    </label>
                    <ImageUpload onImageUpload={handleImageUpload} />
                </div>

                {/* SEO Metadata */}
                <div className="card p-6">
                    <SEOMetadata
                        seo={formData.seo}
                        onChange={handleSEOChange}
                    />
                </div>

                {/* Publish Toggle */}
                <div className="flex items-center">
                    <input
                        type="checkbox"
                        {...register('is_published')}
                        className="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                    />
                    <label className="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        Publish this page
                    </label>
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
