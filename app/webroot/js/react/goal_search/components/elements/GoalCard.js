import React from "react";

export default class GoalCard extends React.Component {
  render() {
    const {goal} = this.props
    if (!goal) {
      return null
    }

    return (

      <div className="panel-block bd-b-sc4">
        <div className="row">
          <div className="col-xxs-12">
            <div className="col-xxs-3 col-xs-2">
              <a href={`/goals/view_info/goal_id:${goal.id}`}>
                <img src="/img/no-image-goal.jpg" className="lazy img-rounded"
                     style={{width: 48, height: 48, display: 'inline'}} data-original={goal.medium_img_url}
                     alt={goal.name}/></a>
            </div>
            <div className="col-xxs-9 col-xs-10">
              <div className="col-xxs-12 goals-page-card-title-wrapper">
                <a href={`/goals/view_info/goal_id:${goal.id}`} className="goals-page-card-title">
                  <p className="goals-page-card-title-text">
                    <span>{goal.name}</span>
                  </p>
                </a>
              </div>
              <ul className="gl-labels mb_8px">
                {goal.goal_labels.map((v) => {
                  return <li className="gl-labels-item" key={v.id}>{v.name}</li>
                })}
              </ul>
              <p className="font_lightgray font_12px">リーダー: {goal.leader.display_username}</p>
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
              <div className="col-xxs-12 ptb_8px">
                <div className="col-xxs-6 col-xs-4">
                  <a className={`btn btn-white-radius ${goal.is_follow && "active"}`} href="#" data-class="toggle-follow" goal-id={1343}>
                    <i className={`fa fa-heart font_rougeOrange mr_4px ${goal.is_follow && "hide"}`} />
                    <span className>{goal.is_follow ? "フォロー中" : "フォロー"}</span>
                  </a>
                </div>
                <div className="col-xxs-6 col-xs-4">
                  <a className={`btn btn-white-radius ${goal.is_member && "active"}`} data-toggle="modal" data-target="#ModalCollabo_1343"
                     href="/goals/ajax_get_collabo_change_modal/goal_id:1343">
                    <i className={`fa fa-child font_rougeOrange mr_4px ${goal.is_member && "hide"}`}/>
                    <span className>{goal.is_member ? "コラボり中" : "コラボる"}</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

GoalCard.propTypes = {
  goal: React.PropTypes.object.isRequired,
}
