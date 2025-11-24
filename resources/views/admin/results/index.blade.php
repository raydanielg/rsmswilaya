<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Results Upload • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="results"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6 md:p-8">
          <div class="max-w-screen-lg mx-auto space-y-6">
            <div class="flex items-center justify-between">
              <h1 class="text-2xl font-semibold">Results</h1>
              <div class="flex items-center gap-2">
                <a href="/results-center" class="text-sm text-[#0B6B3A] hover:underline">View public Results Center →</a>
                <button type="button" id="openResultsModal" class="px-3 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800 text-sm">Upload Result</button>
                <button type="button" id="openBulkModal" class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-sm">Bulk Publish</button>
              </div>
            @if((($tab ?? '') !== 'types') && (($tab ?? '') !== 'year'))
              <div class="bg-white rounded-md border border-[#ecebea] p-5">
                <div class="flex items-center justify-between mb-3">
                  <div class="font-medium">Recent Uploads</div>
                  <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('admin.results.bulk_publish') }}" class="flex items-center gap-2" id="bulkPublishForm">
                      @csrf
                      <input type="hidden" name="action" id="bulkActionInput" value="publish" />
                      <input type="hidden" name="ids[]" value="" id="bulkIdsPlaceholder" />
                      <button type="button" data-action="publish" class="px-3 py-1.5 rounded bg-[#0B6B3A] text-white text-xs hover:bg-[#095a31]">Publish Selected</button>
                      <button type="button" data-action="unpublish" class="px-3 py-1.5 rounded border text-xs hover:bg-gray-50">Unpublish Selected</button>
                    </form>
                    <div class="text-xs text-gray-500">Last 25</div>
                  </div>
                </div>
                <div class="overflow-x-auto">
                  <table class="min-w-full text-sm">
                    <thead>
                      <tr class="text-left bg-gray-50">
                        <th class="px-3 py-2 border-b"><input type="checkbox" id="checkAllResults" /></th>
                        <th class="px-3 py-2 border-b">Created</th>
                        <th class="px-3 py-2 border-b">Class</th>
                        <th class="px-3 py-2 border-b">Type</th>
                        <th class="px-3 py-2 border-b">Year</th>
                        <th class="px-3 py-2 border-b">Region</th>
                        <th class="px-3 py-2 border-b">Council</th>
                        <th class="px-3 py-2 border-b">School</th>
                        <th class="px-3 py-2 border-b">Status</th>
                        <th class="px-3 py-2 border-b">Document</th>
                        <th class="px-3 py-2 border-b">Actions</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y">
                      @forelse(($recent ?? []) as $row)
                        @php
                          $fileUrl = $row->file_url;
                          $isExternal = \Illuminate\Support\Str::startsWith($fileUrl, ['http://','https://']);
                          if ($isExternal) {
                            $parts = parse_url($fileUrl);
                            $host = $parts['host'] ?? '';
                            $path = $parts['path'] ?? '';
                            $sameHost = request()->getHost() === $host;
                            if ($sameHost && $path && !\Illuminate\Support\Str::startsWith($path, '/storage')) {
                              $fileUrl = ltrim($path, '/');
                              $isExternal = false;
                            }
                          }
                          $docUrl = $isExternal ? $fileUrl : \Illuminate\Support\Facades\Storage::url($fileUrl);
                        @endphp
                        <tr class="hover:bg-gray-50">
                          <td class="px-3 py-2"><input type="checkbox" class="resultRowCheckbox" value="{{ $row->id }}" /></td>
                          <td class="px-3 py-2 text-gray-600">{{ \Carbon\Carbon::parse($row->created_at)->diffForHumans() }}</td>
                          <td class="px-3 py-2">{{ $row->exam }}</td>
                          <td class="px-3 py-2">{{ $row->type_name }} <span class="text-xs text-gray-500">({{ $row->type_code }})</span></td>
                          <td class="px-3 py-2">{{ $row->year }}</td>
                          <td class="px-3 py-2">{{ $row->region_name ?: '—' }}</td>
                          <td class="px-3 py-2">{{ $row->district_name ?: '—' }}</td>
                          <td class="px-3 py-2">{{ $row->school_name ?: '—' }}</td>
                          <td class="px-3 py-2">
                            @if($row->published)
                              <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-green-50 text-green-700 border border-green-200">Published</span>
                            @else
                              <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-amber-50 text-amber-700 border border-amber-200">Unpublished</span>
                            @endif
                          </td>
                          <td class="px-3 py-2">
                            <a href="{{ route('pdf.viewer', ['src'=> urlencode($docUrl)]) }}" target="_blank" class="text-[#0B6B3A] hover:underline">View</a>
                            <a href="{{ $docUrl }}" target="_blank" download class="ml-3 text-[#0B6B3A] hover:underline">Download</a>
                          </td>
                          <td class="px-3 py-2">
                            <form method="POST" action="{{ route('admin.results.toggle', $row->id) }}">
                              @csrf
                              <button class="px-2 py-1 text-xs rounded border hover:bg-gray-50">{{ $row->published ? 'Unpublish' : 'Publish' }}</button>
                            </form>
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
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-md border border-[#ecebea] p-5">
                  <div class="font-medium mb-3">Add Result Type</div>
                  <form method="POST" action="{{ route('admin.results.types.store') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                      <div>
                        <label class="block text-sm font-medium mb-1">Code</label>
                        <input type="text" name="code" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. CSEE" required />
                      </div>
                      <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. Certificate of Secondary Education" required />
                      </div>
                    </div>
                    <div>
                      <label class="block text-sm font-medium mb-1">Description</label>
                      <textarea name="description" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Short details about this exam type"></textarea>
                    </div>
                    <div class="flex justify-end">
                      <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Save Type</button>
                    </div>
                  </form>
                </div>
                <div class="bg-white rounded-md border border-[#ecebea] p-5">
                  <div class="font-medium mb-3">Result Types</div>
                  <div class="divide-y">
                    @forelse(($types ?? []) as $t)
                      <div class="py-3 space-y-2">
                        <div class="flex items-center justify-between">
                          <div>
                            <div class="font-medium">{{ $t->name }} <span class="text-xs text-gray-500">({{ $t->code }})</span></div>
                            @if($t->description)
                              <div class="text-sm text-gray-600">{{ $t->description }}</div>
                            @endif
                          </div>
                          <div class="flex items-center gap-2">
                            <details class="relative">
                              <summary class="px-2 py-1 text-sm border rounded cursor-pointer">Edit</summary>
                              <div class="absolute right-0 mt-2 w-96 bg-white border rounded shadow p-3 z-10">
                                <form method="POST" action="{{ route('admin.results.types.update', $t->id) }}" class="space-y-2">
                                  @csrf
                                  <label class="block text-xs">Code
                                    <input type="text" name="code" class="w-full border border-gray-300 rounded px-2 py-1" value="{{ $t->code }}" />
                                  </label>
                                  <label class="block text-xs">Name
                                    <input type="text" name="name" class="w-full border border-gray-300 rounded px-2 py-1" value="{{ $t->name }}" />
                                  </label>
                                  <label class="block text-xs">Description
                                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded px-2 py-1">{{ $t->description }}</textarea>
                                  </label>
                                  <div class="flex justify-end gap-2">
                                    <button class="px-3 py-1.5 rounded bg-[#0B6B3A] text-white text-xs">Update</button>
                                  </div>
                                </form>
                              </div>
                            </details>
                            <form method="POST" action="{{ route('admin.results.types.delete', $t->id) }}" onsubmit="return confirm('Delete type {{ $t->name }}?');">
                              @csrf
                              <button class="px-2 py-1 text-sm border rounded">Delete</button>
                            </form>
                          </div>
                        </div>
                        <form method="POST" action="{{ route('admin.results.types.years', $t->id) }}" class="bg-gray-50 border rounded p-3">
                          @csrf
                          <div class="text-xs text-gray-600 mb-1">Associate with Years</div>
                          <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-auto">
                            @foreach(($years ?? []) as $y)
                              @php $checked = collect(($typeYears[$t->id] ?? []))->pluck('result_year_id')->contains($y->id); @endphp
                              <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="year_ids[]" value="{{ $y->id }}" {{ $checked ? 'checked' : '' }} />
                                <span>{{ $y->year }}</span>
                              </label>
                            @endforeach
                          </div>
                          <div class="flex justify-end mt-2">
                            <button class="px-3 py-1.5 rounded bg-[#0B6B3A] text-white text-xs">Save Years</button>
                          </div>
                        </form>
                      </div>
                    @empty
                      <div class="text-sm text-gray-500">No types yet.</div>
                    @endforelse
                  </div>
                </div>
              </div>
            @endif
            @if(($tab ?? '') === 'year')
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-md border border-[#ecebea] p-5">
                  <div class="font-medium mb-3">Add Year</div>
                  <form method="POST" action="{{ route('admin.results.years.store') }}" class="space-y-3">
                    @csrf
                    <div>
                      <label class="block text-sm font-medium mb-1">Year</label>
                      <input type="number" name="year" min="2000" max="2100" required class="w-full border border-gray-300 rounded px-3 py-2" value="{{ now()->year }}" />
                    </div>
                    <div>
                      <label class="block text-sm font-medium mb-1">Short description</label>
                      <textarea name="description" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. Special exam session details, changes, notes..."></textarea>
                    </div>
                    <div class="flex justify-end">
                      <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Save Year</button>
                    </div>
                  </form>
                </div>
                <div class="bg-white rounded-md border border-[#ecebea] p-5">
                  <div class="font-medium mb-3">Years</div>
                  <div class="divide-y">
                    @forelse(($years ?? []) as $y)
                      <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                          <div class="font-medium">{{ $y->year }}</div>
                          @if($y->description)
                            <div class="text-sm text-gray-600">{{ $y->description }}</div>
                          @endif
                        </div>
                        <div class="flex items-center gap-2">
                          <details class="relative">
                            <summary class="px-2 py-1 text-sm border rounded cursor-pointer">Edit</summary>
                            <div class="absolute right-0 mt-2 w-80 bg-white border rounded shadow p-3 z-10">
                              <form method="POST" action="{{ route('admin.results.years.update', $y->id) }}" class="space-y-2">
                                @csrf
                                <label class="block text-xs">Year
                                  <input type="number" name="year" min="2000" max="2100" required class="w-full border border-gray-300 rounded px-2 py-1" value="{{ $y->year }}" />
                                </label>
                                <label class="block text-xs">Short description
                                  <textarea name="description" rows="3" class="w-full border border-gray-300 rounded px-2 py-1">{{ $y->description }}</textarea>
                                </label>
                                <div class="flex justify-end gap-2">
                                  <button class="px-3 py-1.5 rounded bg-[#0B6B3A] text-white text-xs">Update</button>
                                </div>
                              </form>
                            </div>
                          </details>
                          <form method="POST" action="{{ route('admin.results.years.delete', $y->id) }}" onsubmit="return confirm('Delete year {{ $y->year }}?');">
                            @csrf
                            <button class="px-2 py-1 text-sm border rounded">Delete</button>
                          </form>
                        </div>
                      </div>
                    @empty
                      <div class="text-sm text-gray-500">No years yet.</div>
                    @endforelse
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

    <script>
      window.sidebarOpen = true;
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
      });
    </script>
  </body>
</html>
