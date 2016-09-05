import React, { Component } from 'react'
import { Provider } from 'react-redux'
import { createStore, applyMiddleware } from 'redux'
import { Router, Route, IndexRoute,　browserHistory } from 'react-router'
import { syncHistoryWithStore } from 'react-router-redux'
import thunk from 'redux-thunk';
import { createDevTools } from 'redux-devtools'
import LogMonitor from 'redux-devtools-log-monitor'
import DockMonitor from 'redux-devtools-dock-monitor'
import createReducer from '../reducers/index'

// Container読み込み
import Index from '../components/index'
import AuthContainer from '../containers/auth'
import UserNameContainer from '../containers/user_name'
import PasswordContainer from '../containers/password'
import TeamNameContainer from '../containers/team_name'
import TermContainer from '../containers/term'

const DevTools = createDevTools(
  <DockMonitor toggleVisibilityKey="ctrl-h" changePositionKey="ctrl-q">
    <LogMonitor theme="tomorrow" preserveScrollTop={false} />
  </DockMonitor>
)
const reducer = createReducer()
const store = createStore(
  reducer,
  DevTools.instrument(),
  applyMiddleware(thunk)
)
const history = syncHistoryWithStore(browserHistory, store)

export const unlisten = history.listen( () => {
  history.listen(location => {
    // Ignore First page loading because in this timing it's sent by server-side
    if(cake.data.google_tag_manager_id !== "" && location.action == 'PUSH') {
      sendToGoogleTagManager('app')
    }
  })
})

// Define setup-guide routes
export default class Routes extends Component {
  render() {
    return (
      <Provider store={store}>
        <div>
          <Router history={history}>
            <Route path="/signup" component={Index} >
              <IndexRoute component={AuthContainer} />
              <Route path="auth" component={AuthContainer} />
              <Route path="password" component={PasswordContainer} />
              <Route path="user" component={UserNameContainer} />
              <Route path="team" component={TeamNameContainer} />
              <Route path="term" component={TermContainer} />
            </Route>
          </Router>
          {/* <DevTools / > */}
        </div>
      </Provider>
    );
  }
}
