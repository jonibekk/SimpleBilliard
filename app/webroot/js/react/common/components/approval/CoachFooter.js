import React from 'react'
import ReactDOM from "react-dom"
import { Link } from 'react-router'
import * as TopKeyResult from '../../constants/TopKeyResult'
import InvalidMessageBox from "../InvalidMessageBox";

export class CoachFooter extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      clear_or_not: null,
      important_or_not: null
    }
  }

  getInputDomData() {
    return {
      comment: ReactDOM.findDOMNode(this.refs.comment).value.trim()
    }
  }

  handleClickClear() {
    this.setState({ clear_or_not: TopKeyResult.IS_CLEAR })
  }

  handleClickNotClear() {
    this.setState({ clear_or_not: TopKeyResult.IS_NOT_CLEAR })
  }

  handleClickImportant() {
    this.setState({ important_or_not: TopKeyResult.IS_IMPORTANT })
  }

  handleClickNotImportant() {
    this.setState({ important_or_not: TopKeyResult.IS_NOT_IMPORTANT })
  }

  handleSubmit(e) {
    e.preventDefault()
  }

  handleSubmitSetAsTarget() {
    this.props.handlePostSetAsTarget(this.getInputDomData())
  }

  handleSubmitRemoveFromTarget() {
    const post_data = Object.assign({}, this.getInputDomData(), {
      clear_or_not: this.state.clear_or_not,
      important_or_not: this.state.important_or_not
    })

    this.props.handlePostRemoveFromTarget(post_data)
  }

  render() {
    const can_submit_set_as_target = this.state.clear_or_not === TopKeyResult.IS_CLEAR && this.state.important_or_not === TopKeyResult.IS_IMPORTANT
    const can_submit_remove_from_target = this.state.clear_or_not !== null && this.state.important_or_not !== null
    const validation_errors = this.props.validationErrors
    const loading_image = () => <img src="/img/ajax-loader.gif" className="signup-img-loader" />;

    return (
      <div className="goals-approval-detail-choice">
          <form onSubmit={ this.handleSubmit.bind(this) }>
              <label className="goals-approval-input-label" htmlFor="">Do you think this tKR is clear?</label>
              <a className={`btn ${this.state.clear_or_not === TopKeyResult.IS_CLEAR ? 'goals-approval-btn-choiced' : 'goals-approval-btn-choice'}`} htmlFor="" onClick={ this.handleClickClear.bind(this) }>Clear</a>
              <a className={`btn ${this.state.clear_or_not === TopKeyResult.IS_NOT_CLEAR ? 'goals-approval-btn-choiced' : 'goals-approval-btn-choice'}`} htmlFor="" onClick={ this.handleClickNotClear.bind(this) }>Not Clear</a>

              <label className="goals-approval-input-label" htmlFor="">Do you think that tKR is the most important to achieve the goal?</label>
              <a className={`btn ${this.state.important_or_not === TopKeyResult.IS_IMPORTANT ? 'goals-approval-btn-choiced' : 'goals-approval-btn-choice'}`} htmlFor="" onClick={ this.handleClickImportant.bind(this) }>Yes</a>
              <a className={`btn ${this.state.important_or_not === TopKeyResult.IS_NOT_IMPORTANT ? 'goals-approval-btn-choiced' : 'goals-approval-btn-choice'}`} htmlFor="" onClick={ this.handleClickNotImportant.bind(this) }>No</a>

              <label className="goals-approval-input-label" htmlFor="">Judge this goal to set as target of evaluations.</label>
              <textarea className="form-control goals-approval-detail-input-comment-form" name="comment" ref="comment" id="comment" cols="30" rows="2" placeholder="Add your comment (optional)"></textarea>
              <InvalidMessageBox message={validation_errors.comment}/>
              <div className="goals-approval-detail-choice">
                  { this.props.posting_set_as_target ? loading_image() : '' }
                  <button className={`btn ${can_submit_set_as_target ? 'goals-approval-btn-active' : 'goals-approval-btn-nonactive'}`}
                          type="submit"
                          onClick={ this.handleSubmitSetAsTarget.bind(this) }>Set as target</button>
                  { this.props.posting_remove_from_target ? loading_image() : '' }
                  <button className={`btn ${can_submit_remove_from_target ? 'goals-approval-btn-active' : 'goals-approval-btn-nonactive'}`}
                          type="submit"
                          onClick={ this.handleSubmitRemoveFromTarget.bind(this) }>Remove from target</button>
                  <Link to="/goals/approval/list" className="btn goals-approval-btn-cancel">Cancel</Link>
              </div>
          </form>
      </div>
    )
  }
}

CoachFooter.propTypes = {
  validationErrors: React.PropTypes.object.isRequired,
  posting_set_as_target: React.PropTypes.bool.isRequired,
  posting_remove_from_target: React.PropTypes.bool.isRequired,
  handlePostSetAsTarget: React.PropTypes.func.isRequired,
  handlePostRemoveFromTarget: React.PropTypes.func.isRequired
}
