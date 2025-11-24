<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • Forgot Password</title>
    @vite(['resources/css/app.css'])
  </head>
  <body class="min-h-screen w-full bg-[#F7F7F6]">
    <div class="min-h-screen flex items-center justify-center px-4">
      <div class="w-full max-w-md bg-white rounded border border-[#ecebea] shadow-sm p-6">
        <h1 class="text-xl font-semibold mb-2">Forgot password</h1>
        <p class="text-sm text-[#6b6a67] mb-4">Enter your admin email to receive a verification code.</p>
        <form method="POST" action="{{ route('forgot.post') }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium text-[#3a3937] mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="admin@example.com" />
            @error('email')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Send code</button>
          @if(session('sent'))
            <div class="text-sm text-[#0B6B3A]">A verification code has been sent (demo flow).</div>
          @endif
        </form>
        <div class="text-center mt-4 text-sm"><a href="{{ route('login') }}" class="text-[#0B6B3A] hover:underline">Back to sign in</a></div>
      </div>
    </div>
  </body>
</html>
