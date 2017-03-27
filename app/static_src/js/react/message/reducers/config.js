import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import index from './index'
import search from './search'
import detail from './detail'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      detail,
      index,
      search
    }, {
      routing: routerReducer
    })
  )
}
