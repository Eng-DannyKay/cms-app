export interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'client';
    client?: Client;
}

export interface Client {
    id: number;
    user_id: number;
    company_name: string;
    slug: string;
    website_url?: string;
    logo?: string;
    created_at: string;
    updated_at: string;
}

export interface LoginCredentials {
    email: string;
    password: string;
    remember?: boolean;
}

export interface RegisterData {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    company_name: string;
}
