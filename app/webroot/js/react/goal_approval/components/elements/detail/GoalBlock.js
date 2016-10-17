import React from 'react'
import { GoalCard } from "~/goal_approval/components/elements/detail/GoalCard";

export class GoalBlock extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const goal = this.props.goal
    const is_leader = this.props.is_leader

    return (
      <div>
          <GoalCard goal={ { id: goal.id, name: goal.name, small_img_url: goal.small_img_url, modified: goal.modified } }
                    category={goal.category}
                    top_key_result={goal.top_key_result}
                    is_leader={ is_leader } />
          <GoalCard goal={ goal.goal_change_log }
                    category={goal.category}
                    top_key_result={goal.tkr_change_log}
                    is_leader={ is_leader } />
      </div>
    )
  }
}

GoalBlock.propTypes = {
  goal: React.PropTypes.object,
  is_leader: React.PropTypes.bool
}
GoalBlock.defaultProps = { goal: {}, is_leader: true };
