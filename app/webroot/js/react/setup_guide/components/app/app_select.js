import React from 'react'
import {Link, browserHistory} from 'react-router'

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
                    <a className="setup-items-item pt_10px mt_12px bd-radius_14px"
                       href="https://play.google.com/store/apps/details?id=jp.co.isao.android.goalous" target="_blank">
                        <div className="pull-left mt_3px ml_2px">
                            <div className="setup-items-item-icon">
                                <i className="fa fa-android setup-items-item-icon-fa"></i>
                            </div>
                        </div>
                        <div className="setup-items-item-explain pull-left">
                            <p className="font_bold font_verydark">Install Android app</p>
                            <p className="font_11px font_lightgray">Requires Android 4.4 or later.</p>
                        </div>
                        <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
                            <i className="fa fa-chevron-right font_18px"></i>
                        </div>
                    </a>

                    <a className="setup-items-item pt_10px mt_12px bd-radius_14px"
                       href="https://itunes.apple.com/jp/app/goalous-gorasu-bijinesusns/id1060474459" target="_blank">
                        <div className="pull-left mt_3px ml_2px">
                            <div className="setup-items-item-icon">
                                <i className="fa fa-apple setup-items-item-icon-fa"></i>
                            </div>
                        </div>
                        <div className="setup-items-item-explain pull-left">
                            <p className="font_bold font_verydark">Install iOS app</p>
                            <p className="font_11px font_lightgray">Requires iOS 8.4 or later.</p>
                        </div>
                        <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
                            <i className="fa fa-chevron-right font_18px"></i>
                        </div>
                    </a>

                    <Link to="/setup/" className="setup-items-item pt_10px mt_12px bd-radius_14px">
                        <div className="pull-left mt_3px ml_2px">
                            <div className="setup-items-item-icon">
                                <i className="fa fa-ban setup-items-item-icon-fa"></i>
                            </div>
                        </div>
                        <div className="setup-items-item-explain pull-left">
                            <p className="font_bold font_verydark">I have no iOS/Android devices</p>
                            <p className="font_11px font_lightgray">If you don't have a mobile device.</p>
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
