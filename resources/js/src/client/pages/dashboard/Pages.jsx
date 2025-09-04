import React, { useState } from 'react';
import MainLayout from '../dashboard/components/MainLayout';
import PageList from '@/client/components/pages/PageList';
import PageModal from '@/client/components/pages/PageModal';
import { pageApi } from '@/services/pageApi';
import Button from '@/components/UI/Button';

const Pages = () => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingPage, setEditingPage] = useState(null);
    const [actionLoading, setActionLoading] = useState(false);

    const handleCreatePage = () => {
        setEditingPage(null);
        setIsModalOpen(true);
    };

    const handleEditPage = (page) => {
        setEditingPage(page);
        setIsModalOpen(true);
    };

    const handleSubmitPage = async (data) => {
        setActionLoading(true);
        try {
            if (editingPage) {
                await pageApi.updatePage(editingPage.id, data);
                // TODO: Add success toast
            } else {
                await pageApi.createPage(data);
                // TODO: Add success toast
            }
            setIsModalOpen(false);
            setEditingPage(null);
        } catch (error) {
            console.error('Failed to save page:', error);
            // TODO: Add error toast
        } finally {
            setActionLoading(false);
        }
    };

    return (
        <MainLayout>
            <div className="space-y-6">
                <PageList
                    onEditPage={handleEditPage}
                    onCreatePage={handleCreatePage}
                />

                <PageModal
                    isOpen={isModalOpen}
                    onClose={() => {
                        setIsModalOpen(false);
                        setEditingPage(null);
                    }}
                    onSubmit={handleSubmitPage}
                    initialData={editingPage}
                    loading={actionLoading}
                />
            </div>
        </MainLayout>
    );
};

export default Pages;