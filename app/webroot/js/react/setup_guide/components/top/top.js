import React, { PropTypes } from 'react'
import { Link, browserHistory } from 'react-router'

export default class Top extends React.Component {
  constructor(props, context) {
    super(props, context)
  }
  componentWillMount() {
    this.props.fetchSetupStatus()
  }
  listData() {
    return ([
      {
        index: 1,
        subject: __('Input your profile'),
        explain: __('Set your profile picture and self-info.'),
        link: '/setup/profile/image'
      },
      {
        index: 2,
        subject: __('Login from mobile app'),
        explain: __('Install Goalous iOS and Android apps.'),
        link: '/setup/app/image'
      },
      {
        index: 3,
        subject: __('Create a goal'),
        explain: __('Create or collaborate with a goal.'),
        link: '/setup/goal/image'
      },
      {
        index: 4,
        subject: __('Do an action'),
        explain: __('Add an Action for your Goal.'),
        link: '/setup/action/image'
      },
      {
        index: 5,
        subject: __('Join a circle'),
        explain: __('Create a circle or join.'),
        link: '/setup/circle/image'
      },
      {
        index: 6,
        subject: __('Post to a circle'),
        explain: __('Share your topic with a circle.'),
        link: '/setup/post/image'
      }
    ])
  }
  render() {
    const progressBarStyle = {
      width: String(this.props.top.setup_complete_percent) + '%'
    }
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
    var items = this.listData().map((text) => {
      return (
        <Link to={text.link} className="setup-items-item pt_10px mt_12px bd-radius_14px" key={text.index} >
          <div className="setup-items-item-pic pull-left mt_3px">
            {this.props.top.status[text.index] ? check_icon() : number_radius_box(text.index)}
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
    })
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          Set up Goalous
        </div>
        <div className="setup-status">
          <div className="setup-status-wrapper-progress col">
            <div className="setup-status-progress progress">
              <div className="progress-bar progress-bar-info" role="progressbar"
                   aria-valuenow="50" aria-valuemin="0"
                   aria-valuemax="100" style={progressBarStyle}>
                <span className="ml_12px">{this.props.top.setup_complete_percent}%</span>
              </div>
            </div>
          </div>
          <div className="setup-status-number text-right font_bold">
            <div className="setup-status-number-elem">{this.props.top.setup_rest_count}</div>
          </div>
        </div>
        <div className="setup-status-footer text-right font_18px">STEPS LEFT</div>
        <div className="setup-items">
          {items}
        </div>
      </div>
    )
  }
}

Top.propTypes = {
  status: PropTypes.array,
  setup_rest_count: PropTypes.number,
  setup_complete_percent: PropTypes.number
}
