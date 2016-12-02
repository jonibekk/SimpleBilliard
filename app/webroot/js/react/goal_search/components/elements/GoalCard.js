import React from "react";
import {connect} from "react-redux";
import * as actions from "~/goal_search/actions/goal_actions";

class GoalCard extends React.Component {
  constructor(props) {
    super(props);
    this.follow = this.follow.bind(this)
    this.unfollow = this.unfollow.bind(this)
  }

  follow(e, goal_id) {
    e.preventDefault()
    this.props.dispatch(
      actions.follow(goal_id)
    )
  }

  unfollow(e, goal_id) {
    e.preventDefault()
    this.props.dispatch(
      actions.unfollow(goal_id)
    )
  }

  render() {
    const {goal} = this.props
    if (!goal) {
      return null
    }

    const follow_btn = (
      <a href="#" className="btn btn-white-radius"
         onClick={(e) => this.follow(e, goal.id)}>
        <span className>{__("Follow")}</span>
      </a>
    )

    const unfollow_btn = (
      <a href="#" className="btn btn-white-radius active"
         onClick={(e) => this.unfollow(e, goal.id)}>
        <span className>{__("Following")}</span>
      </a>
    )


    goal.goal_labels = goal.goal_labels ? goal.goal_labels : []

    return (
      <div className="panel-block bd-b-sc4">
        <div className="row">
          <div className="col-xxs-12">
            <div className="col-xxs-3 col-xs-3">
              <div>
                <div className="row mb_8px">
                  <div className="col-xs-12">
                    <a href={`/goals/view_info/goal_id:${goal.id}`}
                       className="text-align_c d-block"
                       target={cake.is_mb_app ? "_self" : "_blank"}
                    >
                      <img src={goal.medium_img_url} className="img-rounded"
                           style={{width: 48, height: 48}} alt={goal.name}/>
                    </a>
                  </div>
                </div>
                {(() => {
                  if (!goal.can_follow) {
                    return null
                  }
                  return (
                    <div className="row text-align_c ">
                      <div className="col-xs-12">
                        {goal.is_follow ? unfollow_btn : follow_btn}
                      </div>
                    </div>
                  )
                })()}
              </div>
            </div>
            <div className="col-xxs-9 col-xs-9 pl_12px">
              <div className="col-xxs-12 goals-page-card-title-wrapper">
                <a href={`/goals/view_info/goal_id:${goal.id}`}
                   className="goals-page-card-title"
                   target={cake.is_mb_app ? "_self" : "_blank"}
                >
                  <p className="goals-page-card-title-text">
                    <span>{goal.name}</span>
                  </p>
                </a>
              </div>
              <ul className="gl-labels mb_8px">
                {goal.goal_labels.map((v) => {
                  return <li className="gl-labels-item" key={v.id}>
                    <a href={`/goals?labels[]=${v.name}`}
                       target={cake.is_mb_app ? "_self" : "_blank"}
                    >{v.name}</a>
                    </li>
                })}
              </ul>
              <p className="font_lightgray font_12px">{__("Leader")}: {goal.leader.display_username}</p>
              <dl className="gl-goal-info-counts">
                <dt className="gl-goal-info-counts-title"><i className="fa fa-check-circle"/></dt>
                <dd className="gl-goal-info-counts-description">{goal.action_count}</dd>
                <dt className="gl-goal-info-counts-title"><i className="fa fa-key"/></dt>
                <dd className="gl-goal-info-counts-description">{goal.kr_count}</dd>
                <dt className="gl-goal-info-counts-title"><i className="fa fa-heart"/></dt>
                <dd className="gl-goal-info-counts-description">{goal.follower_count}</dd>
                <dt className="gl-goal-info-counts-title"><i className="fa fa-child"/></dt>
                <dd className="gl-goal-info-counts-description">{goal.goal_member_count}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

GoalCard.propTypes = {
  goal: React.PropTypes.object,
}

export default connect()(GoalCard);
