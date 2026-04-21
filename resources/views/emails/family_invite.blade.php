<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Secure Workspace Invitation - FinanceAI</title>
    
    <style type="text/css">
        /* Client-specific Resets */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; }

        /* iOS Blue Links Override */
        a[x-apple-data-detectors] {
            color: inherit !important; text-decoration: none !important; font-size: inherit !important;
            font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important;
        }

        /* Hover Effects */
        .cta-button:hover { background-color: #4338ca !important; border-color: #4338ca !important; }
        .text-link:hover { text-decoration: underline !important; color: #4338ca !important; }

        /* Mobile Styles */
        @media screen and (max-width: 600px) {
            .wrapper { width: 100% !important; max-width: 100% !important; }
            .mobile-padding { padding-left: 15px !important; padding-right: 15px !important; padding-top: 20px !important; padding-bottom: 20px !important;}
            .mobile-stack { display: block !important; width: 100% !important; padding-bottom: 15px !important; padding-left: 0 !important; padding-right: 0 !important;}
            .mobile-hide { display: none !important; }
            .mobile-text-center { text-align: center !important; }
            .step-arrow { display: none !important; }
        }
        
        /* Dark Mode Overrides for Email Clients */
        @media (prefers-color-scheme: dark) {
            body, .bg-main { background-color: #0f172a !important; }
            .bg-card { background-color: #1e293b !important; border-color: #334155 !important; }
            .text-dark { color: #f8fafc !important; }
            .text-muted { color: #94a3b8 !important; }
            .bg-sub { background-color: #334155 !important; border-color: #475569 !important; }
        }
    </style>
</head>

@php
    // Safe Data Extraction
    $inviter = $inviterName ?? 'A Network Administrator';
    $workspace = $family->name ?? 'Secure Workspace';
    $rawToken = $invite->token ?? strtoupper(bin2hex(random_bytes(8)));
    $shortToken = strtoupper(substr($rawToken, 0, 16));
    $expiry = isset($invite->expires_at) ? \Carbon\Carbon::parse($invite->expires_at)->format('d M Y, H:i T') : '7 Days';
    $url = $acceptUrl ?? '#';
    $traceId = 'SYS-' . rand(1000, 9999) . '-' . strtoupper(substr(md5($workspace), 0, 4));
@endphp

<body class="bg-main" style="background-color: #f8fafc; margin: 0 !important; padding: 0 !important;">

    <div style="display: none; max-height: 0px; overflow: hidden; color: #f8fafc; font-size: 1px; line-height: 1px;">
        Action Required: {{ $inviter }} has requested your authorization to join {{ $workspace }}. Cryptographic token enclosed.
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>

    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="bg-main" style="background-color: #f8fafc;">
        <tr>
            <td align="center" style="padding: 40px 10px;" class="mobile-padding">
                
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);" class="wrapper bg-card">
                    
                    <tr>
                        <td align="center" style="padding: 40px 0 0 0;">
                            <table border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                                <tr>
                                    <td align="center" style="width: 54px; height: 54px; background-color: #f0fdf4; border-radius: 14px; border: 1px solid #bbf7d0;">
                                        <span style="color: #10b981; font-size: 26px; font-weight: 900; font-family: Helvetica, Arial, sans-serif;">F</span>
                                    </td>
                                </tr>
                            </table>
                            <h1 class="text-dark" style="color: #0f172a; font-size: 24px; font-weight: 900; margin: 16px 0 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; letter-spacing: -0.5px;">FinanceAI</h1>
                            <p class="text-muted" style="color: #64748b; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 2.5px; margin: 6px 0 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">Enterprise Master Node</p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 35px 40px 0 40px;" class="mobile-padding">
                            <h2 class="text-dark" style="color: #0f172a; font-size: 20px; font-weight: 800; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">Connection Request</h2>
                            <p class="text-muted" style="color: #475569; font-size: 15px; line-height: 24px; margin: 15px 0 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                                <strong class="text-dark" style="color: #0f172a;">{{ $inviter }}</strong> has requested your authorization to synchronize your node with the following ledger:
                            </p>
                            
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 25px;">
                                <tr>
                                    <td align="center" style="background-color: #e0e7ff; padding: 16px 24px; border-radius: 12px; border: 1px solid #c7d2fe;">
                                        <span style="color: #4338ca; font-size: 18px; font-weight: 900; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; letter-spacing: -0.5px;">
                                            {{ $workspace }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 30px 40px 0 40px;" class="mobile-padding">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="bg-sub" style="background-color: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px;">
                                <tr>
                                    <td align="left" style="padding: 0 0 15px 0; border-bottom: 1px solid #e2e8f0;">
                                        <p class="text-dark" style="color: #0f172a; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                                            <span style="color: #10b981;">●</span> Security Handshake Payload
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding: 15px 0 0 0;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="35%" style="padding-bottom: 8px;"><span class="text-muted" style="color: #64748b; font-size: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">Trace Hash:</span></td>
                                                <td width="65%" style="padding-bottom: 8px;"><span class="text-dark" style="color: #0f172a; font-size: 12px; font-weight: 600; font-family: 'Courier New', Courier, monospace;">{{ $shortToken }}</span></td>
                                            </tr>
                                            <tr>
                                                <td width="35%" style="padding-bottom: 8px;"><span class="text-muted" style="color: #64748b; font-size: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">Clearance:</span></td>
                                                <td width="65%" style="padding-bottom: 8px;"><span class="text-dark" style="color: #0f172a; font-size: 12px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">Level 1 (Member)</span></td>
                                            </tr>
                                            <tr>
                                                <td><span class="text-muted" style="color: #64748b; font-size: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">Time-To-Live:</span></td>
                                                <td><span style="color: #e11d48; font-size: 12px; font-weight: 700; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">{{ $expiry }}</span></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 35px 40px 25px 40px;" class="mobile-padding">
                            
                            <a href="{{ $url }}" target="_blank" class="cta-button" style="background-color: #4f46e5; border-radius: 12px; color: #ffffff; display: inline-block; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 800; line-height: 54px; text-align: center; text-decoration: none; width: 280px; -webkit-text-size-adjust: none; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 10px -2px rgba(79, 70, 229, 0.3);">
                                    Establish Connection &rarr;
                                </a>
                            </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 0 40px 30px 40px;" class="mobile-padding">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" class="mobile-stack">
                                        <span style="color: #10b981; font-weight: bold; font-size: 12px; font-family: -apple-system, sans-serif;">1.</span>
                                        <span class="text-muted" style="color: #64748b; font-size: 11px; font-weight: 600; font-family: -apple-system, sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">Authenticate</span>
                                    </td>
                                    <td align="center" width="20" class="step-arrow"><span style="color: #cbd5e1; font-size: 12px;">&rarr;</span></td>
                                    <td align="center" class="mobile-stack">
                                        <span style="color: #10b981; font-weight: bold; font-size: 12px; font-family: -apple-system, sans-serif;">2.</span>
                                        <span class="text-muted" style="color: #64748b; font-size: 11px; font-weight: 600; font-family: -apple-system, sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">Sync Ledger</span>
                                    </td>
                                    <td align="center" width="20" class="step-arrow"><span style="color: #cbd5e1; font-size: 12px;">&rarr;</span></td>
                                    <td align="center" class="mobile-stack">
                                        <span style="color: #10b981; font-weight: bold; font-size: 12px; font-family: -apple-system, sans-serif;">3.</span>
                                        <span class="text-muted" style="color: #64748b; font-size: 11px; font-weight: 600; font-family: -apple-system, sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">Collaborate</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 0 40px 30px 40px;" class="mobile-padding">
                            <p class="text-muted" style="color: #94a3b8; font-size: 11px; line-height: 18px; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                                If the button fails, paste this sequence directly into your browser:<br>
                                <a href="{{ $url }}" class="text-link" style="color: #4f46e5; text-decoration: none; font-family: 'Courier New', Courier, monospace; word-break: break-all;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>

                </table>
                
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" class="wrapper">
                    <tr>
                        <td align="center" style="padding: 30px 20px 0 20px;">
                            <p class="text-muted" style="color: #94a3b8; font-size: 10px; line-height: 18px; margin: 0; font-family: 'Courier New', Courier, monospace; text-transform: uppercase; letter-spacing: 1px;">
                                Transmission Origin: <strong class="text-dark" style="color: #64748b;">FinanceAI Master Node</strong><br>
                                Trace ID: <span style="color: #4f46e5;">{{ $traceId }}</span><br>
                                Status: Secure · Encrypted · Auditable
                            </p>
                            <p style="color: #cbd5e1; font-size: 10px; margin: 15px 0 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                                &copy; {{ date('Y') }} FinanceAI Network. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>