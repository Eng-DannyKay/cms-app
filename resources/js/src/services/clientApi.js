import api from './api';

export const clientApi = {

  getProfile: () => api.get('/client/profile'),
  
  updateProfile: (data) => api.put('/client/profile', data),
  
  uploadLogo: (file) => {
    const formData = new FormData();
    formData.append('logo', file);
    return api.post('/client/upload-logo', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  
  deleteLogo: () => api.delete('/client/logo'),
  
  getStats: () => api.get('/client/stats'),
};

export default clientApi;