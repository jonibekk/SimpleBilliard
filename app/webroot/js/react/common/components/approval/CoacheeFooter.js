import React from 'react'
import ReactDOM from 'react-dom'
import { Link } from 'react-router'

export class CoacheeFooter extends React.Component {
  constructor(props) {
    super(props)
  }

  /**
   * 一時的にコレボレータの「 Edit Role」を従来のモーダルで動かす
   * TODO: Edit Role機能実装後この処理は行わないので削除する
   */
  componentDidMount() {
    if(!this.props.is_leader) {
      ReactDOM.findDOMNode(this.refs.collabo_link).setAttribute("data-toggle", "modal")
      ReactDOM.findDOMNode(this.refs.collabo_link).setAttribute("data-target", `#ModalCollabo_${this.props.goal_id}`)
    }
  }

  render() {
    return (
      <div className="goals-approval-detail-choice">
          { this.props.is_leader ?
            <a href={`/goals/${this.props.goal_id}/edit?from=${new Buffer(this.props.current_url).toString('base64')}`} className="btn goals-approval-btn-fullsize-active">{ __('Edit Goal') }</a>
          :
            <a href={`/goals/ajax_get_collabo_change_modal/goal_id:${this.props.goal_id}`}
               className="btn goals-approval-btn-fullsize-active modal-ajax-get-collabo"
               ref="collabo_link">{ __('Edit Role') }</a>
          }
          <Link to="/goals/approval/list" className="btn goals-approval-btn-cancel">{ __("Back") }</Link>
      </div>
    )
  }
}

CoacheeFooter.propTypes = {
  is_leader: React.PropTypes.bool,
  goal_id: React.PropTypes.oneOfType([
    React.PropTypes.string,
    React.PropTypes.number
  ]),
  current_url:React.PropTypes.string
}
