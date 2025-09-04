// Add to src/services/themeApi.js
async customizeTheme(themeId, customizations) {
    try {
        const response = await api.post(`/themes/customize`, {
            theme_id: themeId,
            customizations
        });
        return response.data;
    } catch (error) {
        this.handleError(error, 'Failed to customize theme');
    }
}

async applyTheme(themeId, customizations = null) {
    try {
        const response = await api.post(`/themes/apply`, {
            theme_id: themeId,
            customizations
        });
        return response.data;
    } catch (error) {
        this.handleError(error, 'Failed to apply theme');
    }
}

async resetCustomizations() {
    try {
        const response = await api.post(`/themes/reset`);
        return response.data;
    } catch (error) {
        this.handleError(error, 'Failed to reset customizations');
    }
}
