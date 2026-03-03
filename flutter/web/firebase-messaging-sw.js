/* eslint-disable no-undef */
importScripts('https://www.gstatic.com/firebasejs/10.13.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.13.2/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: 'AIzaSyBt3Y5R24dI1V-qArWRVVXwSvrwrvreyf0',
  appId: '1:785619130237:web:20f9ee0f705e60e4b5de14',
  messagingSenderId: '785619130237',
  projectId: 'proxima-ride-app-devop',
  authDomain: 'proxima-ride-app-devop.firebaseapp.com',
  storageBucket: 'proxima-ride-app-devop.firebasestorage.app',
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  const notification = payload.notification || {};
  const title = notification.title || 'Notification';
  const options = {
    body: notification.body || '',
    data: payload.data || {},
  };

  self.registration.showNotification(title, options);
});
