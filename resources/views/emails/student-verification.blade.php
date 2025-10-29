<div style="background-color:#f5f7fb;padding:24px 0;width:100%;">
	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
		<tr>
			<td style="padding:20px 24px;border-bottom:1px solid #f1f5f9;background:#0f172a;">
				<div style="color:#fff;font-weight:700;font-size:16px;letter-spacing:.3px;">
					{{ config('app.name', 'OJT360') }}
				</div>
			</td>
		</tr>
		<tr>
			<td style="padding:24px; font-family:ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, Ubuntu, 'Helvetica Neue', Arial; color:#0f172a;">
				<p style="margin:0 0 12px 0;">Hello {{ $name }},</p>
				<p style="margin:0 0 16px 0; line-height:1.5;">We received a request to start your student registration for Student ID <strong>{{ $studentId }}</strong>.</p>
				<p style="margin:0 0 16px 0; line-height:1.5;">Please click the button below to continue and complete your registration.</p>

				<p style="margin:0 0 20px 0;">
					<a href="{{ $link }}" style="display:inline-block;background:#7c0a02;color:#ffffff;padding:12px 18px;border-radius:8px;text-decoration:none;font-weight:600;">Complete Registration</a>
				</p>

				<p style="margin:0 0 8px 0; color:#475569; font-size:12px;">This link will expire in 1 hour.</p>
				<p style="margin:0 0 16px 0; color:#475569; font-size:12px;">If you didn’t request this, you can safely ignore this email.</p>

				<p style="margin:0 0 6px 0; color:#64748b; font-size:12px;">If the button doesn’t work, copy and paste this URL into your browser:</p>
				<p style="margin:0 0 0 0; word-break:break-all; color:#334155; font-size:12px;">{{ $link }}</p>
			</td>
		</tr>
		<tr>
			<td style="padding:14px 24px;border-top:1px solid #f1f5f9;background:#f8fafc;color:#64748b;font-size:12px;">
				<p style="margin:0;">© {{ date('Y') }} {{ config('app.name', 'OJT360') }}. All rights reserved.</p>
			</td>
		</tr>
	</table>
</div>


