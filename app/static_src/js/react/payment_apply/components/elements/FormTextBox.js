import React from "react";

export default class FormTextBox extends React.Component {
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

    let showOtherVisionLink = null
    if (visions.length > 1) {
      showOtherVisionLink = (
        <a href="#" className="goals-create-dispaly-vision-see-other" onClick={(e) => this.props.onChangeVision()}>
          <i className="fa fa-refresh" aria-hidden="true"/>
          <span className="goals-create-interactive-link">{__("See Other")}({visions.length - 1})</span>
        </a>
      )
    }

    let groupName = "";
    if (vision.group) {
      groupName = vision.group.name
    } else if (vision.team) (
      groupName = vision.team.name
    )


    return (
      <div className="goals-create-dispaly-vision">
        <h2 className="goals-create-dispaly-vision-title">
          {__("Vision")}
        </h2>
        <div className="goals-create-dispaly-vision-detail">
          <img src={vision.small_img_url ? vision.small_img_url : "/img/no-image-team.jpg"}
               className="goals-create-dispaly-vision-detail-image" alt width={32} height={32}/>
          <div className="goals-create-dispaly-vision-detail-info">
            <p className="goals-create-dispaly-vision-text">
              {groupName}
            </p>
            <p className="goals-create-dispaly-vision-text">
              {vision.name}
            </p>
            {/*<a href="#" className="goals-create-dispaly-vision-text-more">{__("More...")}</a>*/}
          </div>
        </div>
        {showOtherVisionLink}
      </div>
    )

  }
}
Vision.propTypes = {
  visions: React.PropTypes.array,
  visionIdx: React.PropTypes.number,
};
Vision.defaultProps = {visions: [], visionIdx: 0};

