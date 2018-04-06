import React from "react";
import {connect} from "react-redux";
import {deleteUploadedFile} from "~/message/modules/file_upload";
import {formatFileSize} from "~/util/base.js";
import {FileUpload} from "~/common/constants/App";

class UploadPreview extends React.Component {
  constructor(props) {
    super(props)
    this.deleteUploadedFile = this.deleteUploadedFile.bind(this)
  }

  deleteUploadedFile(file_index) {
    this.props.dispatch(
      deleteUploadedFile(file_index)
    );
  }

  render() {
    if (this.props.files.length == 0) {
      return null;
    }

    return (
      <div className="uploadPreviews">
        {this.props.files.map((file, i) => {

          const [size, unit] = formatFileSize(file.size);
          return (
            <div className="uploadPreviews-item" key={i}>
              <div className="uploadPreviews-item-content mb_4px">
                <div className="uploadPreviews-item-content-left">
                  <div className="uploadPreviews-item-thumbnail-wrapper">
                    {file.previewUrl ?
                      <img className="uploadPreviews-item-thumbnail"
                           alt={file.name}
                           src={file.previewUrl}
                      /> :
                      <i className="fa fa-file-o file-other-icon"/>
                    }
                  </div>
                </div>
                <div className="uploadPreviews-item-content-right">
                  <div className="flex">
                    <div className={`uploadPreviews-item-filename flex-extend ${(file.status == FileUpload.Error) && "is-error"}`}>
                      {file.name}
                    </div>
                    <a href="#" className="uploadPreviews-item-delete"
                      onClick={(e) => this.deleteUploadedFile(i)}
                    >
                      <i className="fa fa-times"/>
                    </a>
                  </div>
                  <div className="uploadPreviews-item-filesize">
                    <span className="uploadPreviews-item-filesize-strong">{size}</span> {unit}
                  </div>
                </div>
              </div>
              {(file.status == FileUpload.Uploading) &&
              <div className={`uploadPreviews-item-progressBar mod-rate${file.progress_rate}`}></div>
              }
              {file.status == FileUpload.Error &&
              <p className="uploadPreviews-item-error" key={file.name}>
                {file.err_msg}
              </p>
              }
            </div>
          )
        })}
      </div>
    )
  }
}
UploadPreview.propTypes = {
  files: React.PropTypes.array,
};
UploadPreview.defaultProps = {
  files: [],
};

export default connect()(UploadPreview);
