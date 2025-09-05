import React, { Suspense, lazy } from "react";
import {
    BrowserRouter as Router,
    Routes,
    Route,
    Navigate,
} from "react-router-dom";
import { AuthProvider, useAuth } from "@/contexts/AuthContext";
import ProtectedRoute from "@/Components/ProtectedRoute";
import LoadingSpinner from "@/Components/UI/LoadingSpinner";

const Login = lazy(() => import("@/client/pages/auth/Login"));
const Register = lazy(() => import("@/client/pages/auth/Register"));
const Dashboard = lazy(() => import("@/client/pages/dashboard/Dashboard"));
const Pages = lazy(() => import("@/client/pages/dashboard/Pages"));
const PageEditor = lazy(() => import("@/client/pages/dashboard/PageEditor"));
const Analytics = lazy(() => import("@/client/pages/dashboard/Analytics")); // ✅ fixed import

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
        <Suspense
            fallback={
                <div className="min-h-screen flex items-center justify-center">
                    <LoadingSpinner className="w-8 h-8" />
                </div>
            }
        >
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
                    path="/dashboard/analytics"
                    element={
                        <ProtectedRoute>
                            <Analytics />
                        </ProtectedRoute>
                    }
                />
                <Route
                    path="/"
                    element={
                        <Navigate to={user ? "/dashboard" : "/login"} replace />
                    }
                />
                <Route
                    path="*"
                    element={
                        <Navigate to={user ? "/dashboard" : "/login"} replace />
                    }
                />
            </Routes>
        </Suspense>
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
