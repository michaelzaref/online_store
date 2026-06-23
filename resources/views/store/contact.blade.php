@extends('layouts.store')

@section('title', 'Contact Us')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-16">
    <h1 class="font-display text-3xl font-bold mb-2 text-center">Contact Us</h1>
    <p class="text-brand-text/70 text-center mb-8">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>

    <form action="{{ route('contact.store') }}" method="POST" class="bg-white border border-brand-text/10 rounded-lg p-8 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <input type="text" name="name" placeholder="Your Name" required value="{{ old('name') }}" class="border border-brand-text/15 rounded px-4 py-3">
            <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}" class="border border-brand-text/15 rounded px-4 py-3">
        </div>
        <input type="text" name="subject" placeholder="Subject" value="{{ old('subject') }}" class="w-full border border-brand-text/15 rounded px-4 py-3">
        <textarea name="message" rows="6" placeholder="Your message..." required class="w-full border border-brand-text/15 rounded px-4 py-3">{{ old('message') }}</textarea>
        <button type="submit" class="w-full btn-primary">Send Message</button>
    </form>

    <div class="mt-8 text-center text-sm text-brand-text/60">
        <p>{{ config('store.email') }} · {{ config('store.phone') }}</p>
    </div>
</div>
@endsection
