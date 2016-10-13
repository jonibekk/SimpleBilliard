import React from 'react'
import { CoachCard } from '~/goal_approval/components/elements/list/coach_card'
import { CoacheeCard } from '~/goal_approval/components/elements/list/coachee_card'
import { ViewMoreButton } from '~/goal_approval/components/elements/list/view_more_button'

export default class ListComponent extends React.Component {
  componentWillMount() {
    const is_initialize = true

    this.props.fetchCollaborators(is_initialize)
  }

  render() {
    const data = this.props.list

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">{__("Goal approval list")} <span>({ data.all_approval_count })</span></h1>
          <ul>
            { data.collaborators.map((collaborator) => {
              if(collaborator.is_mine) {
                return <CoacheeCard collaborator={ collaborator } key={collaborator.id}  />;
              } else {
                return <CoachCard collaborator={ collaborator } key={collaborator.id} />;
              }
            }) }
          </ul>
          {/* TODO: fetchCollaboratorsを即時間数で囲わないとなぜかコールした際の引数 がtrueになる。要調査。 */}
          { !data.done_loading_all_data ? <ViewMoreButton handleOnClick={ () => this.props.fetchCollaborators() }
                                                                     is_loading={ data.fetching_collaborators } /> : null }
      </section>
    )
  }
}
ListComponent.propTypes = {
  list: React.PropTypes.object.isRequired,
  fetchCollaborators: React.PropTypes.func.isRequired
}
