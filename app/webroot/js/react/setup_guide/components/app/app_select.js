import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class AppSelect extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  render() {
    return (
        <div>
            <div className="setup-pankuzu font_18px">
                :Set up Goalous <i className="fa fa-angle-right" aria-hidden="true"></i> :Login from mobile app
            </div>

            <div className="setup-items">
                <Link to="/setup/goal/select" className="setup-items-item pt_10px mt_12px bd-radius_14px">
                    <div class="pull-left mt_3px ml_2px">
                        <div class="setup-items-item-icon">
                            <i class="fa fa-android setup-items-item-icon-fa"></i>
                        </div>
                    </div>
                    <div className="setup-items-item-explain pull-left">
                        <p className="font_bold font_verydark">Install Android app</p>
                        <p className="font_11px font_lightgray">Requires Android 4.4 or later.</p>
                    </div>
                    <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
                        <i className="fa fa-chevron-right font_18px"></i>
                    </div>
                </Link>

                <Link to="/setup/goal/select" className="setup-items-item pt_10px mt_12px bd-radius_14px">
                    <div className="pull-left mt_3px ml_2px">
                        <div className="setup-items-item-icon">
                            <img src='/img/setup/sample_men.png' className="setup-items-item-icon-img" alt='aragaki' />
                        </div>
                    </div>
                    <div className="setup-items-item-explain pull-left">
                        <p className="font_bold font_verydark">Open yourself</p>
                        <p className="font_11px font_lightgray">Increasing people who know you.</p>
                    </div>
                    <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
                        <i className="fa fa-chevron-right font_18px"></i>
                    </div>
                </Link>

                <Link to="/setup/goal/select" className="setup-items-item pt_10px mt_12px bd-radius_14px">
                    <div className="pull-left mt_3px ml_2px">
                        <div className="setup-items-item-icon">
                            <img src='/img/setup/sample_men.png' className="setup-items-item-icon-img" alt='aragaki' />
                        </div>
                    </div>
                    <div className="setup-items-item-explain pull-left">
                        <p className="font_bold font_verydark">Give something to team members</p>
                        <p className="font_11px font_lightgray">Be happy everyone.</p>
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
