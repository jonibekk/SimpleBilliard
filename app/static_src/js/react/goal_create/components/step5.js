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

  toggleGroup(groupId) {
    const {groups} = this.props.goal.inputData;

    if (groups[groupId]) {
      delete groups[groupId]
    } else {
      groups[groupId] = true
    }
    
    this.props.updateInputData(groups, 'groups')
  }

  render() {
    const {groups, inputData} = this.props.goal;
    const title = "Let's set up a group to publish to.";
    const description = "Which groups will you share this group with?";
    const description2 = "Please select one or more";
    const canSubmit = Object.keys(inputData.groups).length > 0;

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-create">
        <h1 className="goals-create-heading">{__(title)}</h1>
        <p className="goals-create-description">{__(description)}</p>
        <p className="goals-create-description">{__(description2)}</p>
        <form className="goals-create-input" onSubmit={(e) => this.handleSubmit(e) }>
          <div className="goals-create-list">
            {
              groups ?  groups.map(group => {
                return(
                  <div className="goals-create-list-item" ref={`group-item-${group.id}`}>
                    <div className='left'>
                      <input 
                        type="checkbox" 
                        className="goal-create-checkbox" 
                        onChange={() => {this.toggleGroup(group.id)}}
                        checked={inputData.groups[group.id] === true}
                      />
                    </div>
                    <div className='right'>
                      <div className="goals-create-list-item-title">
                        {group.name}
                      </div>
                      <div className="goals-create-list-item-subtitle">
                        {group.member_count} members
                      </div>
                    </div>
                  </div>
                )
              }) : null
            }
          </div>
          <button type="submit" className="goals-create-btn-next btn" disabled={!canSubmit}>
            {__("Save and share")}
          </button>
          <Link className="goals-create-btn-cancel btn" to={Page.URL_STEP4}>{__("Back")}</Link>
        </form>
      </section>
    )
  }
}
