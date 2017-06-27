/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import ReactDOM from 'react-dom'
import Routes, { unlisten } from './config/routes'
import injectTapEventPlugin from "react-tap-event-plugin";
injectTapEventPlugin();

ReactDOM.render(<Routes />, document.getElementById("message-app"));

// Stop to listen browzer history changing
unlisten()
