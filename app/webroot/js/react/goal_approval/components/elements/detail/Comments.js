import React from 'react'
import ReactDOM from "react-dom";
import Textarea from "react-textarea-autosize";
import { Comment } from "~/goal_approval/components/elements/detail/Comment";

export class Comments extends React.Component {
  constructor(props) {
    super(props)

    this.state = { display_all_comments: false, comment: '' }
  }

  displayAllComments() {
    this.setState({ display_all_comments: true })
  }

  onChange() {
    const comment = ReactDOM.findDOMNode(this.refs.comment).value.trim()

    this.setState({ comment })
  }

  onSubmit() {
    const post_data = {}

    this.props.postComment(post_data)
  }

  render() {
    if(Object.keys(this.props.approval_histories).length == 0) {
      return null
    }

    const comments = this.props.approval_histories
    const latest_comment = comments.length > 0 ? comments[comments.length - 1] : null
    const commets_execpt_latest_comment = comments.length > 1 ? comments.slice(0, -1) : []
    const add_comments = this.props.add_comments
    const display_view_more_comments_button = commets_execpt_latest_comment.length > 0 && !this.state.display_all_comments
    const view_more_comments_button = () => {
      return (
        <a className="goals-approval-detail-view-more-comments" onClick={ this.displayAllComments.bind(this) }>
          <i className="fa fa-angle-down" aria-hidden="true"></i>
          <span className="goals-approval-interactive-link"> { this.props.view_more_text } </span>
        </a>
      )
    }

    return (
      <div className="goals-approval-detail-comments">
          { display_view_more_comments_button ? view_more_comments_button() : null}

          {/* 最新のコメント以外すべて */}
          { this.state.display_all_comments ? commets_execpt_latest_comment.map((comment) => {
            return <Comment comment={ comment } />;
          }) : null}

          {/* 最新のコメント */}
          { latest_comment ? <Comment comment={ latest_comment } /> : null }

          {/* ページ表示後に投稿されたコメント */}
          { add_comments.map((comment) => {
            return <Comment comment={ comment } />;
          })}

          {/* コメント投稿ボックス */}
          <div className="goals-approval-detail-comments-form">
            <form onSubmit={ this.onSubmit }>
              <div className="goals-approval-detail-comments-form-textarea">
                <Textarea className="form-control" rows={1} placeholder={__("Add your comment")} ref="comment" onChange={ this.onChange.bind(this) }></Textarea>
              </div>
              <div className="goals-approval-detail-comments-form-submit">
                <input
                  className="btn goals-approval-detail-comments-form-submit-button"
                  disabled={`${this.props.posting || !this.state.comment ? "disabled" : ""}`}
                  type="submit"
                  value={__("Send")} />
              </div>
            </form>
          </div>
      </div>
    )
  }
}

Comments.propTypes = {
  approval_histories: React.PropTypes.array,
  view_more_text: React.PropTypes.string,
  add_comments: React.PropTypes.array,
  posting: React.PropTypes.bool
}

Comments.defaultProps = { approval_histories: [], view_more_text: '', add_comments: []}
