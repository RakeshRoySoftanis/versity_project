import React, { Component } from 'react';
import { withTranslation } from 'react-i18next';
import { Route, Switch, withRouter } from 'react-router-dom';
// import { browserHistory, IndexRoute } from 'react-router'
import MasterPanelAdmin from "./MasterPanelAdmin";
import MasterPanel from "./MasterPanel";
import UserPanel from "./UserPanel";

class App extends Component {
  render() {
      return (
          // <Router history={browserHistory}>
            <Switch>
                <Route path='/master' component={MasterPanel} />
                <Route path='/admin' component={MasterPanelAdmin} />
                <Route path='/' component={UserPanel} />
            </Switch>
          // </Router>
      );
  }
}

export default withTranslation()(withRouter(App));
