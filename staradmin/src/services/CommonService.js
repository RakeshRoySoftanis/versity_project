import axios from "axios";
axios.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('token');

/**
 * logged in user info
 */
export const postLoggedIn = async (data) => {
    return await axios.post(`/login`, data).then((res) => {
        return res.data;
    });
}

/**
 * send email for forget password
 */
export const forgetPasswordMail = async (data) => {
    return await axios.post(`/forgetPasswordMail`, data).then((res) => {
        return res.data;
    });
}

export const resetPassword = async (data) => {
    return await axios.post(`/resetPassword`, data).then((res) => {
        return res.data;
    });
}

/**
 * Get current logged in user info
 */
export const getLoggedInUser = async () => {
    return await axios.get(`/my-contact`).then((res) => {
        return res.data;
    });
}

/**
 * updateContactInfo
 * update contact info tab data to local & crm
 * @param {*} data 
 */
export const updateContactInfo = async (data) => {
    return await axios.post("/save-contact", data).then((res) => {
        return res.data;
    });
}
/**
 * sendVerificationRequest
 * Passed two factor fields data to create verification code & send
 * @param {*} data 
 */
export const sendVerificationRequest = async (data) => {
    return await axios.post("/send-verification-code", data).then((res) => {
        return res.data;
    });
}
export const sendVerificationCallRequest = async (data) => {
    return await axios.post("/twilio-voice", data).then((res) => {
        return res.data;
    });
}
/**
 * submitVerificationCode
 * Passed two factor verification code to check
 * @param {*} data 
 */
export const submitVerificationCode = async (data) => {
    return await axios.post("/submit-verification-code", data).then((res) => {
        return res.data;
    });
}

export const selectOptions = async (page,serach) => {
    return await axios.get(`/selectOptions/${page}/${serach}`).then((res) => {
        return res.data;
    });
}

export const changeNewPassword = async (data) => {
    return await axios.post("/changePassword", data).then((res) => {
        return res.data;
    });
}