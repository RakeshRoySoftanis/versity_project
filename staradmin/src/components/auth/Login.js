import cogoToast from 'cogo-toast';
import React, { Component } from 'react';
import { Link, Redirect } from 'react-router-dom';
import { PUBLIC_URL } from '../../constants';
import { postLoggedIn } from '../../services/CommonService';

export class Login extends Component {
  state = {
    email : "",
    password : "",
  }

  // after form submit
  handler = (e) => {
      e.preventDefault();

      // process data
      const data = {
          email : this.state.email,
          password : this.state.password,
      }

      // sent to server
      postLoggedIn(data).then(response => {
        localStorage.setItem('token', response.token); //token store on local storage
        // this.setState({
        //     loggedIn : true
        // });

        this.props.setUser(response.user);
        this.props.setLoggedContact(response.logged_contact);
        cogoToast.success(response.message, {position: 'top-right'});
        window.location = "/dashboard";

      }).catch( error => {
        cogoToast.error(error.response.data.message, {position: 'top-right'});
      });


  }
  render() {
    // after login redirect to dashboard
    // if(this.state.loggedIn){
    //   return <Redirect to="/dashboard"></Redirect>
    // }

    // redirect to dashboard

    if(localStorage.getItem('token')){
      return <Redirect to="/dashboard"></Redirect>
    }

    return (
      <div>
        <div className="d-flex align-items-stretch auth auth-img-bg h-100">
          <div className="row flex-grow">
            <div className="col-lg-6 d-flex align-items-center justify-content-center">
              <div className="auth-form-transparent text-left p-3">
                <div className="brand-logo">
                  <img src={require("../../assets/images/portal_logo.png")} alt="logo" />
                </div>
                <h4>Welcome back!</h4>
                <h6 className="font-weight-light">Happy to see you again!</h6>
                <form className="pt-3" onSubmit={this.handler}>

                  <div className="form-group">
                    <label>Username</label>
                    <div className="input-group">
                      <div className="input-group-prepend bg-transparent">
                        <span className="input-group-text bg-transparent border-right-0">
                          <i className="mdi mdi-account-outline text-primary"></i>
                        </span>
                      </div>
                      <input type="text" className="form-control form-control-lg border-left-0" id="exampleInputEmail" placeholder="Email" name="email" required onChange={(e) => {this.setState({email : e.target.value })}} />
                    </div>
                  </div>
                  <div className="form-group">
                    <label>Password</label>
                    <div className="input-group">
                      <div className="input-group-prepend bg-transparent">
                        <span className="input-group-text bg-transparent border-right-0">
                          <i className="mdi mdi-lock-outline text-primary"></i>
                        </span>
                      </div>
                      <input type="password" className="form-control form-control-lg border-left-0" id="exampleInputPassword" placeholder="Password" name="password" required onChange={(e) => {this.setState({password : e.target.value })}} />                        
                    </div>
                  </div>
                  <div className="my-2 d-flex justify-content-between align-items-center">
                    {/* <div className="form-check">
                      <label className="form-check-label text-muted">
                        <input type="checkbox" className="form-check-input" />
                        <i className="input-helper"></i>
                        Keep me signed in
                      </label>
                    </div> */}
                    <Link to={`${PUBLIC_URL}/forget-password`} className="auth-link text-black">Forgot password?</Link>
                  </div>
                  <div className="my-3">
                    <button type="submit" className="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" >LOGIN</button>
                  </div>
                  {/* <div className="text-center mt-4 font-weight-light">
                    Don't have an account? <Link to={`${PUBLIC_URL}/user-pages/register-2`} className="text-primary">Create</Link>
                  </div> */}
                </form>
              </div>
            </div>
            <div className="col-lg-6 login-half-bg d-flex flex-row">
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default Login
