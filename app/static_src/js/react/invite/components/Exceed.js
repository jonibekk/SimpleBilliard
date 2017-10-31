/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import {browserHistory} from "react-router";
import {MaxLength} from "~/common/constants/App";
import Base from "~/common/components/Base";

export default class Exceed extends Base {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        super.componentDidMount.apply(this)
    }

    componentWillUnmount() {
        super.componentWillUnmount.apply(this)
    }

    // Display intercom message window 
    showIntercom(e) {
        e.preventDefault();
        Intercom('showNewMessage', 'Feedback on the new reports feature:');
    }

    render() {
        return (
            <section className="panel panel-default mod-form col-sm-8 col-sm-offset-2 clearfix gl-form">
                <h2 className="gl-form-heading">{__("Upgrade Plan")}</h2>
                <form>
                    <div className="mb_16px">
                        <label className="gl-form-label">
                            {__("This invitation will cause your team's active members to exceed the current plan limit. Please upgrade your plan.")}
                        </label>
                    </div>
                    <div className="btnGroupForForm">
                        <a className="btnGroupForForm-next" href="#" onClick={(e) => this.showIntercom(e)} >{__("Contact Us")}</a>
                    </div>
                </form>
            </section>
        )
    }
}