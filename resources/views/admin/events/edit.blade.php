<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Edit Event • RSMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full" style="font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="events"></admin-sidebar>
        <main class="flex-1 min-w-0 p-4 md:p-8">
          <div class="max-w-screen-md mx-auto space-y-5">
            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3 flex items-center justify-between gap-3 flex-wrap">
              <div>
                <h1 class="text-xl md:text-2xl font-semibold">Edit event</h1>
                <p class="text-sm text-[#6b6a67] mt-1">Update the dates, location or description of this event.</p>
              </div>
              <a href="{{ route('admin.events.index') }}" class="text-sm text-[#0B6B3A] hover:underline">← Back to events</a>
            </div>

            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
            <form method="POST" action="{{ route('admin.events.update', $event->id) }}" class="space-y-4">
              @csrf
              <div>
                <label class="block text-sm font-medium mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                @error('title')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]">{{ old('description', $event->description) }}</textarea>
                @error('description')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1">Start date</label>
                  <input type="date" name="start_date" value="{{ old('start_date', optional($event->start_date)->format('Y-m-d')) }}" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                  @error('start_date')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">End date (optional)</label>
                  <input type="date" name="end_date" value="{{ old('end_date', optional($event->end_date)->format('Y-m-d')) }}" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                  @error('end_date')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Location</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                @error('location')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="flex gap-3 mt-4 justify-end">
                <a href="{{ route('admin.events.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Save changes</button>
              </div>
            </form>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>window.sidebarOpen = false</script>
  </body>
</html>
