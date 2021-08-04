import React, { Component, Suspense, lazy } from 'react';
import { Switch, Route, Redirect } from 'react-router-dom';

import Spinner from '../common/shared/Spinner';
import { MASTER_PUBLIC_URL } from '../constants';

// login
const MasterLogin = lazy(() => import('../components/master/Login'));

//user
const UserList = lazy(() => import('../components/master/user/List'));
const AssignAccount = lazy(() => import('../components/master/user/AssignAccount'));

// setting
const Setting = lazy(() => import('../components/master/settings/Setting'));

class MasterRoutes extends Component {
    render() {

        if (!localStorage.getItem('mstoken')) {
            return (
                <Suspense fallback={<Spinner />}>
                    <Switch>
                        <Route exact path={`${MASTER_PUBLIC_URL}/login`} component={() => <MasterLogin msuser={this.props.msuser} setMSUser={this.props.setMSUser} />} ></Route>
                        <Redirect to={`${MASTER_PUBLIC_URL}/login`} />
                    </Switch>
                </Suspense>
            );
        } else {
            return (
                <Suspense fallback={<Spinner />}>
                    <Switch>

                        {/* clients */}

                        <Route path={`${MASTER_PUBLIC_URL}/users`} component={UserList} />
                        <Route path={`${MASTER_PUBLIC_URL}/master-users-assign-account/:id`} component={AssignAccount} />

                        {/* Settings */}
                        <Route path={`${MASTER_PUBLIC_URL}/Setting/Set-up`} component={Setting} />

                        <Route exact path={`${MASTER_PUBLIC_URL}/login`} component={() => <MasterLogin msuser={this.props.msuser} setMSUser={this.props.setMSUser} />} ></Route>
                        <Redirect to={`${MASTER_PUBLIC_URL}/login`} />

                    </Switch>
                </Suspense>
            );
        }



    }
}

export default MasterRoutes
