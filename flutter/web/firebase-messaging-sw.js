importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

const firebaseConfig = {
  apiKey: 'AIzaSyBt3Y5R24dI1V-qArWRVVXwSvrwrvreyf0',
  authDomain: 'proxima-ride-app-devop.firebaseapp.com',
  projectId: 'proxima-ride-app-devop',
  storageBucket: 'proxima-ride-app-devop.firebasestorage.app',
  messagingSenderId: '785619130237',
  appId: '1:785619130237:web:20f9ee0f705e60e4b5de14',
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
  const notificationTitle = payload.notification?.title || 'ProximaRide';
  const notificationOptions = {
    body: payload.notification?.body,
    icon: payload.notification?.icon || '/favicon.png',
    badge: '/favicon.png',
    data: payload.data || {},
    tag: payload.data?.notification_id || 'notification',
    requireInteraction: false,
  };

  if (notificationOptions.body) {
    return self.registration.showNotification(
      notificationTitle,
      notificationOptions,
    );
  }
});

self.addEventListener('notificationclick', function (event) {
  event.notification.close();

  const notificationData = event.notification.data || {};
  const notificationType =
    notificationData.type || notificationData.notification_type;
  const notificationId =
    notificationData.notification_id || notificationData.message_id;
  const rideId = notificationData.ride_id;
  const postedBy =
    notificationData.posted_by ||
    notificationData.sender_id ||
    notificationData.other_user_id;
  const departure = notificationData.departure;
  const destination = notificationData.destination;
  const lang = notificationData.lang || 'en';

  let targetUrl = '/';

  if (
    notificationType === 'chat' ||
    notificationType === 'chat received' ||
    notificationType === null ||
    !notificationType
  ) {
    targetUrl =
      rideId && postedBy
        ? `/${lang}/chat-detail/${rideId}/${postedBy}`
        : `/${lang}/my-chats`;
  } else if (notificationType === '1' || notificationData.type === '1') {
    targetUrl =
      rideId && departure && destination
        ? `/${lang}/my-ride/${departure}/to/${destination}/${rideId}`
        : `/${lang}/my-chats`;
  } else if (notificationType === '2' || notificationData.type === '2') {
    targetUrl =
      rideId && departure && destination
        ? `/${lang}/ride/${departure}/to/${destination}/${rideId}`
        : `/${lang}/my-chats`;
  } else {
    targetUrl = `/${lang}/my-chats`;
  }

  if (notificationId) {
    fetch(`/read-notification?id=${notificationId}`, {
      method: 'GET',
      credentials: 'include',
    }).catch(function () {});
  }

  event.waitUntil(
    clients
      .matchAll({ type: 'window', includeUncontrolled: true })
      .then(function (clientList) {
        for (let i = 0; i < clientList.length; i++) {
          const client = clientList[i];
          if (client.url.includes(targetUrl) && 'focus' in client) {
            return client.focus();
          }
        }
        if (clients.openWindow) {
          return clients.openWindow(targetUrl);
        }
      }),
  );
});
