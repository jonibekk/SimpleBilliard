import React from 'react'

export default class AvatarsBox extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    let users = this.props.users || []
    // display less than just 4
    users = users.slice(0, 4)
    return (
      <div className="avatorsBox">
        { users.map((user, i) => {
          let profile_img_url = user.profile_img_url || {};
          let size = ''
          if (users.length > 3) {
            size = 'quarter'
          }
          if (users.length == 3) {
            size = (i == 0) ? 'half' : 'quarter'
          }
          if (users.length == 2) {
            size = 'half'
          }
          if (users.length == 1) {
            size = 'one'
          }
          return (
            <div className={`avatorsBox-${size}`} key={ user.id }>
              <img src={ profile_img_url.medium_large || '' } className="lazy" />
            </div>
          )
        })}
      </div>
    )
  }
}
