import React from "react";
import {setExifRotateStyle} from "~/helper_functions/helpers.js";

export default class PhotoUpload extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    // TODO:画面遷移を行うとイベントが発火しなくなる為、コード追加(既存バグ)
    // 将来的に廃止
    $(document).ready(function () {
      $('.fileinput_small').fileinput().on('change.bs.fileinput', function () {
        $(this).children('.nailthumb-container').nailthumb({width: 96, height: 96, fitDirection: 'center center'});
      });
      $('.fileinput-exists,.fileinput-new').fileinput().on('change.bs.fileinput', function (e) {
        setExifRotateStyle(this);
      });
    });
    // TODO:アップロードして画面遷移した後戻った時のサムネイル表示がおかしくなる不具合対応
    // 本来リサイズ後の画像でないと表示がおかしくなるが、アップロードにjqueryプラグインを使用すると
    // リサイズ後の画像情報が取得できない。
    // 画像アップロード後submitした時にimgタグの画像情報を取得してもアップロード前の画像情報を取得してしまう。
    // これはReactの仮想domに反映されていない為。
    const imgPath = this.props.uploadPhoto ? this.props.uploadPhoto.result : this.props.imgUrl;

    return (
      <div>
        <label className="goals-create-input-label">{__("Goal image")}</label>
        <div
          className={`goals-create-input-image-upload fileinput_small ${this.props.uploadPhoto ? "fileinput-exists" : "fileinput-new"}`}
          data-provides="fileinput">
          <div id="preview-photoupload"
            className="fileinput-preview thumbnail nailthumb-container photo-design goals-create-input-image-upload-preview"
            data-trigger="fileinput">
            <img src={imgPath} width={100} height={100} ref="photo_image"/>
          </div>
          <div className="goals-create-input-image-upload-info">
            <label className="goals-create-input-image-upload-info-link " htmlFor="file_photo">
              <span className="fileinput-new">{__("Upload an image")}</span>
              <span className="fileinput-exists">{__("Reselect an image")}</span>
              <input className="goals-create-input-image-upload-info-form" type="file" name="photo" ref="photo"
                     id="file_photo"/>
            </label>
          </div>
        </div>
      </div>
    )
  }
}
PhotoUpload.propTypes = {
  uploadPhoto: React.PropTypes.object,
  imgUrl: React.PropTypes.string,
};
PhotoUpload.defaultProps = {
  imgUrl: "/img/no-image-goal.png"
};
