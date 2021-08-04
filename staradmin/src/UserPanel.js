import React, { Component } from 'react';
import { withRouter, Link, Redirect } from 'react-router-dom';
// import "./front/assets/scss/material-kit-react.scss?v=1.10.0";

// import "bootstrap/scss/bootstrap.scss";
import './common/App.scss';
import "./front/assets/scss/paper-kit.scss?v=1.3.0";
import "./front/assets/demo/demo.css?v=1.3.0";

import AppRoutes from './common/AppRoutes';
import Navbar from './common/shared/Navbar';
import Sidebar from './common/shared/Sidebar';
// import SettingsPanel from './common/shared/SettingsPanel';
import Footer from './common/shared/Footer';
import Index from "./front/views/Index.js";
// import Login from './components/auth/Login';
import { withTranslation } from "react-i18next";
import { Dropdown } from 'react-bootstrap';

//important
import axios from 'axios';
import { connect } from 'react-redux';
import { PUBLIC_URL, API_BASE_URL } from './constants';
axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('token');



class UserPanels extends Component {

  state = {
    user: {},
    logged_contact: {}
  }

  logout = () => {
    // localStorage.clear();
    localStorage.removeItem("token");
    this.props.setUser(null);
    this.props.setLoggedContact(null);
    this.props.dispatcTemplete([]);
    return <Redirect to="/dashboard"></Redirect>
  }

  componentDidMount() {
    this.onRouteChanged();

    // get user data
    axios.get('/user')
      .then((response) => {
        this.setUser(response.data.user);
        this.setLoggedContact(response.data.logged_contact);
        this.setTempleteSetting(response.data.template_setting);
      })
      .then(data => this.props.dispatchName(this.state.logged_contact))
      .then(data => this.props.dispatcUser(this.state.user))
      .then(data => this.props.dispatcTemplete(this.state.template_setting))
      .catch((err) => {
        if (err.response.data.message == "Unauthenticated.") {
          // localStorage.removeItem("token");
          localStorage.setItem('token', "response.token");
          return <Redirect to="/dashboard"></Redirect>

          if (!window.location.href.includes("/password-reset/")) {
            this.props.history.push(PUBLIC_URL + '/login');
          }
        }
      });
  }

  setUser = (user) => {
    this.setState({ user: user })
  }
  setLoggedContact = (logged_contact) => {
    this.setState({ logged_contact: logged_contact })
  }

  setModule_listName = (module_listName) => {
    this.setState({ module_listName: module_listName })
  }
  setTempleteSetting = (template_setting) => {
    this.setState({ template_setting: template_setting })
  }
  setSetting = (setting) => {
    this.setState({ setting: setting })
  }

  setZhSubscriptionOption = (z_option) => {
    this.setState({ z_option: z_option })
  }

  setHasActivitis = (hasActivitis) => {
    this.setState({ hasActivitis: hasActivitis })
  }

   
    

  render() {

    let navbarComponent = localStorage.getItem('token') ? <Navbar user={this.props.user} setting={this.state.setting} setUser={this.setUser} logged_contact={this.props.logged_contact} setLoggedContact={this.setLoggedContact} /> : '';
    let sidebarComponent = localStorage.getItem('token') ? <Sidebar hasActivitis={this.state.hasActivitis} module_listName={this.state.module_listName} user={this.props.user} setUser={this.setUser} logged_contact={this.props.logged_contact} setLoggedContact={this.setLoggedContact} /> : '';
    // let SettingsPanelComponent = localStorage.getItem('token') && !this.state.isFullPageLayout ? <SettingsPanel/> : '';
    let footerComponent = localStorage.getItem('token') ? <Footer /> : '';

    let full_name = this.props.user.First_Name;

    let class_name = "templete_1" +" container-scroller";
    return (

      <div className={class_name}>
        <Index />
        <AppRoutes />
        
      </div>
    );


    if (this.props.templete.id == undefined) {
      //when he login in first
      if (localStorage.getItem('token')) {
        return (
          <div className="container-scroller">
            <div className="container-fluid page-body-wrapper ">
              <div className="main-panel">
                <div className="content-wrapper">
                </div>
              </div>
            </div>
          </div>
        );
      } else {

        //after logout
        return (
          <div className="container-scroller">
            { navbarComponent}
            <div className="container-fluid page-body-wrapper ">
              {sidebarComponent}
              <div className="main-panel">
                <div className="content-wrapper">
                  <AppRoutes user={this.state.user} setUser={this.setUser} logged_contact={this.state.logged_contact} setLoggedContact={this.setLoggedContact} />

                </div>
                {footerComponent}
              </div>
            </div>
          </div>
        );

      }
    }
  }

  componentDidUpdate(prevProps) {
    if (this.props.location !== prevProps.location) {
      this.onRouteChanged();
    }
  }

  onRouteChanged() {
    const { i18n } = this.props;
    const body = document.querySelector('body');
    if (this.props.location.pathname === '/layout/RtlLayout') {
      body.classList.add('rtl');
      i18n.changeLanguage('ar');
    }
    else {
      body.classList.remove('rtl')
      i18n.changeLanguage('en');
    }
    window.scrollTo(0, 0);
    const fullPageLayoutRoutes = ['/', '/login'];
    for (let i = 0; i < fullPageLayoutRoutes.length; i++) {
      if (this.props.location.pathname === fullPageLayoutRoutes[i]) {
        this.setState({
          isFullPageLayout: true
        })
        // document.querySelector('.page-body-wrapper').classList.add('full-page-wrapper');
        break;
      } else {
        this.setState({
          isFullPageLayout: false
        })
        // document.querySelector('.page-body-wrapper').classList.remove('full-page-wrapper');
      }
    }
  }

}

const getTheArray = (commonArray) => {
  return {
    type: 'USER_ADMIN_NAME',
    value: commonArray
  }
}

const getSettingArray = (commonArray) => {
  return {
    type: 'SETTING',
    value: commonArray
  }
}

const getZS_OptionArray = (commonArray) => {
  return {
    type: 'ZH_OPTIONS',
    value: commonArray
  }
}

const getTheLoggedArray = (commonArray) => {
  return {
    type: 'LOGGED_ADMIN_NAME',
    value: commonArray
  }
}

const getTempleteSetting = (commonArray) => {
  return {
    type: 'TEMPLETE_SETTING',
    value: commonArray
  }
}

let mapDispatchToProps = (dispatch) => {
  return {
    dispatchName: (array) => dispatch(getTheArray(array)),
    dispatcUser: (array) => dispatch(getTheLoggedArray(array)),
    dispatcTemplete: (array) => dispatch(getTempleteSetting(array)),
    dispatcSetting: (array) => dispatch(getSettingArray(array)),
    dispatcZh_option: (array) => dispatch(getZS_OptionArray(array))
  };
};

let mapStateToProps = (state) => {
  return {
    isLogged: state.isLogged,
    user: state.user_contact,
    logged_contact: state.logged_contact,
    templete: state.templete
  };
};

const UserPanel = connect(
  mapStateToProps,
  mapDispatchToProps
)(UserPanels);

export default withTranslation()(withRouter(UserPanel));
