import React from "react";

export default class UploadDropZone extends React.Component {
  constructor(props) {
    super(props)
  }
  render() {
    return (
      <div className="uploadFileForm mod-dragOver"
      >
        <div className="uploadFileForm-msg">
          <span className="uploadFileForm-msg-label">
              <i className="fa fa-cloud-upload"></i>
          </span>
        </div>
      </div>
    )
  }
}