import { z } from 'zod';

export const loginSchema = z.object({
    email: z.string().email('Invalid email address'),
    password: z.string().min(1, 'Password is required'),
});

export const registerSchema = z.object({
    name: z.string().min(2, 'Name must be at least 2 characters'),
    email: z.string().email('Invalid email address'),
    password: z.string().min(8, 'Password must be at least 8 characters'),
    password_confirmation: z.string(),
    company_name: z.string().min(2, 'Company name is required'),
}).refine((data) => data.password === data.password_confirmation, {
    message: "Passwords don't match",
    path: ["password_confirmation"],
});

export const passwordStrength = {
    isStrong: (password) => password.length >= 12,
    hasUppercase: (password) => /[A-Z]/.test(password),
    hasLowercase: (password) => /[a-z]/.test(password),
    hasNumber: (password) => /\d/.test(password),
    hasSpecialChar: (password) => /[!@#$%^&*(),.?":{}|<>]/.test(password),
};
