import React from 'react'
import { Comment } from './detail_comment'

export class Comments extends React.Component {
  render() {
    if(!this.props.collaborator) {
      return null
    }

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
  collaborator: React.PropTypes.object.isRequired
}
