import React from 'react'
import { Link } from 'react-router'
import { Comment } from './elements/detail_comment'
import { GoalCard } from './elements/detail_goal_card'
import { UserCard } from './elements/detail_user_card'
import { ApproveSubmitArea } from './elements/detail_submit_area'

export default class DetailComponent extends React.Component {

  componentWillMount() {
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">Confirm this goal</h1>
          <UserCard />
          <GoalCard />
          <div className="goals-approval-detail-comments">
              <h2>comments</h2>
              <Comment />
          </div>
          <ApproveSubmitArea />
      </section>

    )
  }
}
