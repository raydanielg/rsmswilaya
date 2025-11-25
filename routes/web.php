<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

// Global OPTIONS handler to avoid 405 on CORS preflight
Route::options('/{any}', function () {
    return response('', 204)->withHeaders([
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
        'Access-Control-Max-Age' => '86400',
    ]);
})->where('any', '.*');

// Redirect legacy /results/...pdf URLs to the PDF viewer using the unified result-files route
// Example: /results/csee/2025/mwanza/.../file.pdf -> /view/pdf?src=/result-files/results/csee/2025/.../file.pdf
Route::get('/results/{path}', function (string $path) {
    // Normalise any leading slashes or accidental storage/ prefix
    $subPath = 'results/'.ltrim($path, '/');
    $subPath = preg_replace('#^storage/#', '', $subPath);
    $fileUrl = route('results.files.show', ['path' => $subPath]);
    // Pass the raw URL to the viewer; it will be encoded on the frontend when needed
    return redirect()->route('pdf.viewer', ['src' => $fileUrl]);
})->where('path', '.+\.pdf$');

Route::get('/', function () {
    return view('welcome');
});

// Serve result documents safely from storage/app/public/results
Route::get('/result-files/{path}', function (string $path) {
    $path = ltrim($path, '/');
    // Normalize possible "storage/" prefix
    $path = preg_replace('#^storage/#','', $path);
    // Handle accidental duplicate "results/.../results/" segments like
    // results/csee/2025/mwanza/nyamagana/results/file.pdf -> results/csee/2025/mwanza/nyamagana/file.pdf
    $path = preg_replace('#^results/([^?]+?)/results/#', 'results/$1/', $path);

    $disk = Storage::disk('public');
    if (!$disk->exists($path)) {
        // Fallback: many uploads store PDFs flat under results/ while DB paths
        // may include nested folders. If so, use just the filename.
        if (preg_match('#^results/.*/([^/]+\.pdf)$#', $path, $m)) {
            $fallback = 'results/'.$m[1];
            if ($disk->exists($fallback)) {
                $path = $fallback;
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    return $disk->response($path);
})->where('path', '.*')->name('results.files.show');

// Serve blog images safely from storage/app/public without relying on webserver symlink
Route::get('/blog-images/{path}', function (string $path) {
    $path = ltrim($path, '/');
    // allow nested paths like blog/..., blog_gallery/...
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return Storage::disk('public')->response($path);
})->where('path', '.*')->name('blog.images.show');

// Full-screen PDF viewer
Route::get('/view/pdf', function (Request $request) {
    $src = (string)$request->query('src');
    $src = trim($src);
    if (!$src) abort(404);
    // allow only http/https and same-origin paths
    $allowed = str_starts_with($src, 'http://') || str_starts_with($src, 'https://') || str_starts_with($src, '/');
    abort_unless($allowed, 403);
    return view('pdf-viewer', ['src' => $src]);
})->name('pdf.viewer');

// Public status API (for mobile)
Route::get('/api/status', function () {
    $pairs = DB::table('site_settings')->pluck('value','key');
    $enabled = filter_var($pairs['maintenance.enabled'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
    $ends = $pairs['maintenance.ends_at'] ?? null;
    $msg = $pairs['maintenance.message'] ?? null;
    $site = $pairs['general.site_name'] ?? 'RSMS';
    $favicon = $pairs['general.favicon_url'] ?? null; if (!$favicon && !empty($pairs['general.favicon_path'])) $favicon = Storage::url($pairs['general.favicon_path']);
    $hero = $pairs['general.hero_url'] ?? null; if (!$hero && !empty($pairs['general.hero_path'])) $hero = Storage::url($pairs['general.hero_path']);
    return response()->json([
        'site_name'=>$site,
        'maintenance'=>$enabled,
        'maintenance_ends_at'=>$ends,
        'maintenance_message'=>$msg,
        'favicon_url'=>$favicon,
        'hero_url'=>$hero,
    ]);
})->name('api.status');

// Public notifications API (for mobile)
Route::get('/api/notifications', function () {
    $now = now();
    $rows = DB::table('notifications')
        ->where(function($q) use ($now) {
            $q->whereNull('starts_at')->orWhere('starts_at','<=',$now);
        })
        ->where(function($q) use ($now) {
            $q->whereNull('ends_at')->orWhere('ends_at','>=',$now);
        })
        ->orderByDesc('starts_at')
        ->orderByDesc('created_at')
        ->limit(20)
        ->get(['title','body','starts_at','ends_at','created_at']);
    return response()->json($rows);
})->name('api.notifications');

// API: hero (site header/hero assets)
Route::get('/api/hero', function () {
    $pairs = DB::table('site_settings')->pluck('value','key');
    $site = $pairs['general.site_name'] ?? 'RSMS';
    $favicon = $pairs['general.favicon_url'] ?? null; if (!$favicon && !empty($pairs['general.favicon_path'])) $favicon = Storage::url($pairs['general.favicon_path']);
    $hero = $pairs['general.hero_url'] ?? null; if (!$hero && !empty($pairs['general.hero_path'])) $hero = Storage::url($pairs['general.hero_path']);
    return response()->json([
        'site_name' => $site,
        'favicon_url' => $favicon,
        'hero_url' => $hero,
    ]);
})->name('api.hero');

// API: about (static content; falls back to defaults if not configured)
Route::get('/api/about', function () {
    $pairs = DB::table('site_settings')->pluck('value','key');
    $title = $pairs['about.title'] ?? 'About Us';
    $body = $pairs['about.body'] ?? 'This is the RSMS information portal providing examinations updates, results access, events, and publications.';
    return response()->json(['title'=>$title, 'body'=>$body]);
})->name('api.about');

// Public maintenance page
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

// API: results — exams list
Route::get('/api/results/exams', function(){
    return response()->json(['SFNA','PSLE','FTNA','CSEE','ACSEE']);
})->name('api.results.exams');

// API: results — regions list
Route::get('/api/results/regions', function(){
    $regions = DB::table('regions')->orderBy('name')->get(['id','name']);
    return response()->json($regions);
})->name('api.results.regions');

// API: results — districts by region
Route::get('/api/results/districts', function(Request $request){
    $regionId = (int)$request->query('region_id');
    abort_unless($regionId, 422);
    $rows = DB::table('districts')->where('region_id',$regionId)->orderBy('name')->get(['id','name']);
    return response()->json($rows);
})->name('api.results.districts');

// API: results — schools by district (optional search q)
Route::get('/api/results/schools', function(Request $request){
    $districtId = (int)$request->query('district_id');
    abort_unless($districtId, 422);
    $q = trim((string)$request->query('q',''));
    $schools = DB::table('schools')->where('district_id',$districtId)
        ->when($q, fn($qq)=> $qq->where('name','like','%'.$q.'%'))
        ->orderBy('name')->get(['id','name','code']);
    return response()->json($schools);
})->name('api.results.schools');

// API: district schools and grouped types
Route::get('/api/results/district-schools', function (Request $request) {
    $exam = strtoupper((string)$request->query('exam'));
    $year = (int)$request->query('year');
    $region = (string)$request->query('region');
    $district = (string)$request->query('district');
    $regionParam = strtoupper(urldecode(str_replace(['+','-'], ' ', $region)));
    $districtParam = strtoupper(urldecode(str_replace(['+','-'], ' ', $district)));
    $regionRow = DB::table('regions')->whereRaw('upper(name) = ?', [$regionParam])->first();
    $districtRow = $regionRow ? DB::table('districts')->where('region_id',$regionRow->id)->whereRaw('upper(name) = ?', [$districtParam])->first() : null;
    if (!$regionRow || !$districtRow) return response()->json(['schools'=>[], 'types'=>[]]);
    $rows = DB::table('result_documents')
        ->where('result_documents.exam', $exam)
        ->where('result_documents.year', $year)
        ->where('result_documents.region_id', $regionRow->id)
        ->where('result_documents.district_id', $districtRow->id)
        ->where(function($qq){ if (Schema::hasColumn('result_documents','published')) $qq->where('result_documents.published', true); })
        ->join('schools','schools.id','=','result_documents.school_id')
        ->leftJoin('result_types','result_types.id','=','result_documents.result_type_id')
        ->select('schools.id as school_id','schools.name as school_name','result_documents.file_url','result_documents.created_at','result_types.code as type_code','result_types.name as type_name')
        ->orderBy('schools.name')
        ->get();
    $types = [];
    $schoolDocs = [];
    foreach ($rows as $r) {
        // normalize URL
        $fileUrl = $r->file_url;
        $isExternal = Str::startsWith($fileUrl, ['http://','https://']);
        if ($isExternal) {
            $absUrl = $fileUrl;
        } else {
            // strip any leading storage/ prefix, then route via result-files
            $clean = preg_replace('#^/?storage/#','', $fileUrl);
            $clean = ltrim($clean, '/');
            $absUrl = route('results.files.show', ['path' => $clean]);
        }
        // viewer URL for inline PDF view via /view/pdf
        $viewUrl = route('pdf.viewer', ['src' => $absUrl]);

        $code = $r->type_code ?: $exam;
        $name = $r->type_name ?: $exam;
        if (!isset($types[$code])) $types[$code] = ['name'=>$name, 'docs'=>[]];
        $types[$code]['docs'][] = [
            'school_id'=>$r->school_id,
            'school_name'=>$r->school_name,
            'file_url'=>$fileUrl,
            'created_at'=>$r->created_at,
            'url'=> $absUrl,
            'view_url' => $viewUrl,
        ];
        if (!isset($schoolDocs[$r->school_id])) $schoolDocs[$r->school_id] = $absUrl;
    }
    $schoolsQ = DB::table('schools')->where('district_id',$districtRow->id);
    if ($q = trim((string)$request->query('q'))) {
        $schoolsQ->where('name','like','%'.$q.'%');
    }
    $schools = $schoolsQ->orderBy('name')->get()->map(function($s) use ($schoolDocs){
        $abs = $schoolDocs[$s->id] ?? null;
        return [
            'id'=>$s->id,
            'name'=>$s->name,
            'url'=> $abs,
            'view_url' => $abs ? route('pdf.viewer', ['src' => $abs]) : null,
        ];
    });
    return response()->json(['schools'=>$schools, 'types'=>$types]);
})->name('api.results.district_schools');

// API: docs for a single school grouped by type
Route::get('/api/results/school-docs', function (Request $request) {
    $exam = strtoupper((string)$request->query('exam'));
    $year = (int)$request->query('year');
    $region = (string)$request->query('region');
    $district = (string)$request->query('district');
    $schoolId = (int)$request->query('school_id');
    $regionParam = strtoupper(urldecode(str_replace(['+','-'], ' ', $region)));
    $districtParam = strtoupper(urldecode(str_replace(['+','-'], ' ', $district)));
    $regionRow = DB::table('regions')->whereRaw('upper(name) = ?', [$regionParam])->first();
    $districtRow = $regionRow ? DB::table('districts')->where('region_id',$regionRow->id)->whereRaw('upper(name) = ?', [$districtParam])->first() : null;
    if (!$regionRow || !$districtRow || !$schoolId) return response()->json(['types'=>[]]);
    $rows = DB::table('result_documents')
        ->where('result_documents.exam', $exam)
        ->where('result_documents.year', $year)
        ->where('result_documents.region_id', $regionRow->id)
        ->where('result_documents.district_id', $districtRow->id)
        ->where('result_documents.school_id', $schoolId)
        ->where(function($qq){ if (Schema::hasColumn('result_documents','published')) $qq->where('result_documents.published', true); })
        ->leftJoin('result_types','result_types.id','=','result_documents.result_type_id')
        ->select('result_documents.file_url','result_documents.created_at','result_types.code as type_code','result_types.name as type_name')
        ->orderByDesc('result_documents.created_at')
        ->get();
    $types = [];
    foreach ($rows as $r) {
        // Normalize URL to align with /result-files/{path}
        $fileUrl = $r->file_url;
        $isExternal = Str::startsWith($fileUrl, ['http://','https://']);
        if ($isExternal) {
            $absUrl = $fileUrl;
        } else {
            // Strip any leading storage/ prefix, then route via result-files
            $clean = preg_replace('#^/?storage/#','', $fileUrl);
            $clean = ltrim($clean, '/');
            $absUrl = route('results.files.show', ['path' => $clean]);
        }
        $viewUrl = route('pdf.viewer', ['src' => $absUrl]);

        $code = $r->type_code ?: $exam;
        $name = $r->type_name ?: $exam;
        if (!isset($types[$code])) $types[$code] = ['name'=>$name, 'docs'=>[]];
        $types[$code]['docs'][] = [
            'file_url'=>$fileUrl,
            'created_at'=>$r->created_at,
            'url'=>$absUrl,
            'view_url' => $viewUrl,
        ];
    }
    return response()->json(['types'=>$types]);
})->name('api.results.school_docs');

// API: exam types (list)
Route::get('/api/exams/types', function () {
    $types = DB::table('result_types')->orderBy('name')->get();
    $yearMap = DB::table('result_type_year')->get()->groupBy('result_type_id');
    $years = DB::table('result_years')->get()->keyBy('id');
    $data = $types->map(function($t) use ($yearMap, $years){
        $assoc = $yearMap[$t->id] ?? collect();
        $yearList = $assoc->map(fn($r)=> optional($years[$r->result_year_id] ?? null)->year)->filter()->values()->all();
        return [
            'id' => $t->id,
            'code' => $t->code,
            'name' => $t->name,
            'description' => $t->description,
            'years' => $yearList,
            'link' => url('/exams/'.strtolower($t->code)),
        ];
    });
    return response()->json($data);
})->name('api.exams.types');

// API: single exam type detail by code
Route::get('/api/exams/{code}', function (string $code) {
    $type = DB::table('result_types')->whereRaw('upper(code)=?', [strtoupper($code)])->first();
    abort_unless($type, 404);
    $yearIds = DB::table('result_type_year')->where('result_type_id', $type->id)->pluck('result_year_id');
    $yearList = DB::table('result_years')->whereIn('id', $yearIds)->orderByDesc('year')->pluck('year');
    $pairs = DB::table('site_settings')->pluck('value','key');
    $contentKey = 'exams.'.strtolower($type->code).'.content';
    $content = $pairs[$contentKey] ?? null; // optional custom HTML/markdown
    return response()->json([
        'id' => $type->id,
        'code' => $type->code,
        'name' => $type->name,
        'description' => $type->description,
        'years' => $yearList,
        'content' => $content,
        'link' => url('/exams/'.strtolower($type->code)),
    ]);
})->name('api.exams.show');

// Admin: Results upload management
Route::middleware([])->group(function () {
    $ensureAdmin = function () { if (!session('admin_authenticated')) abort(403); };

    Route::get('/admin/results', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $exams = ['SFNA','PSLE','FTNA','CSEE','ACSEE'];
        $regions = DB::table('regions')->orderBy('name')->get();
        $years = DB::table('result_years')->orderByDesc('year')->get();
        $types = DB::table('result_types')->orderBy('name')->get();
        $typeYears = DB::table('result_type_year')->get()->groupBy('result_type_id');
        $recent = DB::table('result_documents')
            ->leftJoin('result_types','result_types.id','=','result_documents.result_type_id')
            ->leftJoin('regions','regions.id','=','result_documents.region_id')
            ->leftJoin('districts','districts.id','=','result_documents.district_id')
            ->leftJoin('schools','schools.id','=','result_documents.school_id')
            ->select(
                'result_documents.id','result_documents.exam','result_documents.year','result_documents.file_url','result_documents.created_at','result_documents.published',
                'result_types.code as type_code','result_types.name as type_name',
                'regions.name as region_name','districts.name as district_name','schools.name as school_name'
            )
            ->orderByDesc('result_documents.created_at')
            ->limit(50)
            ->get();
        $tab = $request->query('tab');
        return view('admin.results.index', compact('exams','regions','years','types','typeYears','recent','tab'));
    })->name('admin.results.index');

    // Public API: list uploaded result documents with filters
    Route::get('/api/results/documents', function (Request $request) {
        $exam = strtoupper((string)$request->query('exam', ''));
        $year = $request->query('year');
        $regionId = $request->query('region_id');
        $districtId = $request->query('district_id');
        $schoolId = $request->query('school_id');
        $limit = min(max((int)$request->query('limit', 50), 1), 200);
        $page = max((int)$request->query('page', 1), 1);
        $offset = ($page - 1) * $limit;

        $base = DB::table('result_documents')
            ->leftJoin('result_types','result_types.id','=','result_documents.result_type_id')
            ->leftJoin('regions','regions.id','=','result_documents.region_id')
            ->leftJoin('districts','districts.id','=','result_documents.district_id')
            ->leftJoin('schools','schools.id','=','result_documents.school_id');

        if ($exam) $base->where('result_documents.exam', $exam);
        if ($year) $base->where('result_documents.year', (int)$year);
        if ($regionId) $base->where('result_documents.region_id', (int)$regionId);
        if ($districtId) $base->where('result_documents.district_id', (int)$districtId);
        if ($schoolId) $base->where('result_documents.school_id', (int)$schoolId);

        $total = (clone $base)->count();

        $rows = $base
            ->orderByDesc('result_documents.year')
            ->orderByDesc('result_documents.created_at')
            ->offset($offset)
            ->limit($limit)
            ->get([
                'result_documents.id',
                'result_documents.exam',
                'result_documents.year',
                'result_documents.file_url',
                'result_documents.created_at',
                'result_documents.updated_at',
                'result_documents.region_id',
                'result_documents.district_id',
                'result_documents.school_id',
                'regions.name as region_name',
                'districts.name as district_name',
                'schools.name as school_name',
                'schools.code as school_code',
                'result_types.code as type_code',
                'result_types.name as type_name',
            ]);

        $items = $rows->map(function($r){
            $fileUrl = $r->file_url;
            $isExternal = Str::startsWith($fileUrl, ['http://','https://']);
            if ($isExternal) {
                $abs = $fileUrl;
            } else {
                $abs = Str::startsWith($fileUrl, ['/storage','storage/'])
                    ? (Str::startsWith($fileUrl, '/') ? $fileUrl : '/'.$fileUrl)
                    : Storage::url($fileUrl);
            }
            return [
                'id' => $r->id,
                'exam' => $r->exam,
                'year' => $r->year,
                'type_code' => $r->type_code,
                'type_name' => $r->type_name,
                'region_id' => $r->region_id,
                'region_name' => $r->region_name,
                'district_id' => $r->district_id,
                'district_name' => $r->district_name,
                'school_id' => $r->school_id,
                'school_name' => $r->school_name,
                'school_code' => $r->school_code,
                'file_url' => $r->file_url,
                'url' => $abs,
                'created_at' => $r->created_at,
                'updated_at' => $r->updated_at,
            ];
        });

        return response()->json([
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ]);
    })->name('api.results.documents');

    Route::post('/admin/results', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'exam' => 'required|string', // class: SFNA/PSLE/FTNA/CSEE/ACSEE
            'year' => 'required|integer|min:2000|max:2100',
            'region_id' => 'nullable|exists:regions,id',
            'district_id' => 'nullable|exists:districts,id',
            'school_id' => 'required|exists:schools,id',
            'school_code' => 'nullable|string|max:50',
            'result_type_id' => 'required|exists:result_types,id',
            'file' => 'nullable|file|mimes:pdf',
            'file_url' => 'nullable|url',
        ]);
        // Validate class and type-year association
        $legacy = ['SFNA','PSLE','FTNA','CSEE','ACSEE'];
        if (!in_array(strtoupper($data['exam']), $legacy, true)) {
            return back()->withErrors(['exam' => 'Please select a valid Results Class'])->withInput();
        }
        $type = DB::table('result_types')->where('id', $data['result_type_id'])->first();
        if (!$type) {
            return back()->withErrors(['result_type_id' => 'Select a valid Results Type'])->withInput();
        }
        $yearIds = DB::table('result_type_year')->where('result_type_id', $type->id)->pluck('result_year_id');
        if ($yearIds->count() > 0) {
            $allowedYears = DB::table('result_years')->whereIn('id', $yearIds)->pluck('year')->toArray();
            if (!in_array((int)$data['year'], $allowedYears, true)) {
                return back()->withErrors(['year' => 'Selected year is not allowed for this Results Type'])->withInput();
            }
        }
        if (empty($data['file']) && empty($data['file_url'])) {
            return back()->withErrors(['file' => 'Provide a PDF file or an external URL'])->withInput();
        }
        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('results', 'public');
        } else {
            $path = $data['file_url'];
        }
        // Infer geography if missing
        $regionId = $data['region_id'] ?? null;
        $districtId = $data['district_id'] ?? null;
        $schoolId = $data['school_id'] ?? null;
        if (!$schoolId && !empty($data['school_code'])) {
            $sch = DB::table('schools')->where('code', $data['school_code'])->first();
            if ($sch) { $schoolId = $sch->id; $districtId = $districtId ?: $sch->district_id; }
        }
        if ($schoolId) {
            $school = DB::table('schools')->where('id', $schoolId)->first();
            if ($school) {
                $districtId = $districtId ?: $school->district_id;
            }
        }
        if ($districtId) {
            $district = DB::table('districts')->where('id', $districtId)->first();
            if ($district) {
                $regionId = $regionId ?: $district->region_id;
            }
        }
        // If schema requires district_id not null when school present, ensure it's set
        if ($schoolId && !$districtId) {
            return back()->withErrors(['district_id' => 'District could not be inferred from the selected school. Please choose council explicitly.'])->withInput();
        }
        DB::table('result_documents')->insert([
            'exam' => $data['exam'],
            'year' => (int)$data['year'],
            'result_type_id' => (int)$type->id,
            'region_id' => $regionId,
            'district_id' => $districtId,
            'school_id' => $schoolId,
            'file_url' => $path,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.results.index');
    })->name('admin.results.store');

    // Toggle publish state per item
    Route::post('/admin/results/{id}/toggle', function ($id, Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $doc = DB::table('result_documents')->where('id',$id)->first();
        if (!$doc) return back();
        DB::table('result_documents')->where('id',$id)->update([
            'published' => !$doc->published,
            'updated_at' => now(),
        ]);
        return back();
    })->name('admin.results.toggle');

    // Bulk publish/unpublish
    Route::post('/admin/results/bulk/publish', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'ids' => 'array',
            'ids.*' => 'integer',
            'action' => 'required|in:publish,unpublish',
        ]);
        if (!empty($data['ids'])) {
            $value = $data['action'] === 'publish';
            DB::table('result_documents')->whereIn('id',$data['ids'])->update(['published'=>$value,'updated_at'=>now()]);
        }
        return back()->with('status', 'Bulk action completed');
    })->name('admin.results.bulk_publish');

    // Year management
    Route::post('/admin/results/years', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:2100|unique:result_years,year',
            'description' => 'nullable|string',
        ]);
        DB::table('result_years')->insert([
            'year' => (int)$data['year'],
            'description' => $data['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.results.index', ['tab'=>'year']);
    })->name('admin.results.years.store');

    Route::post('/admin/results/years/{id}', function ($id, Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:2100|unique:result_years,year,'.$id,
            'description' => 'nullable|string',
        ]);
        DB::table('result_years')->where('id',$id)->update([
            'year' => (int)$data['year'],
            'description' => $data['description'] ?? null,
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.results.index', ['tab'=>'year']);
    })->name('admin.results.years.update');

    Route::post('/admin/results/years/{id}/delete', function ($id) use ($ensureAdmin) {
        $ensureAdmin();
        DB::table('result_years')->where('id',$id)->delete();
        return redirect()->route('admin.results.index', ['tab'=>'year']);
    })->name('admin.results.years.delete');

    // Types management
    Route::post('/admin/results/types', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:result_types,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        DB::table('result_types')->insert([
            'code' => strtoupper($data['code']),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.results.index', ['tab'=>'types']);
    })->name('admin.results.types.store');

    Route::post('/admin/results/types/{id}', function ($id, Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:result_types,code,'.$id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        DB::table('result_types')->where('id',$id)->update([
            'code' => strtoupper($data['code']),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.results.index', ['tab'=>'types']);
    })->name('admin.results.types.update');

    Route::post('/admin/results/types/{id}/delete', function ($id) use ($ensureAdmin) {
        $ensureAdmin();
        DB::table('result_types')->where('id',$id)->delete();
        DB::table('result_type_year')->where('result_type_id',$id)->delete();
        return redirect()->route('admin.results.index', ['tab'=>'types']);
    })->name('admin.results.types.delete');

    Route::post('/admin/results/types/{id}/years', function ($id, Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $yearIds = $request->input('year_ids', []);
        if (!is_array($yearIds)) $yearIds = [];
        // Clear
        DB::table('result_type_year')->where('result_type_id',$id)->delete();
        // Insert
        $rows = [];
        $now = now();
        foreach ($yearIds as $yid) {
            if (!$yid) continue;
            $rows[] = [
                'result_type_id' => (int)$id,
                'result_year_id' => (int)$yid,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if ($rows) DB::table('result_type_year')->insert($rows);
        return redirect()->route('admin.results.index', ['tab'=>'types']);
    })->name('admin.results.types.years');

    // Bulk publish results
    Route::post('/admin/results/bulk', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $validated = $request->validate([
            'exam' => 'required|in:SFNA,PSLE,FTNA,CSEE,ACSEE',
            'year' => 'required|integer|min:2000|max:2100',
            'result_type_id' => 'required|exists:result_types,id',
            'file' => 'required|file|mimes:csv,txt',
        ]);
        $exam = $validated['exam'];
        $year = (int)$validated['year'];
        $type = DB::table('result_types')->where('id', $validated['result_type_id'])->first();
        $yearIds = DB::table('result_type_year')->where('result_type_id', $type->id)->pluck('result_year_id');
        if ($yearIds->count() > 0) {
            $allowedYears = DB::table('result_years')->whereIn('id', $yearIds)->pluck('year')->toArray();
            if (!in_array($year, $allowedYears, true)) {
                return back()->withErrors(['year' => 'Selected year is not allowed for this Results Type'])->withInput();
            }
        }
        $fh = fopen($request->file('file')->getRealPath(), 'r');
        if (!$fh) return back()->withErrors(['file'=>'Failed to read CSV'])->withInput();
        $inserted=0; $skipped=0; $errors=[];
        $header = fgetcsv($fh);
        $map = [];
        $hasHeader = false;
        if ($header) {
            $lower = array_map(fn($h)=> strtolower(trim($h)), $header);
            $hasHeader = in_array('school_code',$lower) || in_array('file_url',$lower);
            if ($hasHeader) {
                foreach ($header as $idx=>$h) { $map[strtolower(trim($h))] = $idx; }
            } else {
                rewind($fh);
            }
        } else { rewind($fh); }
        while (($row = fgetcsv($fh)) !== false) {
            $code = null; $fileUrl = null;
            if ($hasHeader) {
                $code = trim($row[$map['school_code']] ?? '');
                $fileUrl = trim($row[$map['file_url']] ?? '');
            } else {
                $code = trim($row[0] ?? '');
                $fileUrl = trim($row[1] ?? '');
            }
            if (!$code || !$fileUrl) { $skipped++; continue; }
            try {
                $school = DB::table('schools')->where('code', $code)->first();
                if (!$school) { $skipped++; $errors[] = $code; continue; }
                $district = DB::table('districts')->where('id',$school->district_id)->first();
                DB::table('result_documents')->insert([
                    'exam' => $exam,
                    'year' => $year,
                    'result_type_id' => (int)$type->id,
                    'region_id' => $district?->region_id,
                    'district_id' => $school->district_id,
                    'school_id' => $school->id,
                    'file_url' => $fileUrl,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            } catch (\Throwable $e) { $skipped++; $errors[] = $code; }
        }
        fclose($fh);
        return redirect()->route('admin.results.index')->with('status', "Bulk publish completed: $inserted inserted, $skipped skipped");
    })->name('admin.results.bulk');

    // Admin: Notifications
    Route::get('/admin/notifications', function () use ($ensureAdmin) {
        $ensureAdmin();
        $notifications = DB::table('notifications')->orderByDesc('created_at')->limit(100)->get();
        return view('admin.notifications.index', compact('notifications'));
    })->name('admin.notifications.index');

    Route::post('/admin/notifications', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);
        $now = now();
        DB::table('notifications')->insert([
            'title' => $data['title'],
            'body' => $data['body'],
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        return redirect()->route('admin.notifications.index')->with('status','Notification published');
    })->name('admin.notifications.store');

    // Admin: Settings (General & Maintenance)
    Route::get('/admin/settings', function () use ($ensureAdmin) {
        $ensureAdmin();
        $pairs = DB::table('site_settings')->pluck('value','key');
        $settings = [
            'site_name' => $pairs['general.site_name'] ?? 'RSMS',
            'favicon' => $pairs['general.favicon_path'] ?? ($pairs['general.favicon_url'] ?? ''),
            'hero' => $pairs['general.hero_path'] ?? ($pairs['general.hero_url'] ?? ''),
            'maint_enabled' => filter_var($pairs['maintenance.enabled'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
            'maint_ends' => $pairs['maintenance.ends_at'] ?? '',
            'maint_message' => $pairs['maintenance.message'] ?? '',
        ];
        return view('admin.settings.index', compact('settings'));
    })->name('admin.settings');

    Route::post('/admin/settings', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'site_name' => 'required|string|max:150',
            'favicon' => 'nullable|file|mimes:png,ico',
            'favicon_url' => 'nullable|url',
            'hero' => 'nullable|image',
            'hero_url' => 'nullable|url',
            'maint_enabled' => 'nullable|boolean',
            'maint_ends' => 'nullable|string',
            'maint_message' => 'nullable|string',
        ]);
        $upserts = [];
        $now = now();
        $put = function($k,$v) use(&$upserts,$now){ $upserts[] = ['key'=>$k,'value'=>$v,'created_at'=>$now,'updated_at'=>$now]; };
        $put('general.site_name', $data['site_name']);
        if ($request->file('favicon')) {
            $path = $request->file('favicon')->store('assets','public');
            $put('general.favicon_path', $path);
            $put('general.favicon_url', null);
        } elseif (!empty($data['favicon_url'])) {
            $put('general.favicon_url', $data['favicon_url']);
            $put('general.favicon_path', null);
        }
        if ($request->file('hero')) {
            $path = $request->file('hero')->store('assets','public');
            $put('general.hero_path', $path);
            $put('general.hero_url', null);
        } elseif (!empty($data['hero_url'])) {
            $put('general.hero_url', $data['hero_url']);
            $put('general.hero_path', null);
        }
        $put('maintenance.enabled', $request->boolean('maint_enabled') ? 'true' : 'false');
        if (!empty($data['maint_ends'])) $put('maintenance.ends_at', $data['maint_ends']);
        $put('maintenance.message', $data['maint_message'] ?? '');

        foreach ($upserts as $row) {
            DB::table('site_settings')->updateOrInsert(['key'=>$row['key']], ['value'=>$row['value'],'updated_at'=>$now,'created_at'=>$row['created_at']]);
        }
        return redirect()->route('admin.settings')->with('status','Settings saved');
    })->name('admin.settings.save');

    // Admin: Profile (view/update)
    Route::get('/admin/profile', function () use ($ensureAdmin) {
        $ensureAdmin();
        $pairs = DB::table('site_settings')->pluck('value','key');
        $profile = [
            'name' => $pairs['admin.name'] ?? (session('admin_name') ?? 'Administrator'),
            'email' => $pairs['admin.email'] ?? '',
        ];
        return view('admin.profile', compact('profile'));
    })->name('admin.profile');

    Route::post('/admin/profile', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:200',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $pairs = DB::table('site_settings')->pluck('value','key');
        $now = now();
        $upsert = function($k,$v) use($now){ DB::table('site_settings')->updateOrInsert(['key'=>$k], ['value'=>$v,'updated_at'=>$now,'created_at'=>$now]); };
        $upsert('admin.name', $data['name']);
        if (!empty($data['email'])) $upsert('admin.email', $data['email']);
        // Update session display name
        session(['admin_name' => $data['name']]);
        // Change password if provided
        if (!empty($data['password'])) {
            $stored = $pairs['admin.password'] ?? null;
            if ($stored) {
                if (empty($data['current_password']) || !Hash::check($data['current_password'], $stored)) {
                    return back()->withErrors(['current_password' => 'Current password is incorrect'])->withInput();
                }
            }
            $upsert('admin.password', Hash::make($data['password']));
        }
        return back()->with('status','Profile updated');
    })->name('admin.profile.save');

    Route::get('/admin/results/template', function () use ($ensureAdmin) {
        $ensureAdmin();
        $csv = "school_code,file_url\n".
               "S1234,https://example.com/csee_2024_s1234.pdf\n".
               "P5678,https://example.com/psle_2024_p5678.pdf\n";
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="results_template.csv"'
        ]);
    })->name('admin.results.template');
});

// Admin: Publications panel (list folders and folder docs)
Route::middleware([])->group(function () {
    $ensureAdmin = function () { if (!session('admin_authenticated')) abort(403); };

    Route::get('/admin/publications', function () use ($ensureAdmin) {
        $ensureAdmin();
        $folders = DB::table('publication_folders')
            ->leftJoin('publications','publications.folder_id','=','publication_folders.id')
            ->select(
                'publication_folders.id',
                'publication_folders.name',
                'publication_folders.slug',
                'publication_folders.created_at',
                'publication_folders.updated_at',
                DB::raw('COUNT(publications.id) as docs_count')
            )
            ->groupBy(
                'publication_folders.id',
                'publication_folders.name',
                'publication_folders.slug',
                'publication_folders.created_at',
                'publication_folders.updated_at'
            )
            ->orderBy('publication_folders.name','asc')
            ->get();
        return view('admin.publications.index-admin', compact('folders'));
    })->name('admin.pub.index');

    Route::get('/admin/publications/folders/{id}', function (int $id) use ($ensureAdmin) {
        $ensureAdmin();
        $folder = DB::table('publication_folders')->where('id',$id)->first();
        abort_unless($folder, 404);
        $docs = DB::table('publications')->where('folder_id',$id)->orderByDesc('published_at')->orderByDesc('created_at')->get();
        return view('admin.publications.folder-admin', compact('folder','docs'));
    })->name('admin.pub.folder');
});

// Admin: Schools management
Route::middleware([])->group(function () {
    $ensureAdmin = function () { if (!session('admin_authenticated')) abort(403); };

    Route::get('/admin/schools', function () use ($ensureAdmin) {
        $ensureAdmin();
        return view('admin.schools.index');
    })->name('admin.schools.index');

    // API: list schools with filters
    Route::get('/api/admin/schools', function (Request $request) {
        if (!session('admin_authenticated')) abort(403);
        $regionId = $request->query('region_id');
        $districtId = $request->query('district_id');
        $q = DB::table('schools')
            ->join('districts','districts.id','=','schools.district_id')
            ->join('regions','regions.id','=','districts.region_id')
            ->select('schools.id','schools.name','schools.code','schools.level','districts.name as district_name','districts.id as district_id','regions.name as region_name','regions.id as region_id')
            ->orderBy('regions.name')->orderBy('districts.name')->orderBy('schools.name');
        if ($regionId) { $q->where('regions.id', $regionId); }
        if ($districtId) { $q->where('districts.id', $districtId); }
        return response()->json($q->get());
    })->name('api.admin.schools.index');

    // API: create single school
    Route::post('/api/admin/schools', function (Request $request) {
        if (!session('admin_authenticated')) abort(403);
        $data = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:160',
            'code' => 'nullable|string|max:40',
            'level' => 'required|string|in:primary,secondary,other',
        ]);
        $id = DB::table('schools')->insertGetId([
            'district_id' => $data['district_id'],
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'level' => $data['level'],
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $row = DB::table('schools')
            ->join('districts','districts.id','=','schools.district_id')
            ->join('regions','regions.id','=','districts.region_id')
            ->where('schools.id',$id)
            ->select('schools.id','schools.name','schools.code','schools.level','districts.name as district_name','districts.id as district_id','regions.name as region_name','regions.id as region_id')
            ->first();
        return response()->json($row, 201);
    })->name('api.admin.schools.store');

    // API: update school
    Route::post('/api/admin/schools/{id}', function (Request $request, int $id) {
        if (!session('admin_authenticated')) abort(403);
        $data = $request->validate([
            'district_id' => 'sometimes|exists:districts,id',
            'name' => 'required|string|max:160',
            'code' => 'nullable|string|max:40',
            'level' => 'sometimes|string|in:primary,secondary,other',
        ]);
        $exists = DB::table('schools')->where('id', $id)->exists();
        abort_unless($exists, 404);
        DB::table('schools')->where('id', $id)->update([
            'district_id' => $data['district_id'] ?? DB::raw('district_id'),
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'level' => $data['level'] ?? DB::raw('level'),
            'updated_at' => now(),
        ]);
        $row = DB::table('schools')
            ->join('districts','districts.id','=','schools.district_id')
            ->join('regions','regions.id','=','districts.region_id')
            ->where('schools.id',$id)
            ->select('schools.id','schools.name','schools.code','districts.name as district_name','districts.id as district_id','regions.name as region_name','regions.id as region_id')
            ->first();
        return response()->json($row);
    })->name('api.admin.schools.update');

    // API: delete school
    Route::delete('/api/admin/schools/{id}', function (int $id) {
        if (!session('admin_authenticated')) abort(403);
        $deleted = DB::table('schools')->where('id', $id)->delete();
        abort_unless($deleted, 404);
        return response()->json(['deleted'=>true]);
    })->name('api.admin.schools.delete');

    // Bulk upload CSV for schools (requires district selection)
    Route::post('/api/admin/schools/bulk', function (Request $request) {
        if (!session('admin_authenticated')) abort(403);
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'file' => 'required|file|mimes:csv,txt',
        ]);
        $districtId = (int)$validated['district_id'];
        $file = $request->file('file')->getRealPath();
        $handle = fopen($file, 'r');
        if (!$handle) return response()->json(['message'=>'Failed to read file'], 422);
        $inserted = 0; $skipped = 0; $errors = [];
        $header = fgetcsv($handle);
        $hasHeader = false; $map = [];
        if ($header) {
            $lower = array_map(fn($h)=> strtolower(trim($h)), $header);
            $hasHeader = in_array('name', $lower) || in_array('code', $lower);
            if ($hasHeader) {
                foreach ($header as $idx=>$h) { $map[strtolower(trim($h))] = $idx; }
            } else {
                rewind($handle);
            }
        } else { rewind($handle); }
        while (($row = fgetcsv($handle)) !== false) {
            $name = null; $code = null;
            if ($hasHeader) {
                $name = trim($row[$map['name']] ?? '');
                $code = isset($map['code']) ? trim($row[$map['code']] ?? '') : null;
            } else {
                $name = trim($row[0] ?? '');
                $code = trim($row[1] ?? '');
            }
            if (!$name) { $skipped++; continue; }
            try {
                DB::table('schools')->insert([
                    'district_id' => $districtId,
                    'name' => $name,
                    'code' => $code ?: null,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $inserted++;
            } catch (\Throwable $e) {
                $skipped++; $errors[] = $name;
            }
        }
        fclose($handle);
        return response()->json(['inserted'=>$inserted,'skipped'=>$skipped,'errors'=>$errors]);
    })->name('api.admin.schools.bulk');

    // Download CSV template
    Route::get('/admin/schools/template', function () {
        if (!session('admin_authenticated')) abort(403);
        $csv = "name,code\n".
               "Kibasila Secondary, S1234\n".
               "Mchanganyiko Primary, P5678\n";
        $filename = 'schools_template.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    })->name('admin.schools.template');
});

// Admin: Councils (districts) management
Route::middleware([])->group(function () {
    $ensureAdmin = function () { if (!session('admin_authenticated')) abort(403); };

    Route::get('/admin/councils', function () use ($ensureAdmin) {
        $ensureAdmin();
        return view('admin.councils.index');
    })->name('admin.councils.index');

    // API: list councils by region
    Route::get('/api/admin/councils', function (Request $request) {
        if (!session('admin_authenticated')) abort(403);
        $regionId = $request->query('region_id');
        $q = DB::table('districts')
            ->leftJoin('schools','schools.district_id','=','districts.id')
            ->select('districts.id','districts.name', 'districts.region_id', DB::raw('COUNT(schools.id) as schools_count'))
            ->groupBy('districts.id','districts.name','districts.region_id')
            ->orderBy('districts.name');
        if ($regionId) { $q->where('districts.region_id', $regionId); }
        return response()->json($q->get());
    })->name('api.admin.councils.index');

    // API: create single council
    Route::post('/api/admin/councils', function (Request $request) {
        if (!session('admin_authenticated')) abort(403);
        $data = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:160',
        ]);
        $id = DB::table('districts')->insertGetId([
            'region_id' => $data['region_id'],
            'name' => $data['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['id'=>$id,'name'=>$data['name'],'region_id'=>$data['region_id'],'schools_count'=>0], 201);
    })->name('api.admin.councils.store');

    // API: update council name
    Route::post('/api/admin/councils/{id}', function (Request $request, int $id) {
        if (!session('admin_authenticated')) abort(403);
        $data = $request->validate([
            'name' => 'required|string|max:160',
        ]);
        $exists = DB::table('districts')->where('id', $id)->exists();
        abort_unless($exists, 404);
        DB::table('districts')->where('id', $id)->update([
            'name' => $data['name'],
            'updated_at' => now(),
        ]);
        return response()->json(['id'=>$id,'name'=>$data['name']]);
    })->name('api.admin.councils.update');

    // Bulk upload CSV for councils (requires region selection)
    Route::post('/api/admin/councils/bulk', function (Request $request) {
        if (!session('admin_authenticated')) abort(403);
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'file' => 'required|file|mimes:csv,txt',
        ]);
        $regionId = (int)$validated['region_id'];
        $file = $request->file('file')->getRealPath();
        $handle = fopen($file, 'r');
        if (!$handle) return response()->json(['message'=>'Failed to read file'], 422);
        $inserted = 0; $skipped = 0; $errors = [];
        // attempt to read header
        $header = fgetcsv($handle);
        $hasNameHeader = false;
        if ($header) {
            $lower = array_map(fn($h)=> strtolower(trim($h)), $header);
            $hasNameHeader = in_array('name', $lower);
            if (!$hasNameHeader) {
                // treat first line as data, rewind pointer
                rewind($handle);
            }
        } else {
            rewind($handle);
        }
        while (($row = fgetcsv($handle)) !== false) {
            $name = null;
            if ($hasNameHeader) {
                $map = [];
                foreach ($header as $idx=>$h) { $map[strtolower(trim($h))] = $idx; }
                if (isset($map['name'])) { $name = trim($row[$map['name']] ?? ''); }
            } else {
                $name = trim($row[0] ?? '');
            }
            if (!$name) { $skipped++; continue; }
            try {
                DB::table('districts')->insert([
                    'region_id' => $regionId,
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            } catch (\Throwable $e) {
                $skipped++; $errors[] = $name;
            }
        }
        fclose($handle);
        return response()->json(['inserted'=>$inserted,'skipped'=>$skipped,'errors'=>$errors]);
    })->name('api.admin.councils.bulk');

    // Download CSV template
    Route::get('/admin/councils/template', function () {
        if (!session('admin_authenticated')) abort(403);
        $csv = "name\n".
               "Ilala\n".
               "Temeke\n";
        $filename = 'councils_template.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    })->name('admin.councils.template');
});
// Admin: Regions management
Route::middleware([])->group(function () {
    $ensureAdmin = function () { if (!session('admin_authenticated')) abort(403); };

    Route::get('/admin/regions', function () use ($ensureAdmin) {
        $ensureAdmin();
        $regions = DB::table('regions')
            ->leftJoin('districts','districts.region_id','=','regions.id')
            ->leftJoin('schools','schools.district_id','=','districts.id')
            ->groupBy('regions.id','regions.name')
            ->select('regions.id','regions.name', DB::raw('COUNT(schools.id) as schools_count'))
            ->orderBy('regions.name')
            ->get();
        return view('admin.regions.index', compact('regions'));
    })->name('admin.regions.index');

    Route::get('/admin/regions/create', function () use ($ensureAdmin) {
        $ensureAdmin();
        return view('admin.regions.create');
    })->name('admin.regions.create');

    Route::post('/admin/regions', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate(['name' => 'required|string|max:160|unique:regions,name']);
        DB::table('regions')->insert([
            'name' => $data['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.regions.index');
    })->name('admin.regions.store');

    // API: bulk import regions + councils from Excel
    Route::post('/api/admin/regions/bulk-locations', function (Request $request) {
        if (!session('admin_authenticated')) abort(403);
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $validated['file'];
        $collection = Excel::toCollection(null, $file)->first();
        if (!$collection || $collection->isEmpty()) {
            return response()->json(['inserted_regions'=>0,'inserted_councils'=>0,'skipped'=>0,'errors'=>['Empty file']], 422);
        }

        $insertedRegions = 0; $insertedCouncils = 0; $skipped = 0; $errors = [];

        $headerRow = $collection->shift();
        $map = [];
        foreach ($headerRow as $idx => $h) {
            $key = strtolower(trim((string)$h));
            if ($key) { $map[$key] = $idx; }
        }

        $hasRegion = isset($map['region']) || isset($map['region_name']);
        $hasCouncil = isset($map['council']) || isset($map['district']) || isset($map['council_name']) || isset($map['district_name']);
        if (!$hasRegion || !$hasCouncil) {
            return response()->json(['inserted_regions'=>0,'inserted_councils'=>0,'skipped'=>0,'errors'=>['Missing Region / Council columns']], 422);
        }

        foreach ($collection as $row) {
            $regionName = null; $councilName = null;
            $regionIdx = $map['region'] ?? $map['region_name'] ?? null;
            $councilIdx = $map['council'] ?? $map['district'] ?? $map['council_name'] ?? $map['district_name'] ?? null;
            if ($regionIdx !== null) {
                $regionName = trim((string)($row[$regionIdx] ?? ''));
            }
            if ($councilIdx !== null) {
                $councilName = trim((string)($row[$councilIdx] ?? ''));
            }
            if (!$regionName || !$councilName) { $skipped++; continue; }

            try {
                $region = DB::table('regions')->where('name', $regionName)->first();
                if (!$region) {
                    $regionId = DB::table('regions')->insertGetId([
                        'name' => $regionName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $region = (object)['id' => $regionId];
                    $insertedRegions++;
                }

                $exists = DB::table('districts')
                    ->where('region_id', $region->id)
                    ->where('name', $councilName)
                    ->exists();
                if (!$exists) {
                    DB::table('districts')->insert([
                        'region_id' => $region->id,
                        'name' => $councilName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $insertedCouncils++;
                } else {
                    $skipped++;
                }
            } catch (\Throwable $e) {
                $skipped++; $errors[] = $regionName.' - '.$councilName;
            }
        }

        return response()->json([
            'inserted_regions' => $insertedRegions,
            'inserted_councils' => $insertedCouncils,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    })->name('api.admin.regions.bulk-locations');

    // API: Regions list (JSON)
    Route::get('/api/admin/regions', function () {
        if (!session('admin_authenticated')) abort(403);
        $regions = DB::table('regions')
            ->leftJoin('districts','districts.region_id','=','regions.id')
            ->leftJoin('schools','schools.district_id','=','districts.id')
            ->groupBy('regions.id','regions.name','regions.created_at','regions.updated_at')
            ->select('regions.id','regions.name', DB::raw('COUNT(schools.id) as schools_count'))
            ->orderBy('regions.name')
            ->get();
        return response()->json($regions);
    })->name('api.admin.regions.index');

    // API: Create region (JSON)
    Route::post('/api/admin/regions', function (Request $request) {
        if (!session('admin_authenticated')) abort(403);
        $data = $request->validate(['name' => 'required|string|max:160|unique:regions,name']);
        $id = DB::table('regions')->insertGetId([
            'name' => $data['name'], 'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json(['id' => $id, 'name' => $data['name'], 'schools_count' => 0], 201);
    })->name('api.admin.regions.store');

    Route::get('/admin/regions/{id}', function (int $id) use ($ensureAdmin) {
        $ensureAdmin();
        $region = DB::table('regions')->where('id', $id)->first();
        abort_unless($region, 404);
        $schools = DB::table('schools')
            ->join('districts','districts.id','=','schools.district_id')
            ->where('districts.region_id', $id)
            ->select('schools.name as school_name','schools.code','districts.name as district_name')
            ->orderBy('districts.name')->orderBy('schools.name')
            ->get();
        return view('admin.regions.show', compact('region','schools'));
    })->name('admin.regions.show');

    Route::get('/admin/regions/{id}/edit', function (int $id) use ($ensureAdmin) {
        $ensureAdmin();
        $region = DB::table('regions')->where('id', $id)->first();
        abort_unless($region, 404);
        return view('admin.regions.edit', compact('region'));
    })->name('admin.regions.edit');

    Route::post('/admin/regions/{id}', function (Request $request, int $id) use ($ensureAdmin) {
        $ensureAdmin();
        $region = DB::table('regions')->where('id', $id)->first();
        abort_unless($region, 404);
        $data = $request->validate(['name' => 'required|string|max:160|unique:regions,name,'.$id]);
        DB::table('regions')->where('id', $id)->update(['name' => $data['name'], 'updated_at' => now()]);
        return redirect()->route('admin.regions.index');
    })->name('admin.regions.update');
});

// Exam Types pages
Route::prefix('exams')->group(function () {
    Route::view('sfna', 'exams.sfna')->name('exam.sfna');
    Route::view('psle', 'exams.psle')->name('exam.psle');
    Route::view('ftna', 'exams.ftna')->name('exam.ftna');
    Route::view('csee', 'exams.csee')->name('exam.csee');
    Route::view('acsee', 'exams.acsee')->name('exam.acsee');
});

Route::get('/about', function () {
    return view('about');
})->name('about');

// Privacy Policy
Route::get('/privacy-policy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/faq', function () {
    return view('faq');
})->name('faq');

// Exams landing + detail pages
Route::get('/exams', function () {
    return view('exams.index');
})->name('exams.index');

Route::get('/exams/{code}', function (string $code) {
    return view('exams.show', ['code' => strtolower($code)]);
})->name('exams.show');

// Results Center (public)
Route::get('/results-center', function () {
    return view('results.center');
})->name('results.center');

// Results APIs
Route::get('/api/results/filters', function (Request $request) {
    $regionId = $request->query('region_id');
    $districtId = $request->query('district_id');
    $exams = ['SFNA','PSLE','FTNA','CSEE','ACSEE'];
    $regions = DB::table('regions')->orderBy('name')->get(['id','name']);
    $districts = collect();
    $schools = collect();
    if ($regionId) {
        $districts = DB::table('districts')->where('region_id', $regionId)->orderBy('name')->get(['id','name']);
    }
    if ($districtId) {
        $schools = DB::table('schools')->where('district_id', $districtId)->orderBy('name')->get(['id','name']);
    }
    return response()->json([
        'exams' => $exams,
        'regions' => $regions,
        'districts' => $districts,
        'schools' => $schools,
    ]);
})->name('api.results.filters');

Route::get('/api/results/years', function (Request $request) {
    $exam = strtoupper($request->query('exam', ''));
    abort_if(!$exam, 400);
    $years = DB::table('result_documents')->where('exam', $exam)
        ->select('year')->distinct()->orderByDesc('year')->pluck('year');
    return response()->json($years);
})->name('api.results.years');

Route::get('/api/results/list', function (Request $request) {
    $exam = strtoupper($request->query('exam', ''));
    $regionId = $request->query('region_id');
    $districtId = $request->query('district_id');
    $schoolId = $request->query('school_id');
    $q = DB::table('result_documents')
        ->when($exam, fn($qq)=>$qq->where('result_documents.exam',$exam))
        ->leftJoin('result_types','result_types.id','=','result_documents.result_type_id')
        ->select(
            'result_documents.exam',
            'result_documents.year',
            'result_documents.file_url',
            'result_documents.created_at',
            'result_types.code as type_code',
            'result_types.name as type_name'
        );
    if ($regionId) $q->where('result_documents.region_id', $regionId);
    if ($districtId) $q->where('result_documents.district_id', $districtId);
    if ($schoolId) $q->where('result_documents.school_id', $schoolId);
    $rows = $q->orderByDesc('result_documents.year')->orderByDesc('result_documents.created_at')->get();
    $grouped = [];
    foreach ($rows as $r) {
        $fileUrl = $r->file_url;
        $isExternal = Str::startsWith($fileUrl, ['http://','https://']);
        if ($isExternal) {
            $absUrl = $fileUrl;
        } else {
            $clean = preg_replace('#^/?storage/#','', $fileUrl);
            $clean = ltrim($clean, '/');
            $absUrl = route('results.files.show', ['path' => $clean]);
        }
        $title = $r->exam.' '.$r->year;
        $grouped[$r->year][] = [
            'title' => $title,
            'url' => $absUrl,
            'url' => $url,
            'year' => $r->year,
            'created_at' => $r->created_at,
            'type_name' => $r->type_name,
            'type_code' => $r->type_code,
        ];
    }
    krsort($grouped);
    return response()->json($grouped);
})->name('api.results.list');

// Calendar page (events)
Route::get('/calendar', function (Request $request) {
    $now = Carbon::now();
    $monthParam = $request->query('month');
    try {
        $current = $monthParam ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth() : $now->copy()->startOfMonth();
    } catch (\Exception $e) {
        $current = $now->copy()->startOfMonth();
    }

    $start = $current->copy()->startOfMonth();
    $end = $current->copy()->endOfMonth();

    // Fetch events that intersect the month
    $events = DB::table('events')
        ->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
              ->orWhereBetween('end_date', [$start->toDateString(), $end->toDateString()])
              ->orWhere(function ($q2) use ($start, $end) {
                  $q2->where('start_date', '<=', $start->toDateString())
                     ->where(function ($q3) use ($end) {
                         $q3->whereNull('end_date')->orWhere('end_date', '>=', $end->toDateString());
                     });
              });
        })
        ->orderBy('start_date')
        ->get();

    // Build highlighted date set (Y-m-d => true)
    $highlightedDates = [];
    foreach ($events as $ev) {
        $s = Carbon::parse($ev->start_date);
        $e = $ev->end_date ? Carbon::parse($ev->end_date) : $s->copy();
        if ($e->lt($s)) { $e = $s->copy(); }
        $cursor = $s->copy();
        while ($cursor->lte($e)) {
            if ($cursor->between($start, $end)) {
                $highlightedDates[$cursor->toDateString()] = true;
            }
            $cursor->addDay();
        }
    }

    // Build calendar weeks
    $firstDayOfWeek = 1; // 1=Monday
    $firstGrid = $start->copy()->startOfWeek($firstDayOfWeek);
    $lastGrid = $end->copy()->endOfWeek($firstDayOfWeek);
    $weeks = [];
    $cursor = $firstGrid->copy();
    while ($cursor->lte($lastGrid)) {
        $week = [];
        for ($i = 0; $i < 7; $i++) {
            $dateStr = $cursor->toDateString();
            $week[] = [
                'date' => $dateStr,
                'day' => $cursor->day,
                'in_month' => $cursor->month === $current->month,
                'is_today' => $cursor->isToday(),
                'has_event' => isset($highlightedDates[$dateStr]),
            ];
            $cursor->addDay();
        }
        $weeks[] = $week;
    }

    $monthLabel = $current->format('F Y');
    $prevMonth = $current->copy()->subMonth()->format('Y-m');
    $nextMonth = $current->copy()->addMonth()->format('Y-m');

    $recentEvents = DB::table('events')
        ->whereDate('end_date', '>=', Carbon::today()->toDateString())
        ->orWhere(function ($q) {
            $q->whereNull('end_date')->whereDate('start_date', '>=', Carbon::today()->toDateString());
        })
        ->orderBy('start_date')
        ->limit(6)
        ->get();

    return view('calendar', compact('weeks', 'monthLabel', 'prevMonth', 'nextMonth', 'recentEvents', 'current'));
})->name('calendar');

// Events JSON for FullCalendar
Route::get('/api/events', function (Request $request) {
    $start = $request->query('start');
    $end = $request->query('end');
    $q = DB::table('events');
    if ($start && $end) {
        // return events that intersect the requested range
        $q->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
              ->orWhereBetween('end_date', [$start, $end])
              ->orWhere(function ($q2) use ($start, $end) {
                  $q2->where('start_date', '<=', $start)
                     ->where(function ($q3) use ($end) {
                         $q3->whereNull('end_date')->orWhere('end_date', '>=', $end);
                     });
              });
        });
    }
    $rows = $q->orderBy('start_date')->get();
    $events = $rows->map(function ($r) {
        return [
            'title' => $r->title,
            'start' => $r->start_date,
            'end' => $r->end_date ?: $r->start_date,
            'extendedProps' => [
                'location' => $r->location,
                'description' => $r->description,
            ],
        ];
    });
    return response()->json($events);
})->name('api.events');

// Public: recent news (blog posts)
Route::get('/api/news', function () {
    $rows = DB::table('blog_posts')
        ->orderByDesc('published_at')
        ->orderByDesc('created_at')
        ->limit(6)
        ->get();
    $news = $rows->map(function ($p) {
        return [
            'title' => $p->title,
            'slug' => $p->slug,
            'date' => optional($p->published_at)->format('Y-m-d') ?? optional($p->created_at)->format('Y-m-d'),
        ];
    });
    return response()->json($news);
})->name('api.news');

// API: FAQ
Route::get('/api/faq', function () {
    $pairs = DB::table('site_settings')->pluck('value','key');
    $raw = $pairs['faq.items'] ?? '[]';
    $items = [];
    try { $tmp = json_decode($raw, true); if (is_array($tmp)) { $items = array_values(array_filter($tmp, fn($i)=> isset($i['q'],$i['a']))); } } catch (\Throwable $e) {}
    if (!$items) {
        $items = [
            ['q' => 'How can I check my exam results?', 'a' => 'Use the Results Center and select your exam, year, and school to view documents.'],
            ['q' => 'Which browsers are supported?', 'a' => 'Latest versions of Chrome, Edge, Firefox, and Safari are supported.'],
        ];
    }
    return response()->json($items);
})->name('api.faq');

// API: Contacts (DB-backed)
Route::get('/api/contacts', function () {
    $row = DB::table('contact_infos')->orderByDesc('updated_at')->orderByDesc('id')->first();
    $socials = [];
    if ($row && !empty($row->socials)) {
        $decoded = json_decode($row->socials, true);
        if (is_array($decoded)) { $socials = $decoded; }
    }
    return response()->json([
        'email' => $row->email ?? '',
        'phone' => $row->phone ?? '',
        'address' => $row->address ?? '',
        'hours' => $row->hours ?? '',
        'socials' => $socials,
    ]);
})->name('api.contacts');

// API: Contact form submission
Route::post('/api/contacts/messages', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:160',
        'email' => 'required|email:rfc,dns|max:255',
        'subject' => 'nullable|string|max:255',
        'message' => 'required|string|max:5000',
    ]);
    DB::table('contact_messages')->insert([
        'name' => $data['name'],
        'email' => $data['email'],
        'subject' => $data['subject'] ?? null,
        'message' => $data['message'],
        'ip_address' => $request->ip(),
        'user_agent' => substr((string)$request->userAgent(), 0, 500),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    return response()->json(['success' => true], 201);
})->name('api.contacts.messages.store');

// API (Admin): Upsert contact info
Route::post('/api/admin/contacts', function (Request $request) {
    if (!session('admin_authenticated')) abort(403);
    $data = $request->validate([
        'email' => 'nullable|email:rfc,dns|max:255',
        'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'hours' => 'nullable|string|max:255',
        'socials' => 'nullable|array',
        'socials.*.name' => 'required_with:socials|string|max:80',
        'socials.*.url' => 'required_with:socials|url|max:500',
    ]);
    $payload = [
        'email' => $data['email'] ?? null,
        'phone' => $data['phone'] ?? null,
        'address' => $data['address'] ?? null,
        'hours' => $data['hours'] ?? null,
        'socials' => isset($data['socials']) ? json_encode(array_values($data['socials'])) : null,
        'updated_at' => now(),
    ];
    // Keep a single latest row: update if exists; otherwise create
    $existing = DB::table('contact_infos')->orderByDesc('id')->first();
    if ($existing) {
        DB::table('contact_infos')->where('id', $existing->id)->update($payload);
    } else {
        $payload['created_at'] = now();
        DB::table('contact_infos')->insert($payload);
    }
    return response()->json(['success' => true]);
})->name('api.admin.contacts.upsert');

// Legal pages APIs (Terms, Privacy, Policy)
Route::get('/api/terms', function(){
    $pairs = DB::table('site_settings')->pluck('value','key');
    $title = $pairs['legal.terms_title'] ?? 'Terms and Conditions';
    $body  = $pairs['legal.terms_html'] ?? '<p>These Terms and Conditions govern your use of this service.</p>';
    return response()->json(['title'=>$title,'body'=>$body]);
})->name('api.legal.terms');

Route::get('/api/privacy', function(){
    $pairs = DB::table('site_settings')->pluck('value','key');
    $title = $pairs['legal.privacy_title'] ?? 'Privacy Policy';
    $body  = $pairs['legal.privacy_html'] ?? '<p>We value your privacy and describe our practices in this policy.</p>';
    return response()->json(['title'=>$title,'body'=>$body]);
})->name('api.legal.privacy');

Route::get('/api/policy', function(){
    $pairs = DB::table('site_settings')->pluck('value','key');
    $title = $pairs['legal.policy_title'] ?? 'Site Policy';
    $body  = $pairs['legal.policy_html'] ?? '<p>This policy outlines guidelines and acceptable use.</p>';
    return response()->json(['title'=>$title,'body'=>$body]);
})->name('api.legal.policy');

// Legal pages (web)
Route::get('/terms', function(){
    $pairs = DB::table('site_settings')->pluck('value','key');
    $title = $pairs['legal.terms_title'] ?? 'Terms and Conditions';
    $body  = $pairs['legal.terms_html'] ?? '<p>These Terms and Conditions govern your use of this service.</p>';
    return view('legal.page', ['title'=>$title,'body'=>$body,'endpoint'=>'/api/terms']);
})->name('legal.terms');

Route::get('/privacy', function(){
    $pairs = DB::table('site_settings')->pluck('value','key');
    $title = $pairs['legal.privacy_title'] ?? 'Privacy Policy';
    $body  = $pairs['legal.privacy_html'] ?? '<p>We value your privacy and describe our practices in this policy.</p>';
    return view('legal.page', ['title'=>$title,'body'=>$body,'endpoint'=>'/api/privacy']);
})->name('legal.privacy');

Route::get('/policy', function(){
    $pairs = DB::table('site_settings')->pluck('value','key');
    $title = $pairs['legal.policy_title'] ?? 'Site Policy';
    $body  = $pairs['legal.policy_html'] ?? '<p>This policy outlines guidelines and acceptable use.</p>';
    return view('legal.page', ['title'=>$title,'body'=>$body,'endpoint'=>'/api/policy']);
})->name('legal.policy');

// API: calendar grid + events for a given month (YYYY-MM)
Route::get('/api/calendar', function (Request $request) {
    $now = Carbon::now();
    $monthParam = $request->query('month');
    try { $current = $monthParam ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth() : $now->copy()->startOfMonth(); }
    catch (\Exception $e) { $current = $now->copy()->startOfMonth(); }

    $start = $current->copy()->startOfMonth();
    $end = $current->copy()->endOfMonth();
    $rows = DB::table('events')
        ->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
              ->orWhereBetween('end_date', [$start->toDateString(), $end->toDateString()])
              ->orWhere(function ($q2) use ($start, $end) {
                  $q2->where('start_date', '<=', $start->toDateString())
                     ->where(function ($q3) use ($end) {
                         $q3->whereNull('end_date')->orWhere('end_date', '>=', $end->toDateString());
                     });
              });
        })
        ->orderBy('start_date')->get();
    $events = $rows->map(function($r){
        return [
            'id' => $r->id,
            'title' => $r->title,
            'start' => $r->start_date,
            'end' => $r->end_date ?: $r->start_date,
            'location' => $r->location,
        ];
    });
    $firstDayOfWeek = 1; // Monday
    $firstGrid = $start->copy()->startOfWeek($firstDayOfWeek);
    $lastGrid = $end->copy()->endOfWeek($firstDayOfWeek);
    $weeks = [];
    $cursor = $firstGrid->copy();
    $eventDates = [];
    foreach ($rows as $ev) {
        $s = Carbon::parse($ev->start_date);
        $e = $ev->end_date ? Carbon::parse($ev->end_date) : $s->copy();
        if ($e->lt($s)) { $e = $s->copy(); }
        $c = $s->copy();
        while ($c->lte($e)) { $eventDates[$c->toDateString()] = true; $c->addDay(); }
    }
    while ($cursor->lte($lastGrid)) {
        $week = [];
        for ($i=0;$i<7;$i++) {
            $dateStr = $cursor->toDateString();
            $week[] = [
                'date' => $dateStr,
                'day' => $cursor->day,
                'in_month' => $cursor->month === $current->month,
                'is_today' => $cursor->isToday(),
                'has_event' => isset($eventDates[$dateStr]),
            ];
            $cursor->addDay();
        }
        $weeks[] = $week;
    }
    return response()->json(['month' => $current->format('Y-m'), 'weeks' => $weeks, 'events' => $events]);
})->name('api.calendar');

// API: recent events (list)
Route::get('/api/events/recent', function (Request $request) {
    $limit = (int)($request->query('limit', 10)); if ($limit < 1 || $limit > 100) $limit = 10;
    $rows = DB::table('events')->orderByDesc('start_date')->limit($limit)->get();
    $data = $rows->map(function($r){ return [
        'id'=>$r->id, 'title'=>$r->title, 'start'=>$r->start_date, 'end'=>$r->end_date ?: $r->start_date, 'location'=>$r->location
    ];});
    return response()->json($data);
})->name('api.events.recent');

// API: publications folders (with counts)
Route::get('/api/publications/folders', function () {
    $rows = DB::table('publication_folders')
        ->leftJoin('publications','publications.folder_id','=','publication_folders.id')
        ->groupBy('publication_folders.id','publication_folders.name','publication_folders.slug')
        ->select('publication_folders.id','publication_folders.name','publication_folders.slug', DB::raw('COUNT(publications.id) as count'))
        ->orderBy('publication_folders.name')
        ->get();
    return response()->json($rows);
})->name('api.publications.folders');

// API: publications list
Route::get('/api/publications', function (Request $request) {
    $limit = (int)($request->query('limit', 12)); if ($limit < 1 || $limit > 50) $limit = 12;
    $page = max(1, (int)$request->query('page', 1));
    $offset = ($page - 1) * $limit;
    $q = DB::table('publications')->orderByDesc('published_at')->orderByDesc('created_at');
    if ($request->filled('folder')) {
        $folder = DB::table('publication_folders')->where('slug', $request->string('folder'))->first();
        if ($folder) { $q->where('folder_id', $folder->id); }
    }
    $total = $q->count();
    $rows = $q->offset($offset)->limit($limit)->get();
    $items = $rows->map(function($p){
        $view = null; $download = null; $isExternal = false; $path = (string)($p->file_path ?? '');
        if ($path) {
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                $view = $path; $download = $path; $isExternal = true;
            } else {
                // Internal files: separate view (inline) and download URLs
                $view = route('publication.view', ['id' => $p->id]);
                $download = route('publication.download', ['id' => $p->id]);
            }
        }
        return [
            'id' => $p->id,
            'title' => $p->title,
            'file_url' => $view,
            'view_url' => $view,
            'download' => $download,
            'file_size' => $p->file_size,
            'published_at' => optional($p->published_at)->toAtomString() ?? optional($p->created_at)->toAtomString(),
            'external' => $isExternal,
        ];
    });
    return response()->json(['items'=>$items, 'total'=>$total, 'page'=>$page, 'limit'=>$limit]);
})->name('api.publications.index');

// API: publication detail
Route::get('/api/publications/{id}', function (int $id) {
    $p = DB::table('publications')->where('id',$id)->first();
    abort_unless($p, 404);
    $folder = $p->folder_id ? DB::table('publication_folders')->where('id',$p->folder_id)->first() : null;
    $url = null; $view = null; $download = null; $isExternal = false; $path = (string)($p->file_path ?? '');
    if ($path) {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $url = $path; $view = $path; $download = $path; $isExternal = true;
        } else {
            $view = route('publication.view', ['id' => $p->id]);
            $download = route('publication.download', ['id' => $p->id]);
            $url = $view;
        }
    }
    return response()->json([
        'id' => $p->id,
        'title' => $p->title,
        'folder' => $folder ? ['id'=>$folder->id, 'name'=>$folder->name, 'slug'=>$folder->slug] : null,
        'file_url' => $url,
        'view_url' => $view,
        'file_size' => $p->file_size,
        'published_at' => optional($p->published_at)->toAtomString() ?? optional($p->created_at)->toAtomString(),
        'external' => $isExternal,
        'download' => $download,
    ]);
})->name('api.publications.show');

// API: blogs list
Route::get('/api/blogs', function (Request $request) {
    $limit = (int)($request->query('limit', 6)); if ($limit < 1 || $limit > 50) $limit = 6;
    $page = max(1, (int)$request->query('page', 1));
    $offset = ($page - 1) * $limit;
    $q = DB::table('blog_posts')->orderByDesc('published_at')->orderByDesc('created_at');
    $total = $q->count();
    $rows = $q->offset($offset)->limit($limit)->get();
    $items = $rows->map(function($p){
        $img = null;
        if (!empty($p->image_path)) {
            $path = (string)$p->image_path;
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                $img = $path;
            } else {
                // normalize storage or relative paths to use blog-images route
                $clean = preg_replace('#^/?storage/#','', $path);
                $clean = ltrim($clean, '/');
                $img = route('blog.images.show', ['path' => $clean]);
            }
        }
        return [
            'title'=>$p->title,
            'slug'=>$p->slug,
            'excerpt'=>$p->excerpt,
            'date'=> optional($p->published_at)->format('Y-m-d') ?? optional($p->created_at)->format('Y-m-d'),
            'image'=> $img,
            'link'=> url('/blogs/'.$p->slug),
        ];
    });
    return response()->json(['items'=>$items, 'total'=>$total, 'page'=>$page, 'limit'=>$limit]);
})->name('api.blogs.index');

// API: blog detail
Route::get('/api/blogs/{slug}', function (string $slug) {
    $p = DB::table('blog_posts')->where('slug',$slug)->first();
    abort_unless($p, 404);
    $img = null;
    if (!empty($p->image_path)) {
        $path = (string)$p->image_path;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $img = $path;
        } else {
            $clean = preg_replace('#^/?storage/#','', $path);
            $clean = ltrim($clean, '/');
            $img = route('blog.images.show', ['path' => $clean]);
        }
    }
    return response()->json([
        'title'=>$p->title,
        'slug'=>$p->slug,
        'excerpt'=>$p->excerpt,
        'content'=>$p->content,
        'date'=> optional($p->published_at)->format('Y-m-d') ?? optional($p->created_at)->format('Y-m-d'),
        'image'=>$img,
    ]);
})->name('api.blogs.show');

// Admin: Events create
Route::middleware([])->group(function () {
    $ensureAdmin = function () {
        if (!session('admin_authenticated')) { abort(403); }
    };

    Route::get('/admin/events/create', function () use ($ensureAdmin) {
        $ensureAdmin();
        return view('admin.events.create');
    })->name('admin.events.create');

    Route::get('/admin/events', function () use ($ensureAdmin) {
        $ensureAdmin();
        $events = DB::table('events')->orderByDesc('start_date')->paginate(15);
        return view('admin.events.index', compact('events'));
    })->name('admin.events.index');

    Route::post('/admin/events', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
        ]);
        DB::table('events')->insert([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'location' => $data['location'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.events.index');
    })->name('admin.events.store');

    Route::get('/admin/events/{id}/edit', function (int $id) use ($ensureAdmin) {
        $ensureAdmin();
        $event = DB::table('events')->where('id', $id)->first();
        abort_unless($event, 404);
        return view('admin.events.edit', compact('event'));
    })->name('admin.events.edit');

    Route::post('/admin/events/{id}', function (Request $request, int $id) use ($ensureAdmin) {
        $ensureAdmin();
        $event = DB::table('events')->where('id', $id)->first();
        abort_unless($event, 404);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
        ]);
        DB::table('events')->where('id', $id)->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'location' => $data['location'] ?? null,
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.events.index');
    })->name('admin.events.update');

    Route::post('/admin/events/{id}/delete', function (int $id) use ($ensureAdmin) {
        $ensureAdmin();
        DB::table('events')->where('id', $id)->delete();
        return redirect()->route('admin.events.index');
    })->name('admin.events.delete');
});
// Publications (DB-backed)
Route::get('/publications', function () {
    $folders = DB::table('publication_folders')->orderBy('name')->get();
    return view('publications.index', compact('folders'));
})->name('publications.index');

Route::get('/publications/{slug}', function (string $slug) {
    $folder = DB::table('publication_folders')->where('slug', $slug)->first();
    abort_unless($folder, 404);
    $rows = DB::table('publications')->where('folder_id', $folder->id)
        ->orderByDesc('published_at')->orderByDesc('created_at')->get();
    $docs = $rows->map(function ($r) {
        $isExternal = Str::startsWith($r->file_path, ['http://','https://','/http']);
        // For internal files, use a dedicated view route for inline preview, and keep download route for saving
        $viewUrl = $isExternal ? $r->file_path : route('publication.view', ['id' => $r->id]);
        $downloadUrl = $isExternal ? $r->file_path : route('publication.download', ['id' => $r->id]);
        return [
            'name' => $r->title,
            'date' => optional($r->published_at)->format('Y-m-d') ?? optional($r->created_at)->format('Y-m-d'),
            'size' => $r->file_size ? number_format($r->file_size / 1048576, 2).' MB' : '-',
            'url' => $viewUrl,
            'download_url' => $downloadUrl,
        ];
    });
    return view('publications.folder', ['folder' => $folder, 'docs' => $docs]);
})->name('publications.folder');

// View publication file inline (preview)
Route::get('/view/publication/{id}', function (int $id) {
    $publication = DB::table('publications')->where('id', $id)->first();
    abort_unless($publication, 404);

    $isExternal = Str::startsWith($publication->file_path, ['http://','https://','/http']);

    if ($isExternal) {
        return redirect($publication->file_path);
    }

    $filePath = storage_path('app/public/' . $publication->file_path);
    abort_unless(file_exists($filePath), 404);

    // Let the browser decide how to preview based on the file's MIME type
    $mime = mime_content_type($filePath) ?: 'application/octet-stream';
    return response()->file($filePath, ['Content-Type' => $mime]);
})->name('publication.view');

// Download publication file
Route::get('/download/publication/{id}', function (int $id) {
    $publication = DB::table('publications')->where('id', $id)->first();
    abort_unless($publication, 404);
    
    $isExternal = Str::startsWith($publication->file_path, ['http://','https://','/http']);
    
    if ($isExternal) {
        return redirect($publication->file_path);
    }
    
    $filePath = storage_path('app/public/' . $publication->file_path);
    abort_unless(file_exists($filePath), 404);
    
    $filename = $publication->title . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
    
    return response()->download($filePath, $filename);
})->name('publication.download');

// Admin: Publications management (requires admin session)
Route::middleware([])->group(function () {
    $ensureAdmin = function () {
        if (!session('admin_authenticated')) {
            abort(403);
        }
    };

    Route::get('/admin/publications/folders/create', function () use ($ensureAdmin) {
        $ensureAdmin();
        return view('admin.publications.folders-create');
    })->name('admin.pub.folders.create');

    Route::post('/admin/publications/folders', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate(['name' => 'required|string|max:160']);
        $slug = Str::slug($data['name']);
        // Ensure unique slug
        $base = $slug; $i = 1;
        while (DB::table('publication_folders')->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        DB::table('publication_folders')->insert([
            'name' => $data['name'],
            'slug' => $slug,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.pub.index')
            ->with('success', 'Folder "' . $data['name'] . '" created successfully!');
    })->name('admin.pub.folders.store');

    Route::get('/admin/publications/create', function () use ($ensureAdmin) {
        $ensureAdmin();
        $folders = DB::table('publication_folders')->orderBy('name')->get();
        $selectedFolderId = request()->query('folder_id');
        return view('admin.publications.publications-create', compact('folders','selectedFolderId'));
    })->name('admin.pub.create');

    Route::post('/admin/publications', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $validated = $request->validate([
            'folder_id' => 'required|exists:publication_folders,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240', // Max 10MB
            'published_at' => 'nullable|date',
        ]);
        
        $file = $request->file('file');
        $path = $file->store('publications', 'public');
        $size = $file->getSize();
        
        DB::table('publications')->insert([
            'folder_id' => $validated['folder_id'],
            'title' => $validated['title'],
            'file_path' => $path,
            'file_size' => $size,
            'published_at' => $validated['published_at'] ?? now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return redirect()->route('admin.pub.folder', ['id' => $validated['folder_id']])
            ->with('success', 'Document uploaded successfully!');
    })->name('admin.pub.store');

    Route::post('/admin/publications/{id}/delete', function (int $id) use ($ensureAdmin) {
        $ensureAdmin();
        $publication = DB::table('publications')->where('id', $id)->first();
        abort_unless($publication, 404);
        $folderId = $publication->folder_id;
        // Delete stored file if it is a local storage path
        if (!empty($publication->file_path) && !Str::startsWith($publication->file_path, ['http://', 'https://'])) {
            $path = $publication->file_path;
            // Normalise any leading storage/ prefix
            $path = preg_replace('#^/?storage/#','', $path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        DB::table('publications')->where('id', $id)->delete();
        return redirect()->route('admin.pub.folder', ['id' => $folderId])
            ->with('success', 'Document deleted successfully!');
    })->name('admin.pub.delete');
});

// Blogs (DB-backed)
Route::get('/blogs', function (Request $request) {
    $query = DB::table('blog_posts');
    if ($request->filled('category')) {
        $cat = DB::table('blog_categories')->where('slug', $request->string('category'))->first();
        if ($cat) { $query->where('category_id', $cat->id); }
    }
    $posts = $query
        ->orderByDesc('published_at')
        ->orderByDesc('created_at')
        ->limit(12)
        ->get()
        ->map(function ($p) {
            $img = null;
            if (!empty($p->image_path)) {
                $path = (string)$p->image_path;
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    $img = $path;
                } else {
                    $clean = preg_replace('#^/?storage/#','', $path);
                    $clean = ltrim($clean, '/');
                    $img = route('blog.images.show', ['path' => $clean]);
                }
            }
            return [
                'slug' => $p->slug,
                'title' => $p->title,
                'image' => $img,
                'date' => optional($p->published_at)->format('Y-m-d') ?? optional($p->created_at)->format('Y-m-d'),
                'excerpt' => $p->excerpt,
            ];
        });
    $categories = DB::table('blog_categories')
        ->leftJoin('blog_posts','blog_posts.category_id','=','blog_categories.id')
        ->groupBy('blog_categories.id','blog_categories.name','blog_categories.slug')
        ->select('blog_categories.name','blog_categories.slug', DB::raw('COUNT(blog_posts.id) as count'))
        ->orderBy('blog_categories.name')
        ->get();
    $latest = DB::table('blog_posts')->orderByDesc('published_at')->orderByDesc('created_at')->limit(5)->get()->map(function ($p) {
        $img = null;
        if (!empty($p->image_path)) {
            $path = (string)$p->image_path;
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/storage/')) {
                $img = $path;
            } else {
                $img = Storage::url($path);
            }
        }
        return [
            'slug' => $p->slug,
            'title' => $p->title,
            'image' => $img,
            'date' => optional($p->published_at)->format('Y-m-d') ?? optional($p->created_at)->format('Y-m-d'),
        ];
    });
    return view('blogs.index', compact('posts','categories','latest'));
})->name('blogs.index');

Route::get('/blogs/{slug}', function (string $slug) {
    $p = DB::table('blog_posts')->where('slug',$slug)->first();
    abort_unless($p, 404);
    $img = null;
    if (!empty($p->image_path)) {
        $path = (string)$p->image_path;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/storage/')) {
            $img = $path;
        } else {
            $img = Storage::url($path);
        }
    }
    $post = [
        'slug' => $p->slug,
        'title' => $p->title,
        'image' => $img,
        'date' => optional($p->published_at)->format('Y-m-d') ?? optional($p->created_at)->format('Y-m-d'),
        'content' => $p->content,
        'category' => optional(DB::table('blog_categories')->where('id',$p->category_id)->first())->name,
    ];
    $categories = DB::table('blog_categories')
        ->leftJoin('blog_posts','blog_posts.category_id','=','blog_categories.id')
        ->groupBy('blog_categories.id','blog_categories.name','blog_categories.slug')
        ->select('blog_categories.name','blog_categories.slug', DB::raw('COUNT(blog_posts.id) as count'))
        ->orderBy('blog_categories.name')
        ->get();
    $latest = DB::table('blog_posts')->orderByDesc('published_at')->orderByDesc('created_at')->limit(5)->get()->map(function ($p) {
        return [
            'slug' => $p->slug,
            'title' => $p->title,
            'image' => $p->image_path ? Storage::url($p->image_path) : null,
            'date' => optional($p->published_at)->format('Y-m-d') ?? optional($p->created_at)->format('Y-m-d'),
        ];
    });
    return view('blogs.show', compact('post','categories','latest'));
})->name('blogs.show');

// Admin: Blogs create
Route::middleware([])->group(function () {
    $ensureAdmin = function () { if (!session('admin_authenticated')) abort(403); };

    // Blogs list
    Route::get('/admin/blog', function () use ($ensureAdmin) {
        $ensureAdmin();
        $posts = DB::table('blog_posts')->orderByDesc('published_at')->orderByDesc('created_at')->get();
        $cats = DB::table('blog_categories')->orderBy('name')->get();
        return view('admin.blog.index', ['posts' => $posts, 'categories' => $cats]);
    })->name('admin.blog.index');

    // Categories list/manage
    Route::get('/admin/blog/categories', function () use ($ensureAdmin) {
        $ensureAdmin();
        $categories = DB::table('blog_categories')->orderBy('name')->get();
        return view('admin.blog.categories-index', compact('categories'));
    })->name('admin.blog.categories.index');

    Route::get('/admin/blog/categories/create', function () use ($ensureAdmin) {
        $ensureAdmin();
        return view('admin.blog.categories-create');
    })->name('admin.blog.categories.create');

    Route::post('/admin/blog/categories', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate(['name'=>'required|string|max:160']);
        $slug = Str::slug($data['name']);
        $base=$slug; $i=1; while (DB::table('blog_categories')->where('slug',$slug)->exists()) $slug=$base.'-'.$i++;
        DB::table('blog_categories')->insert(['name'=>$data['name'],'slug'=>$slug,'created_at'=>now(),'updated_at'=>now()]);
        return redirect()->route('admin.blog.categories.index');
    })->name('admin.blog.categories.store');

    Route::post('/admin/blog/categories/{id}', function (Request $request, int $id) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate(['name'=>'required|string|max:160']);
        DB::table('blog_categories')->where('id',$id)->update(['name'=>$data['name'],'updated_at'=>now()]);
        return redirect()->route('admin.blog.categories.index');
    })->name('admin.blog.categories.update');

    Route::post('/admin/blog/categories/{id}/delete', function (int $id) use ($ensureAdmin) {
        $ensureAdmin();
        DB::table('blog_posts')->where('category_id',$id)->update(['category_id'=>null]);
        DB::table('blog_categories')->where('id',$id)->delete();
        return redirect()->route('admin.blog.categories.index');
    })->name('admin.blog.categories.delete');

    Route::get('/admin/blog/create', function () use ($ensureAdmin) {
        $ensureAdmin();
        $categories = DB::table('blog_categories')->orderBy('name')->get();
        return view('admin.blog.posts-create', compact('categories'));
    })->name('admin.blog.create');

    Route::post('/admin/blog', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:3072',
            'image_url' => 'nullable|string|max:1000',
            'published_at' => 'nullable|date',
            'status' => 'nullable|in:draft,published,pending',
            'slug' => 'nullable|string|max:255',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_image' => 'nullable|image|max:3072',
            'seo_image_url' => 'nullable|string|max:1000',
            'indexable' => 'nullable|boolean',
            'tags' => 'nullable|string|max:255',
            'allow_comments' => 'nullable|boolean',
            'gallery.*' => 'nullable|image|max:4096',
        ]);
        $slug = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['title']);
        $base=$slug; $i=1; while (DB::table('blog_posts')->where('slug',$slug)->exists()) $slug=$base.'-'.$i++;
        $path = null;
        if (!empty($data['image_url'])) {
            $path = $data['image_url'];
        } elseif ($request->hasFile('image')) {
            $path = $request->file('image')->store('blog', 'public');
        }
        $seoImagePath = null;
        if (!empty($data['seo_image_url'])) {
            $seoImagePath = $data['seo_image_url'];
        } elseif ($request->hasFile('seo_image')) {
            $seoImagePath = $request->file('seo_image')->store('blog', 'public');
        }
        $publishedAt = in_array(($data['status'] ?? 'published'), ['draft','pending']) ? null : ($data['published_at'] ?? now());
        $galleryPaths = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $g) {
                $galleryPaths[] = $g->store('blog_gallery', 'public');
            }
        }
        DB::table('blog_posts')->insert([
            'title' => $data['title'],
            'slug' => $slug,
            'category_id' => $data['category_id'] ?? null,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'image_path' => $path,
            'published_at' => $publishedAt,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'seo_image' => $seoImagePath,
            'indexable' => isset($data['indexable']) ? (bool)$data['indexable'] : true,
            'tags' => $data['tags'] ?? null,
            'gallery' => !empty($galleryPaths) ? json_encode($galleryPaths) : null,
            'allow_comments' => isset($data['allow_comments']) ? (bool)$data['allow_comments'] : true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.blog.index');
    })->name('admin.blog.store');

    // Edit Blog
    Route::get('/admin/blog/{id}/edit', function (int $id) use ($ensureAdmin) {
        $ensureAdmin();
        $post = DB::table('blog_posts')->where('id',$id)->first();
        abort_unless($post, 404);
        $categories = DB::table('blog_categories')->orderBy('name')->get();
        return view('admin.blog.edit', compact('post','categories'));
    })->name('admin.blog.edit');

    Route::post('/admin/blog/{id}', function (Request $request, int $id) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:3072',
            'published_at' => 'nullable|date',
        ]);
        $update = [
            'title' => $data['title'],
            'category_id' => $data['category_id'] ?? null,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'published_at' => $data['published_at'] ?? DB::raw('published_at'),
            'updated_at' => now(),
        ];
        if ($request->hasFile('image')) {
            $update['image_path'] = $request->file('image')->store('blog', 'public');
        }
        DB::table('blog_posts')->where('id',$id)->update($update);
        return redirect()->route('admin.blog.index');
    })->name('admin.blog.update');

    // Delete Blog
    Route::post('/admin/blog/{id}/delete', function (int $id) use ($ensureAdmin) {
        $ensureAdmin();
        DB::table('blog_posts')->where('id',$id)->delete();
        return redirect()->route('admin.blog.index');
    })->name('admin.blog.delete');

    // One-time: migrate images from storage/app/public to public/assets/images
    Route::get('/admin/tools/migrate-blog-images', function () use ($ensureAdmin) {
        $ensureAdmin();
        $moved = ['cover'=>0,'gallery'=>0];
        // Move cover images
        $fromCover = storage_path('app/public/blog');
        $toCover = public_path('assets/images/blog');
        File::ensureDirectoryExists($toCover);
        if (is_dir($fromCover)) {
            foreach (File::files($fromCover) as $f) {
                $name = $f->getFilename();
                $dest = $toCover.DIRECTORY_SEPARATOR.$name;
                if (!File::exists($dest)) {
                    File::move($f->getPathname(), $dest);
                }
                DB::table('blog_posts')->where('image_path','like','%'.$name.'%')->update([
                    'image_path' => 'assets/images/blog/'.$name,
                    'updated_at' => now(),
                ]);
                $moved['cover']++;
            }
        }
        // Move gallery images and update JSON
        $fromGal = storage_path('app/public/blog_gallery');
        $toGal = public_path('assets/images/blog_gallery');
        File::ensureDirectoryExists($toGal);
        if (is_dir($fromGal)) {
            foreach (File::files($fromGal) as $f) {
                $name = $f->getFilename();
                $dest = $toGal.DIRECTORY_SEPARATOR.$name;
                if (!File::exists($dest)) {
                    File::move($f->getPathname(), $dest);
                }
            }
            $rows = DB::table('blog_posts')->whereNotNull('gallery')->get(['id','gallery']);
            foreach ($rows as $r) {
                $arr = json_decode($r->gallery, true) ?: [];
                $new = array_map(function($p){ return 'assets/images/blog_gallery/'.basename($p); }, $arr);
                DB::table('blog_posts')->where('id',$r->id)->update([
                    'gallery'=>json_encode($new),
                    'updated_at'=>now(),
                ]);
                $moved['gallery'] += count($arr);
            }
        }
        return response()->json(['ok'=>true,'moved'=>$moved]);
    })->name('admin.tools.migrate_blog_images');
});

// Admin: User management (admins CRUD minimal)
Route::middleware([])->group(function () {
    $ensureAdmin = function () { if (!session('admin_authenticated')) abort(403); };

    Route::get('/admin/users', function () use ($ensureAdmin) {
        $ensureAdmin();
        $admins = DB::table('admins')->orderBy('name')->get();
        return view('admin.users.index', compact('admins'));
    })->name('admin.users.index');

    Route::get('/admin/users/create', function () use ($ensureAdmin) {
        $ensureAdmin();
        return view('admin.users.create');
    })->name('admin.users.create');

    Route::post('/admin/users', function (Request $request) use ($ensureAdmin) {
        $ensureAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:160',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        DB::table('admins')->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.users.index');
    })->name('admin.users.store');
});

// Authentication (simple admin-only session auth)
Route::get('/login', function () {
    if (session('admin_authenticated')) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $admin = DB::table('admins')->where('email', $validated['email'])->first();
    $overridePassword = session('admin_password_override');
    $passwordOk = false;
    if ($admin) {
        $passwordOk = ($overridePassword && $validated['password'] === $overridePassword) || Hash::check($validated['password'], $admin->password);
    }

    if ($admin && $passwordOk) {
        $request->session()->put('admin_authenticated', true);
        $request->session()->put('admin_name', $admin->name ?? 'Administrator');
        $request->session()->put('admin_email', $admin->email ?? null);
        $request->session()->forget('admin_password_override');
        return redirect()->route('dashboard');
    }

    return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
})->name('login.post');

Route::get('/dashboard', function () {
    if (!session('admin_authenticated')) {
        return redirect()->route('login');
    }
    $kpis = [
        'publications' => DB::table('publications')->count(),
        'posts' => DB::table('blog_posts')->count(),
        'events' => DB::table('events')->count(),
        'admins' => DB::table('admins')->count(),
    ];
    $recentEvents = DB::table('events')->orderByDesc('start_date')->limit(6)->get();
    return view('dashboard', compact('kpis','recentEvents'));
})->name('dashboard');

Route::post('/logout', function (Request $request) {
    $request->session()->forget('admin_authenticated');
    $request->session()->forget('admin_name');
    $request->session()->forget('admin_email');
    return redirect('/');
})->name('logout');

// Forgot password (OTP) routes
Route::get('/forgot', function () {
    return view('auth.forgot');
})->name('forgot');

Route::post('/forgot', function (Request $request) {
    $validated = $request->validate(['email' => 'required|email']);
    $email = $validated['email'];
    $admin = DB::table('admins')->where('email', $email)->first();
    if (!$admin) {
        return back()->withErrors(['email' => 'This email is not authorized']).withInput();
    }
    $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    DB::table('admin_password_resets')->updateOrInsert(
        ['email' => $email],
        ['otp' => $otp, 'expires_at' => now()->addMinutes(10), 'updated_at' => now(), 'created_at' => now()]
    );
    // TODO: send mail with $otp. For demo, we rely on user to know OTP via admin.
    return redirect()->route('reset')->with('sent', true)->with('email', $email);
})->name('forgot.post');

Route::get('/reset', function () {
    return view('auth.reset');
})->name('reset');

Route::post('/reset', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email',
        'otp' => 'required|string',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $record = DB::table('admin_password_resets')
        ->where('email', $validated['email'])
        ->first();
    if (!$record || $record->otp !== $validated['otp'] || now()->greaterThan($record->expires_at)) {
        return back()->withErrors(['otp' => 'Invalid or expired OTP']);
    }
    // Update admin password
    DB::table('admins')->where('email', $validated['email'])->update([
        'password' => Hash::make($validated['password']),
        'updated_at' => now(),
    ]);
    // cleanup
    DB::table('admin_password_resets')->where('email', $validated['email'])->delete();
    return redirect()->route('login')->with('status', 'Password reset. Use your new password to sign in.');
})->name('reset.post');

// Results flow: /results/{exam} -> years -> regions -> districts -> schools -> PDF
Route::prefix('results')->group(function () {
    // /results/{exam}
    Route::get('{exam}', function (string $exam) {
        $exam = strtoupper($exam);
        $years = DB::table('result_documents')
            ->where('exam', $exam)
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
        $examTitle = $exam;
        return view('results.years', compact('examTitle','years'));
    })->name('results.years');

    // /results/{exam}/{year}
    Route::get('{exam}/{year}', function (string $exam, int $year) {
        $exam = strtoupper($exam);
        $regions = DB::table('result_documents')
            ->where('exam', $exam)
            ->where('year', $year)
            ->join('regions','regions.id','=','result_documents.region_id')
            ->select('regions.name')
            ->distinct()->orderBy('regions.name')->get();
        $examTitle = $exam;
        return view('results.regions', compact('examTitle','year','regions'));
    })->name('results.regions');

    // /results/{exam}/{year}/{region}
    Route::get('{exam}/{year}/{region}', function (string $exam, int $year, string $region) {
        $exam = strtoupper($exam);
        $regionParam = strtoupper(urldecode(str_replace(['+','-'], ' ', $region)));
        $regionRow = DB::table('regions')->whereRaw('upper(name) = ?', [$regionParam])->first();
        abort_unless($regionRow, 404);
        $districts = DB::table('result_documents')
            ->where('result_documents.exam', $exam)
            ->where('result_documents.year', $year)
            ->where('result_documents.region_id', $regionRow->id)
            ->join('districts','districts.id','=','result_documents.district_id')
            ->select('districts.name')
            ->distinct()->orderBy('districts.name')->get();
        $examTitle = $exam;
        $regionName = $regionRow->name;
        return view('results.districts', compact('examTitle','year','regionName','districts'));
    })->name('results.districts');

    // /results/{exam}/{year}/{region}/{district}
    Route::get('{exam}/{year}/{region}/{district}', function (string $exam, int $year, string $region, string $district) {
        $exam = strtoupper($exam);
        $regionParam = strtoupper(urldecode(str_replace(['+','-'], ' ', $region)));
        $districtParam = strtoupper(urldecode(str_replace(['+','-'], ' ', $district)));
        $regionRow = DB::table('regions')->whereRaw('upper(name) = ?', [$regionParam])->first();
        abort_unless($regionRow, 404);
        $districtRow = DB::table('districts')->where('region_id',$regionRow->id)->whereRaw('upper(name) = ?', [$districtParam])->first();
        abort_unless($districtRow, 404);
        $q = DB::table('result_documents')
            ->where('result_documents.exam', $exam)
            ->where('result_documents.year', $year)
            ->where('result_documents.region_id', $regionRow->id)
            ->where('result_documents.district_id', $districtRow->id)
            ->where(function($qq){ if (Schema::hasColumn('result_documents','published')) $qq->where('result_documents.published', true); })
            ->join('schools','schools.id','=','result_documents.school_id')
            ->leftJoin('result_types','result_types.id','=','result_documents.result_type_id')
            ->select('schools.id as school_id','schools.name as school_name','result_documents.file_url','result_documents.created_at','result_types.code as type_code','result_types.name as type_name')
            ->orderBy('schools.name');
        $rows = $q->get();
        $types = [];
        $schoolDocs = [];
        foreach ($rows as $r) {
            $code = $r->type_code ?: $exam;
            $name = $r->type_name ?: $exam;
            if (!isset($types[$code])) $types[$code] = ['code'=>$code,'name'=>$name,'docs'=>[]];
            $types[$code]['docs'][] = [
                'school_id'=>$r->school_id,
                'school_name'=>$r->school_name,
                'file_url'=>$r->file_url,
                'created_at'=>$r->created_at,
            ];
            if (!isset($schoolDocs[$r->school_id])) $schoolDocs[$r->school_id] = $r->file_url;
        }
        $allSchools = DB::table('schools')->where('district_id',$districtRow->id)->orderBy('name')->get();
        $examTitle = $exam;
        return view('results.district', [
            'examTitle'=>$examTitle,
            'year'=>$year,
            'regionName'=>$regionRow->name,
            'districtName'=>$districtRow->name,
            'types'=>$types,
            'allSchools'=>$allSchools,
            'schoolDocs'=>$schoolDocs,
        ]);
    })->name('results.schools');

    // /results/{exam}/{year}/{region}/{district}/{school}
    Route::get('{exam}/{year}/{region}/{district}/{school}', function (string $exam, int $year, string $region, string $district, string $school) {
        $exam = strtoupper($exam);
        $regionParam = strtoupper(urldecode(str_replace(['+','-'], ' ', $region)));
        $districtParam = strtoupper(urldecode(str_replace(['+','-'], ' ', $district)));
        $schoolParam = strtoupper(urldecode(str_replace(['+','-'], ' ', $school)));
        $regionRow = DB::table('regions')->whereRaw('upper(name) = ?', [$regionParam])->first();
        $districtRow = $regionRow ? DB::table('districts')->where('region_id',$regionRow->id)->whereRaw('upper(name) = ?', [$districtParam])->first() : null;
        $schoolRow = $districtRow ? DB::table('schools')->where('district_id',$districtRow->id)->whereRaw('upper(name) = ?', [$schoolParam])->first() : null;
        abort_unless($regionRow && $districtRow && $schoolRow, 404);

        $rows = DB::table('result_documents')
            ->leftJoin('result_types','result_types.id','=','result_documents.result_type_id')
            ->where('result_documents.exam', $exam)
            ->where('result_documents.year', $year)
            ->where('result_documents.region_id', $regionRow->id)
            ->where('result_documents.district_id', $districtRow->id)
            ->where('result_documents.school_id', $schoolRow->id)
            ->orderBy('result_types.name')
            ->get([
                'result_documents.file_url',
                'result_types.code as type_code',
                'result_types.name as type_name',
            ]);

        $grouped = [];
        foreach ($rows as $r) {
            $code = $r->type_code ?: 'OTHER';
            $name = $r->type_name ?: 'Other';
            if (!isset($grouped[$code])) {
                $grouped[$code] = ['code'=>$code, 'name'=>$name, 'docs'=>[]];
            }
            $grouped[$code]['docs'][] = ['url'=>$r->file_url];
        }

        return view('results.school', [
            'examTitle'=>$exam,
            'year'=>$year,
            'regionName'=>$regionRow->name,
            'districtName'=>$districtRow->name,
            'schoolName'=>$schoolRow->name,
            'types'=>array_values($grouped),
        ]);
    })->name('results.school');
});
