@extends('mail.customer.layout')

@section('content')
<h1>Welcome to Tech Hub, {{ $customer->name }}!</h1>
<p>We're absolutely thrilled to have you join our community. Tech Hub is your premier destination for the latest technology and enterprise IT solutions.</p>

<p>With your new account, you can now enjoy a faster checkout experience, track your orders in real-time, and get exclusive access to member-only deals.</p>

<div class="divider"></div>

<p>Ready to start shopping? Check out our latest collections today.</p>

<div style="text-align: center;">
    <a href="{{ route('home') }}" class="button">Explore the Shop</a>
</div>

<p style="margin-top: 30px;">If you have any questions, our support team is always here to help. Just reply to this email!</p>

<p>Best Regards,<br><strong>The Tech Hub Team</strong></p>
@endsection
