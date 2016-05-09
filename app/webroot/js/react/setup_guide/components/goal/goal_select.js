import React, { PropTypes } from 'react'
import { Link } from 'react-router'

export default class GoalSelect extends React.Component {
  constructor(props) {
    super(props);
  }
  goalList() {
    return ([
      {
        id: 1,
        pic: '/img/setup/sample_men.png',
        name: __("Talk with team members")
      },
      {
        id: 2,
        pic: '/img/setup/sample_men.png',
        name: __("Lunch with team members")
      },
      {
        id: 3,
        pic: '/img/setup/sample_men.png',
        name: __("Hear complaints of team members")
      }
    ])
  }
  render() {
    const goals = this.goalList().map((goal) => {
      return (
        <div className="setup-items-item pt_10px mt_16px bd-radius_14px"
             key={goal.id}
             onClick={(e) => { this.props.onClickSelectGoal(goal.name) }}>
          <div className="setup-items-item-pic pull-left mt_3px ml_2px">
            <img src={goal.pic} className="setup-items-item-pic-img" alt='' />
          </div>
          <div className="setup-items-item-explain pull-left">
            <p className="font_bold font_verydark">{goal.name}</p>
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
          <Link to="/setup/goal/create">{__('Create your own')} <i className="fa fa-angle-right" aria-hidden="true"></i> </Link>
        </div>
        <div>
          <Link to="/setup/goal/purpose_select" className="btn btn-secondary setup-back-btn-full">{__('Back')}</Link>
        </div>
      </div>
    )
  }
}

GoalSelect.propTypes = {
  onClickSelectGoal: PropTypes.func
}
