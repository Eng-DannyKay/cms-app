
export function handleError(error, defaultMessage) {
        const errorData = error.response?.data;
        const message = errorData?.message || defaultMessage;
        const errors = errorData?.errors;

        const formattedError = new Error(message);
        formattedError.details = errors;
        formattedError.status = error.response?.status;

        throw formattedError;
    }