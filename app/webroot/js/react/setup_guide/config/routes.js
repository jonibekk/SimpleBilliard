/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
/* eslint-enable no-unused-vars */
import { Provider } from 'react-redux'
import { createStore } from 'redux'
import { Router, Route, IndexRoute,　browserHistory } from 'react-router'
import { syncHistoryWithStore } from 'react-router-redux'
import { createDevTools } from 'redux-devtools'
import LogMonitor from 'redux-devtools-log-monitor'
import DockMonitor from 'redux-devtools-dock-monitor'
import createReducer from '../reducers/index'

// How do I write this simply?
import TopContainer from '../containers/top/top'
import Index from '../components/index'
// Profile
import ProfileContainer from '../containers/profile/index'
import ProfileImageContainer from '../containers/profile/profile_image'
import ProfileAddContainer from '../containers/profile/profile_add'
// Goal
import GoalContainer from '../containers/goal/index'
import GoalImageContainer from '../containers/goal/goal_image'
import PurposeSelectContainer from '../containers/goal/purpose_select'
import GoalSelectContainer from '../containers/goal/goal_select'
import GoalCreateContainer from '../containers/goal/goal_create'
// Circle
import CircleContainer from '../containers/circle/index'
import CircleImageContainer from '../containers/circle/circle_image'
import CircleSelectContainer from '../containers/circle/circle_select'
import CircleCreateContainer from '../containers/circle/circle_create'
// Action
import ActionContainer from '../containers/action/index'
import ActionImageContainer from '../containers/action/action_image'
import ActionGoalSelectContainer from '../containers/action/action_goal_select'
import ActionCreateContainer from '../containers/action/action_create'
// App
import AppContainer from '../containers/app/index'
import AppImageContainer from '../containers/app/app_image'
import AppSelectContainer from '../containers/app/app_select'
// Post
import PostContainer from '../containers/post/index'
import PostImageContainer from '../containers/post/post_image'
import PostCircleSelectContainer from '../containers/post/post_circle_select'
import PostCreateContainer from '../containers/post/post_create'

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

const history = syncHistoryWithStore(browserHistory, store)

export const unlisten = history.listen(location => {
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
            <Route path="/setup" component={Index} >
              <IndexRoute component={TopContainer} />
              <Route path="top" component={TopContainer} />
              <Route path="goal" component={GoalContainer} >
                <IndexRoute component={GoalImageContainer} />
                <Route path="image" component={GoalImageContainer} />
                <Route path="purpose_select" component={PurposeSelectContainer} />
                <Route path="select" component={GoalSelectContainer} />
                <Route path="create" component={GoalCreateContainer} />
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
              <Route path="action" component={ActionContainer} >
                <IndexRoute component={ActionImageContainer} />
                <Route path="image" component={ActionImageContainer} />
                <Route path="goal_select" component={ActionGoalSelectContainer} />
                <Route path="create" component={ActionCreateContainer} />
              </Route>
              <Route path="post" component={PostContainer} >
                <IndexRoute component={PostImageContainer} />
                <Route path="image" component={PostImageContainer} />
                <Route path="circle_select" component={PostCircleSelectContainer} />
                <Route path="create" component={PostCreateContainer} />
              </Route>
              <Route path="app" component={AppContainer} >
                <IndexRoute component={AppImageContainer} />
                <Route path="image" component={AppImageContainer} />
                <Route path="select" component={AppSelectContainer} />
              </Route>
            </Route>
          </Router>
          {/*<DevTools />*/}
        </div>
      </Provider>
    );
  }
}
