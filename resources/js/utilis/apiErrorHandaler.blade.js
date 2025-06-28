// src/utils/apiErrorHandler.js
import axios from 'axios';

export default class ApiErrorHandler {
    static getMessage(error) {
        let errorDescription = '';

        // Check if the error is an AxiosError
        if (axios.isAxiosError(error)) {
            const { response, message, code, config } = error;

            // Handle different types of errors
            if (response) {
                // API returned a response, but there was an issue with it (status code)
                switch (response.status) {
                    case 400:
                        errorDescription = `Bad Request: ${response.data.message || 'Invalid request parameters'}`;
                        break;
                    case 401:
                        errorDescription = `Unauthorized: ${response.data.message || 'You need to log in'}`;
                        break;
                    case 403:
                        errorDescription = `Forbidden: ${response.data.message || 'You do not have permission'}`;
                        break;
                    case 404:
                        errorDescription = `Not Found: ${response.data.message || 'Resource not found'}`;
                        break;
                    case 500:
                        errorDescription = `Server Error: ${response.data.message || 'Internal server error'}`;
                        break;
                    case 503:
                        errorDescription = `Service Unavailable: ${response.data.message || 'The server is temporarily down'}`;
                        break;
                    default:
                        errorDescription = `Unexpected error: ${response.statusText || 'An unexpected error occurred'}`;
                        break;
                }

                // Check if the API returned specific error details
                if (response.data.errors) {
                    const res = error.response.data;

                    // Try to get from errors object first
                    if (res.errors) {
                        const firstKey = Object.keys(res.errors)[0];
                        errorDescription = res.errors[firstKey]?.[0] || res.message || errorDescription;
                    } else if (res.message) {
                        errorDescription = res.message;
                    }

                    // Now use it (e.g. show in alert or UI)
                    this.firstErrorMessage = errorDescription;

                }
            } else if (error.request) {
                // No response was received
                errorDescription = `No response from server: ${message}`;
            } else {
                // Something happened while setting up the request
                errorDescription = `Request setup error: ${message}`;
            }
        } else {
            // Non-Axios errors
            errorDescription = `Unexpected error occurred: ${error.message || error}`;
        }

        return errorDescription;
    }
}
