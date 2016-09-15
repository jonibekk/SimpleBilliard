import React from 'react'
import { Comment } from './detail_comment'

export class Comments extends React.Component {
  render() {
    const comments = this.props.comments ? this.props.comments : []

    return (
      <div className="goals-approval-detail-comments">
          <h2>comments</h2>
          <a><i className="fa fa-comments-o" aria-hidden="true"></i> <span className="goals-approval-interactive-link">View all 3 comments</span></a>
          { comments.map( comment => {
            return (
              <Comment comment={ comment } />
            )
          })}
      </div>
    )
  }
}

Comments.propTypes = {
  comments: React.PropTypes.array
}
