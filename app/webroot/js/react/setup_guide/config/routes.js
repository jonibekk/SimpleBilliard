import React, { Component } from 'react'
import { Provider } from 'react-redux'
import { createStore, combineReducers } from 'redux'
import { Router, Route, IndexRoute,　browserHistory } from 'react-router'
import { syncHistoryWithStore, routerReducer } from 'react-router-redux'
import { createDevTools } from 'redux-devtools'
import LogMonitor from 'redux-devtools-log-monitor'
import DockMonitor from 'redux-devtools-dock-monitor'
import * as reducers from '../reducers'
import { initSetupStatus } from '../actions/home_actions'

// How do I write this simply?
import GoalContainer from '../containers/goal'
import ProfileContainer from '../containers/profile'
import TopContainer from '../containers/top'
import CircleContainer from '../containers/circle'
import Index from '../components/index'
import GoalImage from '../components/goal/goal_image'
import PurposeSelect from '../components/goal/purpose_select'
import GoalSelect from '../components/goal/goal_select'
import GoalCreate from '../components/goal/goal_create'
import ProfileImage from '../components/profile/profile_image'
import ProfileAdd from '../components/profile/profile_add'
import CircleImage from '../components/circle/circle_image'
import CircleSelect from '../components/circle/circle_select'

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

// dispatch initial data to store
store.dispatch(initSetupStatus())

const history = syncHistoryWithStore(browserHistory, store)

// Define setup-guide routes
export default class Routes extends Component {
  render() {
    return (
      <Provider store={store}>
        <div>
          <Router history={history}>
            <Route path="/setup" component={Index} >
              <IndexRoute component={TopContainer} />
              <Route path="goal" component={GoalContainer} >
                <IndexRoute component={GoalImage} />
                <Route path="image" component={GoalImage} />
                <Route path="purpose_select" component={PurposeSelect} />
                <Route path="select" component={GoalSelect} />
                <Route path="create" component={GoalCreate} />
              </Route>
              <Route path="profile" component={ProfileContainer} >
                <IndexRoute component={ProfileImage} />
                <Route path="image" component={ProfileImage} />
                <Route path="add" component={ProfileAdd} />
              </Route>
              <Route path="circle" component={CircleContainer} >
                <IndexRoute component={CircleImage} />
                <Route path="image" component={CircleImage} />
                <Route path="select" component={CircleSelect} />
              </Route>
            </Route>
          </Router>
          <DevTools />
        </div>
      </Provider>
    );
  }
};
