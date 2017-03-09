import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import index from './index'
import detail from './detail'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      detail,
      index
    }, {
      routing: routerReducer
    })
  )
}
