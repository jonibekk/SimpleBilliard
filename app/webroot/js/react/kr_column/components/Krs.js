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
          const {key_result, action_results} = kr
          return (
            <li className="dashboard-krs-column">
              <p className="font_verydark kr-name">
                { key_result.name }
              </p>
              <div className="krProgress"
                   data-current_value="$60"
                   data-start_value="$0"
                   data-target_value="$100"
              >
                  <div className="krProgress-bar">
                    <span className="krProgress-text">{ key_result.display_in_progress_bar }</span>
                    <div className={`krProgress-barCurrent is-incomplete mod-rate${key_result.progress_rate}`}></div>
                  </div>
              </div>
              <ul className="dashboard-krs-column-subinfos">
                <li className="action-count">
                  <i className="fa fa-check-circle"></i><span className="action-count-num">{ key_result.action_result_count }</span>
                </li>
                <li className="action-avators">
                  { action_results.map((action) => {
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
                     dangerouslySetInnerHTML={{__html: key_result.action_message}}></p>
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
