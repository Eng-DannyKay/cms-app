import React from 'react';
import { Navigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import LoadingSpinner from './UI/LoadingSpinner';

const ProtectedRoute = ({ children, requireAuth = true, redirectTo = '/login' }) => {
    const { user, loading } = useAuth();

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center">
                <LoadingSpinner className="w-8 h-8" />
            </div>
        );
    }

    if (requireAuth && !user) {
        return <Navigate to={redirectTo} replace />;
    }

    if (!requireAuth && user) {
        return <Navigate to="/dashboard" replace />;
    }

    return children;
};

export default ProtectedRoute;
