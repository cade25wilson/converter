import axios from 'axios';

const api = axios.create({
    baseURL: 'https://fileconverter.services/api',
});

export default api;