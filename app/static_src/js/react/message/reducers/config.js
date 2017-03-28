import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import index from './index'
import search from './search'
import detail from './detail'
import {file_upload} from '~/message/modules/file_upload'
import topic_create from './topic_create'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      detail,
      index,
      search,
      file_upload,
      topic_create,
    }, {
      routing: routerReducer
    })
  )
}
