import React from 'react'
import { Comment } from './detail_comment'

export class Comments extends React.Component {
  render() {
    return (
      <div>
        { this.props.comments.map((comment) => {
          return (
            <Comment comment={ comment } />
          )
        })}
      </div>
    )
  }
}

Comments.propTypes = {
}
