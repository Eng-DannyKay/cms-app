import api from './api';
import {handleError} from '../shared/utills/helper'

class AnalyticsApiService {
    // Get page analytics
    async getPageAnalytics(pageId, period = '30d') {
        try {
            const response = await api.get(`/analytics/pages/${pageId}?period=${period}`);
            return response.data;
        } catch (error) {
            handleError(error, 'Failed to fetch page analytics');
        }
    }

    async getClientAnalytics(period = '30d') {
        try {
            const response = await api.get(`/analytics/summary?period=${period}`);
            return response.data;
        } catch (error) {
            handleError(error, 'Failed to fetch analytics summary');
        }
    }

    async getVisitorTimeline(pageId, period = '7d') {
        try {
            const response = await api.get(`/analytics/pages/${pageId}/timeline?period=${period}`);
            return response.data;
        } catch (error) {
            this.handleError(error, 'Failed to fetch visitor timeline');
        }
    }


    async getGeographicData(pageId, period = '30d') {
        try {
            const response = await api.get(`/analytics/pages/${pageId}/geography?period=${period}`);
            return response.data;
        } catch (error) {
            this.handleError(error, 'Failed to fetch geographic data');
        }
    }

    async getDeviceAnalytics(pageId, period = '30d') {
        try {
            const response = await api.get(`/analytics/pages/${pageId}/devices?period=${period}`);
            return response.data;
        } catch (error) {
            this.handleError(error, 'Failed to fetch device data');
        }
    }

    async exportAnalytics(pageId, format = 'csv', period = '30d') {
        try {
            const response = await api.get(`/analytics/pages/${pageId}/export?format=${format}&period=${period}`, {
                responseType: 'blob'
            });
            return response.data;
        } catch (error) {
            this.handleError(error, 'Failed to export analytics');
        }
    }


}

export const analyticsApi = new AnalyticsApiService();
