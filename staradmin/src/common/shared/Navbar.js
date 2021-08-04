import React, { Component } from 'react';
import { Dropdown } from 'react-bootstrap';
import { connect } from 'react-redux';
import { Link, Redirect } from 'react-router-dom';
import { API_BASE_URL, PUBLIC_URL } from '../../constants';

class Navbars extends Component {
  toggleOffcanvas(temp_name) {

    if(temp_name === "template_three" || temp_name === "template_four" ){
      let el = document.querySelector('.bottom-navbar')
      el.classList.add("sidebar");
      el.classList.add("sidebar-offcanvas");
      el.id = "sidebar";
      el.style.display = "block"
    }

    document.querySelector('.sidebar-offcanvas').classList.toggle('active');
  }

  logout = () => {
    // localStorage.clear();
    localStorage.removeItem("token");
    this.props.setUser(null);
    this.props.setLoggedContact(null);
    this.props.dispatcTemplete([]);
    window.location.reload()
    return <Redirect to="/login"></Redirect>
  }
  
  render () { 
    let full_name; 
    full_name = this.props.user.First_Name;
    // if(this.props.logged_contact){
    //   full_name = this.props.logged_contact.First_Name;
    // }

    return (
      <nav className="navbar col-lg-12 col-12 p-lg-0 fixed-top d-flex flex-row">
        <div className="navbar-menu-wrapper d-flex align-items-center justify-content-between">
          <Link className="navbar-brand brand-logo-mini align-self-center d-lg-none" to={`${PUBLIC_URL}`}> <img style={{width: "50px"}} src={API_BASE_URL + "/public/psettings/logo/" + this.props.setting.minilogo} alt="logo" /> </Link>
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
                  {/* <Dropdown.Item className="dropdown-item py-3 d-flex align-items-center" href="!#" onClick={evt =>evt.preventDefault()}>
                    <p className="mb-0 font-weight-medium float-left">You have 4 new notifications </p>
                    <span className="badge badge-pill badge-primary float-right">View all</span>
                  </Dropdown.Item>
                  <div className="dropdown-divider"></div> */}
                  
                  {/* notification loops will be start here */}
                  
                  {/* notification loops will be end here */}

                </Dropdown.Menu>
              </Dropdown>
            </li>
            
            <li className="nav-item  nav-profile border-0">
              <Dropdown>
                <Dropdown.Toggle className="nav-link count-indicator bg-transparent">
                  <span className="profile-text"> {full_name} !  </span>
                  <img className="img-xs rounded-circle" src={require("../../assets/images/faces/face8.jpg")} alt="Profile" />
                </Dropdown.Toggle>
                <Dropdown.Menu className="preview-list navbar-dropdown pb-3">
                  
                  <Dropdown.Item className="dropdown-item preview-item d-flex align-items-center border-0 mt-2" as={Link} to={`${PUBLIC_URL}/my-profile`}>
                    Manage Accounts
                  </Dropdown.Item>
                  <Dropdown.Item className="dropdown-item preview-item d-flex align-items-center border-0" onClick={this.logout}>
                    Sign Out
                  </Dropdown.Item>
                </Dropdown.Menu>
              </Dropdown>
            </li>
          </ul>
          <button className="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" onClick={ ()=> this.toggleOffcanvas(this.props.templete.template_api_name) }>
            <span className="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
    );
  }
}


const getTempleteSetting = (val = []) => {
  return {
    type: 'TEMPLETE_SETTING',
    value: val
  }
}

let mapStateToProps = (state) => {
  return {
    isLogged: state.isLogged,
    user: state.user_contact ,
    logged_contact: state.logged_contact,
    templete: state.templete,
  };
};

let mapDispatchToProps = (dispatch) => {
  return {
    dispatcTemplete: (array) => dispatch( getTempleteSetting(array) ),
  };
};
const Navbar = connect(
  mapStateToProps ,
  mapDispatchToProps
)(Navbars);

export default Navbar;
