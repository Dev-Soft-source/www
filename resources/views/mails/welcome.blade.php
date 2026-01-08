<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
  <meta charset="utf-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no, date=no, address=no, email=no, url=no">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <title>Welcome to ProximaRide</title>
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
  <div role="article" aria-roledescription="email" aria-label="Ridesharing Email" lang="en">
    <div class="sm-px-4" style="background-color: #fff; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;">
      <table align="center" cellpadding="0" cellspacing="0" role="none">
        <tr>
          <td style="width: 600px; max-width: 100%;background:#fff; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <table style="width: 100%;margin-bottom: 24px;" cellpadding="0" cellspacing="0" role="none">
                <tr>
                    <td align="center" style="display: flex;background:#fff;border-bottom: 3px solid black;">
                        <div style="display: flex; margin: 20px auto;">
                            <a href="">
                                <img src="{{ asset('assets/PROXIMARIDE.png') }}" alt="ProximaRide"
                                    style="height: 100px; vertical-align: middle">
                            </a>
                        </div>
                    </td>
                </tr>
            </table>
            <table style="width: 100%; padding: 10px 16px;" cellpadding="0" cellspacing="0" role="none">
                <tr>
                    <td>
                        <h1 style="font-weight: 700;color: #000;">{{ $greeting_message ?? 'Hi' }} {{ $data['first_name'] }},</h1>
                        <p style="font-weight: 400;color: #000;">Thanks for signing up to ProximaRide, and welcome! </p>
                        <p style="font-weight: 400;color: #000;">I’m Erman, a dad and the founder, and glad that you decided to join us.
                            I started ProximaRide because I wanted to make ridesharing safer, more affordable and more reliable for people like my daughter, who travels to her school in Ottawa (from Montreal) every week. Everyone should arrive at their destination safely, just like her
                        </p>
                        <p style="font-weight: 400;color: #000;">Don’t worry, we don’t send a lot of emails; just the essentials. So, just relax and enjoy the ride </p>
                        <p style="font-weight: 400;color: #000;">We are always here to answer any queries that you may have so feel free to contact us
                            And remember - sharing is caring
                        </p>
                        <p style="font-weight: 400;color: #000;">By the way, have you completed your profile yet? If not yet,
                         <br>
                         <br>
                         <a style="border-radius: 4px; background-color: #2563eb; padding: 8px 16px; font-size: 16px; font-weight: bold; color: #ffffff; text-decoration: none; display: inline-flex;margin: 8px 0 6px 0; margin-bottom: 16px; margin-top: 16px;" href="{{ route('welcomeRoute', ['email' => $data['email']]) }}" target="_blank">click here</a>
                         <br>
                         <br>
                         to do so; it’s only four easy steps and only takes a couple of minutes
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>
                        <p style="color: #000; font-weight: 700;margin-top:12px;">
                            <span style="font-weight: 400;">Again, thank you for joining, and welcome</span> <br>
                                Erman, ProximaRide Founder <br>
                                And the entire {{ env('APP_NAME') }} Team
                        </p>
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
                    unmonitored mailbox. To reply to the messages you receive from drivers and passengers, log in to your inbox
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
                                <td align="center" style="display: flex;">
                                    <div style="display: flex; margin: 0 auto;">
                                        <a aria-label="ProximaRide" target="_blank" href="https://facebook.com"
                                            style="border: 1px solid #d1d5db; display: flex; height: 40px; width: 40px; border-radius: 9999px; background-color: #fffffe;margin-right: 16px;">
                                            <img src="{{ asset('assets/images/facebook.png') }}" alt="facebook icon"
                                                style="max-width: 100%; vertical-align: middle; height: 20px;margin-top: 10px;margin-left: 13px;">
                                        </a>
                                        <a aria-label="ProximaRide" target="_blank" href="https://twitter.com"
                                            style="border: 1px solid #d1d5db; display: flex; height: 40px; width: 40px; border-radius: 9999px; background-color: #fffffe;margin-right: 16px;">
                                            <img src="{{ asset('assets/images/twitter.png') }}" alt="twiiter icon"
                                                style="max-width: 100%; vertical-align: middle; height: 16px;margin-top: 11px;margin-left: 10px;">
                                        </a>
                                        <a aria-label="ProximaRide" target="_blank" href="https://www.instagram.com"
                                            style="border: 1px solid #d1d5db; display: flex; height: 40px; width: 40px; border-radius: 9999px; background-color: #fffffe;margin-right: 16px;">
                                            <img src="{{ asset('assets/images/instagram.png') }}" alt="google icon"
                                                style="max-width: 100%; vertical-align: middle; height: 20px;margin-top: 9px;margin-left: 9px;">
                                        </a>
                                        <a aria-label="ProximaRide" target="_blank" href="https://youtube.com"
                                            style="border: 1px solid #d1d5db; display: flex; height: 40px; width: 40px; border-radius: 9999px; background-color: #fffffe;margin-right: 16px;">
                                            <img src="{{ asset('assets/images/youtube.png') }}" alt="youtube icon"
                                                style="max-width: 100%; vertical-align: middle; height: 16px;margin-top: 11px;margin-left: 8px;">
                                        </a>
                                        <a aria-label="ProximaRide" target="_blank" href="https://www.linkedin.com"
                                            style="border: 1px solid #d1d5db; display: flex; height: 40px; width: 40px; border-radius: 9999px; background-color: #fffffe;">
                                            <img src="{{ asset('assets/images/linkedin.png') }}" alt="linkedin icon"
                                                style="max-width: 100%; vertical-align: middle; height: 16px;margin-top: 10px;margin-left: 12px;">
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
