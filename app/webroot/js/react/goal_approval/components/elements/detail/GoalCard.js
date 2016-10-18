import React from 'react'

export class GoalCard extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const goal = this.props.goal
    const top_key_result = this.props.top_key_result
    const category = this.props.category
    const is_current = this.props.is_current

    if(Object.keys(goal).length == 0) {
      return null
    }

    return (
      <div className={`goals-approval-detail-goal-card ${!is_current && 'previous'}`}>
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src={ goal.small_img_url } alt="" width="32" height="32" />
              <div className="goals-approval-detail-goal-card-info">
                  <p><i className="fa fa-folder-o" aria-hidden="true"></i> { category.name }</p>
                  <p><a href={`/goals/view_info/goal_id:${goal.id}`} className="goals-approval-detail-goal-card-info-link" target="_blank">{ goal.name }</a></p>
                  <div className="goals-approval-detail-goal-card-info-tkr">
                      <h2 className="goals-approval-detail-goal-card-info-tkr-title"><i className="fa fa-key" aria-hidden="true"></i> Top key result</h2>
                      <ul className="goals-approval-detail-goal-card-info-tkr-list">
                          <li>{ top_key_result.name }</li>
                          <li>{ top_key_result.display_value }</li>
                          <li>{ top_key_result.description }</li>
                      </ul>
                  </div>
              </div>
          </div>
      </div>
    )
  }
}

GoalCard.propTypes = {
  goal: React.PropTypes.object,
  is_leader: React.PropTypes.bool,
  is_current: React.PropTypes.bool
}
GoalCard.defaultProps = { goal: {}, is_leader: true, is_current: true };
