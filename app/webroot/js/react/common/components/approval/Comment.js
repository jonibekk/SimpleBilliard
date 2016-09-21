import React from 'react'

export class Comment extends React.Component {
  render() {
    const comment = this.props.comment

    return (
      <div>
        <p className="goals-approval-detail-info-name">{ comment.user.display_username }</p>
        <p>{ comment.comment }</p>
      </div>
    )
  }
}

Comment.propTypes = {
  comment: React.PropTypes.object.isRequired
}
