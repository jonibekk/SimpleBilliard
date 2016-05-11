import { SELECT_CIRCLE_FOR_POST, FETCH_CIRCLES_FOR_POST, FETCH_FILE_UPLOAD_ELEMENT } from '../constants/ActionTypes'

const initialState = {
  circles: [],
  selected_circle: {},
  file_upload_html: ''
}

export default function post(state = initialState, action) {
  switch (action.type) {
    case SELECT_CIRCLE_FOR_POST:
      return Object.assign({}, state, {
        selected_circle: action.selected_circle
      })
    case FETCH_CIRCLES_FOR_POST:
      return Object.assign({}, state, {
        circles: action.circles
      })
    case FETCH_FILE_UPLOAD_ELEMENT:
      return Object.assign({}, state, {
        file_upload_html: action.file_upload_html
      })
    default:
      return state;
  }
}
