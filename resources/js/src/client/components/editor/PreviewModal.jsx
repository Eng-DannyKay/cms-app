import React from 'react';

const PreviewModal = ({ isOpen, onClose, content, title }) => {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div className="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-4xl mx-auto max-h-[90vh] overflow-hidden">
                <div className="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 className="text-xl font-semibold text-gray-900 dark:text-white">
                        Preview: {title}
                    </h2>
                    <button
                        onClick={onClose}
                        className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                    >
                        ✕
                    </button>
                </div>

                <div className="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div className="prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto">
                        {content?.sections?.map((section, index) => (
                            <div key={index} className="mb-6">
                                {section.type === 'heading' && (
                                    <h1>{section.content}</h1>
                                )}
                                {section.type === 'paragraph' && (
                                    <p>{section.content}</p>
                                )}
                                {section.type === 'image' && (
                                    <img 
                                        src={section.content} 
                                        alt="" 
                                        className="rounded-lg max-w-full h-auto"
                                    />
                                )}
                            </div>
                        ))}
                    </div>
                </div>

                <div className="flex justify-end p-6 border-t border-gray-200 dark:border-gray-700">
                    <Button onClick={onClose}>
                        Close Preview
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default PreviewModal;