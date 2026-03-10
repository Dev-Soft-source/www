<!DOCTYPE html>
<html>
<head>
  <title>ProximaRide</title>
  <style>
    @font-face {
      font-family: 'Arial';
      src: url('/../images/fonts/arial.ttf') format('truetype');
      font-weight: 300;
      font-style: normal;
    }
    .hover-bg-_111827:hover {
      background-color: #111827 !important;
    }
    @media (max-width: 600px) {
      .sm-px-4 {
        padding-left: 16px !important;
        padding-right: 16px !important;
      }
      .sm-px-6 {
        padding-left: 24px !important;
        padding-right: 24px !important;
      }
    }
  </style>
</head>
<body style="margin: 0; width: 100%; background-color: #fff; padding: 0; -webkit-font-smoothing: antialiased; word-break: break-word;">
  <div role="article" aria-roledescription="email" aria-label="Holiday Season Email" lang="en">
    <div class="sm-px-4" style="background-color: #fff; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;">
      <table align="center" cellpadding="0" cellspacing="0" role="none">
        <tr>
          <td style="width: 600px; max-width: 100%;background:#fff; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <table style="width: 100%;margin-bottom: 24px;" cellpadding="0" cellspacing="0" role="none">
                <tr>
                    <td align="center" style="background:#fff;border-bottom: 3px solid black; padding: 20px 0;">
                        <center>
                            <table align="center" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('home', app()->getLocale()) }}" style="display: inline-block;">
                                            <img src="{{ asset('assets/PROXIMARIDE.png') }}" alt="ProximaRide" width="200" height="100"
                                                style="display: block; max-width: 200px; max-height: 100px; width: auto; height: auto; border: 0;">
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <table style="width: 100%; padding: 10px 16px;" cellpadding="0" cellspacing="0" role="none">
                <tr>
                    <td>
                        <h1 style="font-weight: 700;color: #000;">Merry Christmas and Happy New Year {{ $data['first_name'] }},</h1>
                        <p style="font-weight: 400;color: #000;">On behalf of the entire ProximaRide family, we are sending you our warmest wishes in the Holiday Season. May your life be filled with wonderful people and magical moments.</p>
                        <p style="font-weight: 400;color: #000;">Have a joyful new year.</p>

                        <!-- Holiday image -->
                        <div style="text-align: center; margin: 20px 0;">
                            <img src="https://i.gyazo.com/2f93e10b8dd0d27d1a7510ee769fc7fe.png" alt="Happy Holidays" style="max-width: 100%; height: auto;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <p style="color: #000; font-weight: 700;margin-top:12px;">The Entire ProximaRide Team</p>
                    </th>
                </tr>
                <tr>
                  <td align="center">
                    <table style="margin-bottom: 24px; margin-top: 16px; width: 100%" cellpadding="0" cellspacing="0"
                    role="none">
                    <tr>
                        <td align="center" style="display: flex;">
                            <div style="display: flex; margin: 0 auto;">
                                <a aria-label="ProximaRide" target="_blank" href="{{ route('contact_us', app()->getLocale()) }}"
                                    style="border-right: 1px solid #000; text-decoration: none; font-weight: 700; color: #000; white-space: nowrap;font-size: 16px; padding-right: 16px; padding-left: 16px;">
                                    Help & Contact
                                </a>
                                <a aria-label="ProximaRide" target="_blank" href="{{ route('terms_use', app()->getLocale()) }}"
                                    style="border-right: 1px solid #000; text-decoration: none; font-weight: 700; color: #000; white-space: nowrap;font-size: 16px; padding-right: 16px; padding-left: 16px;">
                                    Terms of Use
                                </a>
                                <a aria-label="ProximaRide" target="_blank" href="{{ route('coffee_on_wall', app()->getLocale()) }}"
                                    style="text-decoration: none; font-weight: 700; color: #000; white-space: nowrap;font-size: 16px; padding-right: 16px; padding-left: 16px;">
                                    Coffee on the Wall
                                </a>
                            </div>
                        </td>
                    </tr>
                    </table>
                    <div style="border: 1px dashed #000;width: 100%; margin-bottom: 10px;"></div>
                    <p style="text-align: justify;color: #000;margin-bottom: 0;">
                    Please do not reply to this message; it is an automated email and all replies to it are routed to an
                    unmonitored mailbox. To reply to the messages you receive from drivers and passengers, log in to your inbox. We send this email only once a year, but you can still Unsubscribe if you wish
                    </p>
                  </td>
                </tr>
            </table>
            <table style="width: 100%;margin-top: 24px;" cellpadding="0" cellspacing="0" role="none">
            <tr>
                    <td class="sm-px-6"
                        style="background-color: #f3f4f6; padding: 16px 48px; text-align: left; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05)">
                        <table style="margin-bottom: 24px; margin-top: 16px; width: 100%" cellpadding="0" cellspacing="0"
                            role="none">
                            <tr>
                                <td align="center" style="text-align: center;">
                                    <div style="text-align: center;">
                                        <a aria-label="ProximaRide" target="_blank" href="https://facebook.com"
                                            style="border: 1px solid #d1d5db; display: inline-block; height: 40px; width: 40px; border-radius: 50%; background-color: #fffffe; overflow: hidden; text-decoration: none; margin: 0 8px; vertical-align: middle; line-height: 40px; text-align: center;">
                                            <img src="{{ asset('assets/images/facebook.png') }}" alt="facebook icon" width="20" height="20"
                                                style="display: inline-block; vertical-align: middle; max-width: 20px; max-height: 20px;">
                                        </a>
                                        <a aria-label="ProximaRide" target="_blank" href="https://twitter.com"
                                            style="border: 1px solid #d1d5db; display: inline-block; height: 40px; width: 40px; border-radius: 50%; background-color: #fffffe; overflow: hidden; text-decoration: none; margin: 0 8px; vertical-align: middle; line-height: 40px; text-align: center;">
                                            <img src="{{ asset('assets/images/twitter.png') }}" alt="twitter icon" width="20" height="20"
                                                style="display: inline-block; vertical-align: middle; max-width: 20px; max-height: 20px;">
                                        </a>
                                        <a aria-label="ProximaRide" target="_blank" href="https://www.instagram.com"
                                            style="border: 1px solid #d1d5db; display: inline-block; height: 40px; width: 40px; border-radius: 50%; background-color: #fffffe; overflow: hidden; text-decoration: none; margin: 0 8px; vertical-align: middle; line-height: 40px; text-align: center;">
                                            <img src="{{ asset('assets/images/instagram.png') }}" alt="instagram icon" width="20" height="20"
                                                style="display: inline-block; vertical-align: middle; max-width: 20px; max-height: 20px;">
                                        </a>
                                        <a aria-label="ProximaRide" target="_blank" href="https://youtube.com"
                                            style="border: 1px solid #d1d5db; display: inline-block; height: 40px; width: 40px; border-radius: 50%; background-color: #fffffe; overflow: hidden; text-decoration: none; margin: 0 8px; vertical-align: middle; line-height: 40px; text-align: center;">
                                            <img src="{{ asset('assets/images/youtube.png') }}" alt="youtube icon" width="20" height="20"
                                                style="display: inline-block; vertical-align: middle; max-width: 20px; max-height: 20px;">
                                        </a>
                                        <a aria-label="ProximaRide" target="_blank" href="https://www.linkedin.com"
                                            style="border: 1px solid #d1d5db; display: inline-block; height: 40px; width: 40px; border-radius: 50%; background-color: #fffffe; overflow: hidden; text-decoration: none; margin: 0 8px; vertical-align: middle; line-height: 40px; text-align: center;">
                                            <img src="{{ asset('assets/images/linkedin.png') }}" alt="linkedin icon" width="20" height="20"
                                                style="display: inline-block; vertical-align: middle; max-width: 20px; max-height: 20px;">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table style="margin: 8px auto" cellpadding="0" cellspacing="0" role="none">
                            <tr>
                                <td>
                                    <p
                                        style="margin: 0; white-space: nowrap; padding-left: 8px; padding-right: 8px; font-size: 16px; color: #000;">
                                        © 2026 ProximaRide. All Rights Reserved </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </div>
</body>
</html>
