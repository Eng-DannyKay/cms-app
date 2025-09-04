// src/shared/config/navigation.jsx
import {
  LayoutDashboard,
  FileText,
  BarChart3,
  Palette,
  Settings,
} from "lucide-react";

export const navigation = [
  {
    name: "Dashboard",
    href: "/dashboard",
    icon: <LayoutDashboard className="w-5 h-5" />, 
    current: true,
  },
  {
    name: "Pages",
    href: "/dashboard/pages",
    icon: <FileText className="w-5 h-5" />,
    current: false,
  },
  {
    name: "Analytics",
    href: "/dashboard/analytics",
    icon: <BarChart3 className="w-5 h-5" />,
    current: false,
  },
  {
    name: "Themes",
    href: "/dashboard/themes",
    icon: <Palette className="w-5 h-5" />,
    current: false,
  },
  {
    name: "Settings",
    href: "/dashboard/settings",
    icon: <Settings className="w-5 h-5" />,
    current: false,
  },
];

export const userNavigation = [
  { name: "Your Profile", href: "/dashboard/profile" },
  { name: "Settings", href: "/dashboard/settings" },
  { name: "Sign out", href: "/logout" },
];
