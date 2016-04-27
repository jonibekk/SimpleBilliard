import React, { Component } from 'react'
import { Provider } from 'react-redux'
import { createStore, combineReducers } from 'redux'
import { Router, Route, IndexRoute,　browserHistory } from 'react-router'
import { syncHistoryWithStore } from 'react-router-redux'
import { createDevTools } from 'redux-devtools'
import LogMonitor from 'redux-devtools-log-monitor'
import DockMonitor from 'redux-devtools-dock-monitor'
import createReducer from '../reducers/index'
import { fetchCircles } from '../actions/circle_actions'

// How do I write this simply?
import GoalContainer from '../containers/goal'
import AppContainer from '../containers/app'
import TopContainer from '../containers/top/top'
import Index from '../components/index'
import GoalImage from '../components/goal/goal_image'
import PurposeSelect from '../components/goal/purpose_select'
import GoalSelect from '../components/goal/goal_select'
import GoalCreate from '../components/goal/goal_create'

import ProfileContainer from '../containers/profile/index'
import ProfileImageContainer from '../containers/profile/profile_image'
import ProfileAddContainer from '../containers/profile/profile_add'

import CircleContainer from '../containers/circle/index'
import CircleImageContainer from '../containers/circle/circle_image'
import CircleSelectContainer from '../containers/circle/circle_select'
import CircleCreateContainer from '../containers/circle/circle_create'

import AppImage from '../components/app/app_image'
import AppSelect from '../components/app/app_select'

const DevTools = createDevTools(
  <DockMonitor toggleVisibilityKey="ctrl-h" changePositionKey="ctrl-q">
    <LogMonitor theme="tomorrow" preserveScrollTop={false} />
  </DockMonitor>
)

const reducer = createReducer()

const store = createStore(
  reducer,
  DevTools.instrument()
)

// Init circle list for circle select page
fetchCircles(store.dispatch)

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
                <IndexRoute component={ProfileImageContainer} />
                <Route path="image" component={ProfileImageContainer} />
                <Route path="add" component={ProfileAddContainer} />
              </Route>
              <Route path="circle" component={CircleContainer} >
                <IndexRoute component={CircleImageContainer} />
                <Route path="image" component={CircleImageContainer} />
                <Route path="select" component={CircleSelectContainer} />
                <Route path="create" component={CircleCreateContainer} />
              </Route>
              <Route path="app" component={AppContainer} >
                <IndexRoute component={AppImage} />
                <Route path="image" component={AppImage} />
                <Route path="select" component={AppSelect} />
              </Route>
            </Route>
          </Router>
          <DevTools />
        </div>
      </Provider>
    );
  }
}
