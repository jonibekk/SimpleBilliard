import React from "react";
import ReactDOM from "react-dom";
import InfiniteScroll from "redux-infinite-scroll";

export default class Krs extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div className="panel panel-default dashboard-krs">
        <div className="dashboard-krs-header">
          <div className="title">KRs ({ this.props.kr_count })</div>
          <div className="dropdown-toggle goal" data-toggle="dropdown" role="button"
             aria-expanded="false">
            <span>All</span>
            <i className="fa fa-angle-down ml_2px"/>
          </div>
        </div>
        <ul className="dashboard-krs-columns">
        { this.props.krs.map((kr) => {
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
                  <a href="">
                    <img className="lazy" src="/upload/users/1/aa648722a414f4e41607343f945ae4d1_medium.jpeg" />
                  </a>
                  <a href="">
                    <img className="lazy" src="/upload/users/2/cc53004d3fc5af955dc2ba428c022e3e_medium.png" />
                  </a>
                  <a href="">
                    <img className="lazy" src="/upload/attached_files/26/32a64f88923af1ce65cff6f1d16a82cd_small.jpeg" />
                  </a>
                </li>
                <li>
                  <p className="action-message"><span className="action-count">333</span> <span>members in 7 days !</span></p>
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
Krs.defaultProps = { krs: [] };
