
<p>Hello {{ $user->username }},</p>

<p>Thank you for registering! Please click the link below to verify your email address:</p>

<a href="{{ route('user.verify', $token) }}">Verify Email</a>

<p>If you did not create an account, no further action is required.</p>
