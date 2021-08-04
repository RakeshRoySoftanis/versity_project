import React, { Component } from 'react';
import { withRouter } from 'react-router-dom';
import './common/App.scss';
import MasterRoutes from './common/MasterRoutes';
import MasterNavbar from './components/master/layout/Navbar';
import MasterSidebar from './components/master/layout/Sidebar';
// import SettingsPanel from './common/shared/SettingsPanel';
import Footer from './common/shared/Footer';
import { withTranslation } from "react-i18next";

import { getLoggedInMaster } from './services/MasterService';
import cogoToast from 'cogo-toast';
import { PUBLIC_URL } from './constants';
import { connect } from 'react-redux';

class MasterPanels extends Component {
  
  state = {
    msuser : {},
    setting : {},
  }
  componentDidMount() {
    this.onRouteChanged();

    // get user data
    getLoggedInMaster().then(res => {
      this.setMSUser(res.user);
      this.setTheSetting(res.setting);
    })
    .then(data => this.props.dispatchName(this.state.msuser))
    .catch( err => {
      if(err.response.data.status == "TokenExpired"){
        localStorage.removeItem("mstoken");
        cogoToast.success(err.response.data.message, {position: 'top-right'});
        this.props.history.push(PUBLIC_URL+'/master/login');
      }
    });
  }

  setMSUser = (msuser) => {
    this.setState({ msuser : msuser })
  }

  setTheSetting = (setting) => {
    this.setState({ setting : setting })
  }

  render () {

    let navbarComponent = localStorage.getItem('mstoken') ? <MasterNavbar msuser = {this.props.msuser} setting={this.state.setting} setMSUser = {this.setMSUser} /> : '';
    let sidebarComponent = localStorage.getItem('mstoken') ? <MasterSidebar msuser = {this.props.msuser} setting={this.state.setting} setMSUser = {this.setMSUser} /> : '';
    // let SettingsPanelComponent = localStorage.getItem('token') && !this.state.isFullPageLayout ? <SettingsPanel/> : '';
    let footerComponent = localStorage.getItem('mstoken') ? <Footer/> : '';
    
    return (
      <div className="container-scroller">
          { navbarComponent }
          <div className="container-fluid page-body-wrapper ">
          { sidebarComponent }
          <div className="main-panel">
              <div className="content-wrapper">
                
              <MasterRoutes msuser = {this.props.msuser} setMSUser = {this.setMSUser} />
              
              </div>
              { footerComponent }
          </div>
          </div>
      </div>
      
    );
  }

  componentDidUpdate(prevProps) {
    if (this.props.location !== prevProps.location) {
      this.onRouteChanged();
    }
  }

  onRouteChanged() {
    const { i18n } = this.props;
    const body = document.querySelector('body');
    if(this.props.location.pathname === '/layout/RtlLayout') {
      body.classList.add('rtl');
      i18n.changeLanguage('ar');
    }
    else {
      body.classList.remove('rtl')
      i18n.changeLanguage('en');
    }
    window.scrollTo(0, 0);
    const fullPageLayoutRoutes = ['/master/login'];
    for ( let i = 0; i < fullPageLayoutRoutes.length; i++ ) {
      if (this.props.location.pathname === fullPageLayoutRoutes[i]) {
        this.setState({
          isFullPageLayout: true
        })
        document.querySelector('.page-body-wrapper').classList.add('full-page-wrapper');
        break;
      } else {
        this.setState({
          isFullPageLayout: false
        })
        document.querySelector('.page-body-wrapper').classList.remove('full-page-wrapper');
      }
    }
  }

}


const getTheArray = (commonArray) => {
  return {
    type: 'MASTER_ADMIN_NAME',
    value: commonArray
  }
}

let mapDispatchToProps = (dispatch) => {
  return {
    dispatchName: (array) => dispatch( getTheArray(array) )
  };
};

let mapStateToProps = (state) => {

  return {
    isLogged: state.isLogged,
    msuser: state.masterUser
  };
};


const MasterPanel = connect(
  mapStateToProps,
  mapDispatchToProps
)(MasterPanels);

export default withTranslation()(withRouter(MasterPanel));
