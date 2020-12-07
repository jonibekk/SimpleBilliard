/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {Link} from "react-router";
import * as Page from "../constants/Page";
import Base from "~/common/components/Base";

export default class Step5Component extends Base {
  constructor(props) {
    super(props);
    this.toggleGroup = this.toggleGroup.bind(this)
  }

  componentWillMount() {
    this.props.fetchInitialData(Page.STEP5)
  }

  componentDidMount() {
    super.componentDidMount.apply(this)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.redirect_to_home) {
      super.removeBeforeUnloadHandler.apply(this)
      document.location.href = "/"
    }
  }

  componentWillUnmount() {
    super.componentWillUnmount.apply(this)
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.saveGoal()
  }

  toggleGroup(group) {
    const {groups} = this.props.goal.inputData;
    const {id} = group;

    if (id in groups) {
      delete groups[id]
    } else {
      groups[id] = group
    }
    
    this.props.updateInputData(groups, 'groups')
  } 

  canSelectGroup() {
    const {inputData, groups } = this.props.goal;

    if (!inputData.is_wish_approval) {
      return true;
    } 

    for (const group of groups) {
      if (group.coach_belongs) {
        return true;
      }
    }

    return false;
  }

  canSubmit() {
    const {inputData} = this.props.goal;
    const selectedGroups = Object.values(inputData.groups);

    if (inputData.is_wish_approval) {
      // select at least one group that your coach belongs to
      for (const group of selectedGroups) {
        if (group.coach_belongs) {
          return true;
        }
      }
      return false;

    } else {
      // must select at least 1 group
      return Object.keys(selectedGroups).length > 0;
    }
  }

  render() { 
    const {groups, inputData} = this.props.goal;

    const approvalMsg = "You have specified that you wish to be evaluated based on this goal. You must add a group that your coach belongs to so they can approve it.";
    const noCoachGroupBelongsMsg = "There is no group to which your coach belongs. Please contact team administrators for more information.";
    const canSelectGroup = this.canSelectGroup();

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__("Let's set up a group to publish to.")}</h1>
        <p className="goals-create-description">{__( "Which groups will you share this group with?")}</p>
        <p className="goals-create-description">{__( "Please select one or more")}</p>
        {
          !canSelectGroup ? (
            <p className="goals-create-description goal-create-error">{ __(noCoachGroupBelongsMsg) }</p>
          ) : null
        }
        {
          inputData.is_wish_approval ? (
            <p className="goals-create-description">{__(approvalMsg)}</p>
          ) : null
        }
        <form className="goals-create-input" onSubmit={(e) => this.handleSubmit(e) }>
          <div className="goals-create-list">
            {
              groups.length > 0 ?  groups.map(group => {
                return(
                  <div className="goals-create-list-item" ref={`group-item-${group.id}`}>
                    <div className='left'>
                      <input 
                        type="checkbox" 
                        className="goal-create-checkbox" 
                        onChange={() => {this.toggleGroup(group)}}
                        checked={group.id in inputData.groups}
                        disabled={!canSelectGroup}
                      />
                    </div>
                    <div className='right'>
                      <div className="goals-create-list-item-title">
                        {group.name}
                      </div>
                      <div className="goals-create-list-item-subtitle">
                        {group.member_count} {__("members")}
                        {
                          inputData.is_wish_approval && group.coach_belongs ? (
                            <span className="coach-belongs-tag">
                              {__("Your coach belongs to this group")}
                            </span>
                          ) : null
                        }
                      </div>
                    </div>
                  </div>
                )
              }) : (
                (
                  <div className="no-selectable-groups">
                    <i className="fa fa-frown-o"></i>
                    <p>{__("You do not have a group set up.")}</p>
                    <p>{__("Please contact your team administrator.")}</p>
                  </div>
                )
              )
            }
          </div>
          <button type="submit" className="goals-create-btn-next btn" disabled={!this.canSubmit()}>
            {__("Save and share")}
          </button>
          <Link className="goals-create-btn-cancel btn" to={Page.URL_STEP4}>{__("Back")}</Link>
        </form>
      </section>
    )
  }
}
