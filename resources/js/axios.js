import axios from 'axios';

const api = axios.create({
    baseURL: 'https://converter.poshpim.online/api',
});

export default api;