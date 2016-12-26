import * as types from '../constants/ActionTypes'
import {post} from "../../util/api"
import axios from "axios"

export function fetchGoalMember(goal_member_id) {
  return dispatch => {
    return axios.get(`/api/v1/goal_approvals/detail?goal_member_id=${goal_member_id}`)
      .then((response) => {
        /* eslint-disable no-console */
        console.log('fetch success')
        /* eslint-enable no-console */
        dispatch(setGoalMember(response.data.data))
      })
      .catch((error) => {
        /* eslint-disable no-console */
        console.log(error)
        console.log('fetch failed')
        /* eslint-enable no-console */
        dispatch(toTopPage())
      })
  }
}

export function postSetAsTarget(post_data) {
  return (dispatch) => {
    dispatch(postingSetAsTarget())

    return post(`/api/v1/goal_approvals/set_as_target`, post_data, null,
      () => {
        /* eslint-disable no-console */
        console.log('validate success')
        /* eslint-enable no-console */
        dispatch(finishedPostingSetAsTarget())
        dispatch(toListPage())
      },
      (response) => {
        dispatch(finishedPostingSetAsTarget())
        // バリデーションエラーならエラー文言表示、他の原因によるものならリストページにリダイレクト
        if (response.data.validation_errors) {
          /* eslint-disable no-console */
          console.log("validate failed");
          /* eslint-enable no-console */
          dispatch(invalid(response.data.data))
        } else {
          dispatch(toListPage())
        }
      }
    );
  }
}

export function postRemoveFromTarget(post_data) {
  return (dispatch) => {
    dispatch(postingRemovefromTarget())

    return post(`/api/v1/goal_approvals/remove_from_target`, post_data, null,
      () => {
        /* eslint-disable no-console */
        console.log('validate success')
        /* eslint-enable no-console */
        dispatch(finishedPostingRemoveFromTarget())
        dispatch(toListPage())
      },
      (response) => {
        dispatch(finishedPostingSetAsTarget())
        // バリデーションエラーならエラー文言表示、他の原因によるものならリストページにリダイレクト
        if (response.data.validation_errors) {
          /* eslint-disable no-console */
          console.log("validate failed");
          /* eslint-enable no-console */
          dispatch(invalid(response.data.data))
        } else {
          dispatch(toListPage())
        }
      }
    );
  }
}

export function postWithdraw(goal_member_id) {
  return (dispatch) => {
    dispatch(postingWithdraw())

    return post(`/api/v1/goal_approvals/withdraw`, {goal_member: {id: goal_member_id}}, null,
      () => {
        /* eslint-disable no-console */
        console.log('post withdraw success')
        /* eslint-enable no-console */
        dispatch(finishedPostingWithdraw())
        dispatch(toListPage())
      },
      (response) => {
        dispatch(finishedPostingWithdraw())
        /* eslint-disable no-console */
        console.log("failed to withdraw");
        console.log(response);
        /* eslint-enable no-console */
        dispatch(toListPage())
      }
    );
  }
}

export function postComment(postData) {
  return (dispatch) => {
    dispatch(postingComment())

    return post(`/api/v1/goal_approvals/comment`, postData, null,
      (response) => {
        /* eslint-disable no-console */
        console.log('post comment success')
        /* eslint-enable no-console */
        dispatch(finishedPostingComment())
        dispatch(addComment(response.data.data.approval_history))
        dispatch(updateComment(''))
      },
      (response) => {
        dispatch(finishedPostingComment())
        /* eslint-disable no-console */
        console.log("failed to post comment");
        console.log(response);
        /* eslint-enable no-console */
      }
    );
  }
}

export function setGoalMember(goal_member) {
  return {type: types.SET_GOAL_MEMBER, goal_member}
}

export function postingSetAsTarget() {
  return {type: types.POSTING_SET_AS_TARGET}
}

export function finishedPostingSetAsTarget() {
  return {type: types.FINISHED_POSTING_SET_AS_TARGET}
}

export function toListPage() {
  return {type: types.TO_LIST_PAGE}
}

export function toTopPage() {
  return {type: types.TO_TOP_PAGE}
}

export function postingRemovefromTarget() {
  return {type: types.POSTING_REMOVE_FROM_TARGET}
}
export function postingWithdraw() {
  return {type: types.POSTING_WITHDRAW}
}
export function postingComment() {
  return {type: types.POSTING_COMMENT}
}

export function finishedPostingComment() {
  return {type: types.FINISHED_POSTING_COMMENT}
}

export function finishedPostingRemoveFromTarget() {
  return {type: types.FINISHED_POSTING_REMOVE_FROM_TARGET}
}

export function finishedPostingWithdraw() {
  return {type: types.POSTING_WITHDRAW}
}

export function invalid(error) {
  return {type: types.INVALID, error}
}

export function initDetailPage() {
  return {type: types.INIT_DETAIL_PAGE}
}

export function addComment(comment) {
  return {type: types.ADD_COMMENT, comment}
}

export function updateComment(comment) {
  return {type: types.UPDATE_COMMENT, comment}
}
