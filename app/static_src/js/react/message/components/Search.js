import React from 'react'

export default class Search extends React.Component {
  render() {
    return (
      <div className="panel panel-default topicSearchList">
        <div className="topicSearchList-header">
          <div className="topicSearchList-header-searchBox">
            <div className="searchBox">
              <div className="searchBox-remove-icon">
                <i className="fa fa-remove"></i>
              </div>
              <input className="searchBox-input"
                     placeholder={__("Search topic")} />
              <span className="fa fa-search searchBox-button"></span>
            </div>
          </div>
          <div className="topicSearchList-header-cancel"><a href="">Cancel</a></div>
        </div>
        <ul>
          <li className="topicSearchList-item">
            <a href="">
              <div className="avatorsBox">
                <div className="avatorsBox-one">
                  <img src="/img/sekiguchi.jpeg" />
                </div>
              </div>
              <div className="topicSearchList-item-main">
                <div className="topicSearchList-item-main-header">
                  <div className="topicSearchList-item-main-header-title">
                    Keisuke Sekiguchi
                  </div>
                  <div className="topicSearchList-item-main-header-count">
                  </div>
                </div>
                <div className="topicSearchList-item-main-body">
                  Keisuke: Hey!!!
                </div>
              </div>
              <div className="topicSearchList-item-right">
                Jan 30
              </div>
            </a>
          </li>
          <li className="topicSearchList-item">
            <a href="">
              <div className="avatorsBox">
                <div className="avatorsBox-half">
                  <img src="/img/toshiki.jpeg" />
                </div>
                <div className="avatorsBox-half">
                  <img src="/img/takahiro.jpg" />
                </div>
              </div>
              <div className="topicSearchList-item-main">
                <div className="topicSearchList-item-main-header">
                  <div className="topicSearchList-item-main-header-title oneline-ellipsis">
                    <span>
                      Toshiki, Takahiro
                    </span>
                  </div>
                  <div className="topicSearchList-item-main-header-count">
                    (2)
                  </div>
                </div>
                <div className="topicSearchList-item-main-body oneline-ellipsis">
                  Keisuke: Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!
                </div>
              </div>
              <div className="topicSearchList-item-right">
                Mar 17 at 6:47 pm
              </div>
            </a>
          </li>
          <li className="topicSearchList-item">
            <a href="">
            <div className="avatorsBox">
              <div className="avatorsBox-half">
                <img src="/img/toshiki.jpeg" />
              </div>
              <div className="avatorsBox-quarter">
                <img src="/img/takahiro.jpg" />
              </div>
              <div className="avatorsBox-quarter">
                <img src="/img/daiki.jpeg" />
              </div>
            </div>
              <div className="topicSearchList-item-main">
                <div className="topicSearchList-item-main-header">
                  <div className="topicSearchList-item-main-header-title oneline-ellipsis">
                    <span>
                      Toshiki, Takahiro, Daiki
                    </span>
                  </div>
                  <div className="topicSearchList-item-main-header-count">
                    (3)
                  </div>
                </div>
                <div className="topicSearchList-item-main-body oneline-ellipsis">
                  <span>
                    Keisuke: Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!
                  </span>
                </div>
              </div>
              <div className="topicSearchList-item-right">
                Jan 21
              </div>
            </a>
          </li>
          <li className="topicSearchList-item">
            <a href="">
              <div className="avatorsBox">
                <div className="avatorsBox-quarter">
                  <img src="/img/toshiki.jpeg" />
                </div>
                <div className="avatorsBox-quarter">
                  <img src="/img/takahiro.jpg" />
                </div>
                <div className="avatorsBox-quarter">
                  <img src="/img/daiki.jpeg" />
                </div>
                <div className="avatorsBox-quarter">
                  <img src="/img/masayuki.jpg" />
                </div>
              </div>
              <div className="topicSearchList-item-main">
                <div className="topicSearchList-item-main-header">
                  <div className="topicSearchList-item-main-header-title oneline-ellipsis">
                    <span>
                      Toshiki, Takahiro, Daiki, Shohei, Kohei, Masayuki
                    </span>
                  </div>
                  <div className="topicSearchList-item-main-header-count">
                    (10)
                  </div>
                </div>
                <div className="topicSearchList-item-main-body oneline-ellipsis">
                  <span>
                    Keisuke: Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!
                  </span>
                </div>
              </div>
              <div className="topicSearchList-item-right">
                Jan 20
              </div>
            </a>
          </li>
          <li className="topicSearchList-item">
            <a href="">
              <div className="avatorsBox">
                <div className="avatorsBox-quarter">
                  <img src="/img/toshiki.jpeg" />
                </div>
                <div className="avatorsBox-quarter">
                  <img src="/img/takahiro.jpg" />
                </div>
                <div className="avatorsBox-quarter">
                  <img src="/img/daiki.jpeg" />
                </div>
                <div className="avatorsBox-quarter">
                  <img src="/img/masayuki.jpg" />
                </div>
              </div>
              <div className="topicSearchList-item-main">
                <div className="topicSearchList-item-main-header">
                  <div className="topicSearchList-item-main-header-title oneline-ellipsis">
                    <span>
                      Toshiki, Takahiro, Daiki, Shohei, Kohei, Masayuki, aaaaaaaaaaaaaaaaaaaaaaaaaaa
                    </span>
                  </div>
                  <div className="topicSearchList-item-main-header-count">
                    (30211)
                  </div>
                </div>
                <div className="topicSearchList-item-main-body oneline-ellipsis">
                  <span>
                    Keisuke: Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!
                  </span>
                </div>
              </div>
              <div className="topicSearchList-item-right">
                Jan 20
              </div>
            </a>
          </li>
        </ul>
      </div>
    )
  }
}
