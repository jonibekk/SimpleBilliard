import React from 'react'
import {nl2br} from '~/util/element'

export class Comment extends React.Component {
  render() {
    const comment = this.props.comment

    return (
      <div className="goals-approval-detail-comments-comment" key={ comment.id }>
        <p className="goals-approval-detail-info-name" key={ comment.user.display_username }>{ comment.user.display_username }</p>
        <p className="goals-approval-detail-info-important-and-clear-word" key={ comment.clear_and_important_word }>{ comment.clear_and_important_word }</p>
        <p>{ nl2br(comment.comment) }</p>
      </div>
     )
  }
}

Comment.propTypes = {
  comment: React.PropTypes.object.isRequired
}
