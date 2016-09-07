import React, { Component } from 'react'
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
import Index from '../components/index'
import Step1Container from '../containers/step1'
import Step2Container from '../containers/step2'
import Step3Container from '../containers/step3'
import Step4Container from '../containers/step4'

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
            <Route path="/goals" component={Index} >
              <Route path="create/step1" component={Step1Container} />
              <Route path="create/step2" component={Step2Container} />
              <Route path="create/step3" component={Step3Container} />
              <Route path="create/step4" component={Step4Container} />
            </Route>
          </Router>
          {/* <DevTools / > */}
        </div>
      </Provider>
    );
  }
}
