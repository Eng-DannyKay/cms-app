import { useState, useEffect, useCallback } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { pageApi } from '../../../../services/pageApi';
import { themeApi } from '../../../../services/themeApi';
import { pageSchema } from '../../../../shared/validations/page';

const DEFAULT_FORM_VALUES = {
  title: '',
  slug: '',
  content: { sections: [] },
  seo: {
    title: '',
    description: '',
    keywords: '',
    og_image: '',
    no_index: false
  },
  is_published: false,
  theme_id: null,
};

const SECTION_TEMPLATES = {
  hero: {
    type: 'hero',
    title: 'Welcome to Your Website',
    subtitle: 'This is a hero section',
    button_text: 'Get Started',
    button_link: '#',
  },
  features: {
    type: 'features',
    items: [
      { title: 'Feature 1', description: 'Description here', icon: '🚀' },
      { title: 'Feature 2', description: 'Description here', icon: '💡' },
      { title: 'Feature 3', description: 'Description here', icon: '🔧' },
    ],
  },
  default: { type: 'text', content: '' }
};

export const usePageEditor = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const isNewPage = id === 'new';

  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [previewOpen, setPreviewOpen] = useState(false);
  const [themes, setThemes] = useState([]);
  const [page, setPage] = useState(null);
  const [activeSection, setActiveSection] = useState(0);

  const {
    register,
    setValue,
    watch,
    reset,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(pageSchema),
    defaultValues: DEFAULT_FORM_VALUES,
  });

  const formData = watch();
  const loadPageData = useCallback(async () => {
    try {
      const response = await pageApi.getPage(id);
      setPage(response);

      const formValues = {
        title: response.title || '',
        slug: response.slug || '',
        content: {
          sections: response.content?.sections || []
        },
        seo: response.content?.seo || DEFAULT_FORM_VALUES.seo,
        is_published: response.is_published || false,
        theme_id: response.content?.theme_id || null,
      };
      reset(formValues);

      return response;
    } catch (error) {
      console.error('Failed to load page data:', error);
      throw error;
    }
  }, [id, reset]);

  const loadPageAndThemes = useCallback(async () => {
    try {
      setLoading(true);

      const themesResponse = await themeApi.getAvailableThemes().catch(error => {
        console.error('Failed to load themes:', error);
        return { data: [] };
      });

      setThemes(themesResponse.data || []);
      if (!isNewPage) {
        await loadPageData();
      }
    } catch (error) {
      console.error('Failed to load data:', error);
      navigate('/dashboard/pages');
    } finally {
      setLoading(false);
    }
  }, [isNewPage, loadPageData, navigate]);

  const handleSave = useCallback(async (publish = false) => {
    setSaving(true);

    try {
      const payload = {
        title: formData.title,
        slug: formData.slug,
        content: {
          sections: formData.content.sections,
          seo: formData.seo,
          theme_id: formData.theme_id,
        },
        is_published: publish,
      };

      if (page) {
        await pageApi.updatePage(page.id, payload);
      } else {
        const created = await pageApi.createPage(payload);
        navigate(`/dashboard/pages/edit/${created.id}`);
      }
    } catch (error) {
      console.error('Save failed:', error);
      // TODO: Add proper error handling/toast notification
    } finally {
      setSaving(false);
    }
  }, [formData, page, navigate]);

  const handleContentChange = useCallback((content, sectionIndex = activeSection) => {
    const newSections = [...formData.content.sections];
    newSections[sectionIndex] = content;
    setValue('content.sections', newSections);
  }, [formData.content.sections, activeSection, setValue]);

  const handleAddSection = useCallback((type) => {
    const template = SECTION_TEMPLATES[type] || SECTION_TEMPLATES.default;
    const newSection = { ...template, type };

    const newSections = [...formData.content.sections, newSection];
    setValue('content.sections', newSections);
    setActiveSection(newSections.length - 1);
  }, [formData.content.sections, setValue]);

  const handleRemoveSection = useCallback((index) => {
    const newSections = formData.content.sections.filter((_, i) => i !== index);
    setValue('content.sections', newSections);
    setActiveSection(Math.max(0, activeSection - 1));
  }, [formData.content.sections, setValue, activeSection]);

  const handleReorderSection = useCallback((from, to) => {
    const sections = [...formData.content.sections];
    const [moved] = sections.splice(from, 1);
    sections.splice(to, 0, moved);
    setValue('content.sections', sections);
    setActiveSection(to);
  }, [formData.content.sections, setValue]);

  const updateSEO = useCallback((seo) => {
    setValue('seo', seo);
  }, [setValue]);

  useEffect(() => {
    loadPageAndThemes();
  }, [loadPageAndThemes]);

  return {
    loading,
    saving,
    previewOpen,
    setPreviewOpen,
    themes,
    page,
    activeSection,
    setActiveSection,
    isNewPage,
    formData,
    errors,
    register,
    handleSave,
    handleContentChange,
    handleAddSection,
    handleRemoveSection,
    handleReorderSection,
    updateSEO
  };
};

export { DEFAULT_FORM_VALUES, SECTION_TEMPLATES };
