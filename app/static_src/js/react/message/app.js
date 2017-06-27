/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import ReactDOM from 'react-dom'
import Routes, { unlisten } from './config/routes'
import KrColumn from '~/kr_column/components/KrColumn'
import injectTapEventPlugin from "react-tap-event-plugin";
injectTapEventPlugin();

ReactDOM.render(<Routes />, document.getElementById("message-app"));
ReactDOM.render(<KrColumn />, document.getElementById("kr-column"));

// Stop to listen browzer history changing
unlisten()
