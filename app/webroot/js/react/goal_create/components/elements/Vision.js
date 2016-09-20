import React from "react";

export default class Vision extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const visions = this.props.visions
    // ビジョンが無かったらエリア非表示
    if (visions.length == 0) {
      return null
    }
    const vision = visions[this.props.visionIdx]

    return (
      <div className="goals-create-dispaly-vision">
        <h2 className="goals-create-dispaly-vision-title">
          {__("Vision")}：{vision.name}
        </h2>
        <div className="goals-create-dispaly-vision-detail">
          <img src={vision.small_img_url ? vision.small_img_url : "/img/no-image-team.jpg"} className="goals-create-dispaly-vision-detail-image" alt width={32} height={32}/>
          <div className="goals-create-dispaly-vision-detail-info">
            <p className="goals-create-dispaly-vision-text">
              {vision.description}
            </p>
            {/*<a href="#" className="goals-create-dispaly-vision-text-more">{__("More...")}</a>*/}
          </div>
        </div>
        <a href="#" className="goals-create-dispaly-vision-see-other" onClick={(e) => this.props.onChangeVision()}>
          <i className="fa fa-refresh" aria-hidden="true"/>
          <span className="goals-create-interactive-link">{__("See Other")} {visions.length - 1}</span>
        </a>
      </div>
    )

  }
}
Vision.propTypes = {
  visions: React.PropTypes.array,
  visionIdx: React.PropTypes.number,
};
Vision.defaultProps = {visions: [], visionIdx: 0};

