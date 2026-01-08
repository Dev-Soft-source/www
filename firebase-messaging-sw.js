importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
   
// TODO: Replace with your new Firebase project (proxima-ride-app-devop) web app config
const firebaseConfig = {
  apiKey: "AIzaSyBt3Y5R24dI1V-qArWRVVXwSvrwrvreyf0",
  authDomain: "proxima-ride-app-devop.firebaseapp.com",
  projectId: "proxima-ride-app-devop",
  storageBucket: "proxima-ride-app-devop.firebasestorage.app",
  messagingSenderId: "785619130237",
  appId: "1:785619130237:web:20f9ee0f705e60e4b5de14"
};
  
// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Background message handler
messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon
    };

    if (notificationTitle && notificationOptions.body) {
        self.registration.showNotification(notificationTitle, notificationOptions);
    } else {
        console.error('Notification data missing or malformed', payload);
    }
});