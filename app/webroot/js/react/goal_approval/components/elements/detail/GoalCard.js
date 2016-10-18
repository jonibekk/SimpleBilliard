import React from 'react'

export class GoalCard extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    if(Object.keys(this.props.goal).length == 0) {
      return null
    }

    const goal = this.props.goal
    const top_key_result = this.props.top_key_result
    const category = this.props.category

    return (
      <div className="goals-approval-detail-goal-card" key={ goal.modified + top_key_result.modified }>
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src={ goal.small_img_url } alt="" width="32" height="32" />
              <div className="goals-approval-detail-goal-card-info">
                  <p><i className="fa fa-folder-o" aria-hidden="true"></i> { category.name }</p>
                  <p><a href={`/goals/view_info/goal_id:${goal.id}`} className="goals-approval-detail-goal-card-info-link" target="_blank">{ goal.name }</a></p>
                  <div className="goals-approval-detail-goal-card-info-tkr">
                      <h2 className="goals-approval-detail-goal-card-info-tkr-title"><i className="fa fa-key" aria-hidden="true"></i> Top key result</h2>
                      <ul className="goals-approval-detail-goal-card-info-tkr-list">
                          { top_key_result.name && <li>{ top_key_result.name }</li>}
                          { top_key_result.display_value && <li>{ top_key_result.display_value }</li>}
                          { top_key_result.description && <li>{ top_key_result.description }</li>}
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
  top_key_result: React.PropTypes.object,
  category: React.PropTypes.object
}
GoalCard.defaultProps = { goal: {}, top_key_result: {}, category: {} };
