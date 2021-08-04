import axios from "axios";
// axios.defaults.headers.common['AuthToken'] = localStorage.getItem('mstoken');
axios.defaults.params = {}
axios.defaults.params['AuthTokenAdminUser'] = localStorage.getItem('msatoken');

export const getLoggedInAdmin = async () => {
    return await axios.get(`/admin-data`).then((res) => {
        return res.data;
    });
}

export const postAdminLoggedIn = async (data) => {
    return await axios.post(`/admin-login`, data).then((res) => {
        return res.data;
    });
}

export const AdminDashboard = async () => {
    return await axios.get(`/admin-dashboard`).then((res) => {
        return res.data;
    });
}

export const showContactList = async (module_id) => {
    return await axios.get(`/admin-contact-list/` + module_id ).then((res) => {
        return res.data;
    });
}

export const attemptLoggedIn = async (data) => {
    return await axios.post(`/admin-attempt-client-login`, data).then((res) => {
        return res.data;
    });
}
