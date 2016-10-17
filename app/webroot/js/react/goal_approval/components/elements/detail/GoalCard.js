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
    const displayed_previous = this.props.displayed_previous

    if(Object.keys(goal).length == 0) {
      return null
    }
    const view_previous_button = () => {
      return (
        <div className="goals-approval-detail-view-previous">
            <a className="goals-approval-detail-view-more-comments" onClick={ this.props.displayPrevious }>
              <i className="fa fa-angle-down" aria-hidden="true"></i>
              <span className="goals-approval-interactive-link"> { __('View Previous') } </span>
            </a>
        </div>
      )
    }

    return (
      <div className={`${!is_current && 'goals-approval-detail-previous'}`}>
          { !is_current && <p className="goals-approval-detail-previous-info">{ __('Previous goal') }</p> }
          <div className={`goals-approval-detail-goal-card ${!is_current && 'previous'}`} >
              <div className="goals-approval-detail-table">
                  <img className="goals-approval-detail-image" src={ goal.small_img_url } alt="" width="32" height="32" />
                  <div className="goals-approval-detail-info">
                      <p><i className="fa fa-folder-o" aria-hidden="true"></i> { category.name }</p>
                      <p><a href={`/goals/view_info/goal_id:${goal.id}`} className="goals-approval-detail-info-goal-link" target="_blank">{ goal.name }</a></p>
                      <div className="goals-approval-detail-tkr">
                          <h2 className="goals-approval-detail-tkrtitle"><i className="fa fa-key" aria-hidden="true"></i> Top key result</h2>
                          <ul className="goals-approval-detail-tkrlist">
                              <li>{ top_key_result.name }</li>
                              <li>{ top_key_result.display_value }</li>
                              <li>{ top_key_result.description }</li>
                          </ul>
                      </div>
                  </div>
              </div>
              { !displayed_previous && is_current && view_previous_button() }
          </div>
      </div>
    )
  }
}

GoalCard.propTypes = {
  goal: React.PropTypes.object,
  is_leader: React.PropTypes.bool,
  is_current: React.PropTypes.bool,
  displayed_previous: React.PropTypes.bool
}
GoalCard.defaultProps = { goal: {}, is_leader: true, is_current: true, displayed_previous: false };
