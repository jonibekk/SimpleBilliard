import React, { Component } from 'react'
import { Provider } from 'react-redux'
import { createStore, combineReducers } from 'redux'
import { Router, Route, IndexRoute,　browserHistory } from 'react-router'
import { syncHistoryWithStore, routerReducer } from 'react-router-redux'
import { createDevTools } from 'redux-devtools'
import LogMonitor from 'redux-devtools-log-monitor'
import DockMonitor from 'redux-devtools-dock-monitor'
import * as reducers from '../reducers'
import { updateSetupStatus } from '../actions/home_actions'

// How do I write this simply?
import Index from '../components/index'
import Top from '../components/top'
import GoalImage from '../components/goal/goal_image'
import PurposeSelect from '../components/goal/purpose_select'
import GoalSelect from '../components/goal/goal_select'
import GoalCreate from '../components/goal/goal_create'
import ProfileImage from '../components/profile/profile_image'
import ProfileAdd from '../components/profile/profile_add'

const DevTools = createDevTools(
  <DockMonitor toggleVisibilityKey="ctrl-h" changePositionKey="ctrl-q">
    <LogMonitor theme="tomorrow" preserveScrollTop={false} />
  </DockMonitor>
)

const reducer = combineReducers({
  reducers,
  routing: routerReducer
})

const store = createStore(
  reducer,
  DevTools.instrument()
)

// initialize store
store.dispatch(updateSetupStatus)

const history = syncHistoryWithStore(browserHistory, store)

// Define setup-guide routes
export default class Routes extends Component {
  render() {
    return (
      <Provider store={store}>
        <div>
          <Router history={history}>
            <Route path="/setup" component={Index} >
              <IndexRoute component={Top} />
              <Route path="goal_image" component={GoalImage} />
              <Route path="purpose_select" component={PurposeSelect} />
              <Route path="goal_select" component={GoalSelect} />
              <Route path="goal_create" component={GoalCreate} />
              <Route path="profile_image" component={ProfileImage} />
              <Route path="profile_add" component={ProfileAdd} />
            </Route>
          </Router>
          <DevTools />
        </div>
      </Provider>
    );
  }
};
