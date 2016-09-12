import React from 'react'
import { Link } from 'react-router'

export default class ListComponent extends React.Component {

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">

          <h1 className="goals-approval-heading">Goal approval list <span>(2)</span></h1>


          <ul>
              <li className="goals-approval-list-item">
                  <Link className="goals-approval-list-item-link" to="/goals/approval/detail/gucchi">
                      <img className="goals-approval-list-item-image" src="" alt="" width="32" height="32" />

                      <div className="goals-approval-list-item-info">
                          <p className="goals-approval-list-item-info-user-name">User name</p>
                          <p className="goals-approval-list-item-info-goal-name">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                          <p className="goals-approval-list-item-info-goal-attr">Leader・Evaluated</p>
                      </div>

                      <p className="goals-approval-list-item-detail"><i className="fa fa-angle-right" ariaHidden="true"></i>
                      </p>
                  </Link>
              </li>
              <li className="goals-approval-list-item">
                  <Link className="goals-approval-list-item-link" to="/goals/approval/detail/gucchi">
                      <img className="goals-approval-list-item-image" src="" alt="" width="32" height="32" />

                      <div className="goals-approval-list-item-info">
                          <p className="goals-approval-list-item-info-user-name">User name</p>
                          <p className="goals-approval-list-item-info-goal-name">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                          <p className="goals-approval-list-item-info-goal-attr">Leader・Evaluated</p>
                      </div>

                      <p className="goals-approval-list-item-detail"><i className="fa fa-angle-right" ariaHidden="true"></i>
                      </p>
                  </Link>
              </li>
              <li className="goals-approval-list-item">
                  <Link className="goals-approval-list-item-link" to="/goals/approval/detail/gucchi">
                      <img className="goals-approval-list-item-image" src="" alt="" width="32" height="32" />

                      <div className="goals-approval-list-item-info">
                          <p className="goals-approval-list-item-info-user-name">User name</p>
                          <p className="goals-approval-list-item-info-goal-name">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                          <p className="goals-approval-list-item-info-goal-attr">Leader・Evaluated</p>
                      </div>

                      <p className="goals-approval-list-item-detail"><i className="fa fa-angle-right" ariaHidden="true"></i>
                      </p>
                  </Link>
              </li>
          </ul>

      </section>
    )
  }
}
