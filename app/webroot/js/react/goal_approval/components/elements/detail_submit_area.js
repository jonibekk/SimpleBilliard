import React from 'react'
import { Link } from 'react-router'

export class ApproveSubmitArea extends React.Component {
  render() {
    return (
      <div>
        <label className="goals-approval-input-label" htmlFor="">Do you think this tKR is clear?</label>
        <label className="btn" htmlFor="">
            <input type="radio" name="is-tkr-clear" id="" value="1" />Clear
        </label>
        <label className="btn" htmlFor="">
            <input type="radio" name="is-tkr-clear" id="" value="0" />Not Clear
        </label>

        <label className="goals-approval-input-label" htmlFor="">Do you think that tKR is the most important to achieve the goal?</label>
        <label className="btn" htmlFor="">
            <input type="radio" name="is-tkr-important" id="" value="1" />Yes
        </label>
        <label className="btn" htmlFor="">
            <input type="radio" name="is-tkr-important" id="" value="0" />No
        </label>

        <label className="goals-approval-input-label" htmlFor="">Judge this goal to set as targets of evaluations.</label>
        <textarea className="form-control goals-approval-detail-input-comment-form" name="" id="" cols="30" rows="2" placeholder="Add your comment (optional)"></textarea>


        <a className="btn">Set as target</a>
        <a className="btn">Remove from target</a>
        <a className="btn">Cancel</a>
      </div>
    )
  }
}

ApproveSubmitArea.propTypes = {
}
