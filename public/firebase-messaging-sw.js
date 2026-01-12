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
    const notificationTitle = payload.notification.title || 'ProximaRide';
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon || '/assets/favicon.png',
        badge: '/assets/favicon.png',
        data: payload.data || {}, // Store notification data for click handling
        tag: payload.data?.notification_id || 'notification', // Group notifications
        requireInteraction: false
    };

    if (notificationOptions.body) {
        return self.registration.showNotification(notificationTitle, notificationOptions);
    } else {
        console.error('Notification data missing or malformed', payload);
    }
});

// Handle notification clicks
self.addEventListener('notificationclick', function(event) {
    console.log('[firebase-messaging-sw.js] Notification click received.', event);
    
    event.notification.close();

    const notificationData = event.notification.data || {};
    const notificationType = notificationData.type || notificationData.notification_type;
    const notificationId = notificationData.notification_id || notificationData.message_id;
    const rideId = notificationData.ride_id;
    // Support multiple field names for sender/user ID
    const postedBy = notificationData.posted_by || notificationData.sender_id || notificationData.other_user_id;
    const departure = notificationData.departure;
    const destination = notificationData.destination;
    const lang = notificationData.lang || 'en';

    let targetUrl = '/';

    // Determine target URL based on notification type
    if (notificationType === 'chat' || notificationType === 'chat received' || notificationType === null || !notificationType) {
        // Chat/message notification
        if (rideId && postedBy) {
            // Navigate to specific chat
            targetUrl = `/${lang}/chat-detail/${rideId}/${postedBy}`;
        } else {
            // Navigate to inbox
            targetUrl = `/${lang}/my-chats`;
        }
    } else if (notificationType === '1' || notificationData.type === '1') {
        // My ride notification
        if (rideId && departure && destination) {
            targetUrl = `/${lang}/my-ride/${departure}/to/${destination}/${rideId}`;
        } else {
            targetUrl = `/${lang}/my-chats`;
        }
    } else if (notificationType === '2' || notificationData.type === '2') {
        // Ride detail notification
        if (rideId && departure && destination) {
            targetUrl = `/${lang}/ride/${departure}/to/${destination}/${rideId}`;
        } else {
            targetUrl = `/${lang}/my-chats`;
        }
    } else {
        // Default to my chats for message notifications
        targetUrl = `/${lang}/my-chats`;
    }

    // Mark notification as read if we have the notification ID
    if (notificationId) {
        // Make API call to mark as read (this will be handled by the page when it loads)
        fetch(`/read-notification?id=${notificationId}`, {
            method: 'GET',
            credentials: 'include'
        }).catch(err => console.error('Failed to mark notification as read:', err));
    }

    // Open the target URL
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Check if there's already a window open
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            // If no matching window, open a new one
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});