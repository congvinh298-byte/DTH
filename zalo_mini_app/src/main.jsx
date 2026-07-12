import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import 'zmp-ui/zaui.css';
import './css/app.css';

// Config Zalo App parameters if needed (e.g. initialize SDK)
import api from 'zmp-sdk';

const root = createRoot(document.getElementById('root'));
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);
