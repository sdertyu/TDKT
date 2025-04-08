// src/axios.js
import axios from "axios";
import { useGlobalStore } from "@/stores/global";

const instance = axios.create({
    baseURL: "/",
    // thêm headers mặc định nếu cần
});

// Request Interceptor
instance.interceptors.request.use(
    (config) => {
        const loading = useGlobalStore();
        loading.startLoading();
        return config;
    },
    (error) => {
        const loading = useGlobalStore();
        loading.stopLoading();
        return Promise.reject(error);
    }
);

// Response Interceptor
instance.interceptors.response.use(
    (response) => {
        const loading = useGlobalStore();
        loading.stopLoading();
        return response;
    },
    (error) => {
        const loading = useGlobalStore();
        loading.stopLoading();
        return Promise.reject(error);
    }
);

export default instance;
