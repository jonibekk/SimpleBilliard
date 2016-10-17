import React from 'react'
import { GoalCard } from "~/common/components/approval/GoalCard";

export class GoalBlock extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const goal = this.props.goal
    const is_leader = this.props.is_leader

    return (
      <div>
          <GoalCard goal={ goal }
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
