import React from 'react'
import { Comment } from '~/common/components/approval/Comment'

export class Comments extends React.Component {
  constructor(props) {
    super(props)

    this.state = { display_all_comments: false }
    this.displayAllComments = this.displayAllComments.bind(this)
  }

  displayAllComments() {
    this.setState({ display_all_comments: true })
  }

  render() {
    if(Object.keys(this.props.approval_histories).length == 0) {
      return null
    }

    const comments = this.props.approval_histories
    const latest_comment = comments.length > 0 ? comments[comments.length - 1] : null
    const commets_execpt_latest_comment = comments.length > 1 ? comments.slice(0, -1) : []
    const display_view_more_comments_button = commets_execpt_latest_comment.length > 0 && !this.state.display_all_comments
    const view_more_comments_button = () => {
      return (
        <a className="goals-approval-detail-view-more-comments" onClick={ this.displayAllComments }>
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

      </div>
    )
  }
}

Comments.propTypes = {
  approval_histories: React.PropTypes.array,
  view_more_text: React.PropTypes.string
}

Comments.defaultProps = { approval_histories: [], view_more_text: ''}
