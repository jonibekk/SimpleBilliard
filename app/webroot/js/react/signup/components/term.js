import React from 'react'
export default class Term extends React.Component {
  render() {
    return (
      <div className="row">
          {/* <!-- START app/View/Signup/user.ctp --> */}
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">Finaly, Teams Term?</div>
              <div className="signup-description">Teams Term sample text Teams Term sample text Teams Term sample text Teams Term sample text.</div>

              <form action="#" className="form-horizontal validate" novalidate="novalidate" id="" method="post" accept-charset="utf-8">
                  <div className="panel-heading signup-itemtitle">Start month</div>
                  <select className="form-control signup_input-design" data-bv-notempty-message=" " id="TeamStartTermMonth" required="required">
                      <option value="">選択してください</option>
                      <option value="1">１月</option>
                      <option value="2">２月</option>
                      <option value="3">３月</option>
                      <option value="4">４月</option>
                      <option value="5">５月</option>
                      <option value="6">６月</option>
                      <option value="7">７月</option>
                      <option value="8">８月</option>
                      <option value="9">９月</option>
                      <option value="10">１０月</option>
                      <option value="11">１１月</option>
                      <option value="12">１２月</option>
                  </select>

                  <div className="panel-heading signup-itemtitle">Term</div>
                  <div className="required">
                      <select className="form-control signup_input-design">
                          <option value="">選択してください</option>
                          <option value="3">四半期</option>
                          <option value="6">半年</option>
                          <option value="12">年</option>
                      </select>
                  </div>

                  <div className="panel-heading signup-itemtitle">Timezone</div>
                  <p id="SignupTeamsTermTimezoneDefault">
                      <span className="plr_18px">
                        <span className="signup-goal-timezone-label">(GMT+9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</span>
                        <a href="#">変更する</a>
                      </span>
                  </p>
                  <div className="none" id="SignupTeamsTermTimezoneInputWrap">
                      <div className="has-feedback">
                          <select className="form-control signup_input-design" defaultValue="+9.0">
                              <option value="-12.0">(GMT-12:00) Eniwetok, Kwajalein</option>
                              <option value="-11.0">(GMT-11:00) Midway Island, Somoa</option>
                              <option value="-10.0">(GMT-10:00) Hawaii</option>
                              <option value="-9.0">(GMT-09:00) Alaska</option>
                              <option value="-8.0">(GMT-08:00) Pacific Time (US &amp; Canada)</option>
                              <option value="-7.0">(GMT-07:00) Mountain Time (US &amp; Canada)</option>
                              <option value="-6.0">(GMT-06:00) Central Time (US &amp; Canada), Mexico City</option>
                              <option value="-5.0">(GMT-05:00) Eastern Time (US &amp; Canada), Bogota, Lima, Quito</option>
                              <option value="-4.0">(GMT-04:00) Atlantic Time (Canada), Caracas, La Paz</option>
                              <option value="-3.5">(GMT-03:30) Newfoundland</option>
                              <option value="-3.0">(GMT-03:00) Brazil, Buenos Aires, Georgetown</option>
                              <option value="-2.0">(GMT-02:00) Mid-Atlantic</option>
                              <option value="-1.0">(GMT-01:00) Azores, Cape Verde Islands</option>
                              <option value="0.0">(GMT+00:00) Western Europe Time, London, Lisbon, Casablanca, Monrovia</option>
                              <option value="+1.0">(GMT+01:00) CET(Central Europe Time), Brussels, Copenhagen, Madrid, Paris</option>
                              <option value="+2.0">(GMT+02:00) EET(Eastern Europe Time), Kaliningrad, South Africa</option>
                              <option value="+3.0">(GMT+03:00) Baghdad, Kuwait, Riyadh, Moscow, St. Petersburg, Volgograd, Nairobi</option>
                              <option value="+3.5">(GMT+03:30) Tehran</option>
                              <option value="+4.0">(GMT+04:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
                              <option value="+4.5">(GMT+04:30) Kabul</option>
                              <option value="+5.0">(GMT+05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                              <option value="+5.5">(GMT+05:30) Bombay, Calcutta, Madras, New Delhi</option>
                              <option value="+6.0">(GMT+06:00) Almaty, Dhaka, Colombo</option>
                              <option value="+7.0">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                              <option value="+8.0">(GMT+08:00) Beijing, Perth, Singapore, Hong Kong, Chongqing, Urumqi, Taipei</option>
                              <option value="+9.0">(GMT+09:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                              <option value="+9.5">(GMT+09:30) Adelaide, Darwin</option>
                              <option value="+10.0">(GMT+10:00) EAST(East Australian Standard), Guam, Papua New Guinea, Vladivostok</option>
                              <option value="+11.0">(GMT+11:00) Magadan, Solomon Islands, New Caledonia</option>
                              <option value="+12.0">(GMT+12:00) Auckland, Wellington, Fiji, Kamchatka, Marshall Island</option>
                          </select>
                      </div>
                  </div>

                  {/* Alert message */}
                  { (() => { if(this.props.team_name.is_exception) {
                    return <AlertMessageBox message={ this.props.team_name.exception_message } />;
                  }})() }

                  {/* Submit button */}
                  { (() => { if(this.props.team_name.submit_button_is_enabled) {
                    return <EnabledNextButton onSubmit={ () => this.props.postTeamName(this.getInputDomData()) } />;
                  } else {
                    return <DisabledNextButton loader={ this.props.team_name.checking_team_name } />;
                  }})() }

              </form>

          </div>
          {/* <!-- END app/View/Signup/user.ctp --> */}
      </div>
    )
  }
}
