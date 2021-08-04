import React, { Component } from 'react';
import { Dropdown } from 'react-bootstrap';

class Navbar extends Component {
  toggleOffcanvas() {
    document.querySelector('.sidebar-offcanvas').classList.toggle('active');
  }

  logout = () => {
    // localStorage.clear();
    localStorage.removeItem("mstoken");
    this.props.setMSUser(null);
  }
  
  render () { 
    let full_name; 
    if(this.props.msuser){
      full_name = this.props.msuser.fname;
    }

    return (
      <nav className="navbar col-lg-12 col-12 p-lg-0 fixed-top d-flex flex-row">
        <div className="navbar-menu-wrapper d-flex align-items-center justify-content-between">
        <a className="navbar-brand brand-logo-mini align-self-center d-lg-none" href="!#" onClick={evt =>evt.preventDefault()}><img style={{width: "50px"}} src={require("../../../assets/images/portal_logo_mini.png")} alt="logo" /></a>
          <button className="navbar-toggler navbar-toggler align-self-center" type="button" onClick={ () => document.body.classList.toggle('sidebar-icon-only') }>
            <i className="mdi mdi-menu"></i> 
          </button>
          <ul className="navbar-nav navbar-nav-left header-links">
            
          </ul>
          <ul className="navbar-nav navbar-nav-right">
            
            <li className="nav-item  nav-profile border-0 pl-4">
              <Dropdown>
                <Dropdown.Toggle className="nav-link count-indicator p-0 toggle-arrow-hide bg-transparent">
                  <i className="mdi mdi-bell-outline"></i>
                  <span className="count bg-success">4</span>
                </Dropdown.Toggle>
                <Dropdown.Menu className="navbar-dropdown preview-list">
                  
                  {/* <div className="dropdown-divider"></div> */}
                  
                  {/* notification loops will be start here */}
                  {/* <Dropdown.Item className="dropdown-item preview-item d-flex align-items-center" href="!#" onClick={evt =>evt.preventDefault()}> */}
                    {/* <div className="preview-thumbnail">
                      <i className="mdi mdi-alert m-auto text-primary"></i>
                    </div>
                    <div className="preview-item-content py-2">
                      <h6 className="preview-subject font-weight-normal text-dark mb-1">Application Error</h6>
                      <p className="font-weight-light small-text mb-0"> Just now </p>
                    </div> */}
                  {/* </Dropdown.Item> */}
                  {/* notification loops will be end here */}

                  
                </Dropdown.Menu>
              </Dropdown>
            </li>
            
            <li className="nav-item  nav-profile border-0">
              <Dropdown>
                <Dropdown.Toggle className="nav-link count-indicator bg-transparent">
                  <span className="profile-text">{full_name} !</span>
                  <img className="img-xs rounded-circle" src={require("../../../assets/images/faces/face8.jpg")} alt="Profile" />
                </Dropdown.Toggle>
                <Dropdown.Menu className="preview-list navbar-dropdown pb-3">
                  
                  <Dropdown.Item className="dropdown-item preview-item d-flex align-items-center border-0" onClick={this.logout}>
                    Sign Out
                  </Dropdown.Item>
                </Dropdown.Menu>
              </Dropdown>
            </li>
          </ul>
          <button className="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" onClick={this.toggleOffcanvas}>
            <span className="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
    );
  }
}

export default Navbar;
