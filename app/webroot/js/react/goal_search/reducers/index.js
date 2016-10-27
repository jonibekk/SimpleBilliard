import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import goal_search from './goal_search'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      goal_search
    }, {
      routing: routerReducer
    })
  )
}
