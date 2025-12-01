<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign in • RSMS</title>
    @vite(['resources/css/app.css'])
  </head>
  <body class="min-h-screen w-full bg-[#0b6b3a]">
    <!-- Background image overlay -->
    <div class="absolute inset-0 opacity-30" style="background-image: url('/assets/images/PXL_20250502_075450907.RAW-01.COVER.jpg'); background-size: cover; background-position: center;"></div>
    <div class="relative min-h-screen flex items-center justify-center px-4">
      <div class="w-full max-w-md bg-white/95 backdrop-blur rounded shadow-xl border border-[#ecebea]">
        <div class="px-6 pt-6 pb-4 text-center">
          <div class="mx-auto h-10 w-10 rounded-full bg-[#E6FF8A] flex items-center justify-center">
            <svg class="h-5 w-5 text-[#0b6b3a]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3l9 4-9 4-9-4 9-4Zm0 6l9 4-9 4-9-4 9-4Zm0 6l9 4-9 4-9-4 9-4Z" stroke="currentColor" stroke-width="1.5"/></svg>
          </div>
          <h1 class="mt-3 text-xl font-semibold text-[#1b1b18]">RSMS Admin</h1>
          <p class="text-sm text-[#6b6a67]">Sign in to continue</p>
        </div>
        <form method="POST" action="{{ route('login.post') }}" class="px-6 pb-6 space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium text-[#3a3937] mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="admin@example.com" />
            @error('email')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-[#3a3937] mb-1">Password</label>
            <input type="password" name="password" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="••••••••" />
          </div>
          <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded bg-[#0B6B3A] text-white font-medium hover:bg-[#095a31]">Sign in</button>
          <div class="text-center text-xs text-[#6b6a67]">
            <a href="{{ route('forgot') }}" class="hover:underline">I forgot my password</a>
          </div>
        </form>
        <div class="px-6 pb-6">
          <a href="#" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded bg-[#2a2a2a] text-white text-sm hover:bg-[#333]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 3h10a2 2 0 0 1 2 2v14l-5-3-5 3V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.5"/></svg>
            Download RSMS mobile app
          </a>
        </div>
      </div>
    </div>
  </body>
</html>
