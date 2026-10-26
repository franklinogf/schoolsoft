<!DOCTYPE html>
<html lang="{{ __LANG }}">
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#333;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border-radius:6px;overflow:hidden;">
                    <tr>
                        <td style="background:#1f4e79;color:#fff;padding:18px 24px;font-size:18px;font-weight:bold;">{{ $schoolName }}</td>
                    </tr>
                    <tr>
                        <td style="padding:24px;font-size:15px;line-height:1.5;">
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;background:#f9fafb;color:#888;font-size:12px;">
                            {{ __('autopay.email.footer', ['school' => $schoolName]) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
