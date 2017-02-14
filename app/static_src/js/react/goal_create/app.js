/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import ReactDOM from 'react-dom'
import Routes, { unlisten } from './config/routes'
import { AppContainer } from 'react-hot-loader';
// ReactDOM.render(
//   <AppContainer>
//     <Routes />
//   </AppContainer>
//   , document.getElementById("goal-create-app"));
//

ReactDOM.render(<Routes />, document.getElementById("goal-create-app"));

// Stop to listen browzer history changing
unlisten()
