import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import MainLayout from "./components/MainLayout";
import { clientApi } from "../../../services/clientApi";
import LoadingSpinner from "../../../Components/UI/LoadingSpinner";

// ✅ Lucide icons
import {
  BarChart3,
  Users,
  FileText,
  Star,
  LayoutDashboard,
  Palette,
} from "lucide-react";

const Dashboard = () => {
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const fetchDashboardData = async () => {
    try {
      setLoading(true);
      const [statsResponse] = await Promise.all([clientApi.getStats()]);
      setStats(statsResponse.data.data);
    } catch (err) {
      setError("Failed to load dashboard data");
      console.error("Dashboard error:", err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <MainLayout>
        <div className="flex items-center justify-center h-64">
          <LoadingSpinner className="w-8 h-8" />
        </div>
      </MainLayout>
    );
  }

  if (error) {
    return (
      <MainLayout>
        <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl">
          {error}
        </div>
      </MainLayout>
    );
  }

  return (
    <MainLayout>
      <div className="space-y-6">
        {/* Welcome header */}
        <div className="bg-gradient-to-br from-primary to-secondary rounded-2xl p-6 text-white">
          <h1 className="text-2xl font-bold">Welcome back!</h1>
          <p className="text-primary-100 mt-2">
            Here's what's happening with your CMS today.
          </p>
        </div>

        {/* Stats grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div className="card p-6">
            <div className="flex items-center">
              <div className="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-xl">
                <BarChart3 className="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                  Total Views
                </p>
                <p className="text-2xl font-bold text-gray-900 dark:text-white">
                  {stats?.total_views?.toLocaleString() || "0"}
                </p>
              </div>
            </div>
          </div>

          <div className="card p-6">
            <div className="flex items-center">
              <div className="p-3 bg-green-100 dark:bg-green-900/20 rounded-xl">
                <Users className="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                  Visitors
                </p>
                <p className="text-2xl font-bold text-gray-900 dark:text-white">
                  {stats?.total_views ? Math.round(stats.total_views * 0.4) : "0"}
                </p>
              </div>
            </div>
          </div>

          <div className="card p-6">
            <div className="flex items-center">
              <div className="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-xl">
                <FileText className="w-6 h-6 text-purple-600 dark:text-purple-400" />
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                  Pages
                </p>
                <p className="text-2xl font-bold text-gray-900 dark:text-white">
                  {stats?.total_pages || "0"}
                </p>
              </div>
            </div>
          </div>

          <div className="card p-6">
            <div className="flex items-center">
              <div className="p-3 bg-pink-100 dark:bg-pink-900/20 rounded-xl">
                <Star className="w-6 h-6 text-pink-600 dark:text-pink-400" />
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                  Published
                </p>
                <p className="text-2xl font-bold text-gray-900 dark:text-white">
                  {stats?.published_pages || "0"}
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* Quick Actions & Recent Activity */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="card p-6">
            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Quick Actions
            </h2>
            <div className="space-y-3">
              <Link
                to="/dashboard/pages"
                className="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
              >
                <FileText className="w-5 h-5 mr-3" />
                <span>Manage Pages</span>
              </Link>
              <Link
                to="/dashboard/analytics"
                className="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
              >
                <BarChart3 className="w-5 h-5 mr-3" />
                <span>View Analytics</span>
              </Link>
              <Link
                to="/dashboard/themes"
                className="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
              >
                <Palette className="w-5 h-5 mr-3" />
                <span>Customize Theme</span>
              </Link>
            </div>
          </div>

          <div className="card p-6">
            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Recent Activity
            </h2>
            <div className="space-y-3">
              <div className="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <span className="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                Dashboard loaded successfully
                <span className="ml-auto text-xs">Just now</span>
              </div>
              <div className="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <span className="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                Connected to backend API
                <span className="ml-auto text-xs">Just now</span>
              </div>
              <div className="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <span className="w-2 h-2 bg-purple-500 rounded-full mr-3"></span>
                Ready to build your pages!
                <span className="ml-auto text-xs">Just now</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </MainLayout>
  );
};

export default Dashboard;
