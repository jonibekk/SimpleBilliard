import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import topic from './topic'
import message from './message'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      topic,
      message
    }, {
      routing: routerReducer
    })
  )
}
