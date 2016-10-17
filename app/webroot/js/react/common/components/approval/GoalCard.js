import React from 'react'

export class GoalCard extends React.Component {
  constructor(props) {
    super(props)

    this.state = { display_previous: false }
    this.displayPrevious = this.displayPrevious.bind(this)
  }

  displayPrevious() {
    this.setState({ display_previous: true })
  }

  render() {
    const goal = this.props.goal
    const is_leader = this.props.is_leader

    if(Object.keys(goal).length == 0) {
      return null
    }
    const view_previous_button = () => {
      return (
        <div className="goals-approval-detail-view-previous">
            <a className="goals-approval-detail-view-more-comments" onClick={ this.displayPrevious }>
              <i className="fa fa-angle-down" aria-hidden="true"></i>
              <span className="goals-approval-interactive-link"> { __('View Previous') } </span>
            </a>
        </div>
      )
    }

    return (
      <div className={`goals-approval-detail-goal ${is_leader ? 'mod-bgglay' : '' }`} >
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src={ goal.small_img_url } alt="" width="32" height="32" />
              <div className="goals-approval-detail-info">
                  <p><i className="fa fa-folder-o" aria-hidden="true"></i> { goal.category.name }</p>
                  <p><a href={`/goals/view_info/goal_id:${goal.id}`} target="_blank">{ goal.name }</a></p>
                  <div className="goals-approval-detail-tkr">
                      <h2 className="goals-approval-detail-tkrtitle"><i className="fa fa-key" aria-hidden="true"></i> Top key result</h2>
                      <ul className="goals-approval-detail-tkrlist">
                          <li>{ goal.top_key_result.name }</li>
                          <li>{ goal.top_key_result.display_value }</li>
                          <li>{ goal.top_key_result.description }</li>
                      </ul>
                  </div>
              </div>
          </div>
          { !this.state.display_previous && view_previous_button() }
      </div>
    )
  }
}

GoalCard.propTypes = {
  goal: React.PropTypes.object,
  is_leader: React.PropTypes.bool
}
GoalCard.defaultProps = { goal: {}, is_leader: true };
