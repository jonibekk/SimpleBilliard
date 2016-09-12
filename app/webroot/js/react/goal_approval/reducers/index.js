import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import goal_approval from './goal_approval'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      goal_approval
    }, {
      routing: routerReducer
    })
  )
}
