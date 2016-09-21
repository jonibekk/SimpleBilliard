import React from 'react'
import { Comment } from './comment'

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
    if(Object.keys(this.props.collaborator).length == 0) {
      return null
    }

    const comments = this.props.collaborator.approval_histories
    const latest_comment = comments.length > 0 ? comments[comments.length - 1] : null
    const commets_execpt_latest_comment = comments.length > 1 ? comments.slice(0, -1) : []

    return (
      <div className="goals-approval-detail-comments">
          {(() => {
            const display_more_view_comments_button = commets_execpt_latest_comment.length > 0 && !this.state.display_all_comments

            if(display_more_view_comments_button) {
              return (
                <a className="goals-approval-detail-view-more-comments">
                  <i className="fa fa-angle-down" aria-hidden="true"></i>
                  <span className="goals-approval-interactive-link">View all { comments.length - 1 } comments</span>
                </a>
              )
            }
          })()}

          {/* 最後から二番目のコメントまで */}
          {
            // commets_execpt_latest_comment.map((comment) => {
            //   return <Comment comment={ comment } />;
            // })
          }

          {/* 最後のコメントのみ */}
          { (() => {
            if(latest_comment) {
              return <Comment comment={ first_view_comment } />;
            }
          })()}
      </div>
    )
  }
}

Comments.propTypes = {
  collaborator: React.PropTypes.object.isRequired
}
