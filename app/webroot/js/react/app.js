import React, { PropTypes } from 'react';
import ReactDOM from 'react-dom';
import { createStore } from 'redux';
import { Provider, connect } from 'react-redux';

// Action
const INCREMENT_COUNTER = {
    type: 'INCREMENT_COUNTER',
    count: 1
};

const DECREMENT_COUNTER = {
    type: 'DECREMENT_COUNTER',
    count: -1
};

// Reducer
function counter (state = {count: 0}, action) {
    let count = state.count;
    switch (action.type) {
        case 'INCREMENT_COUNTER':
            return {count: count + action.count};
        case 'DECREMENT_COUNTER':
            return {count: count + action.count};
        default:
            return state;
    }
}

// Store
let store = createStore(counter);

// Component
class CounterComponent extends React.Component {
    get style() {
        return {
            displayNone: {
                display: "none"
            },
            displayBlock: {
                display: "block"
            },
            height360: {
                height: 360
            },
            outline: {
                width: 484,
                margin: "auto"
            }
        }
    }
    render () {
        const { count, onClickPlus, onClickMinus } = this.props;
        let buttons = (
            <div className="feeds-post-btns-wrap-left">
                <a href="#" className="feeds-post-like-btn" onClick={onClickPlus}>
                    <i className="fa-thumbs-up fa"></i>
                    いいね！</a>
                <a href="#" className="feeds-post-comment-btn" onClick={onClickMinus}>
                    <i className="fa-comments-o fa"></i>
                    コメント </a>
            </div>
        );
        let like_count = (
            <span>{count}</span>
        );
        return (
            <div id="app-view-elements-feed-posts" style={this.style.outline}>
                <div className="panel panel-default">
                    <div className="post-heading-goal-area panel-body pt_10px plr_11px pb_8px bd-b">
                        <div className="col col-xxs-12">
                            <div className="post-heading-goal-wrapper pull-left">
                                <a href="/goals/ajax_get_goal_description_modal/goal_id:633" className="post-heading-goal
                                    no-line font_verydark modal-ajax-get">
                                    <p className="post-heading-goal-title">
                                        <i className="fa fa-flag font_gray"></i>
                                        <span>ISAOグローバル化を劇的に推進する</span>
                                    </p>
                                </a>
                            </div>
                            <div className="pull-right">
                                <a href="/goals/ajax_get_goal_description_modal/goal_id:633"
                                   className="no-line font_verydark modal-ajax-get">
                                    <img
                                        src="https://goalous-release2-assets.s3.amazonaws.com/goals/633/2c6214002fc227b7a967f6ff56b7b711_small.jpg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1458803785&Signature=MbXD8vF8MotMKb0hKD87L%2FoAK%2Fo%3D"
                                        className="post-heading-goal-avatar  lazy media-object"
                                        data-original="https://goalous-release2-assets.s3.amazonaws.com/goals/633/2c6214002fc227b7a967f6ff56b7b711_small.jpg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&amp;Expires=1458799973&amp;Signature=IYqHUL3di9TiAvVbr34VgcOf0Uk%3D"
                                        width="32px" error-img="/img/no-image-link.png" alt=""
                                        style={this.style.displayBlock}/> </a>
                            </div>
                        </div>
                    </div>
                    <div className="posts-panel-body panel-body">
                        <div className="col col-xxs-12 feed-user">
                            <div className="pull-right">
                                <div className="dropdown">
                                    <a href="#" className="font_lightGray-gray font_11px" data-toggle="dropdown"
                                       id="download">
                                        <i className="fa fa-chevron-down feed-arrow"></i>
                                    </a>
                                    <ul className="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                                        <li>
                                            <form action="/goals/delete_action/action_result_id:1124"
                                                  name="post_56f37755345fc781185011"
                                                  id="post_56f37755345fc781185011" style={this.style.displayNone}
                                                  method="post"><input type="hidden" name="_method"
                                                                       value="POST"/><input type="hidden"
                                                                                            name="data[_Token][key]"
                                                                                            value="eae9bba03ef9fc0339397c8d590818139c9ad8fc"
                                                                                            id="Token986545155"/>

                                                <div style={this.style.displayNone}><input type="hidden"
                                                                                           name="data[_Token][fields]"
                                                                                           value="3e4cbdf7e4178c299368b43f9a6d2a1347a88fb9%3A"
                                                                                           id="TokenFields1179782678"/><input
                                                    type="hidden" name="data[_Token][unlocked]" value=""
                                                    id="TokenUnlocked692896970"/></div>
                                            </form>
                                            <a href="#"
                                               onclick="if (confirm(&quot;\u672c\u5f53\u306b\u3053\u306e\u30a2\u30af\u30b7\u30e7\u30f3\u3092\u524a\u9664\u3057\u307e\u3059\u304b\uff1f&quot;)) { document.post_56f37755345fc781185011.submit(); } event.returnValue = false; return false;">アクションを削除</a>
                                        </li>
                                        <li>
                                            <a href="#" className="copy_me"
                                               onclick="copyToClipboard('https://isao.goalous.com/post_permanent/6837'); return false;">
                                                リンクをコピー</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <a href="/users/view_goals/user_id:1">
                                <img
                                    src="https://goalous-release2-assets.s3.amazonaws.com/users/1/843c2194af311ce15624357b5eb85a4a_medium.JPG?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1458803785&Signature=4FqCi%2FMGXu66dZUBEwhru3znKEM%3D"
                                    className="lazy feed-img"
                                    data-original="https://goalous-release2-assets.s3.amazonaws.com/users/1/843c2194af311ce15624357b5eb85a4a_medium.JPG?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&amp;Expires=1458799973&amp;Signature=rx04gE57feaXr7GO9oLJ69ZwqAU%3D"
                                    alt="" style={this.style.displayNone}/>                        <span
                                className="font_14px font_bold font_verydark">
                            平形 大樹 565-&gt;475-&gt;620(next)                        </span>
                            </a>

                            <div className="font_11px font_lightgray">
                                <span title="2015年 8月30日 22:39"> 8月30日 22:39</span></div>
                        </div>
                        <div
                            className="col col-xxs-12 feed-contents post-contents showmore-circle font_14px font_verydark box-align"
                            id="PostTextBody_6837">
                            <i className="fa fa-check-circle disp_i"></i>&nbsp;バンガロールに、いてきまーす！
                        </div>
                    </div>
                    <div className="col col-xxs-12 pt_10px feed_img_only_one mb_12px"
                         style={this.style.height360}>
                        <a href="https://goalous-release2-assets.s3.amazonaws.com/attached_files/716/6b732219276ce9ddce9c48f26ced5b2f_original.jpg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&amp;Expires=1458799973&amp;Signature=4UBqKNAbLcoN00kwF7YKvoBxI%2FI%3D"
                           rel="lightbox" data-lightbox="FeedLightBox_6837"
                           style={this.style.height360}>
                            <img
                                src="https://goalous-release2-assets.s3.amazonaws.com/attached_files/716/6b732219276ce9ddce9c48f26ced5b2f_small.jpg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1458803785&Signature=ttfBzmhtetxz3OHRyKWmYzs3GxM%3D"
                                alt=""/> </a>
                    </div>

                    <div className="panel-body pt_10px plr_11px pb_8px">
                        <div className="col col-xxs-12 pt_10px">
                        </div>
                        <div className="col col-xxs-12 feeds-post-btns-area">
                            {buttons}
                            <div className="feeds-post-btns-wrap-right">
                                <a href="/posts/ajax_get_post_liked_users/post_id:6837"
                                   className="modal-ajax-get feeds-post-btn-numbers-like">
                                    <i className="fa fa-thumbs-o-up"></i>&nbsp;
                                    {like_count}
                                </a>
                                <a href="/posts/ajax_get_post_red_users/post_id:6837"
                                   className="modal-ajax-get feeds-post-btn-numbers-read">
                                    <i className="fa fa-check"></i>
                           <span>
                               107                           </span>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        );
    }
}

CounterComponent.propTypes = {
    count: PropTypes.number.isRequired,
    onClickPlus: PropTypes.func.isRequired
};

// Containers
function mapStateToProps (state) {
    return {
        count: state.count
    };
}

function mapDispatchToProps (dispatch) {
    return {
        onClickPlus: () => dispatch(INCREMENT_COUNTER),
        onClickMinus: () => dispatch(DECREMENT_COUNTER)
    };
}

let App = connect(
    mapStateToProps,
    mapDispatchToProps
)(CounterComponent);

// main
ReactDOM.render(
    <Provider store={store}>
        <App />
    </Provider>,
    document.getElementById('setup-guide')
);
