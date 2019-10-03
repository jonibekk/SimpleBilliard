import React, { PropTypes } from 'react'
import { Link, browserHistory } from 'react-router'

const TYPE_PROFILE = 1;
const TYPE_APP = 2;
const TYPE_GOAL = 3;
const TYPE_ACTION = 4;
const TYPE_CIRCLE_JOIN = 5;
const TYPE_CIRCLE_POST = 6;

export default class Top extends React.Component {
  constructor(props, context) {
    super(props, context)
  }
  componentWillMount() {
    this.props.fetchSetupStatus()
    this.props.fetchGoals()
  }
  listData() {
    return ([
      {
        index: TYPE_PROFILE,
        subject: __('Input your profile'),
        explain: __('Set your profile picture and self-info.'),
        link: '/setup/profile/image'
      },
      {
        index: TYPE_APP,
        subject: __('Login from mobile app'),
        explain: __("Install Goalous's iOS and Android apps."),
        link: '/setup/app/image'
      }
    ])
  }
  render() {
    const number_radius_box = (index) => {
      return (
        <div className="setup-items-item-radius-number inline-block">
          {index}
        </div>
      )
    }
    const check_icon = () => {
      return (
        <span className="setup-items-item-complete-check">
          <i className="fa fa-check font_33px" aria-hidden="true"></i>
        </span>
      )
    }
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__('Set up Goalous')}
        </div>
        <div className="setup-status">
          <div className="setup-status-wrapper-progress col">
            <div className="setup-status-progress progress">
              <div className="progress-bar setup-progress-bar-info" role="progressbar"
                   aria-valuenow="50" aria-valuemin="0"
                   aria-valuemax="100" style={{width: String(this.props.top.setup_complete_percent) + '%'}}>
                <span className="ml_12px">{this.props.top.setup_complete_percent}%</span>
              </div>
            </div>
          </div>
          <div className="setup-status-completed"
               style={{display: this.props.top.setup_rest_count ? 'none' : 'block'}}>
            <div className="setup-status-completed-text font_18px font_bold text-right">{__('Completed')}</div>
          </div>
          <div className="setup-status-number text-right"
               style={{display: this.props.top.setup_rest_count ? 'block' : 'none'}}>
            <div className="setup-status-number-elem font_bold">{this.props.top.setup_rest_count}</div>
          </div>
        </div>
        <div className="setup-status-footer text-right font_13px">
          {this.props.top.setup_rest_count == 0 ? __('Excellent!') : __('STEPS LEFT')}
        </div>
        <div className="setup-items">
          {
            this.listData().map((text) => {
              const content = () => {
                return (
                  <div>
                    <div className="setup-items-item-pic pull-left mt_3px">
                      {this.props.top.status[text.index] ? check_icon() : number_radius_box(text.index)}
                    </div>
                    <div className="setup-items-item-explain pull-left">
                      <p className="font_bold font_verydark">{text.subject}</p>
                      <p className="font_11px font_lightgray">{text.explain}</p>
                    </div>
                    <div className="setup-items-item-to-right pull-right mt_12px mr_5px"
                      style={{
                      display: (text.index == TYPE_ACTION && this.props.action.goals.length == 0) ? 'none' : 'block'
                      }}>
                      <i className="fa fa-chevron-right font_18px"></i>
                    </div>
                  </div>
                )
              }
              // ゴール作成の場合はリダイレクト
              if (text.index == TYPE_GOAL || text.index == TYPE_ACTION) {
                return (
                  <a href={text.link} className="setup-items-item pt_10px mt_12px bd-radius_14px" key={text.index} >
                    {content()}
                  </a>
                )
              } else {
                return (
                  <Link to={text.link} className="setup-items-item pt_10px mt_12px bd-radius_14px" key={text.index} >
                    {content()}
                  </Link>
                )
              }
            })
          }
        </div>
      </div>
    )
  }
}

Top.propTypes = {
  status: PropTypes.array,
  setup_rest_count: PropTypes.number,
  setup_complete_percent: PropTypes.number,
  goals: PropTypes.array
}
