import React from 'react'
import {nl2br} from '../../../util/element'

export class Comment extends React.Component {
  render() {
    const comment = this.props.comment

    return (
      <div key={ comment.id }>
        <p className="goals-approval-detail-info-name">{ comment.user.display_username }</p>
        <p>{ nl2br(comment.comment) }</p>
      </div>
    )
  }
}

Comment.propTypes = {
  comment: React.PropTypes.object.isRequired
}
