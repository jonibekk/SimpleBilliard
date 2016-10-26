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
    // TODO:delete
    console.log("■GoalCard render")
    console.log({props})

    const {goal} = this.props
    if (!goal) {
      return null
    }

    // TODO:delete
    console.log("follow_btn el create")

    const follow_btn = (
      <a href="#" className="btn btn-white-radius"
         onClick={(e) => this.follow(e, goal.id)}>
        <span className>{__("Follow")}</span>
      </a>
    )

    // TODO:delete
    console.log("unfollow_btn el create")

    const unfollow_btn = (
      <a href="#" className="btn btn-white-radius active"
         onClick={(e) => this.unfollow(e, goal.id)}>
        <span className>{__("Following")}</span>
      </a>
    )


    // TODO:delete
    console.log("render")

    return (
      <div className="panel-block bd-b-sc4">
        <div className="row">
          test
        </div>
      </div>
    )
  }
}

GoalCard.propTypes = {
  goal: React.PropTypes.object,
}

export default connect()(GoalCard);
