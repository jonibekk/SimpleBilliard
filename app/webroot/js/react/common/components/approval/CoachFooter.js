import React from 'react'
import ReactDOM from "react-dom"
import { Link } from 'react-router'
import InvalidMessageBox from "../InvalidMessageBox";

export class CoachFooter extends React.Component {
  constructor(props) {
    super(props);

    this.is_not_clear = 0
    this.is_clear = 1
    this.is_not_important = 0
    this.is_important = 1

    this.state = {
      clear_or_not: null,
      important_or_not: null
    }

    this.handleClickClear = this.handleClickClear.bind(this)
    this.handleClickImportant = this.handleClickImportant.bind(this)
    this.handleClickNotClear = this.handleClickNotClear.bind(this)
    this.handleClickNotImportant = this.handleClickNotImportant.bind(this)
    this.handleSubmit = this.handleSubmit.bind(this)
    this.handleSubmitSetAsTarget = this.handleSubmitSetAsTarget.bind(this)
    this.handleSubmitRemoveFromTarget = this.handleSubmitRemoveFromTarget.bind(this)
  }

  getInputDomData() {
    return {
      comment: ReactDOM.findDOMNode(this.refs.comment).value.trim()
    }
  }

  handleClickClear() {
    this.setState({clear_or_not: this.is_clear})
  }

  handleClickNotClear() {
    this.setState({clear_or_not: this.is_not_clear})
  }

  handleClickImportant() {
    this.setState({important_or_not: this.is_important})
  }

  handleClickNotImportant() {
    this.setState({important_or_not: this.is_not_important})
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
    const can_submit_set_as_target = this.state.clear_or_not === this.is_clear && this.state.important_or_not === this.is_important
    const can_submit_remove_from_target = this.state.clear_or_not !== null && this.state.important_or_not !== null
    const validation_errors = this.props.validationErrors
    const loading_image = () => <img src="/img/ajax-loader.gif" className="signup-img-loader" />;

    return (
      <div className="goals-approval-detail-choice">
          <form onSubmit={ this.handleSubmit }>
              <label className="goals-approval-input-label" htmlFor="">Do you think this tKR is clear?</label>
              <a className={`btn goals-approval-btn-choice${this.state.clear_or_not === this.is_clear ? 'd' : ''}`} htmlFor="" onClick={ this.handleClickClear }>Clear</a>
              <a className={`btn goals-approval-btn-choice${this.state.clear_or_not === this.is_not_clear ? 'd' : ''}`} htmlFor="" onClick={ this.handleClickNotClear }>Not Clear</a>

              <label className="goals-approval-input-label" htmlFor="">Do you think that tKR is the most important to achieve the goal?</label>
              <a className={`btn goals-approval-btn-choice${this.state.important_or_not === this.is_important ? 'd' : ''}`} htmlFor="" onClick={ this.handleClickImportant }>Yes</a>
              <a className={`btn goals-approval-btn-choice${this.state.important_or_not === this.is_not_important ? 'd' : ''}`} htmlFor="" onClick={ this.handleClickNotImportant }>No</a>

              <label className="goals-approval-input-label" htmlFor="">Judge this goal to set as target of evaluations.</label>
              <textarea className="form-control goals-approval-detail-input-comment-form" name="comment" ref="comment" id="comment" cols="30" rows="2" placeholder="Add your comment (optional)"></textarea>
              <InvalidMessageBox message={validation_errors.comment}/>
              <div className="goals-approval-detail-choice">
                  { this.props.posting_set_as_target ? loading_image() : '' }
                  <button className={`btn ${can_submit_set_as_target ? 'goals-approval-btn-active' : 'goals-approval-btn-nonactive'}`}
                          type="submit"
                          onClick={ this.handleSubmitSetAsTarget }>Set as target</button>
                  { this.props.posting_remove_from_target ? loading_image() : '' }
                  <button className={`btn ${can_submit_remove_from_target ? 'goals-approval-btn-active' : 'goals-approval-btn-nonactive'}`}
                          type="submit"
                          onClick={ this.handleSubmitRemoveFromTarget }>Remove from target</button>
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
