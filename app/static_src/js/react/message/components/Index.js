import React from 'react'

export default class Index extends React.Component {
  render() {
    return (
      <div className="panel panel-default topicList">
        <div className="topicList-header">
          <div className="searchBox">
            <div className="searchBox-search-icon">
              <i className="fa fa-search"></i>
            </div>
            <input className="searchBox-input"
                   placeholder={__("Search topic")} />
          </div>
          <div className="topicList-header-middle">
            <div className="topicList-header-middle-label">
              {__("TOPICS")}
            </div>
            <a href="" className="topicList-header-middle-add">
              <i className="fa fa-plus-circle"></i> {__("New Message")}
            </a>
          </div>
        </div>
        <ul>
        <li className="topicList-item">
          <a href="">
            <div className="avatorsBox">
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-one" />
            </div>
            <div className="topicList-item-main">
              <div className="topicList-item-main-header">
                <div className="topicList-item-main-header-title">
                  Keisuke Sekiguchi
                </div>
                <div className="topicList-item-main-header-count">
                </div>
              </div>
              <div className="topicList-item-main-body">
                Keisuke: Hey!!!
              </div>
              <div className="topicList-item-main-footer">
                <span>20 min</span>
              </div>
            </div>
          </a>
        </li>
          <li className="topicList-item">
            <a href="">
              <div className="avatorsBox">
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-one" />
              </div>
              <div className="topicList-item-main">
                <div className="topicList-item-main-header">
                  <div className="topicList-item-main-header-title">
                    Keisuke Sekiguchi
                  </div>
                  <div className="topicList-item-main-header-count">
                  </div>
                </div>
                <div className="topicList-item-main-body">
                  Keisuke: Hey!!!
                </div>
                <div className="topicList-item-main-footer">
                  <i className="fa fa-check is-read"></i>・<span>Jan 20 at 14:57</span>
                </div>
              </div>
            </a>
          </li>
          <li className="topicList-item">
            <a href="">
              <div className="avatorsBox">
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-half" />
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-half right" />
              </div>
              <div className="topicList-item-main">
                <div className="topicList-item-main-header">
                  <div className="topicList-item-main-header-title oneline-ellipsis">
                    <span>
                      Toshiki, Takahiro
                    </span>
                  </div>
                  <div className="topicList-item-main-header-count">
                    (2)
                  </div>
                </div>
                <div className="topicList-item-main-body oneline-ellipsis">
                  Keisuke: Hey!!!
                </div>
                <div className="topicList-item-main-footer">
                  <i className="fa fa-check"></i> 3・<span>Jan 20 at 14:57</span>
                </div>
              </div>
            </a>
          </li>
          <li className="topicList-item">
            <a href="">
            <div className="avatorsBox">
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-half" />
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter right" />
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter right" />
            </div>
              <div className="topicList-item-main">
                <div className="topicList-item-main-header">
                  <div className="topicList-item-main-header-title oneline-ellipsis">
                    <span>
                      Toshiki, Takahiro, Daiki
                    </span>
                  </div>
                  <div className="topicList-item-main-header-count">
                    (3)
                  </div>
                </div>
                <div className="topicList-item-main-body oneline-ellipsis">
                  <span>
                    Keisuke: Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!
                  </span>
                </div>
                <div className="topicList-item-main-footer">
                  <i className="fa fa-check is-read"></i>・<span>Jan 20 at 14:57</span>
                </div>
              </div>
            </a>
          </li>
          <li className="topicList-item">
            <a href="">
            <div className="avatorsBox">
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
            </div>
              <div className="topicList-item-main">
                <div className="topicList-item-main-header">
                  <div className="topicList-item-main-header-title oneline-ellipsis">
                    <span>
                      Toshiki, Takahiro, Daiki, Shohei, Kohei, Masayuki
                    </span>
                  </div>
                  <div className="topicList-item-main-header-count">
                    (10)
                  </div>
                </div>
                <div className="topicList-item-main-body oneline-ellipsis">
                  <span>
                    Keisuke: Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!
                  </span>
                </div>
                <div className="topicList-item-main-footer">
                  <i className="fa fa-check is-read"></i>・<span>Jan 20 at 14:57</span>
                </div>
              </div>
            </a>
          </li>
          <li className="topicList-item">
            <a href="">
            <div className="avatorsBox">
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
              <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
            </div>
              <div className="topicList-item-main">
                <div className="topicList-item-main-header">
                  <div className="topicList-item-main-header-title oneline-ellipsis">
                    <span>
                      Toshiki, Takahiro, Daiki, Shohei, Kohei, Masayuki
                    </span>
                  </div>
                  <div className="topicList-item-main-header-count">
                    (3000)
                  </div>
                </div>
                <div className="topicList-item-main-body oneline-ellipsis">
                  <span>
                    Keisuke: Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!
                  </span>
                </div>
                <div className="topicList-item-main-footer">
                  <i className="fa fa-check is-read"></i>・<span>Jan 20 at 14:57</span>
                </div>
              </div>
            </a>
          </li>
        </ul>
      </div>
    )
  }
}
