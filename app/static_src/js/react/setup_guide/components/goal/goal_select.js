import React, { PropTypes } from 'react'
import { Link, browserHistory } from 'react-router'

export default class GoalSelect extends React.Component {
  constructor(props) {
    super(props);
  }
  goalList() {
    if(this.props.goal.selected_purpose.id == 1) {
      return ([
        {
          id: 1,
          pic_url: cake.url.route_url + '/img/setup/1_1_conversation.png',
          name: __("Talk with team members"),
          value_unit: 1,
          start_value: 0,
          target_value: 5
        },
        {
          id: 2,
          pic_url: cake.url.route_url + '/img/setup/1_2_lunch.png',
          name: __("Lunch with team members"),
          value_unit: 1,
          start_value: 0,
          target_value: 5
        },
        {
          id: 3,
          pic_url: cake.url.route_url + '/img/setup/1_3_complaint.png',
          name: __("Hear complaints of team members"),
          value_unit: 1,
          start_value: 0,
          target_value: 5
        }
      ])
    } else if (this.props.goal.selected_purpose.id == 2) {
      return ([
        {
          id: 4,
          pic_url: cake.url.route_url + '/img/setup/2_1_column.png',
          name: __("Writing working columns"),
          value_unit: 1,
          start_value: 0,
          target_value: 5
        },
        {
          id: 5,
          pic_url: cake.url.route_url + '/img/setup/2_2_food.png',
          name: __("Sharing your lovely foods"),
          value_unit: 1,
          start_value: 0,
          target_value: 5
        },
        {
          id: 6,
          pic_url: cake.url.route_url + '/img/setup/2_3_idea.png',
          name: __("Writing your insistence"),
          value_unit: 1,
          start_value: 0,
          target_value: 5
        }
      ])
    } else {
      return ([
        {
          id: 7,
          pic_url: cake.url.route_url + '/img/setup/3_1_spirit.png',
          name: __("Embodying the orgainization motto"),
          value_unit: 1,
          start_value: 0,
          target_value: 5
        },
        {
          id: 8,
          pic_url: cake.url.route_url + '/img/setup/3_2_prise.png',
          name: __("Prasing someone"),
          value_unit: 1,
          start_value: 0,
          target_value: 5
        },
        {
          id: 9,
          pic_url: cake.url.route_url + '/img/setup/3_3_improve.png',
          name: __("Including your orgainization improvements"),
          value_unit: 1,
          start_value: 0,
          target_value: 5
        }
      ])
    }
  }
  render() {
    const goals = this.goalList().map((goal) => {
      return (
        <div className="setup-items-item pt_10px mt_16px bd-radius_14px"
             key={goal.id}
             onClick={(e) => {
               this.props.onClickSelectGoal(goal)
               browserHistory.push('/setup/goal/create')
             }}>
          <div className="setup-items-item-pic pull-left mt_3px ml_2px">
            <img src={goal.pic_url} className="setup-items-item-pic-img" alt='' />
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
        <p className="setup-items-header-comment">{__("Please choose one.")}</p>
        <div className="setup-items">
          {goals}
        </div>
        <div className="mb_12px">
          <Link to="/setup/goal/create"
                onClick={() => { this.props.initSelectedGoalData() }}>
            {__('Create your own')} <i className="fa fa-angle-right" aria-hidden="true"></i>
          </Link>
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
