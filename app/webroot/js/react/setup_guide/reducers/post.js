import { CAN_SUBMIT_POST, CAN_NOT_SUBMIT_POST, SELECT_CIRCLE_FOR_POST, FETCH_CIRCLES_FOR_POST, FETCH_FILE_UPLOAD_ELEMENT, SET_VALIDATION_ERRORS } from '../constants/ActionTypes'

const initialState = {
  circles: [],
  selected_circle: {},
  file_upload_html: '',
  can_click_submit_button: false,
  validation_errors: []
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
    case CAN_SUBMIT_POST:
      return Object.assign({}, state, {
        can_click_submit_button: true
      })
    case CAN_NOT_SUBMIT_POST:
      return Object.assign({}, state, {
        can_click_submit_button: false
      })
    case SET_VALIDATION_ERRORS:
      return Object.assign({}, state, {
        validation_errors: action.validation_errors
      })
    default:
      return state;
  }
}
