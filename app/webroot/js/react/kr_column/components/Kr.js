import React from "react";

export default class Kr extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { key_result, action_results} = this.props
    return (
      <li className="dashboard-krs-column">
        <p className="font_verydark kr-name">
          { key_result.name }
        </p>
        <div className="krProgress">
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
                <a href="">
                  <img className="lazy" src={ action.user.small_img_url } />
                </a>
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
  }
}
