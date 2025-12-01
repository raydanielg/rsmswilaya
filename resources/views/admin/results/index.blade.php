<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Results Upload • RSMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full" style="font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="results"></admin-sidebar>
        <main class="flex-1 min-w-0 p-4 md:p-8">
          <div class="max-w-6xl mx-auto space-y-6">
            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3">
              <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                  <h1 class="text-xl md:text-2xl font-semibold">Publish Results</h1>
                  <p class="text-sm text-[#6b6a67] mt-1">Manage exam result documents, types and years used in the public Results Center.</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap justify-end">
                  <a href="/results-center" class="text-sm text-[#0B6B3A] hover:underline">View public Results Center →</a>
                  <button type="button" id="openResultsModal" class="px-3 py-2 rounded-full text-sm font-medium text-white bg-primary-700 hover:bg-primary-800">Upload Result</button>
                  <button type="button" id="openBulkModal" class="px-3 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-100">Bulk Publish</button>
                </div>
              </div>
              <div class="mt-4 flex items-center gap-2 text-sm border-t border-[#ecebea] pt-3">
                @php $currentTab = $tab ?? ''; @endphp
                <a href="{{ route('admin.results.index') }}" class="px-3 py-1.5 rounded-full {{ $currentTab === '' ? 'bg-[#0B6B3A] text-white' : 'text-[#3f3e3b] hover:bg-[#f3f2f0]' }}">Recent uploads</a>
                <a href="{{ route('admin.results.index', ['tab' => 'types']) }}" class="px-3 py-1.5 rounded-full {{ $currentTab === 'types' ? 'bg-[#0B6B3A] text-white' : 'text-[#3f3e3b] hover:bg-[#f3f2f0]' }}">Result types</a>
                <a href="{{ route('admin.results.index', ['tab' => 'year']) }}" class="px-3 py-1.5 rounded-full {{ $currentTab === 'year' ? 'bg-[#0B6B3A] text-white' : 'text-[#3f3e3b] hover:bg-[#f3f2f0]' }}">Years</a>
              </div>
            </div>

            @if((($tab ?? '') !== 'types') && (($tab ?? '') !== 'year'))
              <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                  <div>
                    <div class="text-sm font-semibold tracking-wide text-[#1b1b18]">Recent uploads</div>
                    <p class="text-xs text-[#8a8986] mt-0.5">Showing the most recent 50 documents. Use bulk actions to quickly control visibility.</p>
                  </div>
                  <div class="flex items-center gap-3 flex-wrap justify-end">
                    <form method="POST" action="{{ route('admin.results.bulk_publish') }}" class="flex items-center gap-2" id="bulkPublishForm">
                      @csrf
                      <input type="hidden" name="action" id="bulkActionInput" value="publish" />
                      <input type="hidden" name="ids[]" value="" id="bulkIdsPlaceholder" />
                      <button type="button" data-action="publish" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#0B6B3A] text-white text-[11px] font-medium hover:bg-[#095a31]">
                        <span>Publish selected</span>
                      </button>
                      <button type="button" data-action="unpublish" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-[#dad9d6] text-[11px] font-medium text-[#3f3e3b] hover:bg-gray-50">
                        <span>Unpublish selected</span>
                      </button>
                      <button type="button" data-action="delete" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-red-200 bg-red-50 text-[11px] font-medium text-red-700 hover:bg-red-100">
                        <span>Delete selected</span>
                      </button>
                    </form>
                    <div class="text-[11px] text-[#a3a29f]">Last 50 uploads</div>
                  </div>
                </div>
                <div class="overflow-x-auto rounded-lg border border-[#f0efed]">
                  <table class="min-w-full text-xs md:text-sm bg-white">
                    <thead>
                      <tr class="text-left bg-[#f7f7f6] text-[#6b6a67] text-[11px] uppercase tracking-wide">
                        <th class="px-3 py-2 border-b border-[#ecebea] w-8"><input type="checkbox" id="checkAllResults" /></th>
                        <th class="px-3 py-2 border-b border-[#ecebea]">Created</th>
                        <th class="px-3 py-2 border-b border-[#ecebea]">Class</th>
                        <th class="px-3 py-2 border-b border-[#ecebea]">Type</th>
                        <th class="px-3 py-2 border-b border-[#ecebea]">Year</th>
                        <th class="px-3 py-2 border-b border-[#ecebea]">Region</th>
                        <th class="px-3 py-2 border-b border-[#ecebea]">Council</th>
                        <th class="px-3 py-2 border-b border-[#ecebea]">School</th>
                        <th class="px-3 py-2 border-b border-[#ecebea]">Status</th>
                        <th class="px-3 py-2 border-b border-[#ecebea] text-right">Actions</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f3f2f0]">
                      @forelse(($recent ?? []) as $row)
                        @php
                          $fileUrl = $row->file_url;
                          $isExternal = \Illuminate\Support\Str::startsWith($fileUrl, ['http://','https://']);
                          if ($isExternal) {
                            $docUrl = $fileUrl;
                          } else {
                            $clean = preg_replace('#^/?storage/#','', $fileUrl);
                            $clean = ltrim($clean, '/');
                            $docUrl = route('results.files.show', ['path' => $clean]);
                          }
                        @endphp
                        <tr class="hover:bg-[#f9f8f6]" data-row-id="{{ $row->id }}">
                          <td class="px-3 py-2 align-top"><input type="checkbox" class="resultRowCheckbox" value="{{ $row->id }}" /></td>
                          <td class="px-3 py-2 align-top text-[#6b6a67] whitespace-nowrap">{{ \Carbon\Carbon::parse($row->created_at)->diffForHumans() }}</td>
                          <td class="px-3 py-2 align-top font-medium text-[#1b1b18] whitespace-nowrap">{{ $row->exam }}</td>
                          <td class="px-3 py-2 align-top">
                            <div class="text-xs font-medium text-[#1b1b18] truncate">{{ $row->type_name }}</div>
                            <div class="text-[11px] text-gray-500">{{ $row->type_code }}</div>
                          </td>
                          <td class="px-3 py-2 align-top whitespace-nowrap">{{ $row->year }}</td>
                          <td class="px-3 py-2 align-top text-xs text-[#3f3e3b]">{{ $row->region_name ?: '—' }}</td>
                          <td class="px-3 py-2 align-top text-xs text-[#3f3e3b]">{{ $row->district_name ?: '—' }}</td>
                          <td class="px-3 py-2 align-top text-xs text-[#3f3e3b]">{{ $row->school_name ?: '—' }}</td>
                          <td class="px-3 py-2 align-top">
                            @if($row->published)
                              <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded-full bg-green-50 text-green-700 border border-green-200">Published</span>
                            @else
                              <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded-full bg-amber-50 text-amber-700 border border-amber-200">Unpublished</span>
                            @endif
                          </td>
                          <td class="px-3 py-2 align-top text-right">
                            <div class="inline-flex items-center gap-1.5">
                              <a href="{{ $docUrl }}" target="_blank" download class="inline-flex items-center justify-center h-7 w-7 rounded-full border border-[#dad9d6] bg-white text-[#0B6B3A] hover:bg-gray-50" title="Download PDF">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v12"/><path d="M7 11l5 5 5-5"/><path d="M5 19h14"/></svg>
                              </a>
                              <form id="deleteForm-{{ $row->id }}" method="POST" action="{{ route('admin.results.delete', $row->id) }}">
                                @csrf
                                <button type="button" data-delete-form="deleteForm-{{ $row->id }}" class="inline-flex items-center justify-center h-7 w-7 rounded-full border border-red-200 bg-red-50 text-red-700 hover:bg-red-100" title="Delete">
                                  <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 7h12"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 7l1-2h4l1 2"/><path d="M7 7l1 12h8l1-12"/></svg>
                                </button>
                              </form>
                            </div>
                          </td>
                        </tr>
                      @empty
                        <tr><td colspan="11" class="px-3 py-6 text-center text-gray-500">No uploads yet.</td></tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            @endif
            </div>

            @if(session('status'))
              <div class="p-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('status') }}</div>
            @endif

            @if(($tab ?? '') === 'types')
              <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add Result Type card -->
                <div class="lg:col-span-1 bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
                  <div class="text-sm font-semibold text-[#1b1b18] mb-1">Add Result Type</div>
                  <p class="text-xs text-[#8a8986] mb-4">Define exam result types (e.g. mock, prelim, final) that can be attached to uploaded documents.</p>
                  <form method="POST" action="{{ route('admin.results.types.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <div>
                        <label class="block text-xs font-medium mb-1 text-[#4a4946]">Code</label>
                        <input type="text" name="code" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]/60" placeholder="e.g. CSEE" required />
                      </div>
                      <div>
                        <label class="block text-xs font-medium mb-1 text-[#4a4946]">Name</label>
                        <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]/60" placeholder="e.g. Certificate of Secondary Education" required />
                      </div>
                    </div>
                    <div>
                      <label class="block text-xs font-medium mb-1 text-[#4a4946]">Description</label>
                      <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]/60" placeholder="Short details about this exam type"></textarea>
                    </div>
                    <div class="flex justify-end">
                      <button class="px-4 py-2 rounded-full bg-[#0B6B3A] text-white text-sm font-medium hover:bg-[#095a31]">Save Type</button>
                    </div>
                  </form>
                </div>

                <!-- Result types list card -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
                  <div class="flex items-center justify-between mb-3">
                    <div>
                      <div class="text-sm font-semibold text-[#1b1b18]">Result Types</div>
                      <p class="text-xs text-[#8a8986]">Existing result types and their associated years.</p>
                    </div>
                  </div>
                  <div class="border border-[#f0efed] rounded-lg overflow-hidden">
                    <div class="hidden md:grid grid-cols-[2fr,1fr,auto] gap-3 bg-[#f7f7f6] text-[11px] uppercase tracking-wide text-[#6b6a67] px-4 py-2">
                      <div>Type</div>
                      <div>Associated years</div>
                      <div class="text-right pr-1">Actions</div>
                    </div>
                    <div class="divide-y divide-[#f3f2f0]">
                      @forelse(($types ?? []) as $t)
                        <div class="px-4 py-3 space-y-2">
                          <div class="flex flex-col md:grid md:grid-cols-[2fr,1fr,auto] md:items-start md:gap-3">
                            <div>
                              <div class="text-sm font-medium text-[#1b1b18] flex items-center gap-2">
                                <span>{{ $t->name }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-[#f1f5ff] text-[#1d4ed8] border border-[#d4dffb]">{{ $t->code }}</span>
                              </div>
                              @if($t->description)
                                <div class="mt-0.5 text-xs text-[#6b6a67] max-w-xl">{{ $t->description }}</div>
                              @endif
                            </div>
                            <div class="mt-2 md:mt-0 text-xs text-[#4a4946]">
                              @php
                                $associatedYears = collect(($typeYears[$t->id] ?? []))->pluck('result_year_id');
                                $yearLabels = collect(($years ?? []))->whereIn('id', $associatedYears)->pluck('year')->values();
                              @endphp
                              @if($yearLabels->count())
                                <div class="flex flex-wrap gap-1">
                                  @foreach($yearLabels as $yy)
                                    <span class="px-2 py-0.5 rounded-full bg-[#f7f7f6] border border-[#ecebea] text-[11px]">{{ $yy }}</span>
                                  @endforeach
                                </div>
                              @else
                                <span class="text-[#a3a29f]">No years linked</span>
                              @endif
                            </div>
                            <div class="mt-2 md:mt-0 flex items-center gap-2 justify-end">
                              <details class="relative">
                                <summary class="px-3 py-1.5 text-[11px] border border-[#dad9d6] rounded-full cursor-pointer bg-white hover:bg-gray-50">Edit</summary>
                                <div class="absolute right-0 mt-2 w-80 bg-white border border-[#e3e2df] rounded-xl shadow-lg p-3 z-10">
                                  <form method="POST" action="{{ route('admin.results.types.update', $t->id) }}" class="space-y-2 text-xs">
                                    @csrf
                                    <label class="block mb-1">Code
                                      <input type="text" name="code" class="w-full border border-gray-300 rounded-lg px-2 py-1 mt-0.5" value="{{ $t->code }}" />
                                    </label>
                                    <label class="block mb-1">Name
                                      <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-2 py-1 mt-0.5" value="{{ $t->name }}" />
                                    </label>
                                    <label class="block mb-1">Description
                                      <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-2 py-1 mt-0.5">{{ $t->description }}</textarea>
                                    </label>
                                    <div class="flex justify-end gap-2 pt-1">
                                      <button class="px-3 py-1.5 rounded-full bg-[#0B6B3A] text-white text-[11px]">Update</button>
                                    </div>
                                  </form>
                                </div>
                              </details>
                              <form method="POST" action="{{ route('admin.results.types.delete', $t->id) }}" onsubmit="return confirm('Delete type {{ $t->name }}?');">
                                @csrf
                                <button class="px-3 py-1.5 text-[11px] rounded-full border border-red-200 bg-red-50 text-red-700 hover:bg-red-100">Delete</button>
                              </form>
                            </div>
                          </div>
                          <form method="POST" action="{{ route('admin.results.types.years', $t->id) }}" class="mt-3 bg-[#f9f8f6] border border-[#ecebea] rounded-lg p-3">
                            @csrf
                            <div class="text-[11px] text-[#6b6a67] mb-1">Associate with Years</div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-40 overflow-auto">
                              @foreach(($years ?? []) as $y)
                                @php $checked = collect(($typeYears[$t->id] ?? []))->pluck('result_year_id')->contains($y->id); @endphp
                                <label class="flex items-center gap-2 text-xs">
                                  <input type="checkbox" name="year_ids[]" value="{{ $y->id }}" {{ $checked ? 'checked' : '' }} />
                                  <span>{{ $y->year }}</span>
                                </label>
                              @endforeach
                            </div>
                            <div class="flex justify-end mt-2">
                              <button class="px-3 py-1.5 rounded-full bg-[#0B6B3A] text-white text-[11px]">Save Years</button>
                            </div>
                          </form>
                        </div>
                      @empty
                        <div class="px-4 py-4 text-sm text-gray-500">No types yet.</div>
                      @endforelse
                    </div>
                  </div>
                </div>
              </div>
            @endif
            @if(($tab ?? '') === 'year')
              <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add Year card -->
                <div class="lg:col-span-1 bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
                  <div class="text-sm font-semibold text-[#1b1b18] mb-1">Add Year</div>
                  <p class="text-xs text-[#8a8986] mb-4">Create result years available for linking with result types and documents.</p>
                  <form method="POST" action="{{ route('admin.results.years.store') }}" class="space-y-4">
                    @csrf
                    <div>
                      <label class="block text-xs font-medium mb-1 text-[#4a4946]">Year</label>
                      <input type="number" name="year" min="2000" max="2100" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]/60" value="{{ now()->year }}" />
                    </div>
                    <div>
                      <label class="block text-xs font-medium mb-1 text-[#4a4946]">Short description</label>
                      <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]/60" placeholder="e.g. Special exam session details, changes, notes..."></textarea>
                    </div>
                    <div class="flex justify-end">
                      <button class="px-4 py-2 rounded-full bg-[#0B6B3A] text-white text-sm font-medium hover:bg-[#095a31]">Save Year</button>
                    </div>
                  </form>
                </div>

                <!-- Years list card -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
                  <div class="flex items-center justify-between mb-3">
                    <div>
                      <div class="text-sm font-semibold text-[#1b1b18]">Years</div>
                      <p class="text-xs text-[#8a8986]">All configured result years with quick edit and delete actions.</p>
                    </div>
                  </div>
                  <div class="border border-[#f0efed] rounded-lg overflow-hidden">
                    <div class="hidden md:grid grid-cols-[auto,2fr,auto] gap-3 bg-[#f7f7f6] text-[11px] uppercase tracking-wide text-[#6b6a67] px-4 py-2">
                      <div>Year</div>
                      <div>Description</div>
                      <div class="text-right pr-1">Actions</div>
                    </div>
                    <div class="divide-y divide-[#f3f2f0]">
                      @forelse(($years ?? []) as $y)
                        <div class="px-4 py-3 flex flex-col md:grid md:grid-cols-[auto,2fr,auto] md:items-start md:gap-3">
                          <div class="text-sm font-medium text-[#1b1b18]">{{ $y->year }}</div>
                          <div class="mt-2 md:mt-0 text-xs text-[#4a4946]">
                            @if($y->description)
                              <div class="max-w-xl">{{ $y->description }}</div>
                            @else
                              <span class="text-[#a3a29f]">No description</span>
                            @endif
                          </div>
                          <div class="mt-2 md:mt-0 flex items-center gap-2 justify-end">
                            <details class="relative">
                              <summary class="px-3 py-1.5 text-[11px] border border-[#dad9d6] rounded-full cursor-pointer bg-white hover:bg-gray-50">Edit</summary>
                              <div class="absolute right-0 mt-2 w-80 bg-white border border-[#e3e2df] rounded-xl shadow-lg p-3 z-10">
                                <form method="POST" action="{{ route('admin.results.years.update', $y->id) }}" class="space-y-2 text-xs">
                                  @csrf
                                  <label class="block mb-1">Year
                                    <input type="number" name="year" min="2000" max="2100" required class="w-full border border-gray-300 rounded-lg px-2 py-1 mt-0.5" value="{{ $y->year }}" />
                                  </label>
                                  <label class="block mb-1">Short description
                                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-2 py-1 mt-0.5">{{ $y->description }}</textarea>
                                  </label>
                                  <div class="flex justify-end gap-2 pt-1">
                                    <button class="px-3 py-1.5 rounded-full bg-[#0B6B3A] text-white text-[11px]">Update</button>
                                  </div>
                                </form>
                              </div>
                            </details>
                            <form id="deleteYearForm-{{ $y->id }}" method="POST" action="{{ route('admin.results.years.delete', $y->id) }}">
                              @csrf
                              <button type="button" data-delete-form="deleteYearForm-{{ $y->id }}" class="px-3 py-1.5 text-[11px] rounded-full border border-red-200 bg-red-50 text-red-700 hover:bg-red-100">Delete</button>
                            </form>
                          </div>
                        </div>
                      @empty
                        <div class="px-4 py-4 text-sm text-gray-500">No years yet.</div>
                      @endforelse
                    </div>
                  </div>
                </div>
              </div>
            @endif

            <!-- Modal -->
            <div id="resultsModal" class="hidden fixed inset-0 z-50">
              <div class="absolute inset-0 bg-black/40" id="resultsBackdrop"></div>
              <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md w-[95vw] max-w-2xl border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                  <div class="font-semibold text-[#0B6B3A]">Upload Result</div>
                  <button type="button" id="closeResultsModal" class="text-[#6b6a67] hover:text-[#0B6B3A]">✕</button>
                </div>
                <form method="POST" action="{{ route('admin.results.store') }}" enctype="multipart/form-data" class="relative p-4 space-y-4" id="resultsForm">
                  @csrf
                  <div id="resultsUploading" class="hidden absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center z-10">
                    <div class="flex items-center gap-3 text-[#0B6B3A]">
                      <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                      <span>Uploading...</span>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label class="block text-sm font-medium mb-1">Results Class</label>
                      <select id="classSelect" name="exam" required class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50">
                        @foreach($exams as $e)
                          <option value="{{ $e }}">{{ $e }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium mb-1">Results Type</label>
                      <select id="typeSelect" name="result_type_id" required class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50">
                        @foreach(($types ?? []) as $t)
                          <option value="{{ $t->id }}" data-code="{{ $t->code }}">{{ $t->name }} ({{ $t->code }})</option>
                        @endforeach
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium mb-1">Year</label>
                      <select id="yearSelect" name="year" required class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50">
                        @foreach(($years ?? []) as $y)
                          <option value="{{ $y->year }}" @selected($y->year == now()->year)>{{ $y->year }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label class="block text-sm font-medium mb-1">Region</label>
                      <select id="regionSelect" name="region_id" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50">
                        <option value="">-- All --</option>
                        @foreach($regions as $r)
                          <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium mb-1">Council</label>
                      <select id="districtSelect" name="district_id" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50" disabled>
                        <option value="">-- All --</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium mb-1">School <span class="text-red-600">*</span></label>
                      <select id="schoolSelect" name="school_id" required class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50" disabled>
                        <option value="">-- All --</option>
                      </select>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium mb-1">PDF file</label>
                      <input type="file" name="file" accept="application/pdf" class="w-full" />
                      <div class="text-xs text-gray-500 mt-1">or provide external URL instead</div>
                      <input type="url" name="file_url" placeholder="https://example.com/result.pdf" class="w-full border border-gray-300 rounded px-3 py-2 mt-1" />
                      @error('file')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                  </div>

                  <div class="flex justify-end gap-3">
                    <button type="button" id="cancelResultsModal" class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100">Cancel</button>
                    <button class="px-4 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <!-- Delete confirmation modal -->
    <div id="confirmDeleteModal" class="hidden fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40"></div>
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-lg w-[90vw] max-w-md border border-[#e3e2df]">
        <div class="px-5 py-4 border-b border-[#ecebea] flex items-center justify-between">
          <div class="text-sm font-semibold text-[#1b1b18]">Confirm delete</div>
          <button type="button" id="confirmDeleteClose" class="text-[#8a8986] hover:text-[#1b1b18] text-lg leading-none">×</button>
        </div>
        <div class="px-5 pt-4 pb-5 space-y-4">
          <p id="confirmDeleteMessage" class="text-sm text-[#4a4946]">Are you sure you want to delete?</p>
          <div class="flex justify-end gap-3 text-sm">
            <button type="button" id="confirmDeleteCancel" class="px-4 py-2 rounded-full border border-[#dad9d6] bg-white text-[#3f3e3b] hover:bg-gray-50">Cancel</button>
            <button type="button" id="confirmDeleteYes" class="px-4 py-2 rounded-full bg-red-600 text-white hover:bg-red-700">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      window.sidebarOpen = false;
      document.addEventListener('DOMContentLoaded', async function(){
        // Modal wiring
        (function(){
          const m = document.getElementById('resultsModal');
          const o = document.getElementById('openResultsModal');
          const c = document.getElementById('closeResultsModal');
          const x = document.getElementById('cancelResultsModal');
          const b = document.getElementById('resultsBackdrop');
          const open = ()=> m.classList.remove('hidden');
          const close = ()=> m.classList.add('hidden');
          o && o.addEventListener('click', open);
          c && c.addEventListener('click', close);
          x && x.addEventListener('click', close);
          b && b.addEventListener('click', close);
          document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') close(); });
        })();

        // Bulk modal wiring
        (function(){
          const m = document.getElementById('bulkModal');
          const o = document.getElementById('openBulkModal');
          const c = document.getElementById('closeBulkModal');
          const x = document.getElementById('cancelBulkModal');
          const b = document.getElementById('bulkBackdrop');
          const open = ()=> m.classList.remove('hidden');
          const close = ()=> m.classList.add('hidden');
          o && o.addEventListener('click', open);
          c && c.addEventListener('click', close);
          x && x.addEventListener('click', close);
          b && b.addEventListener('click', close);
          document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') close(); });
        })();

        const region = document.getElementById('regionSelect');
        const district = document.getElementById('districtSelect');
        const school = document.getElementById('schoolSelect');
        async function loadFilters(){
          const params = new URLSearchParams();
          if (region.value) params.set('region_id', region.value);
          if (district.value) params.set('district_id', district.value);
          const res = await fetch('/api/results/filters?' + params.toString());
          if (!res.ok) return;
          const data = await res.json();
          // districts
          district.innerHTML = '<option value="">-- All --</option>';
          (data.districts||[]).forEach(d=>{
            const opt = document.createElement('option'); opt.value = d.id; opt.textContent = d.name; district.appendChild(opt);
          });
          district.disabled = !(data.districts&&data.districts.length);
          // schools
          school.innerHTML = '<option value="">-- All --</option>';
          (data.schools||[]).forEach(s=>{
            const opt = document.createElement('option'); opt.value = s.id; opt.textContent = s.name; school.appendChild(opt);
          });
          school.disabled = !(data.schools&&data.schools.length);
        }
        region && region.addEventListener('change', ()=>{ district.value=''; school.value=''; loadFilters(); });
        district && district.addEventListener('change', ()=>{ school.value=''; loadFilters(); });

        // Year filter based on Result Type associations
        const typeSelect = document.getElementById('typeSelect');
        const yearSelect = document.getElementById('yearSelect');
        const bulkTypeSelect = document.getElementById('bulkTypeSelect');
        const bulkYearSelect = document.getElementById('bulkYearSelect');
        // Build a map: TYPE_CODE -> [years]
        const typeYearMap = {
          @if(!empty($types) && !empty($typeYears))
            @foreach($types as $t)
              '{{ $t->code }}': [
                @php $yIds = collect(($typeYears[$t->id] ?? []))->pluck('result_year_id');
                     $ys = collect(($years ?? []))->whereIn('id', $yIds)->pluck('year')->values(); @endphp
                {!! $ys->implode(',') !!}
              ],
            @endforeach
          @endif
        };
        function filterYears(selTypeCode, yearSelectEl){
          if (!yearSelectEl) return;
          const allOptions = Array.from(yearSelectEl.querySelectorAll('option'));
          const allowed = typeYearMap[selTypeCode];
          allOptions.forEach(opt => { opt.hidden = false; opt.disabled = false; });
          if (Array.isArray(allowed) && allowed.length){
            const allowedSet = new Set(allowed.map(v=> String(v)));
            allOptions.forEach(opt => {
              if (!allowedSet.has(opt.value)) { opt.hidden = true; opt.disabled = true; }
            });
            // pick first visible if current is not allowed
            const currentAllowed = allowedSet.has(yearSelectEl.value);
            if (!currentAllowed){
              const first = allOptions.find(o=> !o.hidden);
              if (first) yearSelectEl.value = first.value;
            }
          }
        }
        function getTypeCode(selectEl){
          if (!selectEl) return null;
          const opt = selectEl.options[selectEl.selectedIndex];
          return opt ? opt.getAttribute('data-code') : null;
        }
        typeSelect && typeSelect.addEventListener('change', ()=> filterYears(getTypeCode(typeSelect), yearSelect));
        bulkTypeSelect && bulkTypeSelect.addEventListener('change', ()=> filterYears(getTypeCode(bulkTypeSelect), bulkYearSelect));

        // Build inverse map: YEAR -> [type_codes]
        const yearTypeMap = (function(){
          const map = {};
          const allYears = @json(($years ?? [])->pluck('year'));
          const allTypes = @json(($types ?? []));
          // initialize
          (allYears||[]).forEach(y=> map[y] = []);
          if (allTypes && Object.keys(typeYearMap).length){
            Object.entries(typeYearMap).forEach(([code, yrs])=>{
              (yrs||[]).forEach(y=>{ if (!map[y]) map[y]=[]; map[y].push(code); });
            });
          }
          return map;
        })();
        function filterTypes(selYear, typeSelectEl){
          if (!typeSelectEl) return;
          const allOptions = Array.from(typeSelectEl.querySelectorAll('option'));
          const allowed = yearTypeMap[selYear];
          // If there are explicit associations for this year, filter; otherwise show all
          allOptions.forEach(opt => { opt.hidden = false; opt.disabled = false; });
          if (Array.isArray(allowed) && allowed.length){
            const allowedSet = new Set(allowed.map(String));
            allOptions.forEach(opt => {
              const code = opt.getAttribute('data-code');
              if (!allowedSet.has(code)) { opt.hidden = true; opt.disabled = true; }
            });
            const currentCode = getTypeCode(typeSelectEl);
            if (typeSelectEl.hidden || typeSelectEl.disabled || !currentCode || !allowedSet.has(currentCode)){
              const first = allOptions.find(o=> !o.hidden);
              if (first) typeSelectEl.value = first.value;
            }
          }
        }
        // Wire year -> types filtering
        yearSelect && yearSelect.addEventListener('change', ()=> filterTypes(yearSelect.value, typeSelect));
        bulkYearSelect && bulkYearSelect.addEventListener('change', ()=> filterTypes(bulkYearSelect.value, bulkTypeSelect));

        // Show uploading overlays on submit
        const rf = document.getElementById('resultsForm');
        const bf = document.getElementById('bulkForm');
        rf && rf.addEventListener('submit', ()=>{
          document.getElementById('resultsUploading')?.classList.remove('hidden');
        });
        bf && bf.addEventListener('submit', ()=>{
          document.getElementById('bulkUploading')?.classList.remove('hidden');
        });

        // initial apply both ways
        typeSelect && filterYears(getTypeCode(typeSelect), yearSelect);
        bulkTypeSelect && filterYears(getTypeCode(bulkTypeSelect), bulkYearSelect);
        yearSelect && filterTypes(yearSelect.value, typeSelect);
        bulkYearSelect && filterTypes(bulkYearSelect.value, bulkTypeSelect);

        // Shared delete confirmation modal helper
        const confirmModal = document.getElementById('confirmDeleteModal');
        const confirmMsgEl = document.getElementById('confirmDeleteMessage');
        const confirmYes = document.getElementById('confirmDeleteYes');
        const confirmNo = document.getElementById('confirmDeleteCancel');
        const confirmClose = document.getElementById('confirmDeleteClose');
        let confirmCallback = null;

        function openConfirm(message, cb){
          if (!confirmModal) { if (cb) cb(); return; }
          confirmMsgEl && (confirmMsgEl.textContent = message || 'Are you sure?');
          confirmCallback = cb || null;
          confirmModal.classList.remove('hidden');
        }
        function closeConfirm(){
          confirmModal && confirmModal.classList.add('hidden');
          confirmCallback = null;
        }
        [confirmNo, confirmClose].forEach(btn => {
          btn && btn.addEventListener('click', closeConfirm);
        });
        confirmYes && confirmYes.addEventListener('click', () => {
          const cb = confirmCallback;
          closeConfirm();
          cb && cb();
        });

        // Bulk publish / unpublish / delete wiring (checkboxes + action buttons)
        (function(){
          const bulkForm = document.getElementById('bulkPublishForm');
          const actionInput = document.getElementById('bulkActionInput');
          const checkAll = document.getElementById('checkAllResults');
          const checkboxes = Array.from(document.querySelectorAll('.resultRowCheckbox'));
          const bulkButtons = bulkForm ? Array.from(bulkForm.querySelectorAll('button[data-action]')) : [];

          function getSelectedIds(){
            return checkboxes.filter(cb => cb.checked).map(cb => cb.value);
          }

          checkAll && checkAll.addEventListener('change', () => {
            const checked = !!checkAll.checked;
            checkboxes.forEach(cb => { cb.checked = checked; });
          });

          bulkButtons.forEach(btn => {
            btn.addEventListener('click', () => {
              if (!bulkForm || !actionInput) return;
              const ids = getSelectedIds();
              if (!ids.length) {
                alert('Please select at least one result.');
                return;
              }
              const action = btn.getAttribute('data-action');

              const perform = async () => {
                actionInput.value = action;
                // clear previous dynamic ids
                Array.from(bulkForm.querySelectorAll('input[name="ids[]"]')).forEach((el) => {
                  if (el.id !== 'bulkIdsPlaceholder') el.remove();
                  if (el.id === 'bulkIdsPlaceholder') el.value = '';
                });
                ids.forEach(id => {
                  const input = document.createElement('input');
                  input.type = 'hidden';
                  input.name = 'ids[]';
                  input.value = id;
                  bulkForm.appendChild(input);
                });

                // For publish/unpublish we can still do normal submit (full reload)
                if (action === 'publish' || action === 'unpublish') {
                  bulkForm.submit();
                  return;
                }

                // For delete, use AJAX and update DOM without reload
                try {
                  const res = await fetch(bulkForm.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(bulkForm),
                  });
                  if (!res.ok) return;
                  ids.forEach(id => {
                    const row = document.querySelector(`tr[data-row-id="${id}"]`);
                    row && row.remove();
                  });
                } catch (e) {
                  console.error('Bulk delete failed', e);
                }
              };

              if (action === 'delete') {
                openConfirm('Delete selected result documents?', perform);
              } else {
                perform();
              }
            });
          });
        })();

        // Per-row delete buttons (use modal; AJAX remove for results table, normal submit elsewhere)
        (function(){
          const deleteButtons = Array.from(document.querySelectorAll('button[data-delete-form]'));
          deleteButtons.forEach(btn => {
            btn.addEventListener('click', () => {
              const formId = btn.getAttribute('data-delete-form');
              const form = document.getElementById(formId);
              if (!form) return;
              const row = btn.closest('tr[data-row-id]');
              openConfirm('Delete this item?', async () => {
                // If it's a results row with data-row-id, use AJAX + remove row
                if (row) {
                  try {
                    const res = await fetch(form.action, {
                      method: 'POST',
                      headers: { 'X-Requested-With': 'XMLHttpRequest' },
                      body: new FormData(form),
                    });
                    if (!res.ok) return;
                    row.remove();
                  } catch (e) {
                    console.error('Delete failed', e);
                  }
                } else {
                  // Fallback: normal submit (e.g. Years section)
                  form.submit();
                }
              });
            });
          });
        })();
      });
    </script>
  </body>
</html>
