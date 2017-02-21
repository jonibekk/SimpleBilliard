import React, { Component, PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link, browserHistory } from 'react-router'

export default class GoalCreate extends React.Component {
  constructor(props) {
    super(props);
  }
  getInputDomData() {
    return {
      photo: ReactDOM.findDOMNode(this.refs.photo).files[0],
      name: ReactDOM.findDOMNode(this.refs.name).value.trim(),
      value_unit: ReactDOM.findDOMNode(this.refs.value_unit).value,
      start_value: ReactDOM.findDOMNode(this.refs.start_value).value,
      target_value: ReactDOM.findDOMNode(this.refs.target_value).value,
      start_date: cake.current_term_start_date_format,
      end_date: ReactDOM.findDOMNode(this.refs.end_date).value,
      purpose_name: ReactDOM.findDOMNode(this.refs.purpose_name).value,
      img_url: this.props.goal.selected_goal.pic_url
    }
  }
  render() {
    const unit_list = cake.data.kr_value_unit_list
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__("Create a goal")}
        </div>
        <form className="form-horizontal setup-goal-create-form form-feed-notify"
              encType="multipart/form-data"
              method="post"
              acceptCharset="utf-8"
              onSubmit={(e) => {
                e.preventDefault()
                this.props.onSubmit(this.getInputDomData())
              }}>
          <div className="panel-body">
            <span className="help-block">{__("Purpose")}</span>
            <textarea name="purpose_name" ref="purpose_name" defaultValue={this.props.goal.selected_purpose.name} className="form-control addteam_input-design" required="required" rows="1" cols="30"></textarea>
          </div>
          <div className="panel-body">
            <span className="help-block">{__("Goal Name")}</span>
            <textarea name="name" ref="name" defaultValue={this.props.goal.selected_goal.name} className="form-control addteam_input-design" required="required" rows="1" cols="30"></textarea>
          </div>
          <div className="panel-body">
            <div className="form-inline">
              <div className="form-group">
                <span className="help-block">{__('Unit')}</span>
                <select name="value_unit" ref="value_unit" className="form-control addteam_input-design" required="required"
                        defaultValue={this.props.goal.selected_goal.value_unit}>
                  <option value="0">{unit_list[0]}</option>
                  <option value="3">{unit_list[3]}</option>
                  <option value="4">{unit_list[4]}</option>
                  <option value="1">{unit_list[1]}</option>
                  <option value="2">{unit_list[2]}</option>
                </select>
              </div>
              <div className="form-group">
                <span className="help-block">{__("Initial point")}</span>
                <input name="start_value" ref="start_value" className="form-control addteam_input-design" step="0.1" required="required" type="number" defaultValue={this.props.goal.selected_goal.start_value || 0} />
              </div>
              <div className="form-group setup-form-arrow">
                <i className="fa fa-arrow-right font_18px"></i>
              </div>
              <div className="form-group">
                <span className="help-block">{__("Achieve point")}</span>
                <input name="target_value" ref="target_value" className="form-control addteam_input-design" step="0.1" required="required" type="number" defaultValue={this.props.goal.selected_goal.target_value || 100} />
              </div>
            </div>
          </div>
          <div className="panel-body">
            <span className="help-block">{__("Due Date")}</span>
            <div className="input-group date goal-set-date">
                <input name="end_date" ref="end_date" className="form-control"
                       required="required" type="text"
                       defaultValue={cake.current_term_end_date_format} />
                <span className="input-group-addon"><i className="fa fa-th"></i></span>
            </div>
          </div>
          <div className="panel-body">
            <span className="help-block">{__("Goal Image")}</span>
            <div className="form-inline">
              <div className="fileinput_small fileinput-new" data-provides="fileinput">
                <div className="fileinput-preview thumbnail nailthumb-container photo-design form-group setup-guide-file-preview" data-trigger="fileinput">
                  <i className="fa fa-plus photo-plus-large"
                     style={{display: this.props.goal.selected_goal.pic_url ? 'none' : 'block'}}>
                  </i>
                  <img src={this.props.goal.selected_goal.pic_url}
                       style={{display: this.props.goal.selected_goal.pic_url ? 'block' : 'none'}} />
                </div>
                <div className="form-group">
                  <span className="btn btn-default btn-file ml_16px setup-guide-file-select-btn">
                    <span className="fileinput-new">{__("Select an image")}</span>
                    <span className="fileinput-exists">{__("Reselect an image")}</span>
                    <input type="file" name="photo" ref="photo" className="form-control addteam_input-design" id="GoalPhoto" />
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div>
            <Link to="/setup/goal/select" className="btn btn-secondary setup-back-btn">{__("Back")}</Link>
            <input type="submit" className="btn btn-primary setup-next-btn pull-right" defaultValue={__("Create a goal")} />
          </div>
        </form>
      </div>
    )
  }
}

GoalCreate.propTypes = {
  onSubmit: PropTypes.func
}
