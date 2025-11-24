<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Calendar • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <app-header></app-header>

      <!-- Title bar -->
      <section class="bg-[#0AA74A] text-white">
        <div class="px-4 md:px-8 py-5 flex items-center justify-between">
          <h1 class="text-2xl font-semibold">Academic Calendar</h1>
          <nav class="text-sm text-white/90">
            <a href="/" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <span>Calendar</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Recent events -->
          <section class="lg:col-span-1">
            <div class="flex items-center justify-between mb-3">
              <h2 class="text-xs font-semibold text-[#6b6a67]">RECENT EVENTS</h2>
              @if(session('admin_authenticated'))
                <a href="{{ route('admin.events.create') }}" class="text-sm text-white bg-[#0B6B3A] px-3 py-1 rounded hover:bg-[#095a31]">+ Add</a>
              @endif
            </div>
            <ul class="space-y-3">
              @forelse($recentEvents as $ev)
                <li class="bg-white rounded-md border border-[#ecebea] p-4">
                  <div class="text-sm text-[#6b6a67]">{{ \Carbon\Carbon::parse($ev->start_date)->format('M d, Y') }} @if($ev->end_date) - {{ \Carbon\Carbon::parse($ev->end_date)->format('M d, Y') }} @endif</div>
                  <div class="font-medium text-[#0B6B3A]">{{ $ev->title }}</div>
                  @if($ev->location)
                    <div class="text-sm text-[#6b6a67]">{{ $ev->location }}</div>
                  @endif
                  @if($ev->description)
                    <p class="text-sm mt-1">{{ $ev->description }}</p>
                  @endif
                </li>
              @empty
                <li class="text-sm text-[#6b6a67]">No upcoming events.</li>
              @endforelse
            </ul>
          </section>

          <!-- Big calendar (FullCalendar) -->
          <section class="lg:col-span-2">
            <full-calendar />
          </section>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
