/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
/* eslint-enable no-unused-vars */
import { Provider } from 'react-redux'
import { createStore, applyMiddleware } from 'redux'
import { Router, Route,　browserHistory } from 'react-router'
import { syncHistoryWithStore } from 'react-router-redux'
import thunk from 'redux-thunk';
import { createDevTools } from 'redux-devtools'
import LogMonitor from 'redux-devtools-log-monitor'
import DockMonitor from 'redux-devtools-dock-monitor'
import createReducer from '../reducers/index'

// Container読み込み
import InputContainer from '../containers/input'
import ConfirmContainer from '../containers/confirm'
import ExceedContainer from '../containers/exceed'

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
            <Route path="/users">
              <Route path="invite" component={InputContainer} />
              <Route path="invite/confirm" component={ConfirmContainer} />
              <Route path="invite/exceed" component={ExceedContainer} />
            </Route>
          </Router>
           {/*<DevTools />*/}
        </div>
      </Provider>
    );
  }
}