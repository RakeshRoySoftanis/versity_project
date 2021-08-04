import React, { Component, Suspense, lazy } from 'react';
import { Switch, Route, Redirect, withRouter } from 'react-router-dom';

import Spinner from '../common/shared/Spinner';
import { PUBLIC_URL } from '../constants';

const Dashboard = lazy(() => import('../components/dashboard/Dashboard'));

// login
const Login = lazy(() => import('../components/auth/Login'));
// forget Password
const ForgetPassword = lazy(() => import('../components/auth/ForgetPassword'));
const ResetPassword = lazy(() => import('../components/auth/ResetPassword'));


class AppRoutes extends Component {

  render() {

    if (!localStorage.getItem('token')) {
      return (
        <Suspense fallback={<Spinner />}>
          <Switch>
            <Route exact path={`${PUBLIC_URL}/login`} component={() => <Login user={this.props.user} setUser={this.props.setUser} logged_contact={this.props.logged_contact} setLoggedContact={this.props.setLoggedContact} />} ></Route>
            <Route exact path={`${PUBLIC_URL}/forget-password`} component={ForgetPassword} />
            <Route exact path={`${PUBLIC_URL}/password-reset/:id`} component={ResetPassword} />
            {/* <Redirect to={`${PUBLIC_URL}/login`} /> */}
          </Switch>
        </Suspense>
      );
    } else {
      return (
        <Suspense fallback={<Spinner />}>
          <Switch>
            <Route exact path={`${PUBLIC_URL}/dashboard`} component={Dashboard} />
            

            <Route exact path={`${PUBLIC_URL}/login`} component={() => <Login user={this.props.user} setUser={this.props.setUser} logged_contact={this.props.logged_contact} setLoggedContact={this.props.setLoggedContact} />} ></Route>
            <Redirect to={`${PUBLIC_URL}/login`} />

          </Switch>
        </Suspense>
      );
    }



  }
}

export default AppRoutes;