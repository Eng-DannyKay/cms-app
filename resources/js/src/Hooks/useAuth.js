import { useAuth } from "../contexts/AuthContext.jsx";

export const useLogin = () => {
    const { login } = useAuth();
    return login;
};

export const useRegister = () => {
    const { register } = useAuth();
    return register;
};

export const useLogout = () => {
    const { logout } = useAuth();
    return logout;
};
