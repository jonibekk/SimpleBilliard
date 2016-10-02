import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import goal from './goal'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      goal
    }, {
      routing: routerReducer
    })
  )
}
