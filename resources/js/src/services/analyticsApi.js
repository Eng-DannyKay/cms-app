import api from './api';
import { handleError } from '../shared/utills/helper';

class AnalyticsApiService {
  /**
   * Client-wide summary analytics
   */
  async getClientAnalytics(period = '30d') {
    try {
      const response = await api.get('/pages/analytics/summary', {
        params: { period },
      });
      return response.data;
    } catch (error) {
      handleError(error, 'Failed to fetch client analytics summary');
    }
  }

  /**
   * Top pages for the client
   */
  async getTopPages(period = '30d', limit = 10) {
    try {
      const response = await api.get('/pages/analytics/top-pages', {
        params: { period, limit },
      });
      return response.data;
    } catch (error) {
      handleError(error, 'Failed to fetch top pages');
    }
  }

  /**
   * Single page analytics summary
   */
  async getPageAnalytics(pageId, period = '30d') {
    try {
      const response = await api.get(`/pages/analytics/${pageId}`, {
        params: { period },
      });
      return response.data;
    } catch (error) {
      handleError(error, 'Failed to fetch page analytics');
    }
  }

  /**
   * Timeline (visitors per day/hour)
   */
  async getVisitorTimeline(pageId, period = '7d') {
    try {
      const response = await api.get(`/pages/analytics/${pageId}/timeline`, {
        params: { period },
      });
      return response.data;
    } catch (error) {
      handleError(error, 'Failed to fetch visitor timeline');
    }
  }

  /**
   * Geography data (countries, cities)
   */
  async getGeography(pageId, period = '30d') {
    try {
      const response = await api.get(`/pages/analytics/${pageId}/geography`, {
        params: { period },
      });
      return response.data;
    } catch (error) {
      handleError(error, 'Failed to fetch geography analytics');
    }
  }

  /**
   * Device, browser, platform analytics
   */
  async getDevices(pageId, period = '30d') {
    try {
      const response = await api.get(`/pages/analytics/${pageId}/devices`, {
        params: { period },
      });
      return response.data;
    } catch (error) {
      handleError(error, 'Failed to fetch device analytics');
    }
  }

  /**
   * Export analytics (CSV/XLSX)
   */
  async exportPageAnalytics(pageId, period = '30d', format = 'csv') {
    try {
      const response = await api.get(`/pages/analytics/${pageId}/export`, {
        params: { period, format },
        responseType: 'blob',
      });

      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `page-${pageId}-analytics.${format}`);
      document.body.appendChild(link);
      link.click();
      link.remove();

      return response;
    } catch (error) {
      handleError(error, 'Failed to export page analytics');
    }
  }
}

export const analyticsApi = new AnalyticsApiService();
