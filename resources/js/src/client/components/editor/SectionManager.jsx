import { Star, FileText, Grid, MessageSquare, Phone, Image, ArrowUp, ArrowDown, X } from 'lucide-react';
import React from 'react';

const SectionManager = ({ sections, onAddSection, onRemoveSection, onReorderSection }) => {
    const sectionTypes = [
        { type: 'hero', label: 'Hero Section', icon: <Star className="w-5 h-5" />, description: 'Full-width banner with call-to-action' },
        { type: 'content', label: 'Content Block', icon: <FileText className="w-5 h-5" />, description: 'Rich text content with images' },
        { type: 'features', label: 'Features Grid', icon: <Grid className="w-5 h-5" />, description: 'Grid of features or services' },
        { type: 'testimonials', label: 'Testimonials', icon: <MessageSquare className="w-5 h-5" />, description: 'Customer testimonials carousel' },
        { type: 'contact', label: 'Contact Form', icon: <Phone className="w-5 h-5" />, description: 'Contact information and form' },
        { type: 'gallery', label: 'Image Gallery', icon: <Image className="w-5 h-5" />, description: 'Grid of images or portfolio' },
    ];

    return (
        <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Page Sections
            </h3>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                {sectionTypes.map((section) => (
                    <button
                        key={section.type}
                        onClick={() => onAddSection(section.type)}
                        className="flex items-start p-3 text-left rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary transition-colors"
                    >
                        <span className="text-2xl mr-3">{section.icon}</span>
                        <div>
                            <div className="font-medium text-gray-900 dark:text-white">
                                {section.label}
                            </div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">
                                {section.description}
                            </div>
                        </div>
                    </button>
                ))}
            </div>

            {sections.length > 0 && (
                <div className="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 className="font-medium text-gray-900 dark:text-white mb-2">
                        Current Sections
                    </h4>
                    <div className="space-y-2">
                        {sections.map((section, index) => (
                            <div
                                key={index}
                                className="flex items-center justify-between p-2 bg-white dark:bg-gray-700 rounded border"
                            >
                                <div className="flex items-center">
                                    <span className="text-lg mr-2">
                                        {sectionTypes.find(s => s.type === section.type)?.icon || '📄'}
                                    </span>
                                    <span className="text-sm">
                                        {sectionTypes.find(s => s.type === section.type)?.label || section.type}
                                    </span>
                                </div>
                                <div className="flex space-x-1">
                                    <button
                                        onClick={() => onReorderSection(index, index - 1)}
                                        disabled={index === 0}
                                        className="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-50"
                                    >
                                        ↑
                                    </button>
                                    <button
                                        onClick={() => onReorderSection(index, index + 1)}
                                        disabled={index === sections.length - 1}
                                        className="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-50"
                                    >
                                        ↓
                                    </button>
                                    <button
                                        onClick={() => onRemoveSection(index)}
                                        className="p-1 text-red-400 hover:text-red-600"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
};

export default SectionManager;
