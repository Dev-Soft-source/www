# Flutter Project

This is a Flutter project. Follow the steps below to get it up and running on your local machine, including setup for both Android and iOS platforms.

---

## 🛠️ Getting Started

### Prerequisites

Make sure you have the following installed:

- [Flutter SDK](https://flutter.dev/docs/get-started/install)
- Dart SDK (comes with Flutter)
- Android Studio and/or Visual Studio Code
- Xcode (for iOS development)
- An Android emulator or iOS simulator (or a physical device)

To verify your setup, run:

```bash
flutter doctor
```

---

## Installation

### 2. Get dependencies:

```bash
flutter pub get
```

---

## Running the App

### Android

```bash
flutter run
```

Make sure an Android emulator is running or a device is connected.

### iOS

#### Option A: Using Terminal

```bash
flutter run
```

Make sure an iOS simulator or physical iPhone is connected.

#### Option B: Using Xcode

1. Open the `ios/Runner.xcworkspace` file in Xcode.
2. Select your development team in **Signing & Capabilities**.
3. Run the app using the play button or `Cmd + R`.

> ⚠️ Note: macOS and Xcode are required for iOS builds.

---

## Troubleshooting

- Run `flutter doctor` to check for any missing dependencies.
- Use `flutter clean` and `flutter pub get` if you encounter build errors.
- For iOS-specific issues, ensure CocoaPods is installed and updated:
  
  ```bash
  sudo gem install cocoapods
  pod install --project-directory=ios
  ```

---
