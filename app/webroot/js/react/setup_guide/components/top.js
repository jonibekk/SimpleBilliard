import React from 'react'
import { Link, browserHistory } from 'react-router'

export default class Top extends React.Component {
  constructor(props, context) {
    super(props, context);
  }
  listData() {
    return ([
      {
        index: 1,
        subject: 'Input your profile',
        explain: 'Set your profile picture and self-info.',
        link: '/setup'
      },
      {
        index: 2,
        subject: 'Login from mobile app',
        explain: 'Install Goalous iOS and Android apps.',
        link: '/setup'
      },
      {
        index: 3,
        subject: 'Create a goal',
        explain: 'Create or collaborate with a goal.',
        link: '/setup/goal_image'
      },
      {
        index: 4,
        subject: 'Do an action',
        explain: 'Add an Action for your Goal.',
        link: '/setup'
      },
      {
        index: 5,
        subject: 'Join a circle',
        explain: 'Create a circle or join.',
        link: '/setup'
      },
      {
        index: 6,
        subject: 'Post to a circle',
        explain: 'Share your topic with a circle.',
        link: '/setup'
      }
    ])
  }
  render() {
    var items = this.listData().map((text) => {
      return (
        <Link to={text.link} className="setup-items-item pt_10px mt_12px bd-radius_14px">
          <div className="pull-left mt_3px ml_2px">
            <div className="setup-items-item-radius-number inline-block">
              {text.index}
            </div>
          </div>
          <div className="setup-items-item-explain pull-left">
            <p className="font_bold font_verydark">{text.subject}</p>
            <p className="font_11px font_lightgray">{text.explain}</p>
          </div>
          <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
            <i className="fa fa-chevron-right font_18px"></i>
          </div>
        </Link>
      )
    });
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          Set up Goalous
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
          {items}
        </div>
      </div>
    )
  }
}
