import React from 'react'
import { CoachCard } from '~/goal_approval/components/elements/list/coach_card'
import { CoacheeCard } from '~/goal_approval/components/elements/list/coachee_card'

export default class ListComponent extends React.Component {
  componentWillMount() {
    const is_initialize = true

    this.props.fetchCollaborators(is_initialize)
  }

  render() {
    const data = this.props.list.fetch_data

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">{__("Goal approval list")} <span>({ data.all_approval_count })</span></h1>
          <p className="goals-approval-header-explain">{ __("Evaluation target goals are listed up here.") }</p>
          <p className="goals-approval-header-info">{ data.application_info }</p>
          <ul>
            { data.collaborators.map((collaborator) => {
              if(collaborator.is_mine) {
                return <CoacheeCard collaborator={ collaborator } key={collaborator.id}  />;
              } else {
                return <CoachCard collaborator={ collaborator } key={collaborator.id} />;
              }
            }) }
          </ul>
      </section>
    )
  }
}
ListComponent.propTypes = {
  list: React.PropTypes.object.isRequired,
  fetchCollaborators: React.PropTypes.func.isRequired
}
