import React, { Component, Suspense, lazy } from 'react';
import { Switch, Route, Redirect } from 'react-router-dom';
import Spinner from '../common/shared/Spinner';
import { MASTER_ADMIN_PUBLIC_URL } from '../constants';

const AdminDashboard = lazy(() => import('../components/master/admin/Dashboard'));
const ContactList = lazy(() => import('../components/master/admin/ContactList'));

// login
const AdminLogin = lazy(() => import('../components/master/admin/Login'));

class MasterRoutesAdmin extends Component {
    render() {
        if (!localStorage.getItem('msatoken')) {
            return (
                <Suspense fallback={<Spinner />}>
                    <Switch>
                        <Route exact path={`${MASTER_ADMIN_PUBLIC_URL}/login`} component={() => <AdminLogin msuser={this.props.msuser} setMSUser={this.props.setMSUser} />} ></Route>
                        <Redirect to={`${MASTER_ADMIN_PUBLIC_URL}/login`} />
                    </Switch>
                </Suspense>
            );
        } else {
            return (
                <Suspense fallback={<Spinner />}>
                    <Switch> 
                        <Route exact path={`${MASTER_ADMIN_PUBLIC_URL}/dashboard`} component={AdminDashboard} />
                        <Route exact path={`${MASTER_ADMIN_PUBLIC_URL}/admin-contact-list/:module_id`} component={ContactList} />

                        <Route exact path={`${MASTER_ADMIN_PUBLIC_URL}/login`} component={() => <AdminLogin msuser={this.props.msuser} setMSUser={this.props.setMSUser} />} ></Route>
                        <Redirect to={`${MASTER_ADMIN_PUBLIC_URL}/login`} />

                    </Switch>
                </Suspense>
            );
        }
    }
}

export default MasterRoutesAdmin
