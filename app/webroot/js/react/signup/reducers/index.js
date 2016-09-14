import {
  combineReducers
} from 'redux'
import {
  routerReducer
} from 'react-router-redux'
import auth from './auth'
import password from './password'
import team_name from './team_name'
import term from './term'
import user_name from './user_name'
import validate from './validate'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      auth,
      password,
      team_name,
      term,
      user_name,
      validate
    }, {
      routing: routerReducer
    })
  )
}
