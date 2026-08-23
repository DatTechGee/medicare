<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#eef1f6;font-family:Segoe UI,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef1f6;padding:32px 12px;">
<tr><td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 4px 24px rgba(15,20,40,.08);">
<tr><td style="background:linear-gradient(135deg,#627eea,#00d4aa);padding:26px 34px;">
<span style="font-size:20px;font-weight:800;color:#fff;">&#9829; MediFund</span><br>
<span style="font-size:12px;color:#e8f0ff;font-weight:600;">TRANSPARENT MEDICAL CROWDFUNDING</span>
</td></tr>
<tr><td style="padding:30px 34px 10px 34px;">
<h2 style="margin:0 0 6px;font-size:20px;color:#141428;">Thank you, {{ $name }}!</h2>
<p style="margin:0;font-size:14px;line-height:1.7;color:#5b6478;">
Your blockchain donation has been confirmed on the demo Ethereum network and is now held in the campaign's auditable escrow. Funds are only released to the patient against admin-verified medical milestones.
</p>
</td></tr>
<tr><td style="padding:18px 34px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:2px dashed #d8def0;border-radius:12px;background:#f8fafe;text-align:center;padding:16px;">
<tr><td style="padding:10px;font-size:11px;font-weight:800;letter-spacing:.08em;color:#7c86a0;">AMOUNT</td></tr>
<tr><td style="padding:0 10px 4px;font-size:26px;font-weight:800;color:#141428;">{{ $amount }} &nbsp;<span style="color:#627eea;font-size:14px;">&asymp; {{ number_format($ethAmount, 6) }} ETH</span></td></tr>
<tr><td style="padding:8px 10px 12px;font-size:13px;color:#3d4560;"><strong>{{ $campaignTitle }}</strong></td></tr>
</table>
</td></tr>
<tr><td style="padding:6px 34px 26px 34px;">
<p style="margin:0 0 14px;font-size:12.5px;line-height:1.8;color:#5b6478;word-break:break-all;">
<strong>Transaction hash:</strong><br>{{ $txHash }}
</p>
<a href="{{ $receiptUrl }}" style="display:inline-block;background:linear-gradient(135deg,#627eea,#4b6ceb);color:#ffffff;text-decoration:none;font-size:14px;font-weight:800;padding:12px 26px;border-radius:10px;margin-right:8px;">View Receipt</a>
<a href="{{ $txUrl }}" style="display:inline-block;background:#ffffff;border:1.5px solid #d8def0;color:#3d4560;text-decoration:none;font-size:14px;font-weight:700;padding:11px 22px;border-radius:10px;">Verify on Explorer</a>
</td></tr>
<tr><td style="border-top:1px solid #eaeff7;padding:18px 34px;font-size:11px;line-height:1.7;color:#8b93ab;text-align:center;">
You are receiving this because you donated via MediFund. This demo platform records transactions on a simulated Ethereum network for educational purposes.
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
