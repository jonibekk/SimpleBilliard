import { combineReducers } from 'redux'
import { routerReducer } from 'react-router-redux'
import index from './index'
import search from './search'
import detail from './detail'
import {file_upload} from '~/message/modules/file_upload'
import topic_create from './topic_create'
import topic_members_add from './topic_members_add'

export default function createReducer() {
  return combineReducers(
    Object.assign({}, {
      detail,
      index,
      search,
      topic_create,
      topic_members_add,
      file_upload,
    }, {
      routing: routerReducer
    })
  )
}
