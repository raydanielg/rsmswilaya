<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • Reset Password</title>
    @vite(['resources/css/app.css'])
  </head>
  <body class="min-h-screen w-full bg-[#F7F7F6]">
    <div class="min-h-screen flex items-center justify-center px-4">
      <div class="w-full max-w-md bg-white rounded border border-[#ecebea] shadow-sm p-6">
        <h1 class="text-xl font-semibold mb-2">Verify code & set new password</h1>
        <p class="text-sm text-[#6b6a67] mb-4">Enter the 6-digit code sent to your email and your new password.</p>
        <form method="POST" action="{{ route('reset.post') }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium text-[#3a3937] mb-1">Verification code (OTP)</label>
            <input type="text" name="otp" maxlength="6" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="123456" />
            @error('otp')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-[#3a3937] mb-1">New password</label>
            <input type="password" name="password" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
          </div>
          <div>
            <label class="block text-sm font-medium text-[#3a3937] mb-1">Confirm password</label>
            <input type="password" name="password_confirmation" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
          </div>
          <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Reset password</button>
          <div class="text-center text-sm mt-2"><a href="{{ route('login') }}" class="text-[#0B6B3A] hover:underline">Back to sign in</a></div>
        </form>
      </div>
    </div>
  </body>
</html>
