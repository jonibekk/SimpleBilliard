import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class Top extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          :Set up Goalous
        </div>
        <div className="setup-status">
          <div className="setup-status-wrapper-progress col col-sm-9 col-xs-8">
            <div className="setup-status-progress progress">
              <div className="progress-bar progress-bar-info" role="progressbar"
                   aria-valuenow="50" aria-valuemin="0"
                   aria-valuemax="100">
                  <span className="ml_12px">50%</span>
              </div>
            </div>
          </div>
          <div className="setup-status-number col col-sm-3 col-xs-4 text-right font_bold">
            <div className="setup-status-number-elem">4</div>
          </div>
        </div>
        <div className="setup-status-footer text-right font_18px">:STEPS LEFT</div>

        <div className="setup-items">
          <Link to="/setup" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="pull-left mt_3px ml_2px">
              <div className="setup-items-item-radius-number inline-block">
                1
              </div>
            </div>
            <div className="setup-items-item-explain pull-left">
              <p className="font_bold font_verydark">:Input your profile</p>
              <p className="font_11px font_lightgray">:Set your profile picture and self-info.</p>
            </div>
            <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
              <i className="fa fa-chevron-right font_18px"></i>
            </div>
          </Link>

          <Link to="/setup" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="pull-left mt_3px ml_2px">
              <div className="setup-items-item-radius-number inline-block">
                2
              </div>
            </div>
            <div className="setup-items-item-explain pull-left">
              <p className="font_bold font_verydark">:Login from mobile app</p>
              <p className="font_11px font_lightgray">:Install Goalous iOS and Android apps.</p>
            </div>
            <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
              <i className="fa fa-chevron-right font_18px"></i>
            </div>
          </Link>
          <Link to="/setup/goal_image" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="pull-left mt_3px ml_2px">
              <div className="setup-items-item-radius-number inline-block">
                3
              </div>
            </div>
            <div className="setup-items-item-explain pull-left">
              <p className="font_bold font_verydark">:Create a goal</p>
              <p className="font_11px font_lightgray">:Create or collaborate with a goal.</p>
            </div>
            <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
              <i className="fa fa-chevron-right font_18px"></i>
            </div>
          </Link>
          <Link to="/setup" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="pull-left mt_3px ml_2px">
              <div className="setup-items-item-radius-number inline-block">
                4
              </div>
            </div>
            <div className="setup-items-item-explain pull-left">
              <p className="font_bold font_verydark">:Do an action</p>
              <p className="font_11px font_lightgray">:Add an Action for your Goal.</p>
            </div>
            <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
              <i className="fa fa-chevron-right font_18px"></i>
            </div>
          </Link>
          <Link to="/setup" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="pull-left mt_3px ml_2px">
              <div className="setup-items-item-radius-number inline-block">
                5
              </div>
            </div>
            <div className="setup-items-item-explain pull-left">
              <p className="font_bold font_verydark">:Join a circle</p>
              <p className="font_11px font_lightgray">:Create a circle or join.</p>
            </div>
            <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
              <i className="fa fa-chevron-right font_18px"></i>
            </div>
          </Link>
          <Link to="/setup" className="setup-items-item pt_10px mt_12px bd-radius_14px">
            <div className="pull-left mt_3px ml_2px">
              <div className="setup-items-item-radius-number inline-block">
                6
              </div>
            </div>
            <div className="setup-items-item-explain pull-left">
              <p className="font_bold font_verydark">:Post to a circle</p>
              <p className="font_11px font_lightgray">:Share your topic with a circle.</p>
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
