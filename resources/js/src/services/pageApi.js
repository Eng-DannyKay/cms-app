import api from './api';
import { handleError } from '../shared/utills/helper';

class PageApiService {
    async getPages(params = {}) {
        try {
            const response = await api.get('/pages', { params });
            return response.data;
        } catch (error) {
            handleError(error, 'Failed to fetch pages');
        }
    }

    async getPage(id) {
        try {
            const response = await api.get(`/pages/${id}`);
            return response.data;
        } catch (error) {
            handleError(error, 'Failed to fetch page');
        }
    }

    async createPage(pageData) {
        try {
            const response = await api.post('/pages', pageData);
            return response.data;
        } catch (error) {
            handleError(error, 'Failed to create page');
        }
    }

    async updatePage(id, pageData) {
        try {
            const response = await api.put(`/pages/${id}`, pageData);
            return response.data;
        } catch (error) {
            handleError(error, 'Failed to update page');
        }
    }

    async deletePage(id) {
        try {
            const response = await api.delete(`/pages/${id}`);
            return response.data;
        } catch (error) {
            handleError(error, 'Failed to delete page');
        }
    }

    async publishPage(id) {
        try {
            const response = await api.post(`/pages/${id}/publish`);
            return response.data;
        } catch (error) {
         handleError(error, 'Failed to publish page');
        }
    }

    // Duplicate page
    async duplicatePage(id) {
        try {
            const response = await api.post(`/pages/${id}/duplicate`);
            return response.data;
        } catch (error) {
            handleError(error, 'Failed to duplicate page');
        }
    }


    async getPagePreview(id) {
        try {
            const response = await api.get(`/pages/${id}/preview`);
            return response.data;
        } catch (error) {
            handleError(error, 'Failed to get page preview');
        }
    }

async uploadImage(file) {
        try {
            const formData = new FormData();
            formData.append('image', file);
            
            const response = await api.post('/pages/upload-image', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            return response.data;
        } catch (error) {
            handleError(error, 'Failed to upload image');
        }
    }

   
}

export const pageApi = new PageApiService();