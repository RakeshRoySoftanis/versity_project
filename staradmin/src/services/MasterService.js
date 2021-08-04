import axios from "axios";
// axios.defaults.headers.common['AuthToken'] = localStorage.getItem('mstoken');
// axios.defaults.params = {}
axios.defaults.params['AuthToken'] = localStorage.getItem('mstoken');


/**
 * login to master portal
 */
export const postMasterLoggedIn = async (data) => {
    return await axios.post(`/master-login`, data).then((res) => {
        return res.data;
    });
}

/**
 * login to client portal
 */
export const attemptLoggedIn = async (data) => {
    return await axios.post(`/master-attempt-client-login`, data).then((res) => {
        return res.data;
    });
}

/**
 * Get current logged in master info
 */
export const getLoggedInMaster = async () => {
    return await axios.get(`/master-data`).then((res) => {
        return res.data;
    });
}


/**
 * Get all active client
 * @param {*} page 
 * @param {*} perPage 
 * @param {*} search 
 * @param {*} sortField 
 * @param {*} sortOrder 
 */
export const getPortalClientList = async (page, perPage = "10", search = "", sortField = "id", sortOrder = "desc") => {
    return await axios.get(`/master-portal-client?page=${page}&per_page=${perPage}&sort_field=${sortField}&sort_order=${sortOrder}&search=${search}&delay=1`).then((res) => {
        return res.data;
    });
}





/*
|--------------------------------------------------------------------------
|  ZOHO INTEGRATION Functions
|--------------------------------------------------------------------------
*/


export const getUserList = async (page, perPage = "10", search = "", sortField = "id", sortOrder = "desc") => {
    return await axios.get(`/master-users?page=${page}&per_page=${perPage}&sort_field=${sortField}&sort_order=${sortOrder}&search=${search}&delay=1`).then((res) => {
        return res.data;
    });
}

// save User By master

export const saveUserByMaster = async (data) => {
    return await axios.post("/save-master-users", data).then((res) => {
        return res.data;
    });
}

export const insert_master_user = async (data) => {
    return await axios.post("/insert-master-users", data).then((res) => {
        return res.data;
    });
}

export const getUserById = async (id) => {
    return await axios.get(`/edit-master-users?id=` + id).then((res) => {
        return res.data;
    });
}

export const updateUser = async (data) => {
    return await axios.post(`/update-master-users`, data).then((res) => {
        return res.data;
    });
}

export const deleteUser = async (id) => {
    return await axios.get(`/delete-master-users?id=` + id).then((res) => {
        return res.data;
    });
}

export const userAssignAccount = async (id) => {
    return await axios.get(`/userAssignAccount?id=` + id).then((res) => {
        return res.data;
    });
}

// settings
export const setting = async (id) => {
    return await axios.get(`/Setting`).then((res) => {
        return res.data;
    });
}

export const settingsUpdate = async (data) => {
    return await axios.post("/update-setting", data).then((res) => {
        return res.data;
    });
}

export const settingsLogoUpdate = async (data) => {
    return await axios.post("/setting-logo", data).then((res) => {
        return res.data;
    });
}

export const ColorSetting = async (data) => {
    return await axios.post("/setting-color", data).then((res) => {
        return res.data;
    });
}
