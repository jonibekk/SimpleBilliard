import React from 'react'
import { Link } from 'react-router'

export class UserCard extends React.Component {
  render() {
    return (
      <div className="goals-approval-detail-user">
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src="" alt="" width="32" height="32" />
              <div className="goals-approval-detail-info">
                  <p>name</p>
                  <p>position</p>
              </div>
          </div>
      </div>
    )
  }
}

UserCard.propTypes = {
}
