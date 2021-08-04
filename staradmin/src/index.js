import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import MasterPanel from './MasterPanel';
import "./i18n";
import * as serviceWorker from './serviceWorker';
import {createStore} from "redux";
import allReducers  from './reducers';
import { Provider } from 'react-redux';
import { API_BASE_URL } from './constants';

// important
import axios from 'axios';

const store = createStore(
  allReducers ,
  window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__()
  );

axios.defaults.baseURL = API_BASE_URL+"/api/";

// bearer token save
// axios.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('token');


ReactDOM.render(
  // <BrowserRouter basename="/demo/star-admin-pro-react/template/demo_1/preview">
  //   <App />
  // </BrowserRouter>

  <BrowserRouter basename='/'>

    <Provider store = {store} >
        <App />
    </Provider>
    
  </BrowserRouter>
, document.getElementById('root'));

serviceWorker.unregister();