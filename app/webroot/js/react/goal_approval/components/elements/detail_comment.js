import React from 'react'
import { Link } from 'react-router'

export class Comment extends React.Component {
  render() {
    return (
      <div>
        <a><i className="fa fa-comments-o" aria-hidden="true"></i> <span className="goals-approval-interactive-link">View all 3 comments</span></a>
        <p>coach name</p>
        <p>title</p>
        <p>message</p>
      </div>
    )
  }
}

Comment.propTypes = {
}
