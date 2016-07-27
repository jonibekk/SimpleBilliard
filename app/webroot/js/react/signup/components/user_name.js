import React from 'react'
import ReactDOM from 'react-dom'
import { Link } from 'react-router'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import {range} from '../actions/common_actions'

export default class UserName extends React.Component {

  getInputDomData() {
    return {
      first_name: ReactDOM.findDOMNode(this.refs.first_name).value.trim(),
      last_name: ReactDOM.findDOMNode(this.refs.last_name).value.trim(),
      birth_year: ReactDOM.findDOMNode(this.refs.birth_year).value.trim(),
      birth_month: ReactDOM.findDOMNode(this.refs.birth_month).value.trim(),
      birth_day: ReactDOM.findDOMNode(this.refs.birth_day).value.trim(),
      privacy: ReactDOM.findDOMNode(this.refs.privacy).checked
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postUserName(this.getInputDomData())
  }

  render() {
    return (
      <div>
        <div className="row">
            <div className="panel panel-default panel-signup">
                <div className="panel-heading signup-title">{"What's your name?"}</div>
                <div className="signup-description">username sample textusername sample text username sample text username sample text username sample text username sample text.</div>

                <form className="form-horizontal" acceptCharset="utf-8"
                      onSubmit={ (e) => this.handleSubmit(e) }>
                    <div className="panel-heading signup-itemtitle">your name</div>
                    <input ref="first_name" className="form-control signup_input-design" placeholder="例) Suzuki" type="text"
                           onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} />
                    <input ref="last_name" className="form-control signup_input-design" placeholder="例) Hanako" type="text"
                           onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} />

                    <div className="panel-heading signup-itemtitle">Birthday</div>
                    <div className="form-inline signup_inputs-inline">
                        {/* Birthday year */}
                        <select className="form-control inline-fix" ref="birth_year"
                                onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} >
                           <option value=""></option>
                           {
                             range(1910, new Date().getFullYear()).sort((a,b) => b-a).map( year => {
                               return <option value={year} key={year}>{year}</option>;
                             })
                           }
                        </select>
                        &nbsp;/&nbsp;
                        {/* Birthday month */}
                        <select className="form-control inline-fix" ref="birth_month"
                                onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} >
                           <option value=""></option>
                           <option value="01">Jan</option>
                           <option value="02">Feb</option>
                           <option value="03">Mar</option>
                           <option value="04">Apr</option>
                           <option value="05">May</option>
                           <option value="06">Jun</option>
                           <option value="07">Jul</option>
                           <option value="08">Aug</option>
                           <option value="09">Sep</option>
                           <option value="10">Oct</option>
                           <option value="11">Nov</option>
                           <option value="12">Dec</option>
                        </select>
                        &nbsp;/&nbsp;
                        {/* Birthday day */}
                        <select className="form-control inline-fix" ref="birth_day"
                                onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} >
                           <option value=""></option>
                           {
                             range(1, 31).map( day => {
                               return <option value={day} key={day}>{day}</option>;
                             })
                           }
                        </select>
                    </div>
                    <div className="checkbox signup-checkbox">
                        <label>
                            <input type="checkbox" value="1" id="UserAgreeTos" ref="privacy"
                                   onChange={ () => { this.props.inputUserName(this.getInputDomData()) } } /> Goalousの<Link to="/terms" target="_blank" className="link">利用規約</Link>と<Link to="/privacy_policy" target="_blank" className="link">プライバシーポリシー</Link>に同意します。</label>
                    </div>

                    {/* Alert message */}
                    { (() => { if(this.props.user_name.is_exception) {
                      return <AlertMessageBox message={ this.props.user_name.exception_message } />;
                    }})() }

                    {/* Submit button */}
                    { (() => { if(this.props.user_name.submit_button_is_enabled) {
                      return <EnabledNextButton />;
                    } else {
                      return <DisabledNextButton loader={ this.props.user_name.checking_user_name } />;
                    }})() }

                </form>
            </div>
        </div>
      </div>
    )
  }
}
