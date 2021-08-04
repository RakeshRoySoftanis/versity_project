import React, { Component } from 'react';
import { Link, withRouter } from 'react-router-dom';
import { Collapse } from 'react-bootstrap';
import { API_BASE_URL, MASTER_ADMIN_PUBLIC_URL } from '../../../constants';

class Sidebar extends Component {
  state = {};

  toggleMenuState(menuState) {
    if (this.state[menuState]) {
      this.setState({ [menuState]: false });
    } else if (Object.keys(this.state).length === 0) {
      this.setState({ [menuState]: true });
    } else {
      Object.keys(this.state).forEach(i => {
        this.setState({ [i]: false });
      });
      this.setState({ [menuState]: true });
    }
  }

  toggleMenuState(menuState) {
    if (this.state[menuState]) {
      this.setState({ [menuState]: false });
    } else if (Object.keys(this.state).length === 0) {
      this.setState({ [menuState]: true });
    } else {
      Object.keys(this.state).forEach(i => {
        this.setState({ [i]: false });
      });
      this.setState({ [menuState]: true });
    }
  }

  componentDidUpdate(prevProps) {
    if (this.props.location !== prevProps.location) {
      this.onRouteChanged();
    }
  }

  onRouteChanged() {
    document.querySelector('#sidebar').classList.remove('active');
    Object.keys(this.state).forEach(i => {
      this.setState({ [i]: false });
    });

    const dropdownPaths = [
      // {path:'/apps', state: 'appsMenuOpen'},
      // {path:'/basic-ui', state: 'basicUiMenuOpen'},
      // {path:'/advanced-ui', state: 'advancedUiMenuOpen'},
      // {path:'/form-elements', state: 'formElementsMenuOpen'},
      // {path:'/tables', state: 'tablesMenuOpen'},
      // {path:'/maps', state: 'mapsMenuOpen'},
      // {path:'/icons', state: 'iconsMenuOpen'},
      // {path:'/charts', state: 'chartsMenuOpen'},
      // {path:'/user-pages', state: 'userPagesMenuOpen'},
      // {path:'/error-pages', state: 'errorPagesMenuOpen'},
      // {path:'/general-pages', state: 'generalPagesMenuOpen'},
      // {path:'/ecommerce', state: 'ecommercePagesMenuOpen'},
      { path: '/master/Setting', state: 'setting' },
      { path: '/master/zoho', state: 'zIntgMenuOpen' },
    ];

    dropdownPaths.forEach((obj => {
      if (this.isPathActive(obj.path)) {
        this.setState({ [obj.state]: true })
      }
    }));

  }
  render() {
    let full_name;
    if (this.props.msuser) {
      full_name = this.props.msuser.name;
    }


    return (
      <nav className="master sidebar sidebar-offcanvas" id="sidebar">
        <div className="text-center sidebar-brand-wrapper d-flex align-items-center">
          {(() => {
            if (this.props.setting.logo !== undefined && this.props.setting.minilogo != undefined) {
              return (
                <>
                  <a className="sidebar-brand brand-logo" href=""><img src={API_BASE_URL + "/public/psettings/logo/" + this.props.setting.logo} alt="logo" /></a>
                  <a className="sidebar-brand brand-logo-mini pt-3" href=""><img src={API_BASE_URL + "/public/psettings/logo/" + this.props.setting.minilogo} alt="logo" /></a>
                </>
              )
            }
          })()}
        </div>
        <ul className="nav">
          <li className="nav-item nav-profile not-navigation-link">
            <div className="nav-link border-bottom">

              <div className="nav-link user-switch-dropdown-toggler p-0 toggle-arrow-hide bg-transparent border-0 w-100 ">
                <div className="d-flex justify-content-between align-items-start">
                  <div className="profile-image">
                    <img src={require("../../../assets/images/faces/face8.jpg")} alt="profile" />
                  </div>
                  <div className="text-left">
                    <p className="profile-name">{full_name}</p>
                    <small className="designation text-muted text-small">Admin </small>
                    <span className="status-indicator online"></span>
                  </div>
                </div>
              </div>

            </div>
          </li>

          <li className={this.isPathActive('/admin/dashboard') ? 'nav-item active' : 'nav-item'}>
            <Link className="nav-link" to={`${MASTER_ADMIN_PUBLIC_URL}/dashboard`}>
              <i className="mdi mdi-television menu-icon"></i>
              <span className="menu-title">Accounts</span>
            </Link>
          </li>

        </ul>
      </nav>
    );
  }

  isPathActive(path) {
    return this.props.location.pathname.startsWith(path);
  }

  componentDidMount() {
    this.onRouteChanged();
    // add className 'hover-open' to sidebar navitem while hover in sidebar-icon-only menu
    const body = document.querySelector('body');
    document.querySelectorAll('.sidebar .nav-item').forEach((el) => {

      el.addEventListener('mouseover', function () {
        if (body.classList.contains('sidebar-icon-only')) {
          el.classList.add('hover-open');
        }
      });
      el.addEventListener('mouseout', function () {
        if (body.classList.contains('sidebar-icon-only')) {
          el.classList.remove('hover-open');
        }
      });
    });
  }

}

export default withRouter(Sidebar);