import React, { PropTypes } from 'react'
import { Link } from 'react-router'

export default class ActionGoalSelect extends React.Component {
  constructor(props) {
    super(props);
  }
  componentWillMount() {
    this.props.fetchGoals()
  }
  getGoals() {
    return this.props.action.goals
  }
  render() {
    const goals = this.getGoals().map((goal) => {
      return (
        <div className="setup-items-item pt_10px mt_16px bd-radius_14px"
             key={goal.id}
             onClick={(e) => { this.props.onClickSelectActionGoal(goal.Goal.id) }}>
          <div className="setup-items-item-pic pull-left mt_3px ml_2px">
            <img src={goal.Goal.photo_file_name} className="setup-items-item-pic-img lazy" alt='' />
          </div>
          <div className="setup-items-item-explain pull-left">
            <p className="font_bold font_verydark">{goal.Goal.name}</p>
          </div>
          <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
            <i className="fa fa-chevron-right font_18px"></i>
          </div>
        </div>
      )
    })
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__("Create a goal")}
        </div>
        <div className="setup-items">
          {goals}
        </div>
        <div className="mb_13px">
          <Link to="/setup/goal/create">{__('Create another goal')} <i className="fa fa-angle-right" aria-hidden="true"></i> </Link>
        </div>
        <div>
          <Link to="/setup/" className="btn btn-secondary setup-back-btn-full">{__('Back')}</Link>
        </div>
      </div>
    )
  }
}
