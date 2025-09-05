import Input from '../../../Components/UI/Input';

const SEOMetadata = ({ seo, onChange }) => {
    const updateSEOfield = (field, value) => {
        onChange({
            ...seo,
            [field]: value,
        });
    };

    return (
        <div className="space-y-4">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                SEO Settings
            </h3>

            <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Meta Title
                </label>
                <Input
                    value={seo?.title || ''}
                    onChange={(e) => updateSEOfield('title', e.target.value)}
                    placeholder="Page title for search engines"
                    maxLength={60}
                />
                <div className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {seo?.title?.length || 0}/60 characters
                </div>
            </div>

            <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Meta Description
                </label>
                <textarea
                    value={seo?.description || ''}
                    onChange={(e) => updateSEOfield('description', e.target.value)}
                    placeholder="Page description for search results"
                    rows={3}
                    className="input-primary"
                    maxLength={160}
                />
                <div className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {seo?.description?.length || 0}/160 characters
                </div>
            </div>

            <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Keywords
                </label>
                <Input
                    value={seo?.keywords || ''}
                    onChange={(e) => updateSEOfield('keywords', e.target.value)}
                    placeholder="Comma-separated keywords"
                />
            </div>

            <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Open Graph Image
                </label>
                <Input
                    type="url"
                    value={seo?.og_image || ''}
                    onChange={(e) => updateSEOfield('og_image', e.target.value)}
                    placeholder="URL for social media sharing image"
                />
            </div>

            <div className="flex items-center">
                <input
                    type="checkbox"
                    checked={seo?.no_index || false}
                    onChange={(e) => updateSEOfield('no_index', e.target.checked)}
                    className="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                />
                <label className="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                    Prevent search engines from indexing this page
                </label>
            </div>
        </div>
    );
};

export default SEOMetadata;
