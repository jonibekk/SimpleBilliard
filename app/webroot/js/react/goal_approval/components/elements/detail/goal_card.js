import React from 'react'

export class GoalCard extends React.Component {
  render() {
    if(!this.props.goal) {
      return null
    }
    const goal = this.props.goal
    const is_leader = this.props.is_leader

    return (
      <div className={`goals-approval-detail-goal ${is_leader ? 'mod-bgglay' : '' }`}>
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src={ goal.small_img_url } alt="" width="32" height="32" />
              <div className="goals-approval-detail-info">
                  <p><i className="fa fa-folder-o" aria-hidden="true"></i> { goal.category.name }</p>
                  <p>{ goal.name }</p>
                  <div className="goals-approval-detail-tkr">
                      <h2 className="goals-approval-detail-tkrtitle"><i className="fa fa-key" aria-hidden="true"></i> Top key result</h2>
                      <ul className="goals-approval-detail-tkrlist">
                          <li>{ goal.key_result.name }</li>
                          <li>{ goal.key_result.value }</li>
                          <li>{ goal.key_result.desc }</li>
                      </ul>
                  </div>
                  <a href="" className="goals-approval-detail-tkrlink"><i className="fa fa-angle-down" aria-hidden="true"></i> <span className="goals-approval-interactive-link">View previous</span></a>
              </div>
          </div>
      </div>
    )
  }
}

GoalCard.propTypes = {
  goal: React.PropTypes.object.isRequired,
  is_leader: React.PropTypes.bool.isRequired
}
