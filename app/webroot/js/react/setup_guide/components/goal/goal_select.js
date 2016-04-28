import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class GoalSelect extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__("Create a goal")}
        </div>
        <div className="setup-items">
          <Link to="/setup/goal/create" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="pull-left mt_3px ml_2px">
              <div className="setup-items-item-icon">
                <img src='/img/setup/sample_men.png' className="setup-items-item-icon-img" alt='' />
              </div>
            </div>
            <div className="setup-items-item-explain pull-left">
              <p className="font_bold font_verydark">{__("Talk with team members")}</p>
            </div>
            <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
              <i className="fa fa-chevron-right font_18px"></i>
            </div>
          </Link>

          <Link to="/setup/goal/create" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="pull-left mt_3px ml_2px">
              <div className="setup-items-item-icon">
                <img src='/img/setup/sample_men.png' className="setup-items-item-icon-img" alt='' />
              </div>
            </div>
            <div className="setup-items-item-explain pull-left">
              <p className="font_bold font_verydark">{__("Lunch with team members")}</p>
            </div>
            <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
              <i className="fa fa-chevron-right font_18px"></i>
            </div>
          </Link>

          <Link to="/setup/goal/create" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="pull-left mt_3px ml_2px">
              <div className="setup-items-item-icon">
                <img src='/img/setup/sample_men.png' className="setup-items-item-icon-img" alt='' />
              </div>
            </div>
            <div className="setup-items-item-explain pull-left">
              <p className="font_bold font_verydark">{__("Hear complaints of team members")}</p>
            </div>
            <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
              <i className="fa fa-chevron-right font_18px"></i>
            </div>
          </Link>
        </div>
      </div>
    )
  }
}
