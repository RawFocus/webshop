<!DOCTYPE 'html'>
<html xmlns:v="urn:schemas-microsoft-com:vml">
<head>
  <meta charset="utf-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no, date=no, address=no, email=no, url=no">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <!--[if mso]>
            <noscript>
                <xml>
                    <o:OfficeDocumentSettings xmlns:o="urn:schemas-microsoft-com:office:office">
                        <o:PixelsPerInch>96</o:PixelsPerInch>
                    </o:OfficeDocumentSettings>
                </xml>
            </noscript>
            <style>
                td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family: "Segoe UI", sans-serif; mso-line-height-rule: exactly;}
            </style>
        <![endif]-->
  <style>
    .hover-text-black:hover {
      color: #000 !important
    }
    .hover-underline:hover {
      text-decoration-line: underline !important
    }
    @media (max-width: 600px) {
      .sm-px-4 {
        padding-left: 16px !important;
        padding-right: 16px !important
      }
      .sm-px-6 {
        padding-left: 24px !important;
        padding-right: 24px !important
      }
      .sm-py-8 {
        padding-top: 32px !important;
        padding-bottom: 32px !important
      }
      .sm-leading-8 {
        line-height: 32px !important
      }
    }
  </style>
</head>
<body style="margin: 0; width: 100%; background-color: #f9fafb; padding: 0; color: #000; -webkit-font-smoothing: antialiased; word-break: break-word">
  <div role="article" aria-roledescription="email">
    <div class="sm-px-4" style="background-color: #f8fafc; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif">
      <table align="center" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
          <td style="width: 600px; max-width: 100%">
            <div class="sm-py-8 sm-px-6" style="padding: 48px; text-align: center">
              <a href="https://klimbuddies.nl" target="_blank" alt="Klimbuddies.nl" class="hover-underline hover-text-black" style="color: #4b5563; text-decoration-line: none; text-underline-offset: 2px; transition-property: all; transition-duration: 200ms; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1)">
                <img src="https://klimbuddies.nl/assets/logo-black.e717cfb1.png" style="max-width: 100%; vertical-align: middle; line-height: 1; border: 0; height: 48px" alt="">
                <div style="padding-top: 8px; text-align: center; font-weight: 600; text-transform: uppercase">
                  Klimbuddies.nl
                </div>
              </a>
            </div>
            <table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
              <tr>
                <td class="sm-px-6" style="border-radius: 8px; background-color: #fff; padding: 48px; font-size: 16px; color: #334155; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05)">
                  <h1 class="sm-leading-8" style="margin: 0 0 24px; font-size: 24px; font-weight: 600; color: #000">
                    {{ $title }}
                  </h1>
                  <p style="margin: 0 0 24px; line-height: 24px">
                    {!! $textOne !!}
                  </p>
                  <div>
                    <a href="{{ $link }}" style="display: inline-block; border-radius: 4px; background-color: #4338ca; padding: 16px 24px; font-size: 16px; font-weight: 600; line-height: 1; color: #f8fafc; text-decoration: none">
                      <!--[if mso]>
      <i style="mso-font-width: -100%; letter-spacing: 32px; mso-text-raise: 30px" hidden>&nbsp;</i>
    <![endif]-->
                      <span style="mso-text-raise: 16px">
                      {{ $action }}
                      </span>
                      <!--[if mso]>
      <i style="mso-font-width: -100%; letter-spacing: 32px;" hidden>&nbsp;</i>
    <![endif]-->
                    </a>
                  </div>
                  <p style="margin: 24px 0; line-height: 24px">
                    {!! $textTwo !!}
                  </p>
                  <p style="margin: 24px 0; line-height: 24px;">
                    {!! $textThree !!}
                  </p>
                  <p style="margin: 0; line-height: 24px;">
                    {!! $closing !!}
                  </p>
                </td>
              </tr>
              <tr role="separator">
                <td style="line-height: 48px">&zwj;</td>
              </tr>
              <tr>
                <td style="padding-left: 24px; padding-right: 24px; text-align: center; font-size: 12px; color: #475569">
                  <p style="margin: 0 0 48px; text-transform: uppercase">
                    {!! $copyright !!}
                  </p>
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
