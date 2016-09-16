import React from 'react'
import { Comment } from './detail_comment'

export class Comments extends React.Component {
  render() {
    const comments = this.props.comments ? this.props.comments : []

    return (
      <div className="goals-approval-detail-comments">
          <a><i className="fa fa-angle-down" aria-hidden="true"></i> <span className="goals-approval-interactive-link">View all 3 comments</span></a>

          <p className="goals-approval-detail-info-name">coach name</p>
          <p>message message message message message message mge message message mage message message message message message message message message message message mee message message message message message message message message message message</p>
          <a><span className="goals-approval-interactive-link-more">more</span></a>
      </div>
    )
  }
}

Comments.propTypes = {
  comments: React.PropTypes.array
}
