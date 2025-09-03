import { createContext, useContext, useState, useEffect, useMemo, useCallback } from 'react';
import axios from 'axios';

const AuthContext = createContext();

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    const checkAuth = useCallback(async () => {
        try {
            const response = await axios.get('/api/auth/user');
            setUser(response.data.user);
        } catch (error) {
            setUser(null);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        checkAuth();
    }, [checkAuth]);

    const login = useCallback(async (credentials) => {
        try {
            const response = await axios.post('/api/auth/login', credentials);
            setUser(response.data.user);
            return response.data;
        } catch (error) {
            throw error.response.data;
        }
    }, []);

    const register = useCallback(async (userData) => {
        try {
            const response = await axios.post('/api/auth/register', userData);
            setUser(response.data.user);
            return response.data;
        } catch (error) {
            throw error.response.data;
        }
    }, []);

    const logout = useCallback(async () => {
        try {
            await axios.post('/api/auth/logout');
            setUser(null);
        } catch (error) {
            console.error('Logout error:', error);
        }
    }, []);

    const value = {
        user,
        loading,
        login,
        register,
        logout,
        isAuthenticated: !!user,
        isAdmin: user?.role === 'admin',
        isClient: user?.role === 'client',
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
};
