import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import list from './list'
import detail from './detail'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      list, detail
    }, {
      routing: routerReducer
    })
  )
}
