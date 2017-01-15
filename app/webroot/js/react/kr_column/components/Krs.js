import React from "react";

export default class Krs extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const {krs, kr_count} = this.props
    if (kr_count == null) {
      return null
    }
    return (
      <div className="panel panel-default dashboard-krs">
        <div className="dashboard-krs-header">
          <div className="title">KRs ({ kr_count })</div>
          <div className="dropdown-toggle goal" data-toggle="dropdown" role="button"
             aria-expanded="false">
            <span>All</span>
            <i className="fa fa-angle-down ml_2px"/>
          </div>
        </div>
        <ul className="dashboard-krs-columns">
        { krs.map((kr) => {
          return (
            <li className="dashboard-krs-column">
              <p className="font_verydark kr-name">
                { kr.key_result.name }
              </p>
              <div className="krProgress"
                   data-current_value="$60"
                   data-start_value="$0"
                   data-target_value="$100"
              >
                  <div className="krProgress-bar">
                    <span className="krProgress-text">
                         { kr.key_result.current_value_with_unit} / { kr.key_result.target_value_with_unit}
                    </span>
                    <div className="krProgress-barCurrent is-incomplete mod-rate60"></div>
                  </div>
              </div>
              <ul className="dashboard-krs-column-subinfos">
                <li className="action-count">
                  <i className="fa fa-check-circle"></i><span className="action-count-num">{ kr.key_result.action_result_count }</span>
                </li>
                <li className="action-avators">
                  { kr.action_results.map((action) => {
                    return (
                      <div>
                        <a href="">
                          <img className="lazy" src={ action.user.small_img_url } />
                        </a>
                      </div>
                    )
                  })}
                </li>
                <li>
                  <p className="action-message"
                     dangerouslySetInnerHTML={{__html: kr.action_message}}></p>
                </li>
              </ul>
            </li>
          )
        }) }
        </ul>
      </div>
    )
  }
}

Krs.propTypes = {
  krs: React.PropTypes.array
};
Krs.defaultProps = { krs: [], kr_count: null };
