import React, { Component } from 'react';
import { withRouter } from 'react-router-dom';
import './common/App.scss';
import MasterRoutesAdmin from './common/MasterRoutesAdmin';
import MasterNavbar from './components/master/AdminUserlayout/Navbar';
import MasterSidebar from './components/master/AdminUserlayout/Sidebar';
// import SettingsPanel from './common/shared/SettingsPanel';
import Footer from './common/shared/Footer';
import { withTranslation } from "react-i18next";

import { getLoggedInAdmin } from './services/MasterServiceAdmin';
import cogoToast from 'cogo-toast';
import { MASTER_ADMIN_PUBLIC_URL } from './constants';

class MasterPanelAdmin extends Component {
  
  state = {
    msuser : {},
    setting : {},
  }
  componentDidMount() {
    this.onRouteChanged();

    // get user data
    getLoggedInAdmin().then(res => {
      this.setMSUser(res.user);
      this.setTheSetting(res.setting);
    })
    .catch( err => {
      if( err.response != undefined && err.response.data.status == "TokenExpired"){
        localStorage.removeItem("msatoken");
        cogoToast.success(err.response.data.message, {position: 'top-right'})
        this.props.history.push(MASTER_ADMIN_PUBLIC_URL+'/login')
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
    
    let navbarComponent = localStorage.getItem('msatoken') ? <MasterNavbar msuser = {this.state.msuser} setting={this.state.setting} setMSUser = {this.setMSUser} /> : '';
    let sidebarComponent = localStorage.getItem('msatoken') ? <MasterSidebar msuser = {this.state.msuser} setting={this.state.setting} setMSUser = {this.setMSUser} /> : '';
    // let SettingsPanelComponent = localStorage.getItem('token') && !this.state.isFullPageLayout ? <SettingsPanel/> : '';
    let footerComponent = localStorage.getItem('msatoken') ? <Footer/> : '';
    
    return (
      <div className="container-scroller">
          { navbarComponent }
          <div className="container-fluid page-body-wrapper ">
          { sidebarComponent }
          <div className="main-panel">
              <div className="content-wrapper">
                
              <MasterRoutesAdmin msuser = {this.props.msuser} setMSUser = {this.setMSUser} />
              
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
    const fullPageLayoutRoutes = ['/admin/login'];
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




export default withTranslation()(withRouter(MasterPanelAdmin));
