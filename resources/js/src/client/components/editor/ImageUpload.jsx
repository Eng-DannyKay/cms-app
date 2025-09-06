import Button from '@/Components/UI/Button';
import LoadingSpinner from '@/Components/UI/LoadingSpinner';
import { pageApi } from '@/services/pageApi';
import { useRef, useState } from 'react';

const ImageUpload = ({ onImageUpload }) => {
    const [uploading, setUploading] = useState(false);
    const [dragOver, setDragOver] = useState(false);
    const fileInputRef = useRef(null);

    const handleFileUpload = async (file) => {
        if (!file) return;

        setUploading(true);
        try {
            const response = await pageApi.uploadImage(file);
            onImageUpload(response.data.url);
        } catch (error) {
            console.error('Image upload failed:', error);
            // TODO: Add error toast
        } finally {
            setUploading(false);
        }
    };

    const handleDrop = (e) => {
        e.preventDefault();
        setDragOver(false);

        const files = Array.from(e.dataTransfer.files);
        if (files.length > 0) {
            handleFileUpload(files[0]);
        }
    };

    const handleDragOver = (e) => {
        e.preventDefault();
        setDragOver(true);
    };

    const handleDragLeave = (e) => {
        e.preventDefault();
        setDragOver(false);
    };

    const handleFileSelect = (e) => {
        const file = e.target.files[0];
        if (file) {
            handleFileUpload(file);
        }
    };

    return (
        <div
            className={`border-2 border-dashed rounded-lg p-6 text-center transition-colors ${
                dragOver
                    ? 'border-primary bg-primary/10'
                    : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'
            }`}
            onDrop={handleDrop}
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
        >
            <input
                ref={fileInputRef}
                type="file"
                accept="image/*"
                onChange={handleFileSelect}
                className="hidden"
            />

            {uploading ? (
                <div className="flex flex-col items-center">
                    <LoadingSpinner className="w-8 h-8 mb-2" />
                    <p className="text-sm text-gray-600 dark:text-gray-400">Uploading...</p>
                </div>
            ) : (
                <>
                    <div className="text-4xl mb-2">📸</div>
                    <p className="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Drag & drop an image here, or click to browse
                    </p>
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => fileInputRef.current?.click()}
                    >
                        Select Image
                    </Button>
                </>
            )}
        </div>
    );
};

export default ImageUpload;
