import commonReducer from './common';
import loggedReducer from './isLogged'; 
import logged_contactReducer from './loggedArray';
import user_contactReducer from './userArray';
import masterUserReducer from "./masterUser"; 
import templete_Reducer from "./templete"; 
import setting from "./setting"; 
import zoho from "./zohoOption"; 

import {combineReducers} from "redux";

const allReducers = combineReducers({
    common: commonReducer,
    isLogged: loggedReducer,
    user_contact: user_contactReducer,
    logged_contact: logged_contactReducer,
    masterUser:masterUserReducer,
    templete:templete_Reducer,
    setting,
    zoho

})

export default allReducers;