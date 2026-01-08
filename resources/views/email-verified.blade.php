<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - ProximaRide</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
            text-align: center;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            padding: 40px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 30px;
        }
        .status-success { color: #059669; }
        .status-error { color: #dc2626; }
        .status-already-verified { color: #2563eb; }
        .message {
            font-size: 18px;
            margin: 20px 0;
            line-height: 1.5;
        }
        .app-info {
            font-size: 14px;
            color: #6b7280;
            margin-top: 30px;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 5px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 20px auto;
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">ProximaRide</div>
        
        @if($status === 'success')
            <div class="message status-success">
                ✅ {{ $message }}
            </div>
        @elseif($status === 'error')
            <div class="message status-error">
                ❌ {{ $message }}
            </div>
        @else
            <div class="message status-already-verified">
                ✅ {{ $message }}
            </div>
        @endif

        @if($isApp)
            <div class="app-info">
                <p><strong>You can now close this window and return to the ProximaRide app.</strong></p>
                <p>If you're not automatically redirected, please manually return to the app.</p>
                @if($token)
                    <p style="font-size: 12px; color: #9ca3af;">Authentication token available for app login.</p>
                @endif
            </div>
            
            <script>
                // For app deep linking - try to redirect back to app after 3 seconds
                setTimeout(function() {
                    // Try to open the app with custom URL scheme
                    try {
                        var deepLinkUrl = 'proximaride://email-verified?status={{ $status }}';
                        @if($token)
                            deepLinkUrl += '&token={{ $token }}';
                        @endif
                        window.location.href = deepLinkUrl;
                    } catch (e) {
                        // Fallback - close window if opened in popup
                        try {
                            window.close();
                        } catch (closeError) {
                            // Can't close, just display message
                        }
                    }
                }, 3000);
                
                // Also try immediate redirect for better UX
                try {
                    var deepLinkUrl = 'proximaride://email-verified?status={{ $status }}';
                    @if($token)
                        deepLinkUrl += '&token={{ $token }}';
                    @endif
                    window.location.href = deepLinkUrl;
                } catch (e) {
                    // App not installed or can't handle, show web message
                }
            </script>
        @else
            <div class="app-info">
                <p>Redirecting you to the login page...</p>
            </div>
            
            <script>
                setTimeout(function() {
                    window.location.href = '{{ route("login") }}';
                }, 2000);
            </script>
        @endif
    </div>
</body>
</html>