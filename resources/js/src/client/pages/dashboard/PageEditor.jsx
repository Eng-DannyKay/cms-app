import React, { useCallback } from 'react';

// Components
import MainLayout from '../dashboard/components/MainLayout';
import SectionManager from '../../components/editor/SectionManager';
import SEOMetadata from '../../components/editor/SEOMetadata';
import PreviewModal from '../../components/editor/PreviewModal';
import TextEditor from '../../components/editor/TextEditor';
import ImageUpload from '../../components/editor/ImageUpload';
import HeroSectionEditor from '../../components/editor/HeroSectionEditor';
import FeaturesSectionEditor from '../../components/editor/FeaturesSectionEditor';

// Hook
import { usePageEditor } from './hooks/usePageEditor';

import Button from '../../../Components/UI/Button';
import Input from '../../../Components/UI/Input';
import LoadingSpinner from '../../../Components/UI/LoadingSpinner';

const PageEditor = () => {
  const {
    loading,
    saving,
    previewOpen,
    setPreviewOpen,
    themes,
    page,
    activeSection,
    setActiveSection,
    formData,
    errors,
    register,
    handleSave,
    handleContentChange,
    handleAddSection,
    handleRemoveSection,
    handleReorderSection,
    updateSEO
  } = usePageEditor();

  const renderSectionEditor = useCallback((section, idx) => {
    const editorProps = {
      section,
      onChange: (content) => handleContentChange(content, idx)
    };

    switch (section.type) {
      case 'hero':
        return <HeroSectionEditor {...editorProps} />;
      case 'features':
        return <FeaturesSectionEditor {...editorProps} />;
      case 'image':
        return (
          <ImageUpload
            onImageUpload={(url) => handleContentChange({ ...section, content: url }, idx)}
          />
        );
      default:
        return <TextEditor content={section} {...editorProps} />;
    }
  }, [handleContentChange]);

  if (loading) {
    return (
      <MainLayout>
        <div className="flex items-center justify-center h-64">
          <LoadingSpinner className="w-8 h-8" />
        </div>
      </MainLayout>
    );
  }

  const currentSection = formData.content.sections[activeSection];
  const pageTitle = page ? 'Edit Page' : 'Create Page';
  const publishButtonText = page?.is_published ? 'Update' : 'Publish';

  return (
    <MainLayout>
      <div className="space-y-6">
        <div className="flex flex-col sm:flex-row justify-between items-center">
          <h1 className="text-2xl font-bold">{pageTitle}</h1>
          <div className="space-x-2">
            <Button
              variant="outline"
              onClick={() => setPreviewOpen(true)}
            >
              Preview
            </Button>
            <Button
              variant="secondary"
              onClick={() => handleSave(false)}
              loading={saving}
            >
              Save Draft
            </Button>
            <Button
              variant="primary"
              onClick={() => handleSave(true)}
              loading={saving}
            >
              {publishButtonText}
            </Button>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input
            {...register('title')}
            placeholder="Title"
            error={errors.title?.message}
          />
          <Input
            {...register('slug')}
            placeholder="Slug"
            error={errors.slug?.message}
          />
        </div>

        <select {...register('theme_id')} className="input-primary">
          <option value="">Default Theme</option>
          {themes.map((theme) => (
            <option key={theme.id} value={theme.id}>
              {theme.name}
            </option>
          ))}
        </select>

        <SectionManager
          sections={formData.content.sections}
          onAddSection={handleAddSection}
          onRemoveSection={handleRemoveSection}
          onReorderSection={handleReorderSection}
          active={activeSection}
          setActive={setActiveSection}
        />

        {currentSection && (
          <div className="border p-4 rounded">
            {renderSectionEditor(currentSection, activeSection)}
          </div>
        )}

        <SEOMetadata
          seo={formData.seo}
          onChange={updateSEO}
        />

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
