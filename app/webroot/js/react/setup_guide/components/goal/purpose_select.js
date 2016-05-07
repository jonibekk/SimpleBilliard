import React, { PropTypes } from 'react'

export default class PurposeSelect extends React.Component {
  constructor(props) {
    super(props);
  }
  purposeList() {
    return ([
      {
        id: 1,
        pic: '/img/setup/sample_men.png',
        name: __("Do something with team members"),
        explain: __("You do something worthwhile.")
      },
      {
        id: 2,
        pic: '/img/setup/sample_men.png',
        name: __("Open yourself"),
        explain: __("Increasing people who know you.")
      },
      {
        id: 3,
        pic: '/img/setup/sample_men.png',
        name: __("Give something to team members"),
        explain: __("Be happy everyone.")
      }
    ])
  }
  render() {
    const purposes = this.purposeList().map((purpose) => {
      return (
        <div className="setup-items-item pt_10px mt_16px bd-radius_14px"
             key={purpose.id}
             onClick={(e) => { this.props.onClickSelectPurpose(purpose.name) }}>
          <div className="setup-items-item-pic pull-left mt_3px ml_2px">
            <img src={purpose.pic} className="setup-items-item-pic-img" alt='' />
          </div>
          <div className="setup-items-item-explain pull-left">
            <p className="font_bold font_verydark">{purpose.name}</p>
            <p className="font_11px font_lightgray">{purpose.explain}</p>
          </div>
          <div className="setup-items-item-to-right pull-right mt_12px mr_5px">
            <i className="fa fa-chevron-right font_18px"></i>
          </div>
        </div>
      )
    })
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__('Create a goal')}
        </div>
        <div className="setup-items">
          {purposes}
        </div>
      </div>
    )
  }
}

PurposeSelect.propTypes = {
  onClickSelectPurpose: PropTypes.func
}
