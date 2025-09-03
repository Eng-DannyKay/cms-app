import React from 'react';
import { Link } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { registerSchema, passwordStrength } from '../../../shared/validations/auth';
import { useRegister } from '../../../Hooks/useAuth';
import Input from '../../../Components/UI/Input';
import Button from '../../../Components/UI/Button';

const Register = () => {
    const register = useRegister();
    const [error, setError] = React.useState('');
    const [password, setPassword] = React.useState('');

    const {
        register: registerField,
        handleSubmit,
        formState: { errors, isSubmitting },
        watch,
    } = useForm({
        resolver: zodResolver(registerSchema),
    });

    const passwordValue = watch('password', '');

    React.useEffect(() => {
        setPassword(passwordValue);
    }, [passwordValue]);

    const onSubmit = async (data) => {
        try {
            setError('');
            await register(data);
        } catch (err) {
            setError(err.message || 'Registration failed. Please try again.');
        }
    };

    const getPasswordStrength = (pass) => {
        if (pass.length === 0) return 0;
        let strength = 0;
        if (pass.length >= 8) strength += 20;
        if (passwordStrength.hasUppercase(pass)) strength += 20;
        if (passwordStrength.hasLowercase(pass)) strength += 20;
        if (passwordStrength.hasNumber(pass)) strength += 20;
        if (passwordStrength.hasSpecialChar(pass)) strength += 20;
        return strength;
    };

    const passwordStrengthValue = getPasswordStrength(password);

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div className="max-w-md w-full space-y-8">
                <div>
                    <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Create your account
                    </h2>
                    <p className="mt-2 text-center text-sm text-gray-600">
                        Or{' '}
                        <Link
                            to="/login"
                            className="font-medium text-primary hover:text-primary/80"
                        >
                            sign in to existing account
                        </Link>
                    </p>
                </div>

                <form className="mt-8 space-y-6" onSubmit={handleSubmit(onSubmit)}>
                    {error && (
                        <div className="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
                            {error}
                        </div>
                    )}

                    <div className="space-y-4">
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
                                Full Name
                            </label>
                            <Input
                                id="name"
                                type="text"
                                autoComplete="name"
                                error={errors.name?.message}
                                {...registerField('name')}
                            />
                        </div>

                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                                Email address
                            </label>
                            <Input
                                id="email"
                                type="email"
                                autoComplete="email"
                                error={errors.email?.message}
                                {...registerField('email')}
                            />
                        </div>

                        <div>
                            <label htmlFor="company_name" className="block text-sm font-medium text-gray-700 mb-1">
                                Company Name
                            </label>
                            <Input
                                id="company_name"
                                type="text"
                                autoComplete="organization"
                                error={errors.company_name?.message}
                                {...registerField('company_name')}
                            />
                        </div>

                        <div>
                            <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1">
                                Password
                            </label>
                            <Input
                                id="password"
                                type="password"
                                autoComplete="new-password"
                                error={errors.password?.message}
                                {...registerField('password')}
                                onChange={(e) => setPassword(e.target.value)}
                            />

                            {password && (
                                <div className="mt-2">
                                    <div className="w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            className="h-2 rounded-full transition-all duration-300"
                                            style={{
                                                width: `${passwordStrengthValue}%`,
                                                backgroundColor: passwordStrengthValue < 40
                                                    ? '#ef4444'
                                                    : passwordStrengthValue < 80
                                                    ? '#f59e0b'
                                                    : '#10b981'
                                            }}
                                        />
                                    </div>
                                    <p className="text-xs text-gray-500 mt-1">
                                        Strength: {passwordStrengthValue}%
                                    </p>
                                </div>
                            )}
                        </div>

                        <div>
                            <label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700 mb-1">
                                Confirm Password
                            </label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                autoComplete="new-password"
                                error={errors.password_confirmation?.message}
                                {...registerField('password_confirmation')}
                            />
                        </div>
                    </div>

                    <div className="flex items-center">
                        <input
                            id="terms"
                            name="terms"
                            type="checkbox"
                            className="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                            required
                        />
                        <label htmlFor="terms" className="ml-2 block text-sm text-gray-900">
                            I agree to the{' '}
                            <a href="#" className="text-primary hover:text-primary/80">
                                Terms and Conditions
                            </a>
                        </label>
                    </div>

                    <div>
                        <Button
                            type="submit"
                            variant="primary"
                            size="lg"
                            loading={isSubmitting}
                            className="w-full"
                        >
                            Create Account
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default Register;
