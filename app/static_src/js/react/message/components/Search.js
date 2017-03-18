import React from 'react'

export default class Search extends React.Component {
  render() {
    return (
      <div className="panel panel-default topicSearchList">
        <div className="topicSearchList-header">
          <div className="searchBox">
            <div className="searchBox-search-icon">
              <i className="fa fa-search"></i>
            </div>
            <div className="searchBox-remove-icon">
              <i className="fa fa-remove"></i>
            </div>
            <input className="searchBox-input"
                   placeholder={__("Search topic")} />
          </div>
          <div className="topicSearchList-header-cancel"><a href="">Cancel</a></div>
        </div>
        <ul>
          <li className="topicSearchList-item">
            <a href="">
              <div className="avatorsBox">
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-one" />
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
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-half" />
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-half right" />
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
                  Keisuke: Hey!!!
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
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-half" />
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter right" />
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter right" />
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
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
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
                    Keisuke: Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!
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
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
                <img src="https://goalous-release2-assets.s3.amazonaws.com/users/30/f51770b538cc65e2aa58301d6d9adc28_medium_large.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489853736&Signature=L6NJwvGpuf26yQGCegj775vnoEs%3D" className="avatorsBox-quarter" />
              </div>
              <div className="topicSearchList-item-main">
                <div className="topicSearchList-item-main-header">
                  <div className="topicSearchList-item-main-header-title oneline-ellipsis">
                    <span>
                      Toshiki, Takahiro, Daiki, Shohei, Kohei, Masayuki
                    </span>
                  </div>
                  <div className="topicSearchList-item-main-header-count">
                    (30211)
                  </div>
                </div>
                <div className="topicSearchList-item-main-body oneline-ellipsis">
                  <span>
                    Keisuke: Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!Hey!!!
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
