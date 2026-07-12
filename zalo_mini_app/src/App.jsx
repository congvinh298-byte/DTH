import React from 'react';
import { App } from 'zmp-ui';

const MyApp = () => {
  return (
    <App>
      <iframe 
        src="https://dienmayhieu.com" 
        style={{
          width: '100vw',
          height: '100vh',
          border: 'none',
          display: 'block'
        }}
        title="Điện Máy Hiếu"
        allow="geolocation; microphone; camera"
      />
    </App>
  );
};

export default MyApp;
