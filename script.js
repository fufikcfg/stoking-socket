const WebSocket = require('ws');

const WS_URL = 'ws://localhost:8080';
const TOKEN = '123';

const socket = new WebSocket(WS_URL);

socket.on('open', () => {
  console.log('WebSocket connected');
  const authData = {
    token: TOKEN,
    user_agent: "Node.js test client"
  };
  console.log('Sending authentication:', authData);
  socket.send(JSON.stringify(authData));
});

socket.on('message', (data) => {
  try {
    const message = JSON.parse(data);
    console.log('Server response:', message);
    
    if (message.auth === 'success') {
      console.log('Authentication successful');
      console.log('User ID:', message.user_id);
      console.log('Session ID:', message.session_id);
      
      socket.send(JSON.stringify({
        type: 'message',
        content: 'Content!'
      }));
    } 
    else if (message.auth === 'fail') {
      console.error('Authentication failed:', message.error);
      socket.close();
    }
    else {
      console.log('Received general message:', message);
    }
  } catch (e) {
    console.log('Raw message:', data);
  }
});

socket.on('error', (error) => {
  console.error('WebSocket error:', error.message);
});

socket.on('close', (code, reason) => {
  console.log(`Connection closed (code ${code}, reason: ${reason})`);
});