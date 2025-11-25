<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>{{ $folder->name }} • Publications • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="publications"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6 md:p-8">
          <div class="max-w-screen-xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm text-[#6b6a67]"><a class="hover:underline" href="{{ route('admin.pub.index') }}">Publications</a> /</div>
                <h1 class="text-2xl font-semibold">{{ $folder->name }}</h1>
              </div>
              <div class="flex gap-2">
                <a href="{{ route('admin.pub.create', ['folder_id' => $folder->id]) }}" class="px-3 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800 text-sm">Add Document</a>
              </div>
            </div>

            @if(session('success'))
              <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
              </div>
            @endif

            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">
              <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                      <th class="px-4 py-2">Title</th>
                      <th class="px-4 py-2">Published</th>
                      <th class="px-4 py-2">Size</th>
                      <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($docs as $d)
                      <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-900 font-medium">{{ $d->title }}</td>
                        <td class="px-4 py-2 text-sm">{{ optional($d->published_at ?? $d->created_at)->format('M d, Y') }}</td>
                        <td class="px-4 py-2 text-sm">{{ $d->file_size ? number_format($d->file_size/1048576,2).' MB' : '-' }}</td>
                        <td class="px-4 py-2 text-right">
                          @php($url = \Illuminate\Support\Str::startsWith($d->file_path, ['http://','https://']) ? $d->file_path : \Illuminate\Support\Facades\Storage::url($d->file_path))
                          @php($downloadUrl = \Illuminate\Support\Str::startsWith($d->file_path, ['http://','https://']) ? $d->file_path : route('publication.download', ['id' => $d->id]))
                          <a href="{{ $url }}" target="_blank" class="text-sm px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 mr-2">View</a>
                          <a href="{{ $downloadUrl }}" download="{{ $d->title }}" class="text-sm px-3 py-1.5 rounded-lg text-white bg-primary-700 hover:bg-primary-800 mr-2">Download</a>
                          <form action="{{ route('admin.pub.delete', ['id' => $d->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                            @csrf
                            <button type="submit" class="text-sm px-3 py-1.5 rounded-lg border border-red-200 text-red-700 bg-white hover:bg-red-50">Delete</button>
                          </form>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td class="px-4 py-6 text-sm text-gray-500" colspan="4">No documents in this folder yet.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>window.sidebarOpen = true</script>
  </body>
</html>
