import { handleError } from "../shared/utills/helper";

class ThemeApiService {
    async customizeTheme(themeId, customizations) {
        try {
            const response = await api.post(`/themes/customize`, {
                theme_id: themeId,
                customizations,
            });
            return response.data;
        } catch (error) {
            handleError(error, "Failed to customize theme");
        }
    }

    async applyTheme(themeId, customizations = null) {
        try {
            const response = await api.post(`/themes/apply`, {
                theme_id: themeId,
                customizations,
            });
            return response.data;
        } catch (error) {
            handleError(error, "Failed to apply theme");
        }
    }

    async resetCustomizations() {
        try {
            const response = await api.post(`/themes/reset`);
            return response.data;
        } catch (error) {
            handleError(error, "Failed to reset customizations");
        }
    }

    async getAvailableThemes() {
        try {
            const response = await apiClient.get("/api/themes");
            return response.data;
        } catch (error) {
            handleError(error, "Failed to fetch themes");
            return { data: [] };
        }
    }
}

export const themeApi = new ThemeApiService();
