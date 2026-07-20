@php($appUrl = config('app.url'))
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ملخّص يومك</title>
</head>
<body style="margin:0;padding:0;background:#f4efe4;font-family:'Segoe UI',Tahoma,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4efe4;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 6px 24px rgba(15,56,45,.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#1f6b52,#0f382d);padding:28px 24px;text-align:center;">
                            <div style="font-size:26px;font-weight:bold;color:#ffffff;">صوت</div>
                            <div style="font-size:13px;color:#ecd98f;margin-top:4px;">{{ $s['hijri'] }}</div>
                        </td>
                    </tr>

                    {{-- Greeting --}}
                    <tr>
                        <td style="padding:24px 24px 8px;">
                            <div style="font-size:18px;font-weight:bold;color:#0f382d;">السلام عليكم، {{ $user->name }}</div>
                            <div style="font-size:14px;color:#5b6b64;margin-top:6px;">
                                @if ($s['all_done'])
                                    ما شاء الله! أتممت عبادات اليوم كاملة. تقبّل الله منك 🌙
                                @else
                                    هذا ملخّص عباداتك اليوم — واصل، فالمداومة خير.
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- Summary rows --}}
                    <tr>
                        <td style="padding:8px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                @foreach ([
                                    ['🕌 الصلوات', $s['prayers_done'].' / '.$s['prayers_total'], $s['prayers_done'] >= 5],
                                    ['📖 ورد القرآن', $s['quran_pages'].' / '.$s['quran_goal'].' صفحة', $s['quran_pages'] >= $s['quran_goal']],
                                    ['📿 الأذكار', $s['azkar_done'].' / 2', $s['azkar_done'] >= 2],
                                    ['🤲 الصدقة', $s['sadaka_done'] ? 'تمّت' : 'لم تُسجَّل', $s['sadaka_done']],
                                ] as $row)
                                    <tr>
                                        <td style="padding:10px 0;border-bottom:1px solid #f0ece1;font-size:15px;color:#0f382d;">{{ $row[0] }}</td>
                                        <td style="padding:10px 0;border-bottom:1px solid #f0ece1;font-size:15px;text-align:left;color:{{ $row[2] ? '#1f6b52' : '#9a8f7a' }};font-weight:bold;">
                                            {{ $row[1] }} {!! $row[2] ? '✓' : '' !!}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    {{-- Stats --}}
                    <tr>
                        <td style="padding:16px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="33%" style="text-align:center;padding:12px;background:#f4efe4;border-radius:14px;">
                                        <div style="font-size:22px;font-weight:bold;color:#1f6b52;">+{{ $s['xp_today'] }}</div>
                                        <div style="font-size:11px;color:#5b6b64;">نقاط اليوم</div>
                                    </td>
                                    <td width="4"></td>
                                    <td width="33%" style="text-align:center;padding:12px;background:#f4efe4;border-radius:14px;">
                                        <div style="font-size:22px;font-weight:bold;color:#b8941f;">{{ $s['streak_overall'] }}</div>
                                        <div style="font-size:11px;color:#5b6b64;">أيام متتالية</div>
                                    </td>
                                    <td width="4"></td>
                                    <td width="33%" style="text-align:center;padding:12px;background:#f4efe4;border-radius:14px;">
                                        <div style="font-size:22px;font-weight:bold;color:#1f6b52;">{{ $s['level'] }}</div>
                                        <div style="font-size:11px;color:#5b6b64;">المستوى</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- CTA --}}
                    <tr>
                        <td style="padding:8px 24px 28px;text-align:center;">
                            <a href="{{ $appUrl }}/dashboard" style="display:inline-block;background:#d4af37;color:#0f382d;text-decoration:none;font-weight:bold;font-size:15px;padding:13px 28px;border-radius:14px;">افتح التطبيق</a>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:18px 24px;background:#faf8f3;text-align:center;font-size:12px;color:#9a8f7a;">
                            صوت · saout.net<br>
                            <a href="{{ $appUrl }}/account" style="color:#9a8f7a;">إدارة إشعارات البريد</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
