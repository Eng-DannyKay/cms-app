import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from '@/contexts/AuthContext';
import ProtectedRoute from '@/components/ProtectedRoute';
import Login from '@/client/pages/auth/Login';
import Register from '@/client/pages/auth/Register';
import LoadingSpinner from '@/components/UI/LoadingSpinner';
import Dashboard from '@/client/pages/dashboard/Dashboard';
import Pages from '@/client/pages/dashboard/Pages';
import PageEditor from '@/client/pages/dashboard/PageEditor';
import Analytics from '@/client/pages/dashboard/PageEditor';


function AppContent() {
    const { user, loading } = useAuth();

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center">
                <LoadingSpinner className="w-8 h-8" />
            </div>
        );
    }

    return (
        <Routes>
            <Route
                path="/login"
                element={
                    <ProtectedRoute requireAuth={false}>
                        <Login />
                    </ProtectedRoute>
                }
            />
            <Route
                path="/register"
                element={
                    <ProtectedRoute requireAuth={false}>
                        <Register />
                    </ProtectedRoute>
                }
            />
            <Route
                path="/dashboard"
                element={
                    <ProtectedRoute>
                        <Dashboard />
                    </ProtectedRoute>
                }
            />
            <Route 
                path="/dashboard/pages" 
                element={
                    <ProtectedRoute>
                        <Pages />
                    </ProtectedRoute>
                } 
            />
            <Route 
                path="/dashboard/pages/edit/:id" 
                element={
                    <ProtectedRoute>
                        <PageEditor />
                    </ProtectedRoute>
                } 
            />
              <Route 
                path="/dashboard/pages/edit/:id" 
                element={
                    <ProtectedRoute>
                        <Analytics />
                    </ProtectedRoute>
                } 
            />
            <Route
                path="/"
                element={
                    <Navigate to={user ? '/dashboard' : '/login'} replace />
                }
            />
            <Route
                path="*"
                element={
                    <Navigate to={user ? '/dashboard' : '/login'} replace />
                }
            />
        </Routes>
    );
}

function App() {
    return (
        <AuthProvider>
            <Router>
                <AppContent />
            </Router>
        </AuthProvider>
    );
}

export default App;
