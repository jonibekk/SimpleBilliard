import React, { Component, PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link, browserHistory } from 'react-router'

export default class GoalCreate extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  handleSubmit(e) {
    e.preventDefault()
//    dispatch(addTodo(input.value))
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          :Set up Goalous <i className="fa fa-angle-right" aria-hidden="true"></i> :Create a goal
        </div>
        <form onSubmit={this.handleSubmit.bind(this)} className="form-horizontal" encType="multipart/form-data" method="post" acceptCharset="utf-8">
          <div className="panel-body">
            <span className="help-block">Goal Name</span>
            <textarea ref="goal_name" className="form-control addteam_input-design" required="required" rows="1" cols="30"></textarea>
          </div>
          <div className="panel-body">
            <div className="form-inline">
              <div className="form-group">
                <span className="help-block">Unit</span>
                <select ref="value_unit" className="form-control addteam_input-design" required="required">
                  <option defaultValue="0">%</option>
                  <option defaultValue="3">¥</option>
                  <option defaultValue="4">$</option>
                  <option defaultValue="1">その他の単位</option>
                  <option defaultValue="2">なし</option>
                </select>
              </div>
              <div className="form-group">
                <span className="help-block">Initial point</span>
                <input className="form-control addteam_input-design" step="0.1" required="required" type="number" defaultValue="0" />
              </div>
              <div className="form-group setup-form-arrow">
                <i className="fa fa-arrow-right font_18px"></i>
              </div>
              <div className="form-group">
                <span className="help-block">Achieve point</span>
                <input name="data[Goal][target_value]" className="form-control addteam_input-design" step="0.1" required="required" type="number" defaultValue="100" />
              </div>
            </div>
          </div>
          <div className="panel-body">
            <span className="help-block">Due Date</span>
            <div className="input-group date goal-set-date">
                <input className="form-control" defaultValue="2016/09/30" default="2016/09/30" required="required" type="text" />
                <span className="input-group-addon"><i className="fa fa-th"></i></span>
            </div>
          </div>
          <div className="panel-body">
            <span className="help-block">Goal Image</span>
            <div className="form-inline">
              <div className="fileinput_small fileinput-new" data-provides="fileinput">
                <div className="fileinput-preview thumbnail nailthumb-container photo-design form-group" data-trigger="fileinput">
                  <i className="fa fa-plus photo-plus-large"></i>
                </div>
                <div className="form-group">
                  <span className="btn btn-default btn-file ml_16px">
                    <span className="fileinput-new">画像を選択</span>
                    <span className="fileinput-exists">画像を再選択</span>
                    <input type="file" name="data[Goal][photo]" className="form-control addteam_input-design" id="GoalPhoto" />
                  </span>
                  <span className="help-block inline-block font_11px">10MB以下</span>
                </div>
              </div>
            </div>
          </div>
          <div className="panel-body">
            <input type="submit" className="btn btn-primary" defaultValue="Create a goal" />
          </div>
        </form>
      </div>
    )
  }
}

GoalCreate.propTypes = {
//  onAddGoalSubmitted: PropTypes.func.isRequired
}
