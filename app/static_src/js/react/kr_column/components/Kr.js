import React from "react";

export default class Kr extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      is_opened_kr: false
    }
    this.toggleKrOpened = this.toggleKrOpened.bind(this)
  }

  /**
   * KR詳細情報表示ステータスを切り替える
   */
  toggleKrOpened() {
    if (this.state.is_opened_kr) {
      this.setState({ is_opened_kr: false })
    } else {
      this.setState({ is_opened_kr: true })
    }
  }

  render() {
    const { key_result, action_results, goal } = this.props
    if (!key_result) {
      return null
    }

    const is_complete = key_result.completed
    const action_post_link = `/goals/add_action/key_result_id:${key_result.id}/goal_id:${goal.id}`
    const action_list_link = `/goals/view_actions/goal_id:${goal.id}/page_type:list/key_result_id:${key_result.id}`
    return (
      <li className="dashboard-krs-column" key={ key_result.id }>
        <div className="dashboard-krs-column-wrapper">
          {/* KR name & progressBarエリア */}
          <div className="left">
            <div className="flex mb_2px pointer" onClick={ this.toggleKrOpened }>
              <p className={`flex-extend font_verydark kr-name ${this.state.is_opened_kr ? 'is-opened' : 'is-closed'}`}>
                { key_result.name }
              </p>
              <div className="dashboard-krs-column-kr-opener">
                <i className={`fa ${this.state.is_opened_kr ? "fa-angle-up" : "fa-angle-down"} ml_2px`}/>
              </div>
            </div>
            <div className={`oneline-ellipsis font_12px mt_4px ${!this.state.is_opened_kr && 'none'}`}>
              <a href={`/goals/view_krs/goal_id:${goal.id}`} className="font_verydark">
                <i className="fa fa-flag-o mr_4px"></i>
                <span>{ goal.name }</span>
              </a>
            </div>
            { key_result.description && this.state.is_opened_kr && (() => {
              return (
                <div className="font_12px mt_4px">
                  <div className="flex">
                    <div className="mr_6px"><i className="fa fa-sticky-note-o"></i></div>
                    <p className="font_verydark">
                      { key_result.description }
                    </p>
                  </div>
                  <a className="close-button disp_blk mt_4px mb_4px"
                     onClick={ this.toggleKrOpened }>
                    <i className="fa fa-angle-up" /> { __('Close') }
                  </a>
                </div>
              )
            })() }
            <div className="krProgress">
              <div className="krProgress-bar">
                <span className="krProgress-text">{ key_result.display_in_progress_bar }</span>
                <div className={`krProgress-barCurrent ${key_result.progress_rate == 100 ? 'is-complete' : 'is-incomplete'} mod-rate${key_result.progress_rate}`}></div>
              </div>
            </div>
          </div>
          {/* アクションボタンエリア */}
          <div className="right">
            { !is_complete && (() => {
              return (
                <a href={ action_post_link } label="Add Action">
                  <div className="dashboard-krs-column-add-action">
                      <span>+</span>
                  </div>
                  {/* TODO: If another language needed, have to move translation to cake_variables.ctp */}
                  {cake.lang === 'jpn' ? <p>ｱｸｼｮﾝ<br />追加</p> : <p>Add Action</p>}
                </a>
              )
            })()}
          </div>
        </div>
        <div className="dashboard-krs-column-footer">
          <a href={ (key_result.completed || action_results.length > 0) ? action_list_link : action_post_link }
             className="action-link">
            <ul className="dashboard-krs-column-subinfos">
              <li className="action-count">
                <i className="fa fa-check-circle"></i><span className="action-count-num">{ key_result.action_result_count }</span>
              </li>
              <span>・</span>
              <li className="action-avators">
                {/* 最近アクションしたユーザー画像を表示 */}
                { !key_result.completed && action_results.map((action) => {
                  return (
                    <img className="lazy" src={ action.user.small_img_url } key={ action.user.id } />
                  )
                })}
              </li>
              <li>
                {/* 渡ってくるデータにHTMLが含まれているためdangerouslySetInnerHTMLを泣く泣く使っている */}
                <p className="action-message"
                   dangerouslySetInnerHTML={{__html: key_result.action_message}}></p>
              </li>
            </ul>
          </a>
        </div>
      </li>
    )
  }
}
