import React from "react";
import ReactDOM from "react-dom";
import * as KeyCode from "../../common/constants/KeyCode";
import InvalidMessageBox from "../../common/components/InvalidMessageBox";
import CategorySelect from "../../common/components/goal/CategorySelect";
import LabelInput from "../../common/components/goal/LabelInput";

export default class Edit extends React.Component {
  constructor(props) {
    super(props)
    this.state = {}

    this.handleChange = this.handleChange.bind(this)
    this.deleteLabel = this.deleteLabel.bind(this)
    this.addLabel = this.addLabel.bind(this)
  }

  componentWillMount() {
    this.props.fetchInitialData(this.props.params.goalId)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      browserHistory.push(`/goals/${this.props.params.goalId}/edit/confirm`)
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    if (e.keyCode == KeyCode.ENTER) {
      return false
    }
    this.props.validateGoal(this.getInputDomData())
  }

  deleteLabel(e) {
    const label = e.currentTarget.getAttribute("data-label")
    this.props.deleteLabel(label)
  }

  addLabel(e) {
    // Enterキーを押した時にラベルとして追加
    if (e.keyCode == KeyCode.ENTER) {
      this.props.addLabel(e.target.value)
    }
  }

  getInputDomData() {
    const photo = ReactDOM.findDOMNode(this.refs.photo).files[0]
    if (!photo) {
      return {}
    }
    return {photo}
  }

  onKeyPress(e) {
    // ラベル入力でEnterキーを押した場合submitさせない
    // e.keyCodeはonKeyPressイベントでは取れないのでe.charCodeを使用
    if (e.charCode == KeyCode.ENTER) {
      e.preventDefault()
      return false
    }
  }

  handleChange(e, childKey = "") {
    console.log("-----handleChange")
    console.log({value:e.target.value})
    this.props.updateInputData({[e.target.name]: e.target.value}, childKey)
  }

  render() {
    // TODO:画面遷移を行うとイベントが発火しなくなる為、コード追加(既存バグ)
    // 将来的に廃止
    $('.fileinput_small').fileinput().on('change.bs.fileinput', function () {
      $(this).children('.nailthumb-container').nailthumb({width: 96, height: 96, fitDirection: 'center center'});
    });

    const {suggestions, keyword, validationErrors, inputData, goal} = this.props.goal
    // TODO:アップロードして画面遷移した後戻った時のサムネイル表示がおかしくなる不具合対応
    // 本来リサイズ後の画像でないと表示がおかしくなるが、アップロードにjqueryプラグインを使用すると
    // リサイズ後の画像情報が取得できない。
    // 画像アップロード後submitした時にimgタグの画像情報を取得してもアップロード前の画像情報を取得してしまう。
    // これはReactの仮想domに反映されていない為。
    console.log("goal data")
    console.log(this.props.goal.goal)
    const imgPath = inputData.photo ? inputData.photo.result : goal.medium_large_img_url;
    console.log({imgPath})

    console.log("render start")
    console.log({inputData})
    return (
      <div className="panel panel-default col-sm-8 col-sm-offset-2 goals-create">
        <form className="goals-create-input"
              encType="multipart/form-data"
              method="post"
              acceptCharset="utf-8"
              onSubmit={(e) => this.handleSubmit(e)}>
          <section className="mb_12px">
            <h1 className="goals-create-heading">{__("Set your goal name")}</h1>
            <p
              className="goals-create-description">{__("Your name will displayed along with your goals and posts in Goalous.")}</p>

            <label className="goals-create-input-label">{__("Goal name?")}</label>
            <input name="name" className="form-control goals-create-input-form" type="text"
                   placeholder="e.g. Get goalous users" ref="name"
                   onChange={this.handleChange} value={inputData.name}/>
            <InvalidMessageBox message={validationErrors.name}/>

            <CategorySelect
              onChange={(e) => this.props.updateInputData({goal_category_id: e.target.value})}
              categories={this.props.goal.categories}
              value={inputData.goal_category_id}/>
            <InvalidMessageBox message={validationErrors.goal_category_id}/>

            <LabelInput
              suggestions={suggestions}
              keyword={keyword}
              inputLabels={inputData.labels}
              onSuggestionsFetchRequested={this.props.onSuggestionsFetchRequested}
              onSuggestionsClearRequested={this.props.onSuggestionsClearRequested}
              renderSuggestion={(s) => <span>{s.name}</span>}
              getSuggestionValue={(s) => this.props.onSuggestionsFetchRequested}
              onChange={this.props.onChangeAutoSuggest}
              onSuggestionSelected={this.props.onSuggestionSelected}
              shouldRenderSuggestions={() => true}
              onDeleteLabel={this.deleteLabel}
              onKeyDown={this.addLabel}
              onKeyPress={this.onKeyPress}
            />
            <InvalidMessageBox message={validationErrors.labels}/>

            <label className="goals-create-input-label">{__("Goal image?")}</label>
            <div
              className={`goals-create-input-image-upload fileinput_small ${inputData.photo ? "fileinput-exists" : "fileinput-new"}`}
              data-provides="fileinput">
              <div
                className="fileinput-preview thumbnail nailthumb-container photo-design goals-create-input-image-upload-preview"
                data-trigger="fileinput">
                <img src={imgPath} width={100} height={100} ref="photo_image"/>
              </div>
              <div className="goals-create-input-image-upload-info">
                <p className="goals-create-input-image-upload-info-text">
                  {__("This is sample image if you want to upload your original image")}
                </p>
                <label className="goals-create-input-image-upload-info-link " htmlFor="file_photo">
                  <span className="fileinput-new">{__("Upload a image")}</span>
                  <span className="fileinput-exists">{__("Reselect an image")}</span>
                  <input className="goals-create-input-image-upload-info-form" type="file" name="photo" ref="photo"
                         id="file_photo"/>
                </label>
              </div>
            </div>
            <InvalidMessageBox message={validationErrors.photo}/>

            <label className="goals-create-input-label">{__("Term?")}</label>
            <select name="term_type" className="form-control goals-create-input-form mod-select" ref="term_type"
                    value={inputData.term_type} onChange={this.handleChange}>
              <option value="current">{__("Current Term")}</option>
              <option value="next">{__("Next Term")}</option>
            </select>
            <InvalidMessageBox message={validationErrors.term_type}/>

            <label className="goals-create-input-label">{__("Description")}</label>
            <textarea className="goals-create-input-form mod-textarea" name="description" onChange={this.handleChange}
                      value={inputData.description}/>
            <InvalidMessageBox message={validationErrors.description}/>

            <label className="goals-create-input-label">{__("Start date")}</label>
            <input className="goals-create-input-form" type="date" name="start_date" onChange={this.handleChange}
                   value={inputData.start_date}/>
            <InvalidMessageBox message={validationErrors.start_date}/>

            <label className="goals-create-input-label">{__("End date")}</label>
            <input className="goals-create-input-form" type="date" name="end_date" onChange={this.handleChange}
                   value={inputData.end_date}/>
            <InvalidMessageBox message={validationErrors.end_date}/>
            <label className="goals-create-input-label">{__("Weight")}</label>
            <select className="goals-create-input-form mod-select" name="priority" ref="priority"
                    value={inputData.priority} onChange={this.handleChange}>
              {
                this.props.goal.priorities.map((v) => {
                  return (
                    <option key={v.id} value={v.id}>{v.label}</option>
                  )
                })
              }
            </select>
            <InvalidMessageBox message={validationErrors.priority}/>
          </section>
          <section className="mb_32px">
            <h1 className="goals-create-heading">{__("Set a top key result (tKR)")}</h1>
            <p className="goals-create-description">{__("Set measurable target to achieve your goal.")}</p>
            <label className="goals-create-input-label">{__("tKR name")}</label>
            <input name="name" type="text" value={inputData.key_result.name}
                   className="form-control goals-create-input-form goals-create-input-form-tkr-name"
                   placeholder="e.g. Increase monthly active users" onChange={(e) => this.handleChange(e, "key_result")}/>
            <InvalidMessageBox message={validationErrors.key_result.name}/>

            <label className="goals-create-input-label">{__("Unit & Range")}</label>
            <select name="value_unit" value={inputData.key_result.value_unit}
                    className="form-control goals-create-input-form goals-create-input-form-tkr-range-unit mod-select"
                    onChange={(e) => this.handleChange(e, "key_result")}>
              {
                this.props.goal.units.map((v) => {
                  return (
                    <option key={v.id} value={v.id}>{v.label}</option>
                  )
                })
              }
            </select>
            <InvalidMessageBox message={validationErrors.key_result.value_unit}/>

            <div className="goals-create-layout-flex">
              <input name="start_value" value={inputData.key_result.start_value}
                     className="form-control goals-create-input-form goals-create-input-form-tkr-range" type="text"
                     placeholder={0} onChange={(e) => this.handleChange(e, "key_result")}/>
              <span className="goals-create-input-form-tkr-range-symbol">&gt;</span>
              <input name="target_value" value={inputData.key_result.target_value}
                     className="form-control goals-create-input-form goals-create-input-form-tkr-range" type="text"
                     placeholder={100} onChange={(e) => this.handleChange(e, "key_result")}/>
            </div>
            <InvalidMessageBox message={validationErrors.key_result.start_value}/>
            <InvalidMessageBox message={validationErrors.key_result.target_value}/>

            <label className="goals-create-input-label">{__("Description")}</label>
            <textarea name="description" value={inputData.key_result.description}
                      className="form-control goals-create-input-form mod-textarea" onChange={(e) => this.handleChange(e, "key_result")}/>
            <InvalidMessageBox message={validationErrors.key_result.description}/>

          </section>


          <button type="submit" className="goals-create-btn-next btn">{__("Confirm")} ></button>
          <a className="goals-create-btn-cancel btn" href="/">{__("Cancel")}</a>
        </form>
      </div>
    )
  }
}
